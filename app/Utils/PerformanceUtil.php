<?php

namespace App\Utils;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Performance Optimization Utility
 * Provides methods for batch loading, eager loading, and caching to reduce N+1 queries
 */
class PerformanceUtil
{
    /**
     * Batch load variations with eager loaded relations
     * Replaces individual Variation::find() calls in loops
     *
     * @param array $variation_ids
     * @param array $relations ['media', 'product', etc]
     * @return \Illuminate\Support\Collection Keyed by variation ID for easy lookup
     */
    public static function batchLoadVariations($variation_ids, $relations = ['media'])
    {
        if (empty($variation_ids)) {
            return collect();
        }

        return \App\Variation::whereIn('id', array_unique($variation_ids))
            ->with($relations)
            ->get()
            ->keyBy('id');
    }

    /**
     * Batch load products with eager loaded relations
     * Replaces individual Product::find() calls in loops
     *
     * @param array $product_ids
     * @param array $relations ['variations', 'stocks', etc]
     * @return \Illuminate\Support\Collection Keyed by product ID for easy lookup
     */
    public static function batchLoadProducts($product_ids, $relations = [])
    {
        if (empty($product_ids)) {
            return collect();
        }

        return \App\Product::whereIn('id', array_unique($product_ids))
            ->with($relations)
            ->get()
            ->keyBy('id');
    }

    /**
     * Batch load users with eager loaded relations
     * Replaces individual User::find() calls in loops
     *
     * @param array $user_ids
     * @param array $relations ['roles', 'permissions', etc]
     * @return \Illuminate\Support\Collection Keyed by user ID for easy lookup
     */
    public static function batchLoadUsers($user_ids, $relations = [])
    {
        if (empty($user_ids)) {
            return collect();
        }

        return \App\User::whereIn('id', array_unique($user_ids))
            ->with($relations)
            ->get()
            ->keyBy('id');
    }

    /**
     * Batch load lot numbers for variations in a single query
     * Replaces multiple getLotNumbersFromVariation() calls in loops
     *
     * @param array $variation_ids
     * @param int $business_id
     * @param int $location_id
     * @return array Keyed by variation_id for easy lookup
     */
    public static function batchLoadLotNumbers($variation_ids, $business_id, $location_id)
    {
        if (empty($variation_ids)) {
            return [];
        }

        $lot_numbers = \App\PurchaseLine::join(
            'purchase_lines_inventory_details AS plid',
            'purchase_lines.id',
            '=',
            'plid.purchase_line_id'
        )
            ->where('purchase_lines.variation_id', '!=', null)
            ->where('plid.location_id', $location_id)
            ->whereIn('purchase_lines.variation_id', array_unique($variation_ids))
            ->select(
                'purchase_lines.variation_id',
                'plid.quantity',
                'purchase_lines.lot_number',
                'purchase_lines.expiry_date',
                'purchase_lines.id'
            )
            ->get();

        // Group by variation_id for easy lookup
        return $lot_numbers->groupBy('variation_id')->toArray();
    }

    /**
     * Batch load transaction sell line modifiers
     * Replaces individual query per line item
     *
     * @param array $parent_sell_line_ids
     * @param string $children_type ['modifier', 'combo', etc]
     * @return array Keyed by parent_sell_line_id
     */
    public static function batchLoadSellingLineChildren($parent_sell_line_ids, $children_type = 'modifier')
    {
        if (empty($parent_sell_line_ids)) {
            return [];
        }

        $children = \App\TransactionSellLine::whereIn(
            'parent_sell_line_id',
            array_unique($parent_sell_line_ids)
        )
            ->where('children_type', $children_type)
            ->get();

        return $children->groupBy('parent_sell_line_id')->toArray();
    }

    /**
     * Cache dropdown results to avoid repeated queries
     * Dropdowns rarely change, so caching for 1 hour is safe
     *
     * @param string $cache_key
     * @param callable $callback Function that returns dropdown data
     * @param int $ttl_minutes Cache TTL in minutes (default: 60)
     * @return mixed Cached dropdown data
     */
    public static function cacheDropdown($cache_key, callable $callback, $ttl_minutes = 60)
    {
        return Cache::remember($cache_key, $ttl_minutes * 60, $callback);
    }

    /**
     * Clear dropdown cache (call after creating/updating relevant data)
     *
     * @param array $cache_keys Keys to clear
     */
    public static function clearDropdownCache($cache_keys = [])
    {
        foreach ($cache_keys as $key) {
            Cache::forget($key);
        }
    }

    /**
     * Get only required columns from a query to reduce memory usage
     * Useful when you don't need all columns
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $columns Column names to select
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function selectOptimized($query, $columns)
    {
        if (empty($columns)) {
            return $query;
        }
        return $query->select($columns);
    }

    /**
     * CRITICAL: Cache all dropdown queries
     * These queries rarely change but run on every page load
     * Caching eliminates hundreds of queries per day
     */
    public static function getDropdownData($type, $business_id, $extra_params = [])
    {
        $cache_key = "dropdown_{$type}_bid_{$business_id}";
        if (!empty($extra_params)) {
            $normalized_params = $extra_params;
            ksort($normalized_params);
            $cache_key .= '_' . md5(json_encode($normalized_params));
        }
        
        return Cache::remember($cache_key, 120 * 60, function () use ($type, $business_id, $extra_params) {
            switch($type) {
                case 'users':
                    return \App\User::forDropdown($business_id);
                case 'contacts':
                    $contact_type = $extra_params['contact_type'] ?? null;
                    return \App\Contact::forDropdown($business_id, $contact_type);
                case 'locations':
                    return \App\BusinessLocation::forDropdown($business_id, $extra_params['include_inactive'] ?? false);
                case 'units':
                    return \App\Unit::forDropdown($business_id, $extra_params['multi_unit'] ?? false);
                case 'categories':
                    $category_type = $extra_params['category_type'] ?? 'product';
                    return \App\Category::forDropdown($business_id, $category_type);
                case 'brands':
                    return \App\Brands::forDropdown($business_id);
                case 'tax_rates':
                    return \App\TaxRate::forDropdown($business_id);
                case 'price_groups':
                    return \App\SellingPriceGroup::forDropdown($business_id);
                case 'customer_groups':
                    return \App\CustomerGroup::forDropdown($business_id);
                case 'accounts':
                    return \App\Account::forDropdown($business_id, false);
                case 'expense_categories':
                    return \App\ExpenseCategory::forDropdown($business_id);
                default:
                    return [];
            }
        });
    }

