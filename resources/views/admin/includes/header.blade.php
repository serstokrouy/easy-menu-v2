<div class="header">
    <div class="header-card">

        <div class="left-side">

            <!-- Mobile Toggle -->

            <div class="logo">
                <img src="{{ asset('assets/web-logo.png') }}" alt="logo" class="logo-image">
            </div>

            <div class="header-title">
                <h1 class="title">EASY-MENU</h1>
            </div>
            <button class="menu-toggle">
                <i class="fa-solid fa-bars"></i>
            </button>
            <a href="{{ route('admin.staffNotifications.index') }}" class="notification-toggle" aria-label="View notifications" id="notificationToggle">
                <i class="fa-solid fa-bell"></i>
                <span class="notification-count" id="notificationCount" style="display: {{ !empty($newStaffNotificationsCount) ? 'inline-flex' : 'none' }};">{{ $newStaffNotificationsCount ?? '' }}</span>
            </a>

        </div>

        <div class="actions" id="navMenu">
            <a href="{{ route('admin.dashboard.index') }}" class="action-btn">
                <i class="fa-solid fa-chart-pie"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('admin.menu.index')}}" class="action-btn">
                <i class="fa-solid fa-utensils"></i>
                <span>Menu</span>
            </a>

            <a href="{{ route('admin.orders.index')}}" class="action-btn">
                <i class="fa-solid fa-receipt"></i>
                <span>Orders</span>
            </a>

            <a href="{{ route('tables.index')}}" class="action-btn">
                <i class="fa-solid fa-chair"></i>
                <span>Table</span>
            </a>

            <a href="{{ route('categories.index')}}" class="action-btn">
                <i class="fa-solid fa-tag"></i>
                <span>Categories</span>
            </a>

            <a class="action-btn">
                <i class="fa-solid fa-chart-line"></i>
                <span>Reports</span>
            </a>
        </div>

        <div class="profile">
            <div class="profile-info">
                <p class="profile-name">3sach</p>
                <img
                    src="{{ asset('assets/profile-image.jpg') }}"
                    alt="profile"
                    class="profile-image"
                >
            </div>
        </div>

    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // Mobile menu toggle
    const toggle = document.querySelector('.menu-toggle');
    const menu = document.getElementById('navMenu');
    const button = document.querySelector('button');

    // 2. Add the click event listener (using optional chaining ? to prevent errors if button doesn't exist)
    button?.addEventListener('click', () => {
        console.log('click');
    });

    // Navigation buttons
    const actionButtons = document.querySelectorAll('.action-btn');
    actionButtons.forEach(button => {
        button.addEventListener('click', () => {
            console.log('click');

            // Remove active from all buttons
            actionButtons.forEach(btn => {
                btn.classList.remove('active');
            });

            // Add active to clicked button
            button.classList.add('active');

            // Close mobile menu
            if (window.innerWidth <= 768) {
                menu?.classList.remove('show');
            }
        });
    });

    // Toggle mobile menu
    toggle?.addEventListener('click', () => {
        menu?.classList.toggle('show');
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (
            window.innerWidth <= 768 &&
            menu &&
            !menu.contains(e.target) &&
            toggle &&
            !toggle.contains(e.target)
        ) {
            menu.classList.remove('show');
        }
    });

    async function refreshNotificationCount() {
        try {
            const response = await fetch('{{ route('admin.staffNotifications.count') }}');
            if (!response.ok) {
                return;
            }
            const data = await response.json();
            const countEl = document.getElementById('notificationCount');

            if (data.count > 0) {
                countEl.textContent = data.count;
                countEl.style.display = 'inline-flex';
            } else {
                countEl.style.display = 'none';
            }
        } catch (error) {
            console.error('Notification refresh failed:', error);
        }
    }

    const notificationRefreshInterval = 10000;
    refreshNotificationCount();
    setInterval(refreshNotificationCount, notificationRefreshInterval);
});
</script>
