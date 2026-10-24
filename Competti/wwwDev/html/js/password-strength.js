// html/js/password-strength.js

document.addEventListener('DOMContentLoaded', () => {
    const passwordInput = document.getElementById('password');
    const confirmInput = document.getElementById('confirm_password');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const matchText = document.getElementById('matchText');
    const submitBtn = document.getElementById('submitBtn');

    // Elementos de la lista de requisitos
    const reqs = {
        length: document.getElementById('req-length'),
        upper: document.getElementById('req-upper'),
        number: document.getElementById('req-number'),
        special: document.getElementById('req-special')
    };

    // Función corregida: Valida con total seguridad si el icono existe antes de alterarlo
    const toggleReq = (element, isValid) => {
        if (!element) return; // Si el elemento no existe en el HTML, ignora el paso
        
        const icon = element.querySelector('i');
        
        if (isValid) {
            element.classList.add('valid');
            // Cambia el icono solo si el querySelector encontró la etiqueta <i>
            if (icon) {
                icon.className = 'fa-solid fa-circle-check'; 
            }
        } else {
            element.classList.remove('valid');
            if (icon) {
                icon.className = 'fa-solid fa-circle-dot';
            }
        }
    };

    const evaluatePassword = () => {
        const val = passwordInput.value;
        let score = 0;

        // Si el campo está vacío, reseteamos todo a su estado inicial gris
        if (!val) {
            if (strengthBar) strengthBar.style.width = '0%';
            if (strengthText) {
                strengthText.textContent = 'Seguridad de la contraseña';
                strengthText.style.color = '#64748b';
            }
            Object.values(reqs).forEach(el => {
                if (el) {
                    el.classList.remove('valid');
                    const icon = el.querySelector('i');
                    if (icon) icon.className = 'fa-solid fa-circle-dot';
                }
            });
            return 0;
        }

        // 1. Validar largo (mínimo 8 caracteres)
        if (val.length >= 8) {
            score++;
            toggleReq(reqs.length, true);
        } else {
            toggleReq(reqs.length, false);
        }

        // 2. Validar Mayúscula
        if (/[A-Z]/.test(val)) {
            score++;
            toggleReq(reqs.upper, true);
        } else {
            toggleReq(reqs.upper, false);
        }

        // 3. Validar Número
        if (/[0-9]/.test(val)) {
            score++;
            toggleReq(reqs.number, true);
        } else {
            toggleReq(reqs.number, false);
        }

        // 4. Validar Caracter Especial
        if (/[@$!%*#?&.\-_+*/=]/.test(val)) {
            score++;
            toggleReq(reqs.special, true);
        } else {
            toggleReq(reqs.special, false);
        }

        // Renderizar la barra de progreso de fuerza de forma segura
        if (strengthBar && strengthText) {
            switch (score) {
                case 1:
                    strengthBar.style.width = '25%';
                    strengthBar.style.backgroundColor = '#ef4444'; // Rojo
                    strengthText.textContent = 'Seguridad: Muy débil ❌';
                    strengthText.style.color = '#ef4444';
                    break;
                case 2:
                    strengthBar.style.width = '50%';
                    strengthBar.style.backgroundColor = '#f59e0b'; // Naranja
                    strengthText.textContent = 'Seguridad: Débil ⚠️';
                    strengthText.style.color = '#f59e0b';
                    break;
                case 3:
                    strengthBar.style.width = '75%';
                    strengthBar.style.backgroundColor = '#3b82f6'; // Azul
                    strengthText.textContent = 'Seguridad: Media 👍';
                    strengthText.style.color = '#3b82f6';
                    break;
                case 4:
                    strengthBar.style.width = '100%';
                    strengthBar.style.backgroundColor = '#10b981'; // Verde
                    strengthText.textContent = 'Seguridad: ¡Excelente y fuerte! 💪';
                    strengthText.style.color = '#10b981';
                    break;
                default:
                    strengthBar.style.width = '0%';
                    break;
            }
        }

        return score;
    };

    const validateMatch = () => {
        if (!passwordInput || !confirmInput || !matchText) return false;
        
        const p1 = passwordInput.value;
        const p2 = confirmInput.value;

        if (!p2) {
            matchText.textContent = '';
            return false;
        }

        if (p1 === p2) {
            matchText.textContent = '✓ Las contraseñas coinciden';
            matchText.style.color = '#10b981';
            return true;
        } else {
            matchText.textContent = '✕ Las contraseñas no coinciden';
            matchText.style.color = '#ef4444';
            return false;
        }
    };

    const checkFormValidity = () => {
        const score = evaluatePassword();
        const matches = validateMatch();

        if (submitBtn) {
            // El formulario solo se desbloquea al cumplir las 4 reglas y coincidir la confirmación
            if (score === 4 && matches) {
                submitBtn.removeAttribute('disabled');
                submitBtn.style.opacity = '1';
                submitBtn.style.pointerEvents = 'auto';
            } else {
                submitBtn.setAttribute('disabled', 'true');
                submitBtn.style.opacity = '0.6';
                submitBtn.style.pointerEvents = 'none';
            }
        }
    };

    // Escuchar eventos en tiempo real si los elementos existen
    if (passwordInput) passwordInput.addEventListener('input', checkFormValidity);
    if (confirmInput) confirmInput.addEventListener('input', checkFormValidity);

    // Ejecutar al cargar la página
    checkFormValidity();
});
