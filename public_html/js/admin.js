// ===== ADMIN PANEL - JAVASCRIPT MODERNO =====
class AdminPanel {
    constructor() {
        this.init();
    }

    // Inicialización principal
    init() {
        this.setupEventListeners();
        this.setupSidebar();
        this.setupModals();
        this.setupForms();
        this.setupTables();
        this.setupFileUploads();
        this.setupNotifications();
    }

    // ===== CONFIGURACIÓN DE EVENT LISTENERS =====
    setupEventListeners() {
        // Toggle del sidebar
        this.setupSidebarToggle();
        
        // Dropdowns de usuario
        this.setupUserDropdowns();
        
        // Modales
        this.setupModalHandlers();
        
        // Confirmaciones de eliminación
        this.setupDeleteConfirmations();
        
        // Tabs y navegación
        this.setupTabs();
        
        // Búsqueda en tiempo real
        this.setupRealTimeSearch();
    }

    // ===== SIDEBAR FUNCTIONALITY =====
    setupSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const menuToggle = document.getElementById('menuToggle');
        const mainContent = document.querySelector('.admin-main');

        if (menuToggle && sidebar) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.toggle('hidden');
                mainContent.classList.toggle('full-width');
                this.saveSidebarState();
            });
        }

        // Restaurar estado del sidebar
        this.restoreSidebarState();
    }

    setupSidebarToggle() {
        const sidebarItems = document.querySelectorAll('.sidebar-link');
        
        sidebarItems.forEach(item => {
            item.addEventListener('click', (e) => {
                if (window.innerWidth <= 768) {
                    document.getElementById('adminSidebar')?.classList.add('hidden');
                    document.querySelector('.admin-main')?.classList.add('full-width');
                }
                
                // Agregar efecto de ripple
                this.createRippleEffect(e);
            });
        });
    }

    // ===== USER DROPDOWNS =====
    setupUserDropdowns() {
        const userGreeting = document.querySelector('.user-greeting');
        const userDropdown = document.querySelector('.user-dropdown');

        if (userGreeting && userDropdown) {
            userGreeting.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('show');
            });

            // Cerrar al hacer clic fuera
            document.addEventListener('click', () => {
                userDropdown.classList.remove('show');
            });
        }
    }

    // ===== MODAL MANAGEMENT =====
    setupModals() {
        this.setupModalTriggers();
        this.setupModalCloseHandlers();
    }

    setupModalHandlers() {
        // Modal triggers
        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-modal-target]');
            if (trigger) {
                const modalId = trigger.dataset.modalTarget;
                this.openModal(modalId);
            }
        });

        // Close modals on background click
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                this.closeModal(e.target);
            }
        });

        // Close modals with ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }
        });
    }

    setupModalCloseHandlers() {
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-close') || 
                e.target.closest('.modal-close')) {
                const modal = e.target.closest('.modal');
                this.closeModal(modal);
            }
        });
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            
            // Enfocar primer elemento input
            const firstInput = modal.querySelector('input, textarea, select');
            if (firstInput) firstInput.focus();
        }
    }

    closeModal(modal) {
        if (modal) {
            modal.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    closeAllModals() {
        document.querySelectorAll('.modal.show').forEach(modal => {
            this.closeModal(modal);
        });
    }

    // ===== FORM HANDLING =====
    setupForms() {
        this.setupFormValidation();
        this.setupFormSubmissions();
        this.setupRichTextEditors();
    }

    setupFormValidation() {
        const forms = document.querySelectorAll('form[needs-validation]');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    this.showFormErrors(form);
                }
            });
        });

        // Validación en tiempo real
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('blur', () => {
                this.validateField(input);
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
        } else if (field.type === 'url' && value && !this.isValidUrl(value)) {
            isValid = false;
            this.showFieldError(field, 'Por favor ingresa una URL válida');
        } else if (field.hasAttribute('minlength') && value.length < field.getAttribute('minlength')) {
            isValid = false;
            this.showFieldError(field, `Mínimo ${field.getAttribute('minlength')} caracteres`);
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

    showFormErrors(form) {
        const firstInvalid = form.querySelector('.is-invalid');
        if (firstInvalid) {
            firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstInvalid.focus();
        }
    }

    setupFormSubmissions() {
        document.addEventListener('submit', async (e) => {
            const form = e.target;
            
            if (form.classList.contains('ajax-form')) {
                e.preventDefault();
                await this.handleAjaxFormSubmit(form);
            }
        });
    }

    async handleAjaxFormSubmit(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn?.textContent;
        
        try {
            // Mostrar estado de carga
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.classList.add('loading');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
            }
            
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();
            
            if (result.success) {
                this.showNotification(result.message || '¡Operación exitosa!', 'success');
                
                // Redireccionar si se especifica
                if (result.redirect) {
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1500);
                }
                
                // Recargar si se especifica
                if (result.reload) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
            } else {
                this.showNotification(result.message || 'Error en la operación', 'error');
            }
            
        } catch (error) {
            console.error('Error submitting form:', error);
            this.showNotification('Error de conexión. Intenta nuevamente.', 'error');
        } finally {
            // Restaurar botón
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.classList.remove('loading');
                submitBtn.textContent = originalText;
            }
        }
    }

    setupRichTextEditors() {
        // Inicializar editores ricos si TinyMCE está disponible
        if (typeof tinymce !== 'undefined') {
            const editors = document.querySelectorAll('.rich-editor');
            editors.forEach((editor, index) => {
                tinymce.init({
                    selector: `#${editor.id}`,
                    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table paste code help wordcount',
                    toolbar: 'undo redo | blocks | bold italic underline | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image media | code',
                    height: 400,
                    menubar: false,
                    branding: false,
                    promotion: false,
                    language: 'es',
                    content_style: `
                        body { 
                            font-family: 'Inter', sans-serif; 
                            font-size: 14px; 
                            line-height: 1.6; 
                            color: #2D3748;
                        }
                        h1, h2, h3, h4, h5, h6 { 
                            color: #4B5320; 
                            margin-bottom: 0.5rem;
                        }
                    `,
                    images_upload_handler: async (blobInfo) => {
                        return new Promise((resolve, reject) => {
                            const formData = new FormData();
                            formData.append('file', blobInfo.blob(), blobInfo.filename());
                            
                            fetch('../admin/gestion-medios.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    resolve(data.url);
                                } else {
                                    reject(data.error);
                                }
                            })
                            .catch(reject);
                        });
                    }
                });
            });
        }
    }

    // ===== TABLE FUNCTIONALITY =====
    setupTables() {
        this.setupTableSorting();
        this.setupTableFilters();
        this.setupBulkActions();
    }

    setupTableSorting() {
        document.querySelectorAll('.admin-table th[data-sort]').forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => {
                this.sortTable(header);
            });
        });
    }

    sortTable(header) {
        const table = header.closest('table');
        const columnIndex = Array.from(header.parentNode.children).indexOf(header);
        const isNumeric = header.dataset.sort === 'numeric';
        const isAscending = header.classList.contains('sort-asc');
        
        // Remover clases de ordenamiento previas
        table.querySelectorAll('th').forEach(th => {
            th.classList.remove('sort-asc', 'sort-desc');
        });
        
        // Alternar dirección
        header.classList.toggle('sort-asc', !isAscending);
        header.classList.toggle('sort-desc', isAscending);
        
        const rows = Array.from(table.querySelectorAll('tbody tr'));
        
        rows.sort((a, b) => {
            const aValue = a.children[columnIndex].textContent.trim();
            const bValue = b.children[columnIndex].textContent.trim();
            
            let comparison = 0;
            
            if (isNumeric) {
                comparison = parseFloat(aValue) - parseFloat(bValue);
            } else {
                comparison = aValue.localeCompare(bValue, 'es', { sensitivity: 'base' });
            }
            
            return isAscending ? -comparison : comparison;
        });
        
        // Reordenar filas
        const tbody = table.querySelector('tbody');
        tbody.innerHTML = '';
        rows.forEach(row => tbody.appendChild(row));
    }

    setupTableFilters() {
        const searchInputs = document.querySelectorAll('.table-search');
        
        searchInputs.forEach(input => {
            input.addEventListener('input', (e) => {
                this.filterTable(e.target);
            });
        });
    }

    filterTable(searchInput) {
        const table = searchInput.closest('.admin-card').querySelector('table');
        const searchTerm = searchInput.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    }

    setupBulkActions() {
        const bulkSelect = document.querySelector('.bulk-select');
        const bulkActions = document.querySelector('.bulk-actions');
        
        if (bulkSelect) {
            bulkSelect.addEventListener('change', (e) => {
                const checkboxes = document.querySelectorAll('.row-select:checked');
                bulkActions.style.display = checkboxes.length > 0 ? 'flex' : 'none';
            });
        }
    }

    // ===== FILE UPLOAD HANDLING =====
    setupFileUploads() {
        this.setupDragAndDrop();
        this.setupFilePreviews();
    }

    setupDragAndDrop() {
        const dropZones = document.querySelectorAll('.upload-form');
        
        dropZones.forEach(zone => {
            zone.addEventListener('dragover', (e) => {
                e.preventDefault();
                zone.classList.add('dragover');
            });
            
            zone.addEventListener('dragleave', () => {
                zone.classList.remove('dragover');
            });
            
            zone.addEventListener('drop', (e) => {
                e.preventDefault();
                zone.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    this.handleFileUpload(files[0], zone);
                }
            });
        });
    }

    setupFilePreviews() {
        document.addEventListener('change', (e) => {
            if (e.target.type === 'file' && e.target.files.length > 0) {
                this.previewFile(e.target.files[0], e.target);
            }
        });
    }

    handleFileUpload(file, uploadZone) {
        const formData = new FormData();
        formData.append('archivo', file);
        
        // Mostrar progreso
        const progress = this.createUploadProgress();
        uploadZone.appendChild(progress);
        
        fetch('../admin/gestion-medios.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            progress.remove();
            
            if (data.success) {
                this.showNotification('Archivo subido correctamente', 'success');
                // Recargar la página o actualizar la lista de medios
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.showNotification(data.error || 'Error al subir archivo', 'error');
            }
        })
        .catch(error => {
            progress.remove();
            this.showNotification('Error de conexión', 'error');
            console.error('Upload error:', error);
        });
    }

    createUploadProgress() {
        const progress = document.createElement('div');
        progress.className = 'upload-progress';
        progress.innerHTML = `
            <div class="progress-bar">
                <div class="progress-fill"></div>
            </div>
            <span>Subiendo archivo...</span>
        `;
        return progress;
    }

    previewFile(file, input) {
        const previewContainer = input.parentNode.querySelector('.file-preview');
        if (!previewContainer) return;
        
        previewContainer.innerHTML = '';
        
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.onload = () => URL.revokeObjectURL(img.src);
            previewContainer.appendChild(img);
        } else {
            const icon = document.createElement('div');
            icon.className = 'file-icon';
            icon.textContent = this.getFileIcon(file.type);
            previewContainer.appendChild(icon);
            
            const name = document.createElement('div');
            name.className = 'file-name';
            name.textContent = file.name;
            previewContainer.appendChild(name);
        }
    }

    getFileIcon(fileType) {
        const icons = {
            'application/pdf': '📄',
            'application/zip': '📦',
            'audio/': '🎵',
            'video/': '🎬',
            'text/': '📝'
        };
        
        for (const [type, icon] of Object.entries(icons)) {
            if (fileType.startsWith(type)) return icon;
        }
        
        return '📁';
    }

    // ===== NOTIFICATION SYSTEM =====
    setupNotifications() {
        // Sistema de notificaciones toast
        this.notificationContainer = this.createNotificationContainer();
        document.body.appendChild(this.notificationContainer);
    }

    createNotificationContainer() {
        const container = document.createElement('div');
        container.className = 'notification-container';
        return container;
    }

    showNotification(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.innerHTML = `
            <div class="notification-content">
                <span class="notification-icon">${this.getNotificationIcon(type)}</span>
                <span class="notification-message">${message}</span>
                <button class="notification-close">&times;</button>
            </div>
        `;
        
        this.notificationContainer.appendChild(notification);
        
        // Animación de entrada
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Cerrar al hacer clic
        notification.querySelector('.notification-close').addEventListener('click', () => {
            this.hideNotification(notification);
        });
        
        // Auto-remover después de la duración
        if (duration > 0) {
            setTimeout(() => {
                this.hideNotification(notification);
            }, duration);
        }
        
        return notification;
    }

    hideNotification(notification) {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }

    getNotificationIcon(type) {
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        return icons[type] || icons.info;
    }

    // ===== DELETE CONFIRMATIONS =====
    setupDeleteConfirmations() {
        document.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('[data-delete-confirm]');
            if (deleteBtn) {
                e.preventDefault();
                this.showDeleteConfirmation(deleteBtn);
            }
        });
    }

    showDeleteConfirmation(deleteBtn) {
        const message = deleteBtn.dataset.deleteConfirm || '¿Estás seguro de que quieres eliminar este elemento?';
        const confirmText = deleteBtn.dataset.confirmText || 'Eliminar';
        const cancelText = deleteBtn.dataset.cancelText || 'Cancelar';
        
        const modal = document.createElement('div');
        modal.className = 'modal show';
        modal.innerHTML = `
            <div class="modal-content">
                <div class="modal-header">
                    <h3>Confirmar Eliminación</h3>
                    <button class="modal-close">&times;</button>
                </div>
                <div class="modal-body">
                    <p>${message}</p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline" data-action="cancel">${cancelText}</button>
                    <button class="btn btn-danger" data-action="confirm">${confirmText}</button>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        modal.querySelector('[data-action="cancel"]').addEventListener('click', () => {
            document.body.removeChild(modal);
        });
        
        modal.querySelector('[data-action="confirm"]').addEventListener('click', () => {
            if (deleteBtn.tagName === 'A') {
                window.location.href = deleteBtn.href;
            } else if (deleteBtn.tagName === 'BUTTON' && deleteBtn.form) {
                deleteBtn.form.submit();
            }
        });
        
        modal.querySelector('.modal-close').addEventListener('click', () => {
            document.body.removeChild(modal);
        });
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });
    }

    // ===== TAB SYSTEM =====
    setupTabs() {
        document.querySelectorAll('.tab-nav').forEach(nav => {
            nav.addEventListener('click', (e) => {
                const tabBtn = e.target.closest('.tab-btn');
                if (tabBtn) {
                    e.preventDefault();
                    this.switchTab(tabBtn);
                }
            });
        });
    }

    switchTab(tabBtn) {
        const tabContainer = tabBtn.closest('.tabs');
        const tabId = tabBtn.dataset.tab;
        
        // Remover clases activas
        tabContainer.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        tabContainer.querySelectorAll('.tab-pane').forEach(pane => {
            pane.classList.remove('active');
        });
        
        // Activar tab seleccionado
        tabBtn.classList.add('active');
        const targetPane = tabContainer.querySelector(`#${tabId}`);
        if (targetPane) targetPane.classList.add('active');
    }

    // ===== REAL-TIME SEARCH =====
    setupRealTimeSearch() {
        const searchInputs = document.querySelectorAll('[data-search]');
        
        searchInputs.forEach(input => {
            let timeout;
            input.addEventListener('input', (e) => {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    this.performSearch(e.target.value, e.target.dataset.search);
                }, 300);
            });
        });
    }

    performSearch(query, searchType) {
        // Implementar búsqueda en tiempo real según el tipo
        console.log(`Searching for: ${query} in ${searchType}`);
        // Aquí se puede implementar AJAX para búsquedas en tiempo real
    }

    // ===== UTILITY METHODS =====
    createRippleEffect(event) {
        const btn = event.currentTarget;
        const circle = document.createElement('span');
        const diameter = Math.max(btn.clientWidth, btn.clientHeight);
        const radius = diameter / 2;
        
        circle.style.width = circle.style.height = `${diameter}px`;
        circle.style.left = `${event.clientX - btn.getBoundingClientRect().left - radius}px`;
        circle.style.top = `${event.clientY - btn.getBoundingClientRect().top - radius}px`;
        circle.classList.add('ripple');
        
        const ripple = btn.getElementsByClassName('ripple')[0];
        if (ripple) ripple.remove();
        
        btn.appendChild(circle);
    }

    saveSidebarState() {
        const sidebar = document.getElementById('adminSidebar');
        const isCollapsed = sidebar?.classList.contains('hidden');
        localStorage.setItem('adminSidebarCollapsed', isCollapsed);
    }

    restoreSidebarState() {
        const isCollapsed = localStorage.getItem('adminSidebarCollapsed') === 'true';
        const sidebar = document.getElementById('adminSidebar');
        const mainContent = document.querySelector('.admin-main');
        
        if (sidebar && mainContent) {
            if (isCollapsed) {
                sidebar.classList.add('hidden');
                mainContent.classList.add('full-width');
            }
        }
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    isValidUrl(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    }

    // ===== STATIC METHODS FOR GLOBAL ACCESS =====
    static showLoading(selector) {
        const element = document.querySelector(selector);
        if (element) {
            element.classList.add('loading');
        }
    }

    static hideLoading(selector) {
        const element = document.querySelector(selector);
        if (element) {
            element.classList.remove('loading');
        }
    }

    static formatDate(dateString) {
        const options = { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return new Date(dateString).toLocaleDateString('es-ES', options);
    }

    static formatCurrency(amount, currency = 'ARS') {
        return new Intl.NumberFormat('es-AR', {
            style: 'currency',
            currency: currency
        }).format(amount);
    }
}

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', () => {
    window.adminPanel = new AdminPanel();
    
    // Exponer métodos útiles globalmente
    window.showNotification = (message, type) => window.adminPanel.showNotification(message, type);
    window.formatDate = AdminPanel.formatDate;
    window.formatCurrency = AdminPanel.formatCurrency;
});

