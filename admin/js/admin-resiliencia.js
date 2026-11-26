/**
 * FMRU Admin - Sistema Centralizado de Resiliencia y Prevención de Pérdida de Datos
 * Cumplimiento OWASP: Sin almacenamiento de credenciales/CSRF, prevención DOM-XSS y TTL de 24h.
 */
(function () {
    'use strict';

    const MAX_FILE_SIZE = 2 * 1024 * 1024;       // 2 MB por imagen
    const MAX_TOTAL_SIZE = 7.5 * 1024 * 1024;    // 7.5 MB máximo total (protección post_max_size PHP)
    const DRAFT_TTL_MS = 24 * 60 * 60 * 1000;    // Expiración de borradores: 24 horas
    const DRAFT_PREFIX = 'fmru_draft_v1:';

    // Mapa de generadores dinámicos existentes en Competencias, Serpientes y Landings
    const DYNAMIC_ADDERS = {
        'xvsv_club_nombre[]': 'agregarClubXvsV',
        'xvsf_club_nombre[]': 'agregarClubXvsF',
        'tarjeta_titulo[]': 'agregarTarjeta',
        'go_tarjeta_titulo[]': 'agregarTarjetaGameOn',
        'r7s_tarjeta_titulo[]': 'agregarTarjetaR7s',
        'sptf_equipo_nombre[]': 'agregarEquipoSptF',
        'sptv_equipo_nombre[]': 'agregarEquipoSptV',
        'sptf_staff_rol[]': 'agregarStaffSptF',
        'sptv_staff_rol[]': 'agregarStaffSptV',
        'sptf_hist_temporada[]': 'agregarHistoricoSptF',
        'sptv_hist_no[]': 'agregarHistoricoSptV',
        'precio_nombre[]': 'agregarPrecio'
    };

    let hayCambiosSinGuardar = false;

    document.addEventListener('DOMContentLoaded', function () {
        inyectarContenedoresUI();
        monitorearConexionRed();
        limpiarBorradoresExpirados();
        configurarLimpiezaLogout();

        // Seleccionar solo formularios de edición/creación (excluye modales/botones de eliminar)
        const formularios = Array.from(document.querySelectorAll('form[method="POST"], form[method="post"]'))
            .filter(form => !form.querySelector('input[name="eliminar_id"]') && form.dataset.noResilience !== 'true');

        formularios.forEach((form, index) => {
            inicializarValidacionArchivos(form);
            inicializarAutoguardado(form, index);
            inicializarEnvioAsincrono(form, index);
        });

        // Aviso nativo si intentan cerrar la pestaña o apagarse con cambios pendientes
        window.addEventListener('beforeunload', function (e) {
            if (hayCambiosSinGuardar) {
                e.preventDefault();
                e.returnValue = '';
            }
        });
    });

    // =========================================================
    // UI: TOASTS FLOTANTES E INDICADOR DE RED (BOOTSTRAP 5.3)
    // =========================================================
    function inyectarContenedoresUI() {
        if (!document.getElementById('fmruToastContainer')) {
            const container = document.createElement('div');
            container.id = 'fmruToastContainer';
            container.className = 'toast-container position-fixed top-0 end-0 p-3';
            container.style.zIndex = '1095';
            document.body.appendChild(container);
        }

        if (!document.getElementById('fmruOfflineBanner')) {
            const banner = document.createElement('div');
            banner.id = 'fmruOfflineBanner';
            banner.className = 'alert alert-warning shadow-lg border-warning d-none position-fixed bottom-0 end-0 m-3 py-2 px-3 d-flex align-items-center';
            banner.style.zIndex = '1095';
            banner.innerHTML = '<i class="bi bi-wifi-off fs-5 me-2 text-danger"></i><div class="small fw-bold">Sin internet: Tus cambios se están respaldando en este equipo.</div>';
            document.body.appendChild(banner);
        }
    }

    function mostrarToast(mensaje, tipo = 'success', accionHtml = '') {
        const container = document.getElementById('fmruToastContainer');
        if (!container) return;

        const colores = {
            success: { bg: 'text-bg-success', icon: 'bi-check-circle-fill' },
            danger:  { bg: 'text-bg-danger',  icon: 'bi-exclamation-octagon-fill' },
            warning: { bg: 'text-bg-warning', icon: 'bi-wifi-off' },
            info:    { bg: 'text-bg-dark',    icon: 'bi-info-circle-fill' }
        };
        const conf = colores[tipo] || colores.info;

        const toastEl = document.createElement('div');
        toastEl.className = `toast align-items-center ${conf.bg} border-0 shadow-lg mb-2`;
        toastEl.setAttribute('role', 'alert');
        toastEl.setAttribute('aria-live', 'assertive');
        toastEl.setAttribute('aria-atomic', 'true');

        const wrapper = document.createElement('div');
        wrapper.className = 'd-flex p-2';

        const body = document.createElement('div');
        body.className = 'toast-body d-flex align-items-start gap-2 w-100';

        const icon = document.createElement('i');
        icon.className = `bi ${conf.icon} fs-5 flex-shrink-0`;

        const textWrap = document.createElement('div');
        textWrap.className = 'flex-grow-1';

        const msgNode = document.createElement('div');
        msgNode.className = 'fw-semibold';
        msgNode.textContent = mensaje; // OWASP: textContent previene inyección XSS
        textWrap.appendChild(msgNode);

        if (accionHtml) {
            const actionContainer = document.createElement('div');
            actionContainer.className = 'mt-2';
            actionContainer.innerHTML = accionHtml; // Solo HTML interno controlado
            textWrap.appendChild(actionContainer);
        }

        const btnClose = document.createElement('button');
        btnClose.type = 'button';
        btnClose.className = `btn-close ${tipo === 'warning' ? '' : 'btn-close-white'} me-2 m-auto`;
        btnClose.setAttribute('data-bs-dismiss', 'toast');
        btnClose.setAttribute('aria-label', 'Cerrar');

        body.appendChild(icon);
        body.appendChild(textWrap);
        wrapper.appendChild(body);
        wrapper.appendChild(btnClose);
        toastEl.appendChild(wrapper);
        container.appendChild(toastEl);

        const autohide = (tipo === 'success' || tipo === 'info');
        if (window.bootstrap && bootstrap.Toast) {
            const bsToast = new bootstrap.Toast(toastEl, { autohide: autohide, delay: 4500 });
            bsToast.show();
            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        }
    }

    function monitorearConexionRed() {
        const offlineBanner = document.getElementById('fmruOfflineBanner');
        const actualizarEstado = () => {
            if (!navigator.onLine) {
                offlineBanner.classList.remove('d-none');
            } else {
                if (!offlineBanner.classList.contains('d-none')) {
                    offlineBanner.classList.add('d-none');
                    mostrarToast('Conexión a internet restablecida. Ya puedes guardar tus cambios.', 'success');
                }
            }
        };
        window.addEventListener('offline', actualizarEstado);
        window.addEventListener('online', actualizarEstado);
        if (!navigator.onLine) offlineBanner.classList.remove('d-none');
    }

    // =========================================================
    // CAPA 1: VALIDACIÓN PREVENTIVA DE IMÁGENES (>2MB Y FORMATO)
    // =========================================================
    function inicializarValidacionArchivos(form) {
        form.addEventListener('change', function (e) {
            const input = e.target;
            if (!input || input.tagName !== 'INPUT' || input.type !== 'file') return;

            limpiarErrorInput(input);
            if (!input.files || input.files.length === 0) return;

            const acceptAttr = (input.getAttribute('accept') || 'image/jpeg,image/png,image/webp')
                .split(',')
                .map(s => s.trim().toLowerCase())
                .filter(Boolean);

            let pesoTotalInput = 0;

            for (const file of input.files) {
                pesoTotalInput += file.size;
                const fileMime = (file.type || '').toLowerCase();
                const fileExt = '.' + (file.name.split('.').pop() || '').toLowerCase();

                const formatoValido = acceptAttr.some(rule => {
                    if (rule.endsWith('/*')) return fileMime.startsWith(rule.replace('/*', '/'));
                    if (rule.startsWith('.')) return fileExt === rule;
                    return fileMime === rule;
                });

                if (!formatoValido) {
                    const msg = `El archivo "${file.name}" no tiene un formato permitido (${acceptAttr.join(', ')}). Tus textos siguen intactos.`;
                    marcarErrorInput(input, msg);
                    mostrarToast(msg, 'danger');
                    input.value = '';
                    return;
                }

                if (file.size > MAX_FILE_SIZE) {
                    const pesoMb = (file.size / (1024 * 1024)).toFixed(2);
                    const msg = `La imagen "${file.name}" pesa ${pesoMb} MB y supera el límite máximo de 2.00 MB. Elige una imagen más ligera; tus textos no se han perdido.`;
                    marcarErrorInput(input, msg);
                    mostrarToast(msg, 'danger');
                    input.value = '';
                    return;
                }
            }

            // Verificar peso acumulado de todos los inputs file del formulario
            let pesoGlobalForm = 0;
            form.querySelectorAll('input[type="file"]').forEach(fInput => {
                if (fInput.files) {
                    Array.from(fInput.files).forEach(f => { pesoGlobalForm += f.size; });
                }
            });

            if (pesoGlobalForm > MAX_TOTAL_SIZE) {
                const totalMb = (pesoGlobalForm / (1024 * 1024)).toFixed(2);
                const msg = `El total de imágenes seleccionadas (${totalMb} MB) excede el límite de envío simultáneo (7.50 MB). Súbelas en dos tandas para evitar errores.`;
                marcarErrorInput(input, msg);
                mostrarToast(msg, 'danger');
                input.value = '';
            }
        });
    }

    function marcarErrorInput(input, mensaje) {
        input.classList.add('is-invalid');
        let feedback = input.parentElement.querySelector('.invalid-feedback.fmru-file-error');
        if (!feedback) {
            feedback = document.createElement('div');
            feedback.className = 'invalid-feedback fmru-file-error fw-semibold d-block mt-1';
            input.insertAdjacentElement('afterend', feedback);
        }
        feedback.textContent = mensaje;
    }

    function limpiarErrorInput(input) {
        input.classList.remove('is-invalid');
        const feedback = input.parentElement.querySelector('.invalid-feedback.fmru-file-error');
        if (feedback) feedback.remove();
    }

    // =========================================================
    // CAPA 3: AUTOGUARDADO LOCAL (CONTRA APAGONES / CRASH DE PC)
    // =========================================================
    function obtenerClaveBorrador(form, index) {
        const identificadorSeccion =
            form.querySelector('input[name="seccion_competencia"]')?.value ||
            form.querySelector('input[name="seccion_serpientes"]')?.value ||
            form.querySelector('input[name="tab_origen"]')?.value ||
            `form_${index}`;
        return `${DRAFT_PREFIX}${window.location.pathname}${window.location.search}:${identificadorSeccion}`;
    }

    function serializarFormulario(form) {
        if (window.tinymce) window.tinymce.triggerSave();

        const datos = {};
        const elementos = form.querySelectorAll('input, textarea, select');

        elementos.forEach(el => {
            if (!el.name || el.disabled) return;
            // OWASP: Jamás almacenar contraseñas, archivos ni tokens CSRF en localStorage
            if (el.type === 'password' || el.type === 'file' || el.type === 'submit' || el.type === 'button') return;
            // if (/csrf/i.test(el.name)) return;
            
            // Por esta validación extendida OWASP:
            if (/csrf|password|clave/i.test(el.name) || el.dataset.noDraft === 'true') return;

            const esArray = el.name.endsWith('[]');
            if (el.type === 'checkbox') {
                if (esArray) {
                    if (!datos[el.name]) datos[el.name] = [];
                    datos[el.name].push(el.checked);
                } else {
                    datos[el.name] = el.checked;
                }
            } else if (el.type === 'radio') {
                if (el.checked) datos[el.name] = el.value;
            } else {
                if (esArray) {
                    if (!datos[el.name]) datos[el.name] = [];
                    datos[el.name].push(el.value);
                } else {
                    datos[el.name] = el.value;
                }
            }
        });

        return datos;
    }

    function restaurarFormulario(form, datos) {
        // 1. Ajustar cantidad de elementos dinámicos (tarjetas, clubes, staff, histórico, precios)
        Object.keys(DYNAMIC_ADDERS).forEach(fieldName => {
            if (!Array.isArray(datos[fieldName])) return;
            const fnName = DYNAMIC_ADDERS[fieldName];
            const actuales = form.querySelectorAll(`[name="${CSS.escape(fieldName)}"]`);
            const diferencia = datos[fieldName].length - actuales.length;

            if (diferencia > 0 && typeof window[fnName] === 'function') {
                for (let i = 0; i < diferencia; i++) {
                    window[fnName]();
                }
            }
        });

        // 2. Poblar valores de forma segura (sin inyectar HTML crudo en el DOM)
        const contadoresArray = {};
        const elementos = form.querySelectorAll('input, textarea, select');

        elementos.forEach(el => {
            if (!el.name || el.type === 'password' || el.type === 'file' || /csrf/i.test(el.name)) return;
            if (!(el.name in datos)) return;

            const esArray = el.name.endsWith('[]');
            let valor;

            if (esArray) {
                const idx = contadoresArray[el.name] || 0;
                contadoresArray[el.name] = idx + 1;
                if (!Array.isArray(datos[el.name]) || idx >= datos[el.name].length) return;
                valor = datos[el.name][idx];
            } else {
                valor = datos[el.name];
            }

            if (el.type === 'checkbox') {
                el.checked = Boolean(valor);
            } else if (el.type === 'radio') {
                el.checked = (el.value === valor);
            } else {
                el.value = valor;
                // Sincronizar hacia el iframe visual de TinyMCE si aplica
                if (el.tagName === 'TEXTAREA' && window.tinymce) {
                    const editor = el.id ? window.tinymce.get(el.id) : window.tinymce.editors.find(ed => ed.targetElm === el);
                    if (editor) editor.setContent(String(valor));
                }
            }
            el.dispatchEvent(new Event('change', { bubbles: true }));
        });
    }

    function inicializarAutoguardado(form, index) {
        const storageKey = obtenerClaveBorrador(form, index);
        let debounceTimer = null;

        // Verificar si existe un borrador previo sin guardar
        try {
            const raw = localStorage.getItem(storageKey);
            if (raw) {
                const parsed = JSON.parse(raw);
                if (parsed && parsed.timestamp && (Date.now() - parsed.timestamp) < DRAFT_TTL_MS && parsed.data) {
                    mostrarAvisoRestauracion(form, storageKey, parsed);
                } else {
                    localStorage.removeItem(storageKey);
                }
            }
        } catch (e) {
            localStorage.removeItem(storageKey);
        }

        const guardarBorrador = () => {
            hayCambiosSinGuardar = true;
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                try {
                    const payload = {
                        timestamp: Date.now(),
                        data: serializarFormulario(form)
                    };
                    localStorage.setItem(storageKey, JSON.stringify(payload));
                } catch (err) {
                    // Cuota de localStorage llena: fallo silencioso sin romper el formulario
                }
            }, 600);
        };

        form.addEventListener('input', guardarBorrador);
        form.addEventListener('change', guardarBorrador);

        // Detectar clics en botones de eliminar tarjeta/fila dentro del formulario
        form.addEventListener('click', function (e) {
            if (e.target.closest('button[onclick*="remove"], button[onclick*="agregar"]')) {
                setTimeout(guardarBorrador, 150);
            }
        });

        // Enganchar eventos de escritura dentro de TinyMCE (actuales y futuros)
        const vincularEditorTiny = (editor) => {
            editor.on('input Change Undo Redo', () => {
                if (form.contains(editor.targetElm)) guardarBorrador();
            });
        };

        if (window.tinymce) {
            window.tinymce.editors.forEach(vincularEditorTiny);
            window.tinymce.on('AddEditor', (e) => vincularEditorTiny(e.editor));
        } else {
            window.addEventListener('load', () => {
                if (window.tinymce) {
                    window.tinymce.editors.forEach(vincularEditorTiny);
                    window.tinymce.on('AddEditor', (e) => vincularEditorTiny(e.editor));
                }
            });
        }
    }

    function mostrarAvisoRestauracion(form, storageKey, borrador) {
        const fecha = new Date(borrador.timestamp).toLocaleTimeString('es-MX', { hour: '2-digit', minute: '2-digit' });
        const banner = document.createElement('div');
        banner.className = 'alert alert-warning border-warning shadow-sm d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4';
        banner.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="bi bi-clock-history fs-5 me-2 text-dark"></i>
                <span class="small text-dark">Se encontró un <strong>borrador sin guardar</strong> en este equipo (de las <strong>${fecha}</strong>).</span>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-dark fw-bold btn-restaurar-borrador">
                    <i class="bi bi-arrow-counterclockwise me-1"></i> Restaurar cambios
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary btn-descartar-borrador">Descartar</button>
            </div>
        `;

        form.prepend(banner);

        banner.querySelector('.btn-restaurar-borrador').addEventListener('click', function () {
            restaurarFormulario(form, borrador.data);
            hayCambiosSinGuardar = true;
            banner.remove();
            mostrarToast('Borrador restaurado en pantalla. Revisa los datos y presiona Guardar.', 'info');
        });

        banner.querySelector('.btn-descartar-borrador').addEventListener('click', function () {
            localStorage.removeItem(storageKey);
            banner.remove();
        });
    }

    function limpiarBorradoresExpirados() {
        try {
            Object.keys(localStorage).forEach(key => {
                if (key.startsWith(DRAFT_PREFIX)) {
                    const item = JSON.parse(localStorage.getItem(key) || '{}');
                    if (!item.timestamp || (Date.now() - item.timestamp) > DRAFT_TTL_MS) {
                        localStorage.removeItem(key);
                    }
                }
            });
        } catch (e) {}
    }

    function configurarLimpiezaLogout() {
        document.querySelectorAll('a[href*="logout.php"]').forEach(link => {
            link.addEventListener('click', () => {
                hayCambiosSinGuardar = false;
                Object.keys(localStorage).forEach(key => {
                    if (key.startsWith(DRAFT_PREFIX)) localStorage.removeItem(key);
                });
            });
        });
    }

    // =========================================================
    // CAPA 2: ENVÍO ASÍNCRONO (FETCH) SIN RECARGA DE PÁGINA
    // =========================================================
    async function refrescarTokenCsrf(form) {
        const csrfInput = form.querySelector('input[name="csrf_token"]');
        if (!csrfInput) return;
        try {
            const resp = await fetch(window.location.href, { credentials: 'same-origin' });
            const html = await resp.text();
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const nuevoToken = doc.querySelector('input[name="csrf_token"]')?.value;
            if (nuevoToken) csrfInput.value = nuevoToken;
        } catch (e) {}
    }

    function inicializarEnvioAsincrono(form, index) {
        const storageKey = obtenerClaveBorrador(form, index);

        form.addEventListener('submit', async function (e) {
            e.preventDefault();

            if (window.tinymce) window.tinymce.triggerSave();

            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            if (!navigator.onLine) {
                mostrarToast(
                    'No hay conexión a internet en este momento. Tus cambios quedaron respaldados en pantalla; intenta guardar en cuanto regrese la red.',
                    'warning'
                );
                return;
            }

            const btnSubmit = form.querySelector('button[type="submit"]');
            const htmlOriginalBtn = btnSubmit ? btnSubmit.innerHTML : '';

            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Guardando sin recargar...';
            }

            try {
                // Refrescar CSRF silenciosamente por si el admin reabrió sesión en otra pestaña
                await refrescarTokenCsrf(form);

                const formData = new FormData(form);
                const urlDestino = form.getAttribute('action') || window.location.href;

                const response = await fetch(urlDestino, {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData,
                    credentials: 'same-origin'
                });

                // Caso 1: Sesión caducada en el servidor (redirigió al login index.php)
                if (response.status === 401 || (response.redirected && /\/index\.php(\?|$)/i.test(response.url))) {
                    mostrarToast(
                        'Tu sesión expiró por inactividad. Tus cambios siguen en pantalla; inicia sesión en una nueva pestaña y vuelve a presionar Guardar.',
                        'danger',
                        '<a href="index.php" target="_blank" rel="noopener" class="btn btn-sm btn-light fw-bold">Abrir Login en nueva pestaña <i class="bi bi-box-arrow-up-right ms-1"></i></a>'
                    );
                    return;
                }

                const contentType = response.headers.get('content-type') || '';

                // Caso 2: Respuesta JSON directa (si el endpoint envía JSON)
                if (contentType.includes('application/json')) {
                    const data = await response.json();
                    if (!response.ok || data.status === 'danger') {
                        mostrarToast(data.message || 'Revisa los datos ingresados. Tus cambios siguen en pantalla.', 'danger');
                    } else {
                        localStorage.removeItem(storageKey);
                        hayCambiosSinGuardar = false;
                        form.querySelectorAll('input[type="file"]').forEach(f => { f.value = ''; });
                        mostrarToast(data.message || 'Cambios guardados correctamente.', 'success');
                        if (data.redirect) {
                            setTimeout(() => { window.location.href = data.redirect; }, 900);
                        }
                    }
                    return;
                }

                // Caso 3: Respuesta HTML tradicional (compatibilidad 100% automática con tus PHP actuales)
                const htmlText = await response.text();
                const doc = new DOMParser().parseFromString(htmlText, 'text/html');
                const alertaServidor = doc.querySelector('.alert');
                const textoAlerta = alertaServidor ? alertaServidor.textContent.trim() : '';
                const esError = !response.ok || (alertaServidor && (alertaServidor.classList.contains('alert-danger') || alertaServidor.classList.contains('alert-warning')));

                if (esError) {
                    mostrarToast(
                        textoAlerta || 'El servidor rechazó el guardado (verifica el peso de las imágenes o los campos). Tus textos siguen intactos.',
                        'danger'
                    );
                    return;
                }

                // Éxito confirmado: limpiar borrador local y bandera de bloqueo
                localStorage.removeItem(storageKey);
                hayCambiosSinGuardar = false;
                const bannerBorrador = form.querySelector('.btn-restaurar-borrador')?.closest('.alert');
                if (bannerBorrador) bannerBorrador.remove();

                // Si el PHP redirigió a un listado distinto (ej. documento_form.php -> ver_documentos.php)
                const urlFinal = new URL(response.url, window.location.origin);
                if (response.redirected && urlFinal.pathname !== window.location.pathname) {
                    mostrarToast(textoAlerta || 'Registro guardado exitosamente. Redirigiendo...', 'success');
                    setTimeout(() => { window.location.href = response.url; }, 1000);
                    return;
                }

                // Limpiar inputs de archivo para no volver a subir la misma imagen en el siguiente clic
                form.querySelectorAll('input[type="file"]').forEach(f => { f.value = ''; });

                // Si estamos creando un registro nuevo (?id no existe en formularios individuales), limpiar campos
                const paramsUrl = new URLSearchParams(window.location.search);
                const esFormularioAltaIndividual = !paramsUrl.has('id') && /(banner_form|sponsor_form|noticia_form|landing_form|documento_form)\.php$/i.test(window.location.pathname);

                if (esFormularioAltaIndividual) {
                    const nuevoForm = doc.querySelector('form[method="POST"], form[method="post"]');
                    if (nuevoForm) {
                        form.reset();
                        if (window.tinymce) window.tinymce.editors.forEach(ed => ed.setContent(''));
                        const nuevoOrden = nuevoForm.querySelector('input[name="orden"]')?.value;
                        const actualOrden = form.querySelector('input[name="orden"]');
                        if (nuevoOrden && actualOrden) actualOrden.value = nuevoOrden;
                    }
                } else {
                    // Sincronizar miniaturas de imágenes actualizadas sin recargar la página
                    sincronizarMiniaturas(form, doc, index);
                }

                mostrarToast(textoAlerta || 'Cambios guardados correctamente.', 'success');

            } catch (error) {
                // Caída abrupta de red durante la subida (Wi-Fi desconectado, timeout o microcorte)
                try {
                    localStorage.setItem(storageKey, JSON.stringify({
                        timestamp: Date.now(),
                        data: serializarFormulario(form)
                    }));
                } catch (e) {}

                mostrarToast(
                    'Se interrumpió la conexión durante el envío. Tus cambios siguen en pantalla y se guardó una copia local; vuelve a intentar en unos segundos.',
                    'warning'
                );
            } finally {
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = htmlOriginalBtn;
                }
            }
        });
    }

    function sincronizarMiniaturas(formActual, docNuevo, formIndex) {
        const formsNuevos = Array.from(docNuevo.querySelectorAll('form[method="POST"], form[method="post"]'))
            .filter(f => !f.querySelector('input[name="eliminar_id"]'));
        const formEquivalente = formsNuevos[formIndex];
        if (!formEquivalente) return;

        const imgsActuales = formActual.querySelectorAll('img.img-thumbnail, img.current-image-banner, img.current-image-sponsor, img.current-image-noticia');
        const imgsNuevas = formEquivalente.querySelectorAll('img.img-thumbnail, img.current-image-banner, img.current-image-sponsor, img.current-image-noticia');

        if (imgsActuales.length === imgsNuevas.length) {
            imgsActuales.forEach((img, idx) => {
                const nuevoSrc = imgsNuevas[idx].getAttribute('src');
                if (nuevoSrc) img.setAttribute('src', nuevoSrc);
            });
        }
    }
})();