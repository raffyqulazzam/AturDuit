<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>@yield('title', 'AturDuit - Manage Keuangan')</title>
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.js"></script>
    <!-- Alpine.js CDN -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#3b82f6',
                            600: '#2563eb',
                            700: '#1d4ed8',
                            800: '#1e40af',
                            900: '#1e3a8a'
                        }
                    }
                }
            }
        }
        
        // Initialize dark mode on page load
        if (localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    
    <style>
        /* Chart container styling */
        .chart-container {
            position: relative;
            height: 320px;
            width: 100%;
            margin: 0 auto;
        }
        
        /* Ensure canvas is responsive */
        .chart-container canvas {
            max-width: 100% !important;
            height: 320px !important;
            display: block;
        }
        
        /* Loading state for charts */
        .chart-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 320px;
            color: #6b7280;
            font-size: 14px;
        }
        
        /* Custom scrollbar for better UX */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        
        /* Prevent flash of unstyled content for Alpine.js elements */
        [x-cloak] { 
            display: none !important; 
        }
        
        /* Smooth dropdown transitions */
        .dropdown-transition {
            transition: opacity 0.2s ease-in-out, transform 0.2s ease-in-out;
        }
        
        /* Hide dropdown initially */
        .dropdown-hidden {
            opacity: 0;
            transform: translateY(-10px);
            visibility: hidden;
        }
        
        /* Show dropdown when active */
        .dropdown-shown {
            opacity: 1;
            transform: translateY(0);
            visibility: visible;
        }
    </style>
    
    @yield('styles')
