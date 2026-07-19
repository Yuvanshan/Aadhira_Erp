<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Product;
use App\Unit;
use App\Category;
use App\Variation;
use App\ProductVariation;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ImportGroceryProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:grocery-products {file?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import grocery products from Excel file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = $this->argument('file') ?? storage_path('../SL_Grocery_Products_ERP_Import.xlsx');

        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1;
        }

        $this->info("Starting product import from: $file");

        try {
            // Read the Excel file
            $rows = Excel::toArray([], $file);
            $data = $rows[0] ?? [];

            if (empty($data)) {
                $this->error("No data found in Excel file");
                return 1;
            }

            // Skip header row
            $dataRows = array_slice($data, 1);
            
            $imported = 0;
            $failed = 0;
            
            // Get default values
            $businessId = 1;
            $createdBy = 1;
            $unit = Unit::first() ?? Unit::create(['name' => 'Piece', 'short_name' => 'pc']);
            $category = Category::where('slug', 'food-grocery')->orWhere('id', 21)->first() 
                ?? Category::where('business_id', $businessId)->first();

            foreach ($dataRows as $row) {
                try {
                    // Skip empty rows
                    if (empty($row[0])) {
                        continue;
                    }

                    $productName = $row[0] ?? 'Unknown Product';
                    $sku = !empty($row[1]) ? $row[1] : 'SKU-' . time() . '-' . rand(1000, 9999);
                    $purchasePrice = (float)($row[2] ?? 0);
                    $sellingPrice = (float)($row[3] ?? 0);
                    $openingStock = (int)($row[4] ?? 0);
                    $reorderLevel = (int)($row[5] ?? 10);
                    $tax = !empty($row[6]) ? (int)$row[6] : null;

                    // Check if product already exists
                    $existing = Product::where('sku', $sku)
                        ->where('business_id', $businessId)
                        ->first();

                    if ($existing) {
                        $this->line("Skipping existing product: $productName (SKU: $sku)");
                        continue;
                    }

                    // Create product
                    $product = Product::create([
                        'name' => $productName,
                        'business_id' => $businessId,
                        'type' => 'single',
                        'unit_id' => $unit->id,
                        'category_id' => $category?->id,
                        'sku' => $sku,
                        'barcode_type' => 'C128',
                        'tax' => $tax,
                        'tax_type' => 'exclusive',
                        'enable_stock' => 1,
                        'alert_quantity' => $reorderLevel,
                        'created_by' => $createdBy,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Create product variation
                    $productVariation = ProductVariation::create([
                        'product_id' => $product->id,
                        'name' => 'Default',
                        'created_by' => $createdBy,
                    ]);

                    // Create variation with prices
                    Variation::create([
                        'product_variation_id' => $productVariation->id,
                        'name' => 'Default',
                        'sub_sku' => $sku,
                        'default_purchase_price' => $purchasePrice,
                        'dpp_inc_tax' => $purchasePrice,
                        'default_sell_price' => $sellingPrice,
                        'sell_price_inc_tax' => $sellingPrice,
                        'profit_percent' => $this->calculateProfitPercent($purchasePrice, $sellingPrice),
                        'created_by' => $createdBy,
                    ]);

                    $imported++;
                    $this->line("✓ Imported: $productName");

                } catch (\Exception $e) {
                    $failed++;
                    $this->error("✗ Failed to import row: " . $e->getMessage());
                }
            }

            $this->info("\n====== Import Complete ======");
            $this->info("Successfully imported: $imported products");
            $this->error("Failed: $failed products");

            return 0;

        } catch (\Exception $e) {
            $this->error("Import failed: " . $e->getMessage());
            return 1;
        }
    }

    /**
     * Calculate profit percent
     */
    private function calculateProfitPercent($purchasePrice, $sellingPrice)
    {
        if ($purchasePrice == 0) {
            return 0;
        }
        return round((($sellingPrice - $purchasePrice) / $purchasePrice) * 100, 2);
    }
}
