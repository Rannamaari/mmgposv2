<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\{Customer, Motorcycle, Service, Part};

class CacheService
{
    const CACHE_TTL = 3600; // 1 hour
    const QUICK_CACHE_TTL = 300; // 5 minutes

    public function getQuickServices(): array
    {
        return Cache::tags(['services', 'quick_services'])->remember(
            'quick_services',
            self::CACHE_TTL,
            function () {
                $services = Service::where('is_active', true)
                    ->where('is_quick_service', true)
                    ->orderBy('name')
                    ->get(['id', 'name', 'default_price']);

                $parts = Part::where('is_active', true)
                    ->where('is_quick_service', true)
                    ->orderBy('name')
                    ->get(['id', 'name', 'price']);

                return [
                    'services' => $services,
                    'parts' => $parts,
                ];
            }
        );
    }

    public function getActiveServices(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::tags(['services'])->remember(
            'active_services',
            self::CACHE_TTL,
            fn() => Service::where('is_active', true)->orderBy('name')->get()
        );
    }

    public function getActiveParts(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::tags(['parts'])->remember(
            'active_parts',
            self::CACHE_TTL,
            fn() => Part::where('is_active', true)->orderBy('name')->get()
        );
    }

    public function getCustomers(): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::tags(['customers'])->remember(
            'all_customers',
            self::QUICK_CACHE_TTL,
            fn() => Customer::orderBy('name')->get()
        );
    }

    public function getCustomerMotorcycles(int $customerId): \Illuminate\Database\Eloquent\Collection
    {
        return Cache::tags(['motorcycles', 'customers'])->remember(
            "customer_{$customerId}_motorcycles",
            self::QUICK_CACHE_TTL,
            fn() => Motorcycle::where('customer_id', $customerId)->orderBy('plate_no')->get()
        );
    }

    public function clearCustomerCache(int $customerId = null): void
    {
        if ($customerId) {
            Cache::tags(['customers', 'motorcycles'])->forget("customer_{$customerId}_motorcycles");
        }
        Cache::tags(['customers'])->flush();
    }

    public function clearServiceCache(): void
    {
        Cache::tags(['services', 'quick_services'])->flush();
    }

    public function clearPartCache(): void
    {
        Cache::tags(['parts', 'quick_services'])->flush();
    }

    public function clearMotorcycleCache(): void
    {
        Cache::tags(['motorcycles'])->flush();
    }

    public function clearAllCache(): void
    {
        Cache::tags(['customers', 'motorcycles', 'services', 'parts', 'quick_services'])->flush();
    }
}