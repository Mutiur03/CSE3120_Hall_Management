/* ==========================================================================
   Hall Management System — Admin behaviours
   Sidebar collapse (desktop), sidebar drawer (mobile), submenu accordion.
   ========================================================================== */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        const body = document.body;

        // Restore collapsed state from previous visit.
        if (localStorage.getItem('admin.sidebarCollapsed') === '1') {
            body.classList.add('sidebar-collapsed');
        }

        // Desktop collapse toggle (in sidebar header).
        const toggle = document.getElementById('sidebarToggle');
        if (toggle) {
            toggle.addEventListener('click', function () {
                body.classList.toggle('sidebar-collapsed');
                localStorage.setItem(
                    'admin.sidebarCollapsed',
                    body.classList.contains('sidebar-collapsed') ? '1' : '0'
                );
            });
        }

        // Mobile drawer toggle (in topbar).
        const toggleMobile = document.getElementById('sidebarToggleMobile');
        if (toggleMobile) {
            toggleMobile.addEventListener('click', function () {
                body.classList.toggle('sidebar-open');
            });
        }

        // Close mobile drawer when clicking the backdrop.
        document.addEventListener('click', function (e) {
            if (
                body.classList.contains('sidebar-open') &&
                !e.target.closest('#sidebar') &&
                !e.target.closest('#sidebarToggleMobile')
            ) {
                body.classList.remove('sidebar-open');
            }
        });

        // Submenu accordion.
        document.querySelectorAll('.has-submenu > .nav-link').forEach(function (link) {
            const item = link.parentElement;
            const submenu = item.querySelector('.submenu');

            // Mark items whose submenu starts open (active route).
            if (submenu && submenu.classList.contains('show')) {
                item.classList.add('open');
            }

            link.addEventListener('click', function (e) {
                if (link.getAttribute('href') === '#' || !link.getAttribute('href')) {
                    e.preventDefault();
                }
                if (!submenu) return;

                const isOpen = item.classList.contains('open');

                // Collapse siblings for a clean accordion.
                document.querySelectorAll('.has-submenu.open').forEach(function (other) {
                    if (other !== item) {
                        other.classList.remove('open');
                        const sm = other.querySelector('.submenu');
                        if (sm) sm.classList.remove('show');
                    }
                });

                item.classList.toggle('open', !isOpen);
                submenu.classList.toggle('show', !isOpen);
            });
        });
    });
})();
