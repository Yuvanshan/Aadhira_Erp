<?php
/**
 * Quick Fix Script - Updates Invoice Branding and Clears Caches
 * Run this from: php fix_invoice_branding.php
 * Or in: php artisan tinker < fix_invoice_branding.php
 */

// This script updates the invoice layout with professional branding

// Method 1: Using Laravel if available
if (file_exists(__DIR__ . '/app/pos_system/bootstrap/app.php')) {
    $app = require __DIR__ . '/app/pos_system/bootstrap/app.php';
    
    try {
        // Update invoice layout
        \DB::table('invoice_layouts')->where('id', 1)->update([
            'header_text' => 'Mahdev Pvt Ltd',
            'footer_text' => 'Thank You - Mahdev Pvt Ltd'
        ]);
        
        echo "✅ Database updated successfully!\n";
        echo "   Header: Mahdev Pvt Ltd\n";
        echo "   Footer: Thank You - Mahdev Pvt Ltd\n\n";
        
        // Clear caches
        \Artisan::call('cache:clear');
        echo "✅ Cache cleared!\n";
        
        \Artisan::call('config:cache');
        echo "✅ Config cached!\n\n";
        
        // Show success
        echo "🎉 All updates complete! Restart your app to see changes.\n";
        
    } catch (Exception $e) {
        echo "❌ Error: " . $e->getMessage() . "\n";
    }
}

// Method 2: Direct database update (if Laravel not available)
else {
    echo "Laravel environment not found. Using direct database connection...\n";
    
    // Get database credentials from config file
    $configFile = __DIR__ . '/app/pos_system/config/database.php';
    if (file_exists($configFile)) {
        // Read config and update database directly
        echo "Please run this SQL instead:\n\n";
        echo "UPDATE invoice_layouts \n";
        echo "SET header_text = 'Mahdev Pvt Ltd',\n";
        echo "    footer_text = 'Thank You -  Pvt Ltd'\n";
        echo "WHERE id = 1;\n";
    }
}
