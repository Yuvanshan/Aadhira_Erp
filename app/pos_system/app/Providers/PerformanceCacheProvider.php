<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\User;
use App\Contact;
use App\BusinessLocation;
use App\Unit;
use App\Category;
use App\Brands;
use App\TaxRate;
use App\SellingPriceGroup;
use App\CustomerGroup;
use App\Account;
use App\ExpenseCategory;
use App\Product;
use App\Utils\PerformanceUtil;
use Illuminate\Support\Facades\Cache;

/**
 * Performance Cache Provider
 * Manages global caching for dropdown data and clears cache when data changes
 * This ensures dropdowns are cached but cache is invalidated on updates
 */
class PerformanceCacheProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Clear dropdown caches when users are created/updated/deleted
        User::created(function ($user) {
            PerformanceUtil::clearDropdownCaches($user->business_id, ['users']);
        });
        User::updated(function ($user) {
            PerformanceUtil::clearDropdownCaches($user->business_id, ['users']);
        });
        User::deleted(function ($user) {
            PerformanceUtil::clearDropdownCaches($user->business_id, ['users']);
        });

        // Clear dropdown caches when contacts are created/updated/deleted
        Contact::created(function ($contact) {
            PerformanceUtil::clearDropdownCaches($contact->business_id, ['contacts']);
        });
        Contact::updated(function ($contact) {
            PerformanceUtil::clearDropdownCaches($contact->business_id, ['contacts']);
        });
        Contact::deleted(function ($contact) {
            PerformanceUtil::clearDropdownCaches($contact->business_id, ['contacts']);
        });

        // Clear dropdown caches when locations are created/updated/deleted
        BusinessLocation::created(function ($location) {
            PerformanceUtil::clearDropdownCaches($location->business_id, ['locations']);
        });
        BusinessLocation::updated(function ($location) {
            PerformanceUtil::clearDropdownCaches($location->business_id, ['locations']);
        });
        BusinessLocation::deleted(function ($location) {
            PerformanceUtil::clearDropdownCaches($location->business_id, ['locations']);
        });

        // Clear dropdown caches when units are created/updated/deleted
        Unit::created(function ($unit) {
            PerformanceUtil::clearDropdownCaches($unit->business_id, ['units']);
        });
        Unit::updated(function ($unit) {
            PerformanceUtil::clearDropdownCaches($unit->business_id, ['units']);
        });
        Unit::deleted(function ($unit) {
            PerformanceUtil::clearDropdownCaches($unit->business_id, ['units']);
        });

        // Clear dropdown caches when categories are created/updated/deleted
        Category::created(function ($category) {
            PerformanceUtil::clearDropdownCaches($category->business_id, ['categories']);
        });
        Category::updated(function ($category) {
            PerformanceUtil::clearDropdownCaches($category->business_id, ['categories']);
        });
        Category::deleted(function ($category) {
            PerformanceUtil::clearDropdownCaches($category->business_id, ['categories']);
        });

        // Clear dropdown caches when brands are created/updated/deleted
        Brands::created(function ($brand) {
            PerformanceUtil::clearDropdownCaches($brand->business_id, ['brands']);
        });
        Brands::updated(function ($brand) {
            PerformanceUtil::clearDropdownCaches($brand->business_id, ['brands']);
        });
        Brands::deleted(function ($brand) {
            PerformanceUtil::clearDropdownCaches($brand->business_id, ['brands']);
        });

        // Clear dropdown caches when tax rates are created/updated/deleted
        TaxRate::created(function ($tax) {
            PerformanceUtil::clearDropdownCaches($tax->business_id, ['tax_rates']);
        });
        TaxRate::updated(function ($tax) {
            PerformanceUtil::clearDropdownCaches($tax->business_id, ['tax_rates']);
        });
        TaxRate::deleted(function ($tax) {
            PerformanceUtil::clearDropdownCaches($tax->business_id, ['tax_rates']);
        });

        // Clear dropdown caches when price groups are created/updated/deleted
        SellingPriceGroup::created(function ($group) {
            PerformanceUtil::clearDropdownCaches($group->business_id, ['price_groups']);
        });
        SellingPriceGroup::updated(function ($group) {
            PerformanceUtil::clearDropdownCaches($group->business_id, ['price_groups']);
        });
        SellingPriceGroup::deleted(function ($group) {
            PerformanceUtil::clearDropdownCaches($group->business_id, ['price_groups']);
        });

        // Clear dropdown caches when customer groups are created/updated/deleted
        CustomerGroup::created(function ($group) {
            PerformanceUtil::clearDropdownCaches($group->business_id, ['customer_groups']);
        });
        CustomerGroup::updated(function ($group) {
            PerformanceUtil::clearDropdownCaches($group->business_id, ['customer_groups']);
        });
        CustomerGroup::deleted(function ($group) {
            PerformanceUtil::clearDropdownCaches($group->business_id, ['customer_groups']);
        });

        // Clear dropdown caches when accounts are created/updated/deleted
        Account::created(function ($account) {
            PerformanceUtil::clearDropdownCaches($account->business_id, ['accounts']);
        });
        Account::updated(function ($account) {
            PerformanceUtil::clearDropdownCaches($account->business_id, ['accounts']);
        });
        Account::deleted(function ($account) {
            PerformanceUtil::clearDropdownCaches($account->business_id, ['accounts']);
        });

        // Clear dropdown caches when expense categories are created/updated/deleted
        ExpenseCategory::created(function ($category) {
            PerformanceUtil::clearDropdownCaches($category->business_id, ['expense_categories']);
        });
        ExpenseCategory::updated(function ($category) {
            PerformanceUtil::clearDropdownCaches($category->business_id, ['expense_categories']);
        });
        ExpenseCategory::deleted(function ($category) {
            PerformanceUtil::clearDropdownCaches($category->business_id, ['expense_categories']);
        });

        // Clear only known related caches when products are modified.
        Product::created(function ($product) {
            self::clearProductRelatedCaches($product->business_id);
        });
        Product::updated(function ($product) {
            self::clearProductRelatedCaches($product->business_id);
        });
        Product::deleted(function ($product) {
            self::clearProductRelatedCaches($product->business_id);
        });

        // Clear business caches when business settings change
        \App\Business::updated(function ($business) {
            self::clearBusinessCaches($business->id);
        });

        // Clear user permission caches when roles/permissions change
        \App\User::updated(function ($user) {
            Cache::forget("user_permissions_{$user->id}");
        });
    }

    /**
     * Clear business-related caches
     */
    private static function clearBusinessCaches($business_id)
    {
        $cache_keys = [
            "business_settings_{$business_id}",
            "business_locations_{$business_id}",
        ];

        foreach ($cache_keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Clear product-adjacent caches without flushing the entire cache store.
     */
    private static function clearProductRelatedCaches($business_id)
    {
        $cache_keys = [
            'layout_system_settings',
            'layout_module_additional_script',
            'module_assets',
            "business_settings_{$business_id}",
            "business_locations_{$business_id}",
        ];

        foreach ($cache_keys as $key) {
            Cache::forget($key);
        }
    }
}