</head>
<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <div class="flex h-screen bg-gray-50 dark:bg-gray-900">
        <!-- Sidebar -->
        <div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0">
            <div class="flex flex-col flex-grow pt-5 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="flex items-center flex-shrink-0 px-4">
                    <div class="flex items-center">
                        <img src="{{ asset('images/logo.png') }}" alt="AturDuit" class="w-8 h-8 rounded-lg">
                        <span class="ml-3 text-xl font-bold text-gray-900 dark:text-white">AturDuit</span>
                    </div>
                </div>
                
                <div class="mt-8 flex-grow flex flex-col">
                    <nav class="flex-1 px-2 space-y-1">
                        <!-- Core Features - Most Important -->
                        <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-blue-50 dark:bg-blue-900/50 border-r-4 border-blue-600 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-l-md">
                            <i data-lucide="home" class="mr-3 h-5 w-5"></i>
                            Dashboard
                        </a>
                        
                        <!-- Daily Usage Features -->
                        <a href="{{ route('transactions.index') }}" class="{{ request()->routeIs('transactions.*') ? 'bg-blue-50 dark:bg-blue-900/50 border-r-4 border-blue-600 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i data-lucide="credit-card" class="mr-3 h-5 w-5"></i>
                            Transaksi
                        </a>
                        
                        <a href="{{ route('accounts.index') }}" class="{{ request()->routeIs('accounts.*') ? 'bg-blue-50 dark:bg-blue-900/50 border-r-4 border-blue-600 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i data-lucide="wallet" class="mr-3 h-5 w-5"></i>
                            Dompet
                        </a>
                        
                        <!-- Setup & Configuration -->
                        <a href="{{ route('categories.index') }}" class="{{ request()->routeIs('categories.*') ? 'bg-blue-50 dark:bg-blue-900/50 border-r-4 border-blue-600 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i data-lucide="tag" class="mr-3 h-5 w-5"></i>
                            Kategori
                        </a>
                        
                        <a href="{{ route('account-types.index') }}" class="{{ request()->routeIs('account-types.*') ? 'bg-blue-50 dark:bg-blue-900/50 border-r-4 border-blue-600 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i data-lucide="layers" class="mr-3 h-5 w-5"></i>
                            Jenis Dompet
                        </a>
                        
                        <!-- Planning & Goals -->
                        <a href="{{ route('budgets.index') }}" class="{{ request()->routeIs('budgets.*') ? 'bg-blue-50 dark:bg-blue-900/50 border-r-4 border-blue-600 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i data-lucide="target" class="mr-3 h-5 w-5"></i>
                            Budget
                        </a>
                        
                        <a href="{{ route('savings-goals.index') }}" class="{{ request()->routeIs('savings-goals.*') ? 'bg-blue-50 dark:bg-blue-900/50 border-r-4 border-blue-600 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i data-lucide="piggy-bank" class="mr-3 h-5 w-5"></i>
                            Tabungan
                        </a>
                        
                        <!-- Analysis & Reports -->
                        <a href="{{ route('reports.index') }}" class="{{ request()->routeIs('reports.*') ? 'bg-blue-50 dark:bg-blue-900/50 border-r-4 border-blue-600 text-blue-700 dark:text-blue-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-2 py-2 text-sm font-medium rounded-md">
                            <i data-lucide="bar-chart-3" class="mr-3 h-5 w-5"></i>
                            Laporan
                        </a>
                    </nav>
                </div>
            </div>
        </div>
        
        <!-- Main content -->
        <div class="md:pl-64 flex flex-col flex-1">
            <!-- Top bar -->
            <div class="sticky top-0 z-10 flex-shrink-0 flex h-16 bg-white dark:bg-gray-800 shadow border-b border-gray-200 dark:border-gray-700">
                <div class="flex-1 px-4 flex justify-between items-center">
                    <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">@yield('page-title', 'Dashboard')</h1>
                    
                    <div class="flex items-center space-x-4">
                        
                        <!-- Notifications Dropdown -->
                        <div class="relative" x-data="{ open: false, unreadCount: 1 }" x-cloak>
                            <button @click.stop="open = !open" type="button" class="bg-white dark:bg-gray-800 p-1 rounded-full text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 relative">
                                <i data-lucide="bell" class="h-6 w-6"></i>
                                <!-- Notification Badge -->
                                <span x-show="unreadCount > 0" x-text="unreadCount" class="absolute -top-1 -right-1 h-5 w-5 bg-red-500 text-white text-xs rounded-full flex items-center justify-center min-w-[20px]"></span>
                            </button>
                            
                            <!-- Notifications Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute right-0 mt-2 w-96 bg-white dark:bg-gray-800 rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 dark:ring-gray-700 z-50">
                                <div class="py-2">
                                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifikasi</h3>
                                        <button class="text-xs text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Tandai semua dibaca</button>
                                    </div>
                                    <div class="max-h-80 overflow-y-auto">
                                        <div class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-700 transition-colors bg-blue-50 dark:bg-blue-900/20">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0">
                                                    <div class="w-8 h-8 rounded-full flex items-center justify-center bg-yellow-100 dark:bg-yellow-900/50">
                                                        <i data-lucide="alert-triangle" class="w-4 h-4 text-yellow-600 dark:text-yellow-400"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-medium text-gray-900 dark:text-white">Budget Alert</p>
                                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Pengeluaran untuk kategori Makanan sudah mencapai 80% dari budget bulanan.</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-2">Baru saja</p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                                        <a href="#" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 font-medium">Lihat semua notifikasi</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ 
                            open: false,
                            stats: { 
                                formatted: { 
                                    total_balance: 'Loading...', 
                                    net_income: 'Loading...' 
                                } 
                            }
                        }" x-init="
                            fetch('/api/quick-stats')
                                .then(response => response.json())
                                .then(data => { stats = data; })
                                .catch(() => { 
                                    stats = { 
                                        formatted: { 
                                            total_balance: 'Rp 0,-', 
                                            net_income: 'Rp 0,-' 
                                        } 
                                    }; 
                                });
                        " x-cloak>
                            <button @click.stop="open = !open" type="button" class="bg-white rounded-full flex text-sm focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                                    <i data-lucide="user" class="w-4 h-4 text-blue-600"></i>
                                </div>
                            </button>
                            
                            <!-- Profile Dropdown Menu -->
                            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1" class="absolute right-0 mt-2 w-72 bg-white dark:bg-gray-800 rounded-lg shadow-xl ring-1 ring-black ring-opacity-5 dark:ring-gray-700 z-50">
                                <div class="p-4">
                                    <!-- User Info Header -->
                                    <div class="flex items-center space-x-3 pb-4 border-b border-gray-200 dark:border-gray-700">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center">
                                            <span class="text-white font-semibold text-lg">{{ substr(Auth::user()->name ?? 'G', 0, 1) }}</span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ Auth::user()->name ?? 'Guest User' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email ?? 'guest@aturduit.com' }}</p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-300 mt-1">
                                                <div class="w-2 h-2 bg-green-400 rounded-full mr-1"></div>
                                                Online
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Quick Stats -->
                                    <div class="py-3 border-b border-gray-200 dark:border-gray-700">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Quick Stats</p>
                                        <div class="grid grid-cols-2 gap-3">
                                            <div class="text-center p-2 bg-blue-50 dark:bg-blue-900/50 rounded-lg">
                                                <p class="text-xs text-gray-600 dark:text-gray-400">Saldo Total</p>
                                                <p class="text-sm font-semibold text-blue-600 dark:text-blue-400" x-text="stats.formatted?.total_balance || 'Loading...'"></p>
                                            </div>
                                            <div class="text-center p-2 bg-green-50 dark:bg-green-900/50 rounded-lg">
                                                <p class="text-xs text-gray-600 dark:text-gray-400">Net Bulan Ini</p>
                                                <p class="text-sm font-semibold text-green-600 dark:text-green-400" x-text="stats.formatted?.net_income || 'Loading...'"></p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Quick Actions -->
                                    <div class="py-3 border-b border-gray-200 dark:border-gray-700">
                                        <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Quick Actions</p>
                                        <div class="space-y-1">
                                            <a href="#" class="flex items-center px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/50 hover:text-blue-700 dark:hover:text-blue-300 rounded-md transition-colors">
                                                <i data-lucide="settings" class="w-4 h-4 mr-3"></i>
                                                Pengaturan Akun
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <!-- Settings & Dark Mode -->
                                    <div class="pt-3">
                                        <div class="space-y-1">
                                            <button @click="
                                                document.documentElement.classList.toggle('dark');
                                                localStorage.setItem('darkMode', document.documentElement.classList.contains('dark'));
                                                setTimeout(() => lucide.createIcons(), 100);
                                            " class="flex items-center w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md transition-colors">
                                                <i data-lucide="moon" class="w-4 h-4 mr-3 dark:hidden"></i>
                                                <i data-lucide="sun" class="w-4 h-4 mr-3 hidden dark:block"></i>
                                                <span class="dark:hidden">Dark Mode</span>
                                                <span class="hidden dark:block">Light Mode</span>
                                            </button>
                                            <form method="POST" action="{{ route('logout') }}" class="block">
                                                @csrf
                                                <button type="submit" class="flex items-center w-full px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/50 rounded-md transition-colors">
                                                    <i data-lucide="log-out" class="w-4 h-4 mr-3"></i>
                                                    Keluar
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Page content -->
            <main class="flex-1 overflow-y-auto">
                @yield('content')
            </main>
        </div>
    </div>
    
    <!-- Initialize Lucide Icons -->
    <script>
        // Prevent flash by ensuring Alpine.js is ready before showing content
        document.addEventListener('DOMContentLoaded', () => {
            // Hide body initially to prevent flash
            document.body.style.visibility = 'hidden';
        });
        
        lucide.createIcons();
        
        // Re-create icons when Alpine.js updates the DOM
        document.addEventListener('alpine:initialized', () => {
            setTimeout(() => {
                lucide.createIcons();
                // Show body after Alpine.js is ready
                document.body.style.visibility = 'visible';
            }, 100);
        });
        
        // Fallback to show body if Alpine.js takes too long
        setTimeout(() => {
            document.body.style.visibility = 'visible';
        }, 2000);
    </script>
    
    <!-- Alpine.js Components -->
    <script>
        // Notification Dropdown Component
        function notificationDropdown() {
            return {
                open: false,
                notifications: [],
                unreadCount: 0,
                
                async init() {
                    await this.loadNotifications();
                },
                
                async loadNotifications() {
                    try {
                        const response = await fetch('/notifications');
                        const data = await response.json();
                        this.notifications = data.notifications || [];
                        this.updateUnreadCount();
                    } catch (error) {
                        console.error('Error loading notifications:', error);
                        // Set default notifications if API fails
                        this.notifications = [
                            {
                                id: 1,
                                title: 'Budget Alert',
                                message: 'Pengeluaran untuk kategori "Makanan" sudah mencapai 80% dari budget bulanan.',
                                type: 'warning',
                                icon: 'alert-triangle',
                                created_at: new Date().toISOString(),
                                read_at: null
                            },
                            {
                                id: 2,
                                title: 'Transaksi Baru',
                                message: 'Transaksi pemasukan sebesar Rp 5.000.000,- telah ditambahkan ke akun BCA.',
                                type: 'success',
                                icon: 'check-circle',
                                created_at: new Date(Date.now() - 3600000).toISOString(),
                                read_at: null
                            },
                            {
                                id: 3,
                                title: 'Target Tabungan',
                                message: 'Selamat! Anda telah mencapai 75% dari target tabungan untuk "Liburan 2025".',
                                type: 'success',
                                icon: 'target',
                                created_at: new Date(Date.now() - 7200000).toISOString(),
                                read_at: null
                            }
                        ];
                        this.updateUnreadCount();
                    }
                },
                
                updateUnreadCount() {
                    this.unreadCount = this.notifications.filter(n => !n.read_at).length;
                },
                
                async markAsRead(notificationId) {
                    try {
                        await fetch('/notifications/mark-read', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ id: notificationId })
                        });
                        
                        // Update local state
                        const notification = this.notifications.find(n => n.id === notificationId);
                        if (notification && !notification.read_at) {
                            notification.read_at = new Date().toISOString();
                            this.updateUnreadCount();
                        }
                    } catch (error) {
                        console.error('Error marking notification as read:', error);
                        // Update local state even if API fails
                        const notification = this.notifications.find(n => n.id === notificationId);
                        if (notification && !notification.read_at) {
                            notification.read_at = new Date().toISOString();
                            this.updateUnreadCount();
                        }
                    }
                },
                
                async markAllAsRead() {
                    try {
                        await fetch('/notifications/mark-all-read', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });
                        
                        // Update all notifications to read
                        this.notifications.forEach(notification => {
                            if (!notification.read_at) {
                                notification.read_at = new Date().toISOString();
                            }
                        });
                        this.updateUnreadCount();
                    } catch (error) {
                        console.error('Error marking all notifications as read:', error);
                        // Update local state even if API fails
                        this.notifications.forEach(notification => {
                            if (!notification.read_at) {
                                notification.read_at = new Date().toISOString();
                            }
                        });
                        this.updateUnreadCount();
                    }
                },
                
                getTimeAgo(dateString) {
                    const date = new Date(dateString);
                    const now = new Date();
                    const diff = now - date;
                    const minutes = Math.floor(diff / 60000);
                    
                    if (minutes < 1) return 'Baru saja';
                    if (minutes < 60) return `${minutes} menit yang lalu`;
                    
                    const hours = Math.floor(minutes / 60);
                    if (hours < 24) return `${hours} jam yang lalu`;
                    
                    const days = Math.floor(hours / 24);
                    return `${days} hari yang lalu`;
                }
            }
        }
        
        // Profile Dropdown Component
        function profileDropdown() {
            return {
                open: false,
                stats: {},
                
                async init() {
                    await this.loadStats();
                },
                
                async loadStats() {
                    try {
                        const response = await fetch('/api/quick-stats');
                        this.stats = await response.json();
                    } catch (error) {
                        console.error('Error loading quick stats:', error);
                        // Set default values if API fails
                        this.stats = {
                            total_balance: 0,
                            net_income: 0,
                            formatted: {
                                total_balance: 'Rp 0,-',
                                net_income: 'Rp 0,-'
                            }
                        };
                    }
                },
                
                toggleDarkMode() {
                    const isDark = document.documentElement.classList.contains('dark');
                    document.documentElement.classList.toggle('dark', !isDark);
                    localStorage.setItem('darkMode', !isDark);
                    setTimeout(() => lucide.createIcons(), 100);
                }
            }
        }
    </script>
    
    @yield('scripts')
</body>
</html>
