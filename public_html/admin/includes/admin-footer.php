<?php
// admin/includes/admin-footer.php
?>
    </main>
</div>

<script src="../js/admin.js"></script>
<script>
// Inicialización básica del panel admin
document.addEventListener('DOMContentLoaded', function() {
    // Toggle del sidebar
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('adminSidebar');
    const main = document.querySelector('.admin-main');
    
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('hidden');
            main.classList.toggle('full-width');
        });
    }
    
    // User dropdown
    const userGreeting = document.querySelector('.user-greeting');
    const userDropdown = document.querySelector('.user-dropdown');
    
    if (userGreeting && userDropdown) {
        userGreeting.addEventListener('click', function(e) {
            e.stopPropagation();
            userDropdown.classList.toggle('show');
        });
        
        // Cerrar dropdown al hacer clic fuera
        document.addEventListener('click', function() {
            userDropdown.classList.remove('show');
        });
    }
});
</script>
</body>
</html>