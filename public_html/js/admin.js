// ===== ADMIN PANEL - JAVASCRIPT MODERNO ES6+ =====
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
        this.setupRealTimeSearch();
        console.log('✅ Admin Panel inicializado correctamente');
    }

    // ===== CONFIGURACIÓN DE EVENT LISTENERS =====
    setupEventListeners() {
        // Delegación de eventos para mejor performance
        document.addEventListener('click', this.handleGlobalClick.bind(this));
        document.addEventListener('keydown', this.handleGlobalKeydown.bind(this));
        document.addEventListener('submit', this.handleFormSubmit.bind(this));
        document.addEventListener('input', this.handleGlobalInput.bind(this));
        
        // Eventos de red
        window.addEventListener('online', this.handleOnlineStatus.bind(this));
        window.addEventListener('offline', this.handleOfflineStatus.bind(this));
    }

    // ===== MANEJO DE EVENTOS GLOBALES =====
    handleGlobalClick(e) {
        // Dropdowns
        if (e.target.closest('.dropdown-toggle')) {
            this.toggleDropdown(e.target.closest('.dropdown-toggle'));
            return;
        }

        // Cerrar dropdowns al hacer clic fuera
        if (!e.target.closest('.dropdown')) {
            this.closeAllDropdowns();
        }

        // Modales
        if (e.target.closest('[data-modal-target]')) {
            const target = e.target.closest('[data-modal-target]');
            this.openModal(target.dataset.modalTarget);
            return;
        }

        if (e.target.classList.contains('modal')) {
            this.closeModal(e.target);
            return;
        }

        if (e.target.closest('.modal-close')) {
            this.closeModal(e.target.closest('.modal'));
            return;
        }

        // Confirmaciones de eliminación
        if (e.target.closest('[data-delete-confirm]')) {
            e.preventDefault();
            const target = e.target.closest('[data-delete-confirm]');
            this.showDeleteConfirmation(target);
            return;
        }

        // Tabs
        if (e.target.closest('.tab-btn')) {
            const tabBtn = e.target.closest('.tab-btn');
            this.switchTab(tabBtn);
            return;
        }

        // Efecto ripple en botones
        if (e.target.closest('.btn') || e.target.closest('.sidebar-link')) {
            this.createRippleEffect(e);
        }
    }

    handleGlobalKeydown(e) {
        // Cerrar modales con ESC
        if (e.key === 'Escape') {
            this.closeAllModals();
            this.closeAllDropdowns();
        }

        // Navegación con teclado en formularios
        if (e.key === 'Enter' && e.target.closest('.modal')) {
            const submitBtn = e.target.closest('.modal').querySelector('.btn-primary');
            if (submitBtn && !e.target.closest('.action-buttons')) {
                submitBtn.click();
            }
        }
    }

    handleFormSubmit(e) {
        const form = e.target;
        
        // Validación de formularios
        if (form.classList.contains('needs-validation')) {
            if (!this.validateForm(form)) {
                e.preventDefault();
                form.classList.add('was-validated');
                return;
            }
        }

        // Formularios AJAX
        if (form.classList.contains('ajax-form')) {
            e.preventDefault();
            this.handleAjaxFormSubmit(form);
            return;
        }

        // Mostrar estado de carga
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            this.showButtonLoading(submitBtn);
        }
    }

    handleGlobalInput(e) {
        // Búsqueda en tiempo real
        if (e.target.dataset.search) {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                this.performSearch(e.target.value, e.target.dataset.search);
            }, 300);
        }

        // Validación en tiempo real
        if (e.target.classList.contains('form-control')) {
            this.validateField(e.target);
        }
    }

    // ===== SIDEBAR FUNCTIONALITY =====
    setupSidebar() {
        this.sidebar = document.getElementById('adminSidebar');
        this.menuToggle = document.getElementById('menuToggle');
        this.mainContent = document.querySelector('.admin-main');

        if (this.menuToggle && this.sidebar) {
            this.menuToggle.addEventListener('click', () => this.toggleSidebar());
        }

        this.restoreSidebarState();
        this.setupSidebarResize();
    }

    toggleSidebar() {
        this.sidebar.classList.toggle('hidden');
        this.mainContent.classList.toggle('full-width');
        this.saveSidebarState();
    }

    setupSidebarResize() {
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (window.innerWidth <= 1024) {
                    this.sidebar.classList.add('hidden');
                    this.mainContent.classList.add('full-width');
                }
            }, 250);
        });
    }

    saveSidebarState() {
        const isCollapsed = this.sidebar?.classList.contains('hidden');
        localStorage.setItem('adminSidebarCollapsed', isCollapsed);
    }

    restoreSidebarState() {
        const isCollapsed = localStorage.getItem('adminSidebarCollapsed') === 'true';
        if (this.sidebar && this.mainContent && isCollapsed) {
            this.sidebar.classList.add('hidden');
            this.mainContent.classList.add('full-width');
        }
    }

    // ===== DROPDOWNS =====
    toggleDropdown(toggle) {
        const dropdown = toggle.closest('.dropdown');
        const menu = dropdown.querySelector('.dropdown-menu');
        menu.classList.toggle('show');
    }

    closeAllDropdowns() {
        document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
            menu.classList.remove('show');
        });
    }

    // ===== MODAL MANAGEMENT =====
    setupModals() {
        this.modals = document.querySelectorAll('.modal');
        this.setupModalAccessibility();
    }

    setupModalAccessibility() {
        this.modals.forEach(modal => {
            modal.setAttribute('aria-hidden', 'true');
            modal.setAttribute('role', 'dialog');
            modal.setAttribute('aria-labelledby', modal.id + '-title');
        });
    }

    openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('show');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            
            // Trap focus inside modal
            this.trapFocus(modal);
            
            // Animar entrada
            setTimeout(() => {
                modal.style.opacity = '1';
            }, 10);
            
            // Enfocar primer elemento input
            const firstInput = modal.querySelector('input, textarea, select, button');
            if (firstInput) firstInput.focus();
        }
    }

    closeModal(modal) {
        if (modal) {
            modal.classList.remove('show');
            modal.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            
            // Liberar focus trap
            this.releaseFocus();
        }
    }

    closeAllModals() {
        document.querySelectorAll('.modal.show').forEach(modal => {
            this.closeModal(modal);
        });
    }

    trapFocus(modal) {
        const focusableElements = modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        modal.addEventListener('keydown', (e) => {
            if (e.key === 'Tab') {
                if (e.shiftKey) {
                    if (document.activeElement === firstElement) {
                        e.preventDefault();
                        lastElement.focus();
                    }
                } else {
                    if (document.activeElement === lastElement) {
                        e.preventDefault();
                        firstElement.focus();
                    }
                }
            }
        });
    }

    releaseFocus() {
        // Restaurar focus al elemento que abrió el modal
        const lastFocused = document.activeElement;
        if (lastFocused && lastFocused.closest('.modal')) {
            const opener = document.querySelector('[data-modal-target].last-focused');
            if (opener) {
                opener.focus();
                opener.classList.remove('last-focused');
            }
        }
    }

    // ===== FORM HANDLING =====
    setupForms() {
        this.setupFormValidation();
        this.setupRichTextEditors();
        this.setupFileInputs();
    }

    setupFormValidation() {
        // Agregar validación HTML5 personalizada
        const forms = document.querySelectorAll('form[needs-validation]');
        forms.forEach(form => {
            form.setAttribute('novalidate', 'true');
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
        this.removeFieldError(field);
        
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
        } else if (field.hasAttribute('minlength') && value.length < parseInt(field.getAttribute('minlength'))) {
            isValid = false;
            this.showFieldError(field, `Mínimo ${field.getAttribute('minlength')} caracteres`);
        } else if (field.hasAttribute('maxlength') && value.length > parseInt(field.getAttribute('maxlength'))) {
            isValid = false;
            this.showFieldError(field, `Máximo ${field.getAttribute('maxlength')} caracteres`);
        } else if (field.type === 'number') {
            const min = field.getAttribute('min');
            const max = field.getAttribute('max');
            const numValue = parseFloat(value);
            
            if (min && numValue < parseFloat(min)) {
                isValid = false;
                this.showFieldError(field, `El valor mínimo permitido es ${min}`);
            } else if (max && numValue > parseFloat(max)) {
                isValid = false;
                this.showFieldError(field, `El valor máximo permitido es ${max}`);
            }
        }
        
        if (isValid && value) {
            field.classList.add('is-valid');
        }
        
        return isValid;
    }

    showFieldError(field, message) {
        field.classList.add('is-invalid');
        
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
        
        // Scroll al campo con error
        field.scrollIntoView({ behavior: 'smooth', block: 'center' });
        field.focus();
    }

    removeFieldError(field) {
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) existingError.remove();
    }

    async handleAjaxFormSubmit(form) {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn?.innerHTML;
        
        try {
            // Mostrar estado de carga
            this.showButtonLoading(submitBtn);
            
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
                
                // Manejar redirecciones
                if (result.redirect) {
                    setTimeout(() => {
                        window.location.href = result.redirect;
                    }, 1500);
                }
                
                // Recargar página
                if (result.reload) {
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                }
                
                // Limpiar formulario
                if (result.clearForm) {
                    form.reset();
                }
                
            } else {
                this.showNotification(result.message || 'Error en la operación', 'error');
                
                // Mostrar errores de campo específicos
                if (result.errors) {
                    this.showFormErrors(form, result.errors);
                }
            }
            
        } catch (error) {
            console.error('Error submitting form:', error);
            this.showNotification('Error de conexión. Intenta nuevamente.', 'error');
        } finally {
            // Restaurar botón
            this.hideButtonLoading(submitBtn, originalText);
        }
    }

    showFormErrors(form, errors) {
        Object.keys(errors).forEach(fieldName => {
            const field = form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                this.showFieldError(field, errors[fieldName]);
            }
        });
    }

    setupRichTextEditors() {
        // Inicializar TinyMCE si está disponible
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

    setupFileInputs() {
        document.addEventListener('change', (e) => {
            if (e.target.type === 'file') {
                this.previewFile(e.target.files[0], e.target);
            }
        });
    }

    // ===== TABLE FUNCTIONALITY =====
    setupTables() {
        this.setupTableSorting();
        this.setupTableFilters();
        this.setupBulkActions();
        this.setupResponsiveTables();
    }

    setupTableSorting() {
        document.querySelectorAll('.admin-table th[data-sort]').forEach(header => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => this.sortTable(header));
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
        
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
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
        
        // Reordenar filas con animación
        this.animateTableSort(tbody, rows);
    }

    animateTableSort(tbody, rows) {
        tbody.style.opacity = '0.5';
        
        setTimeout(() => {
            tbody.innerHTML = '';
            rows.forEach(row => tbody.appendChild(row));
            
            setTimeout(() => {
                tbody.style.opacity = '1';
            }, 50);
        }, 200);
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
        
        let visibleCount = 0;
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            const isVisible = text.includes(searchTerm);
            row.style.display = isVisible ? '' : 'none';
            
            if (isVisible) {
                visibleCount++;
                row.classList.add('fade-in');
            } else {
                row.classList.remove('fade-in');
            }
        });
        
        // Mostrar mensaje si no hay resultados
        this.updateTableEmptyState(table, visibleCount);
    }

    updateTableEmptyState(table, visibleCount) {
        let emptyRow = table.querySelector('.no-results-message');
        
        if (visibleCount === 0 && !emptyRow) {
            emptyRow = document.createElement('tr');
            emptyRow.className = 'no-results-message';
            emptyRow.innerHTML = `
                <td colspan="100" class="text-center p-6">
                    <div class="text-gray-500">
                        <i class="fas fa-search fa-2x mb-2"></i>
                        <p>No se encontraron resultados</p>
                    </div>
                </td>
            `;
            table.querySelector('tbody').appendChild(emptyRow);
        } else if (visibleCount > 0 && emptyRow) {
            emptyRow.remove();
        }
    }

    setupBulkActions() {
        const bulkSelect = document.querySelector('.bulk-select');
        const bulkActions = document.querySelector('.bulk-actions');
        
        if (bulkSelect && bulkActions) {
            bulkSelect.addEventListener('change', (e) => {
                const checkboxes = document.querySelectorAll('.row-select:checked');
                bulkActions.style.display = checkboxes.length > 0 ? 'flex' : 'none';
            });
        }
    }

    setupResponsiveTables() {
        // Hacer tablas responsivas en móviles
        if (window.innerWidth <= 768) {
            document.querySelectorAll('.admin-table').forEach(table => {
                if (!table.classList.contains('responsive')) {
                    this.makeTableResponsive(table);
                }
            });
        }
    }

    makeTableResponsive(table) {
        const headers = Array.from(table.querySelectorAll('th')).map(th => th.textContent);
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            cells.forEach((cell, index) => {
                if (headers[index]) {
                    cell.setAttribute('data-label', headers[index]);
                }
            });
        });
        
        table.classList.add('responsive');
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
        // Ya configurado en setupFileInputs
    }

    async handleFileUpload(file, uploadZone) {
        const formData = new FormData();
        formData.append('archivo', file);
        
        // Mostrar progreso
        const progress = this.createUploadProgress();
        uploadZone.appendChild(progress);
        
        try {
            const response = await fetch('../admin/gestion-medios.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            progress.remove();
            
            if (data.success) {
                this.showNotification('Archivo subido correctamente', 'success');
                // Recargar la página o actualizar la lista de medios
                setTimeout(() => window.location.reload(), 1000);
            } else {
                this.showNotification(data.error || 'Error al subir archivo', 'error');
            }
        } catch (error) {
            progress.remove();
            this.showNotification('Error de conexión', 'error');
            console.error('Upload error:', error);
        }
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
                <button class="notification-close" aria-label="Cerrar notificación">&times;</button>
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
            } else if (deleteBtn.onclick) {
                deleteBtn.onclick();
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
        if (targetPane) {
            targetPane.classList.add('active');
            targetPane.classList.add('fade-in');
        }
    }

    // ===== REAL-TIME SEARCH =====
    setupRealTimeSearch() {
        // Ya configurado en handleGlobalInput
    }

    performSearch(query, searchType) {
        console.log(`Buscando: "${query}" en ${searchType}`);
        // Aquí se puede implementar AJAX para búsquedas en tiempo real
    }

    // ===== STATUS Y CONEXIÓN =====
    handleOnlineStatus() {
        this.showNotification('Conexión restaurada', 'success', 3000);
    }

    handleOfflineStatus() {
        this.showNotification('Estás trabajando sin conexión', 'warning', 0);
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
        
        setTimeout(() => {
            if (circle.parentNode === btn) {
                btn.removeChild(circle);
            }
        }, 600);
    }

    showButtonLoading(button) {
        if (!button) return;
        
        button.disabled = true;
        button.classList.add('loading');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
        button.dataset.originalHtml = originalHTML;
    }

    hideButtonLoading(button, originalHTML = null) {
        if (!button) return;
        
        button.disabled = false;
        button.classList.remove('loading');
        button.innerHTML = originalHTML || button.dataset.originalHtml || 'Enviar';
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

    // ===== MÉTODOS ESTÁTICOS PARA ACCESO GLOBAL =====
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

    static formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
}

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', () => {
    // Inicializar el panel de administración
    window.adminPanel = new AdminPanel();
    
    // Exponer métodos útiles globalmente
    window.showNotification = (message, type) => window.adminPanel.showNotification(message, type);
    window.formatDate = AdminPanel.formatDate;
    window.formatCurrency = AdminPanel.formatCurrency;
    window.formatFileSize = AdminPanel.formatFileSize;
    
    console.log('🚀 Panel de Administración cargado y listo');
});

// ===== ERROR HANDLING GLOBAL =====
window.addEventListener('error', (e) => {
    console.error('Error global:', e.error);
    if (window.adminPanel) {
        window.adminPanel.showNotification('Ha ocurrido un error inesperado', 'error');
    }
});

// ===== UNHANDLED PROMISE REJECTIONS =====
window.addEventListener('unhandledrejection', (e) => {
    console.error('Promise rejection:', e.reason);
    if (window.adminPanel) {
        window.adminPanel.showNotification('Error en operación asíncrona', 'error');
    }
});

// ===== PERFORMANCE MONITORING =====
if ('performance' in window) {
    window.addEventListener('load', () => {
        const perfData = performance.getEntriesByType('navigation')[0];
        console.log('Tiempo de carga:', perfData.loadEventEnd - perfData.fetchStart, 'ms');
    });
}

// ===== SERVICE WORKER PARA CACHÉ (OPCIONAL) =====
if ('serviceWorker' in navigator && window.location.hostname !== 'localhost') {
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
