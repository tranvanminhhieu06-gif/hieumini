/**
 * HieuMini - Admin Panel Scripts (UI/UX Pro Max Edition)
 */

document.addEventListener('DOMContentLoaded', () => {
  const adminSidebar = document.getElementById('admin-sidebar');
  const adminSidebarToggle = document.getElementById('admin-sidebar-toggle');
  const sidebarCollapsePin = document.getElementById('sidebar-collapse-pin');
  const adminSidebarBackdrop = document.getElementById('admin-sidebar-backdrop');

  // ==========================================
  // 1. Sidebar Desktop Collapse & Storage
  // ==========================================
  const isCollapsed = localStorage.getItem('admin_sidebar_collapsed') === 'true';
  if (isCollapsed && window.innerWidth > 992) {
    document.body.classList.add('sidebar-collapsed');
  }

  function toggleDesktopCollapse() {
    document.body.classList.toggle('sidebar-collapsed');
    const currentlyCollapsed = document.body.classList.contains('sidebar-collapsed');
    localStorage.setItem('admin_sidebar_collapsed', currentlyCollapsed);
  }

  if (sidebarCollapsePin) {
    sidebarCollapsePin.addEventListener('click', toggleDesktopCollapse);
  }

  // ==========================================
  // 2. Sidebar Mobile Drawer Toggle
  // ==========================================
  function openMobileSidebar() {
    if (adminSidebar && adminSidebarBackdrop) {
      adminSidebar.classList.add('mobile-open');
      adminSidebarBackdrop.classList.add('active');
      document.body.style.overflow = 'hidden';
    }
  }

  function closeMobileSidebar() {
    if (adminSidebar && adminSidebarBackdrop) {
      adminSidebar.classList.remove('mobile-open');
      adminSidebarBackdrop.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  if (adminSidebarToggle) {
    adminSidebarToggle.addEventListener('click', () => {
      if (window.innerWidth <= 992) {
        if (adminSidebar && adminSidebar.classList.contains('mobile-open')) {
          closeMobileSidebar();
        } else {
          openMobileSidebar();
        }
      } else {
        toggleDesktopCollapse();
      }
    });
  }

  if (adminSidebarBackdrop) {
    adminSidebarBackdrop.addEventListener('click', closeMobileSidebar);
  }

  // ==========================================
  // 3. Admin Notification Dropdown Toggle
  // ==========================================
  const notificationToggleBtn = document.getElementById('notification-toggle-btn');
  const notificationDropdown = document.getElementById('notification-dropdown');

  if (notificationToggleBtn && notificationDropdown) {
    notificationToggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      // Close other dropdowns
      if (adminUserDropdown) adminUserDropdown.classList.remove('show');
      notificationDropdown.classList.toggle('show');
    });
  }

  // ==========================================
  // 4. Admin User Profile Dropdown Toggle
  // ==========================================
  const adminUserBtn = document.getElementById('admin-user-btn');
  const adminUserDropdown = document.getElementById('admin-user-dropdown');

  if (adminUserBtn && adminUserDropdown) {
    adminUserBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      // Close other dropdowns
      if (notificationDropdown) notificationDropdown.classList.remove('show');
      adminUserDropdown.classList.toggle('show');
    });
  }

  // Close dropdowns on outside click or Escape key
  document.addEventListener('click', (e) => {
    if (notificationDropdown && !notificationDropdown.contains(e.target) && notificationToggleBtn && !notificationToggleBtn.contains(e.target)) {
      notificationDropdown.classList.remove('show');
    }
    if (adminUserDropdown && !adminUserDropdown.contains(e.target) && adminUserBtn && !adminUserBtn.contains(e.target)) {
      adminUserDropdown.classList.remove('show');
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeMobileSidebar();
      if (notificationDropdown) notificationDropdown.classList.remove('show');
      if (adminUserDropdown) adminUserDropdown.classList.remove('show');
    }
  });

  // ==========================================
  // 5. Delete Confirmations
  // ==========================================
  document.querySelectorAll('.btn-delete-confirm').forEach(btn => {
    btn.addEventListener('click', (e) => {
      if (!confirm('Bạn có chắc chắn muốn xóa mục này không? Hành động này không thể hoàn tác.')) {
        e.preventDefault();
      }
    });
  });

  // ==========================================
  // 6. Image Preview Before Upload
  // ==========================================
  const imgInput = document.querySelector('.image-upload-input');
  const imgPreview = document.querySelector('.image-preview-target');
  if (imgInput && imgPreview) {
    imgInput.addEventListener('change', function() {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          imgPreview.src = e.target.result;
          imgPreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
      }
    });
  }
});
