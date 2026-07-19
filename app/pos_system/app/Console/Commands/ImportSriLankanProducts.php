<?php

namespace App\Console\Commands;

use App\Brands;
use App\BusinessLocation;
use App\Category;
use App\Product;
use App\TaxRate;
use App\Transaction;
use App\Unit;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Variation;
use App\VariationValueTemplate;
use DB;
use Excel;
use Illuminate\Console\Command;

class ImportSriLankanProducts extends Command
{
    /**
     * All Utils instance.
     */
    protected $productUtil;

    protected $moduleUtil;

    private $barcode_types;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:sri-lankan-products {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import Sri Lankan grocery products from CSV';

    /**
     * Create a new command instance.
     *
     * @param  ProductUtil  $productUtil
     * @param  ModuleUtil  $moduleUtil
     * @return void
     */
    public function __construct(ProductUtil $productUtil, ModuleUtil $moduleUtil)
    {
        parent::__construct();
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;

        //barcode types
        $this->barcode_types = $this->productUtil->barcode_types();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = $this->argument('file') ?? public_path('files/import_products_csv_template.csv');

        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return Command::FAILURE;
        }

        $this->info("Starting product import from: $file");

        try {
            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            $parsed_array = Excel::toArray([], $file);

            //Remove header row
            $imported_data = array_splice($parsed_array[0], 1);

            $business_id = 1;
            $user_id = 1;
            $default_profit_percent = 25.00;

            $formated_data = [];

            $is_valid = true;
            $error_msg = '';

            $total_rows = count($imported_data);

            $this->info("Processing $total_rows products...");

            $business_locations = BusinessLocation::where('business_id', $business_id)->get();

            // Preload all required data to avoid queries in loop
            $units = Unit::where('business_id', $business_id)->get();
            $tax_rates = TaxRate::where('business_id', $business_id)->get()->keyBy('name');
            $existing_brands = Brands::where('business_id', $business_id)->pluck('id', 'name');
            $existing_categories = Category::where('business_id', $business_id)->where('category_type', 'product')->get()->keyBy('name');
            $existing_skus = Product::where('business_id', $business_id)->pluck('id', 'sku')->toArray();
            $locations_by_name = $business_locations->keyBy('name');

            DB::beginTransaction();
            $imported = 0;
            $failed = 0;
            foreach ($imported_data as $key => $value) {

                //Check if any column is missing
                if (count($value) < 35) {
                    $this->error("Row " . ($key + 1) . ": Some columns are missing, found " . count($value));
                    $failed++;
                    continue;
                }

                $row_no = $key + 1;
                $product_array = [];
                $product_array['business_id'] = $business_id;
                $product_array['created_by'] = $user_id;

                //Add name
                $product_name = trim($value[0]);
                if (! empty($product_name)) {
                    $product_array['name'] = $product_name;
                } else {
                    $this->error("Row $row_no: Product name is empty");
                    $failed++;
                    continue;
                }

                //Add brand
                $brand_name = trim($value[1]);
                if (! empty($brand_name)) {
                    if (!isset($existing_brands[$brand_name])) {
                        $brand = Brands::create([
                            'name' => $brand_name,
                            'business_id' => $business_id,
                            'created_by' => $user_id
                        ]);
                        $existing_brands[$brand_name] = $brand->id;
                    }
                    $product_array['brand_id'] = $existing_brands[$brand_name];
                }

                //Add unit
                $unit_name = trim($value[2]);
                $unit = $units->where('short_name', $unit_name)->first();
                if (empty($unit)) {
                    $unit = $units->first();
                }
                $product_array['unit_id'] = $unit->id ?? 1;

                //Add category
                $category_name = trim($value[3]);
                if (! empty($category_name)) {
                    if (!isset($existing_categories[$category_name])) {
                        $category = Category::create([
                            'name' => $category_name,
                            'business_id' => $business_id,
                            'category_type' => 'product',
                            'created_by' => $user_id
                        ]);
                        $existing_categories[$category_name] = $category;
                    }
                    $product_array['category_id'] = $existing_categories[$category_name]->id;
                }

                //Sub category
                $sub_category_name = trim($value[4]);
                if (! empty($sub_category_name)) {
                    $sub_category = Category::where('name', $sub_category_name)
                        ->where('business_id', $business_id)
                        ->where('category_type', 'product')
                        ->first();
                    if (empty($sub_category)) {
                        $sub_category = Category::create([
                            'name' => $sub_category_name,
                            'business_id' => $business_id,
                            'category_type' => 'product',
                            'parent_id' => $product_array['category_id'] ?? null,
                            'created_by' => $user_id
                        ]);
                    }
                    $product_array['sub_category_id'] = $sub_category->id;
                }

                //SKU
                $sku = trim($value[5]);
                if (! empty($sku)) {
                    if (isset($existing_skus[$sku])) {
                        $this->warn("Row $row_no: SKU $sku already exists, skipping");
                        $failed++;
                        continue;
                    }
                    $product_array['sku'] = $sku;
                } else {
                    $product_array['sku'] = 'SKU-' . time() . '-' . rand(1000, 9999);
                }
                $existing_skus[$product_array['sku']] = 1;

                //Barcode type
                $barcode_type = trim($value[6]);
                if (! empty($barcode_type)) {
                    $product_array['barcode_type'] = $barcode_type;
                }

                //Manage stock
                $manage_stock = trim($value[7]);
                $product_array['enable_stock'] = !empty($manage_stock) ? $manage_stock : 1;

                //Alert quantity
                $alert_quantity = trim($value[8]);
                $product_array['alert_quantity'] = !empty($alert_quantity) ? $alert_quantity : 10;

                //Expires in
                $expires_in = trim($value[9]);
                if (! empty($expires_in)) {
                    $product_array['expires_in'] = $expires_in;
                }

                //Expiry period unit
                $expiry_period_unit = trim($value[10]);
                if (! empty($expiry_period_unit)) {
                    $product_array['expiry_period_unit'] = $expiry_period_unit;
                }

                //Applicable tax
                $tax_name = trim($value[11]);
                if (! empty($tax_name)) {
                    $tax = $tax_rates->get($tax_name);
                    if ($tax) {
                        $product_array['tax'] = $tax->id;
                        $product_array['tax_type'] = trim($value[12]) == 'inclusive' ? 'inclusive' : 'exclusive';
                    }
                }

                //Product type
                $product_type = trim($value[13]);
                $product_array['type'] = !empty($product_type) ? $product_type : 'single';

                //Variation name (skip for now)
                //Variation values (skip for now)

                //Purchase price including tax
                $purchase_price_inc = trim($value[17]);
                $purchase_price_exc = trim($value[18]);
                $profit_margin = trim($value[19]);
                $selling_price = trim($value[20]);

                //Opening stock
                $opening_stock = trim($value[21]);
                $location_name = trim($value[22]);

                //Create product
                $product = Product::create($product_array);

                //Create product variation
                $product_variation = \App\ProductVariation::create([
                    'name' => $product_name,
                    'product_id' => $product->id,
                    'is_dummy' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                //Create variation
                $variation_data = [
                    'name' => $product_name,
                    'product_id' => $product->id,
                    'sub_sku' => $product_array['sku'],
                    'product_variation_id' => $product_variation->id,
                    'default_purchase_price' => $purchase_price_exc ?: ($purchase_price_inc / 1.12),
                    'dpp_inc_tax' => $purchase_price_inc ?: ($purchase_price_exc * 1.12),
                    'profit_percent' => $profit_margin ?: $default_profit_percent,
                    'default_sell_price' => $selling_price ?: (($purchase_price_inc ?: ($purchase_price_exc * 1.12)) * (1 + ($profit_margin ?: $default_profit_percent) / 100)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $variation = Variation::create($variation_data);

                //Opening stock
                if (!empty($opening_stock)) {
                    $location = $locations_by_name->get($location_name) ?? $business_locations->first();
                    if ($location) {
                        \App\VariationLocationDetails::create([
                            'product_id' => $product->id,
                            'product_variation_id' => $product_variation->id,
                            'variation_id' => $variation->id,
                            'location_id' => $location->id,
                            'qty_available' => $opening_stock,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                $imported++;
                $this->info("Imported: $product_name");
            }

            DB::commit();
            $this->info("Import completed. Imported: $imported, Failed: $failed");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Import failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
