<x-filament-panels::page>
    <div class="text-center py-12">
        <div class="text-6xl mb-4">🏪</div>
        <h2 class="text-2xl font-bold text-gray-900 mb-4">Redirecting to POS System...</h2>
        <p class="text-gray-600">You will be redirected to the Point of Sale system in a moment.</p>
        
        <script>
            // Fallback redirect in case mount() redirect doesn't work
            setTimeout(function() {
                window.location.href = '/pos';
            }, 1000);
        </script>
    </div>
</x-filament-panels::page>