// ===== GLOBAL EVENT HANDLERS =====
document.addEventListener('click', (e) => {
    // Dropdown menus
    if (e.target.classList.contains('dropdown-toggle')) {
        const dropdown = e.target.closest('.dropdown');
        dropdown.querySelector('.dropdown-menu').classList.toggle('show');
    } else {
        // Cerrar todos los dropdowns al hacer clic fuera
        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// Prevenir envío de formularios inválidos
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (form.classList.contains('needs-validation') && !form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        form.classList.add('was-validated');
    }
});

// ===== SERVICE WORKER PARA CACHÉ (OPCIONAL) =====
if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/admin-sw.js')
            .then(registration => {
                console.log('SW registered: ', registration);
            })
            .catch(registrationError => {
                console.log('SW registration failed: ', registrationError);
            });
    });
}

// ===== OFFLINE DETECTION =====
window.addEventListener('online', () => {
    window.adminPanel?.showNotification('Conexión restaurada', 'success');
});

window.addEventListener('offline', () => {
    window.adminPanel?.showNotification('Estás trabajando sin conexión', 'warning');
});

// ===== ERROR HANDLING GLOBAL =====
window.addEventListener('error', (e) => {
    console.error('Error global:', e.error);
    window.adminPanel?.showNotification('Ha ocurrido un error inesperado', 'error');
});

// ===== PERFORMANCE MONITORING =====
if ('performance' in window) {
    window.addEventListener('load', () => {
        const perfData = performance.getEntriesByType('navigation')[0];
        console.log('Tiempo de carga:', perfData.loadEventEnd - perfData.fetchStart, 'ms');
    });
}