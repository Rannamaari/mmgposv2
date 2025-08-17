<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS System - Garage Management</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Custom Styles -->
    <style>
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Smooth transitions */
        * {
            transition: all 0.2s ease;
        }

        /* Focus styles */
        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        /* Button hover effects */
        button:hover {
            transform: translateY(-1px);
        }

        button:active {
            transform: translateY(0);
        }
    </style>
</head>

<body class="bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <div class="flex-shrink-0 flex items-center">
                        <span class="text-2xl mr-2">🏍️</span>
                        <h1 class="text-xl font-bold text-gray-900">Garage POS</h1>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    @auth
                        <!-- Show user name -->
                        <span class="text-gray-700 text-sm">
                            Welcome, <span class="font-semibold">{{ auth()->user()->name }}</span>
                        </span>
                        
                        <!-- Dashboard link - for all authenticated users -->
                        <a href="/admin" class="text-green-600 hover:text-green-900 px-3 py-2 rounded-md text-sm font-medium">
                            📊 Dashboard
                        </a>
                        
                        <!-- Admin Panel link - only for admin users (if they want full admin features) -->
                        <!-- Admin Panel link - available for all authenticated users -->
                            <a href="/admin" class="text-blue-600 hover:text-blue-900 px-3 py-2 rounded-md text-sm font-medium">
                                ⚙️ Admin Panel
                            </a>
                        
                        
                        <!-- Sign Out button -->
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md text-sm font-medium flex items-center">
                                🚪 Sign Out
                            </button>
                        </form>
                    @else
                        <!-- Login link for unauthenticated users -->
                        <a href="/admin/login" class="text-blue-600 hover:text-blue-900 px-3 py-2 rounded-md text-sm font-medium">
                            🔐 Login
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        @livewire('p-o-s-page')
    </main>

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Custom Scripts -->
    <script>
        // Auto-hide flash messages
        document.addEventListener('DOMContentLoaded', function () {
            const flashMessages = document.querySelectorAll('[class*="fixed"]');
            flashMessages.forEach(function (message) {
                setTimeout(function () {
                    message.style.opacity = '0';
                    setTimeout(function () {
                        message.remove();
                    }, 300);
                }, 3000);
            });
        });

        // Prevent form submission on Enter key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
            }
        });
    </script>
</body>

</html>