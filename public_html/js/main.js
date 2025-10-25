// ===== MAIN.JS - FRONTEND JAVASCRIPT MODERNO =====
class FrontendApp {
    constructor() {
        this.init();
    }

    // Inicialización principal
    init() {
        this.setupMobileMenu();
        this.setupSmoothScroll();
        this.setupFormValidation();
        this.setupAnimations();
        this.setupContactForm();
        this.setupProductInteractions();
        this.setupPaymentProcess();
    }

    // ===== MOBILE MENU =====
    setupMobileMenu() {
        const menuToggle = document.querySelector('.menu-toggle');
        const navMenu = document.querySelector('nav ul');

        if (menuToggle && navMenu) {
            menuToggle.addEventListener('click', () => {
                navMenu.classList.toggle('show');
                menuToggle.classList.toggle('active');
            });

            // Cerrar menú al hacer clic en un enlace
            document.querySelectorAll('nav a').forEach(link => {
                link.addEventListener('click', () => {
                    navMenu.classList.remove('show');
                    menuToggle.classList.remove('active');
                });
            });
        }
    }

    // ===== SMOOTH SCROLL =====
    setupSmoothScroll() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // ===== FORM VALIDATION =====
    setupFormValidation() {
        const forms = document.querySelectorAll('form[needs-validation]');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    this.showFormErrors(form);
                }
            });

            // Validación en tiempo real
            form.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });
                
                input.addEventListener('input', () => {
                    this.clearFieldError(input);
                });
            });
        });
    }

    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('.form-control[required]');
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        
        // Remover estados previos
        field.classList.remove('is-invalid', 'is-valid');
        
        // Validaciones básicas
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            this.showFieldError(field, 'Este campo es obligatorio');
        } else if (field.type === 'email' && value && !this.isValidEmail(value)) {
            isValid = false;
            this.showFieldError(field, 'Por favor ingresa un email válido');
        } else if (field.type === 'tel' && value && !this.isValidPhone(value)) {
            isValid = false;
            this.showFieldError(field, 'Por favor ingresa un teléfono válido');
        }
        
        if (isValid && value) {
            field.classList.add('is-valid');
        }
        
        return isValid;
    }

    showFieldError(field, message) {
        field.classList.add('is-invalid');
        
        // Remover mensaje de error anterior
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) existingError.remove();
        
        // Crear nuevo mensaje de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }

    clearFieldError(field) {
        field.classList.remove('is-invalid');
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) existingError.remove();
    }

    showFormErrors(form) {
        const firstInvalid = form.querySelector('.is-invalid');
        if (firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalid.focus();
        }
    }

    // ===== ANIMATIONS =====
    setupAnimations() {
        // Animación al hacer scroll
        this.setupScrollAnimations();
        
        // Animación de contadores
        this.setupCounters();
        
        // Animación de tarjetas al hover
        this.setupCardAnimations();
    }

    setupScrollAnimations() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        // Observar elementos para animación
        document.querySelectorAll('.feature-card, .service-card, .testimonial-card, .product-card').forEach(el => {
            observer.observe(el);
        });
    }

    setupCounters() {
        const counters = document.querySelectorAll('.counter');
        
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const duration = 2000; // 2 segundos
            const step = target / (duration / 16); // 60 FPS
            
            let current = 0;
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    counter.textContent = target + '+';
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current);
                }
            }, 16);
        });
    }

    setupCardAnimations() {
        document.querySelectorAll('.feature-card, .product-card, .blog-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });
    }

    // ===== CONTACT FORM =====
    setupContactForm() {
        const contactForm = document.getElementById('contactForm');
        
        if (contactForm) {
            contactForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                if (!this.validateForm(contactForm)) {
                    return;
                }
                
                await this.handleContactFormSubmit(contactForm);
            });
        }
    }

    async handleContactFormSubmit(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        
        try {
            // Mostrar estado de carga
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
            
            const formData = new FormData(form);
            
            const response = await fetch('procesar-contacto.php', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.text();
            
            if (response.ok) {
                this.showNotification('¡Mensaje enviado correctamente! Te contactaremos pronto.', 'success');
                form.reset();
            } else {
                throw new Error('Error en el servidor');
            }
            
        } catch (error) {
            console.error('Error:', error);
            this.showNotification('Error al enviar el mensaje. Por favor, intenta nuevamente.', 'error');
        } finally {
            // Restaurar botón
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    }

    // ===== PRODUCT INTERACTIONS =====
    setupProductInteractions() {
        this.setupProductGallery();
        this.setupPaymentMethods();
    }

    setupProductGallery() {
        const galleries = document.querySelectorAll('.product-gallery');
        
        galleries.forEach(gallery => {
            const mainImage = gallery.querySelector('.gallery-main img');
            const thumbnails = gallery.querySelectorAll('.gallery-thumbnails img');
            
            thumbnails.forEach(thumb => {
                thumb.addEventListener('click', function() {
                    // Actualizar imagen principal
                    mainImage.src = this.src;
                    mainImage.alt = this.alt;
                    
                    // Actualizar thumbnails activos
                    thumbnails.forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                });
            });
        });
    }

    setupPaymentMethods() {
        const paymentMethods = document.querySelectorAll('input[name="metodo_pago"]');
        
        paymentMethods.forEach(method => {
            method.addEventListener('change', function() {
                // Mostrar/ocultar información específica del método de pago
                const methodInfo = document.getElementById(`${this.value}-info`);
                if (methodInfo) {
                    document.querySelectorAll('.payment-info').forEach(info => {
                        info.style.display = 'none';
                    });
                    methodInfo.style.display = 'block';
                }
            });
        });
    }

    // ===== PAYMENT PROCESS =====
    setupPaymentProcess() {
        const paymentForm = document.querySelector('.checkout-form form');
        
        if (paymentForm) {
            paymentForm.addEventListener('submit', (e) => {
                // Validación adicional para pagos
                const email = document.getElementById('email').value;
                const nombre = document.getElementById('nombre').value;
                
                if (!this.isValidEmail(email)) {
                    e.preventDefault();
                    this.showNotification('Por favor ingresa un email válido', 'error');
                    return;
                }
                
                if (nombre.trim().length < 2) {
                    e.preventDefault();
                    this.showNotification('Por favor ingresa tu nombre completo', 'error');
                    return;
                }
                
                // Mostrar loading state
                const submitBtn = paymentForm.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            });
        }
    }

    // ===== NOTIFICATION SYSTEM =====
    showNotification(message, type = 'info') {
        // Crear notificación
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;
        
        // Estilos para la notificación
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#48BB78' : type === 'error' ? '#F56565' : '#4299E1'};
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 10000;
            max-width: 400px;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        
        document.body.appendChild(notification);
        
        // Animación de entrada
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Cerrar notificación
        notification.querySelector('.notification-close').addEventListener('click', () => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        });
        
        // Auto-remover después de 5 segundos
        setTimeout(() => {
            if (notification.parentNode) {
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }
        }, 5000);
    }

    // ===== UTILITY METHODS =====
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    isValidPhone(phone) {
        const phoneRegex = /^[+]?[(]?[0-9]{1,4}[)]?[-\s.]?[0-9]{1,4}[-\s.]?[0-9]{1,9}$/;
        return phoneRegex.test(phone);
    }
}

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', () => {
    window.frontendApp = new FrontendApp();
});

// ===== GLOBAL FUNCTIONS =====
// Función para formatear precios
function formatPrice(amount, currency = 'ARS') {
    return new Intl.NumberFormat('es-AR', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

// Función para formatear fechas
function formatDate(dateString) {
    const options = { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    return new Date(dateString).toLocaleDateString('es-ES', options);
}

// ===== LOADING STATES =====
function showLoading(selector) {
    const element = document.querySelector(selector);
    if (element) {
        element.classList.add('loading');
    }
}

function hideLoading(selector) {
    const element = document.querySelector(selector);
    if (element) {
        element.classList.remove('loading');
    }
}