    /**
     * Clear all dropdown caches when data changes
     * Call this after creating/updating/deleting contacts, users, locations, etc.
     */
    public static function clearDropdownCaches($business_id, $types = [])
    {
        if (empty($types)) {
            // Clear all dropdowns for this business
            $types = ['users', 'contacts', 'locations', 'units', 'categories', 'brands', 'tax_rates', 'price_groups', 'customer_groups', 'accounts', 'expense_categories'];
        }
        
        foreach ($types as $type) {
            Cache::forget("dropdown_{$type}_bid_{$business_id}");
        }
    }

    /**
     * Batch load transactions with all relations to avoid N+1 on reports
     */
    public static function batchLoadTransactions($transaction_ids, $relations = ['contact', 'location', 'sell_lines', 'payment_lines'])
    {
        if (empty($transaction_ids)) {
            return collect();
        }

        return \App\Transaction::whereIn('id', array_unique($transaction_ids))
            ->with($relations)
            ->get()
            ->keyBy('id');
    }

    /**
     * Batch load contacts with all relations
     */
    public static function batchLoadContacts($contact_ids, $relations = [])
    {
        if (empty($contact_ids)) {
            return collect();
        }

        return \App\Contact::whereIn('id', array_unique($contact_ids))
            ->with($relations)
            ->get()
            ->keyBy('id');
    }

    /**
     * Batch load categories (avoid N+1 when displaying product categories)
     */
    public static function batchLoadCategories($category_ids)
    {
        if (empty($category_ids)) {
            return collect();
        }

        return \App\Category::whereIn('id', array_unique($category_ids))
            ->get()
            ->keyBy('id');
    }

    /**
     * Optimized report data fetching - avoid complex loops
     * Loads transaction details with eager loading for reports
     */
    public static function getTransactionsForReport($business_id, $filters = [])
    {
        $query = \App\Transaction::where('business_id', $business_id)
            ->with(['contact', 'location', 'sell_lines.variation.product', 'payment_lines']);

        if (!empty($filters['start_date'])) {
            $query->whereDate('transaction_date', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->whereDate('transaction_date', '<=', $filters['end_date']);
        }
        if (!empty($filters['location_id'])) {
            $query->where('location_id', $filters['location_id']);
        }
        if (!empty($filters['contact_id'])) {
            $query->where('contact_id', $filters['contact_id']);
        }

        return $query->get();
    }

    /**
     * Optimize product listing queries - reduce column selection
     */
    public static function getProductsOptimized($business_id, $filters = [])
    {
        $query = \App\Product::where('business_id', $business_id)
            ->with(['media', 'variations.variation_location_details'])
            ->select(['id', 'name', 'sku', 'type', 'enable_stock', 'is_inactive', 'image', 'business_id']);

        if (!empty($filters['location_id'])) {
            $query->whereHas('product_locations', function ($q) use ($filters) {
                $q->where('location_id', $filters['location_id']);
            });
        }
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }
        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Cache business settings to avoid repeated database queries
     */
    public static function getCachedBusinessSettings($business_id)
    {
        return Cache::remember("business_settings_{$business_id}", 3600, function () use ($business_id) {
            return \App\Business::find($business_id);
        });
    }

    /**
     * Cache user permissions to avoid repeated permission checks
     */
    public static function getCachedUserPermissions($user_id)
    {
        return Cache::remember("user_permissions_{$user_id}", 1800, function () use ($user_id) {
            $user = \App\User::with(['roles.permissions'])->find($user_id);
            return $user ? $user->getAllPermissions()->pluck('name')->toArray() : [];
        });
    }

    /**
     * Cache frequently accessed business locations
     */
    public static function getCachedBusinessLocations($business_id)
    {
        return Cache::remember("business_locations_{$business_id}", 3600, function () use ($business_id) {
            return \App\BusinessLocation::where('business_id', $business_id)->get();
        });
    }

    /**
     * Clear all caches for a business (useful after major data changes)
     */
    public static function clearAllBusinessCaches($business_id)
    {
        $cache_keys = [
            "business_settings_{$business_id}",
            "business_locations_{$business_id}",
        ];

        // Add all dropdown caches
        $dropdown_types = ['users', 'contacts', 'locations', 'units', 'categories', 'brands', 'tax_rates', 'price_groups', 'customer_groups', 'accounts', 'expense_categories'];
        foreach ($dropdown_types as $type) {
            $cache_keys[] = "dropdown_{$type}_bid_{$business_id}";
        }

        foreach ($cache_keys as $key) {
            Cache::forget($key);
        }
    }
}
