<?php
/**
 * Script to update invoice layout branding to "Mahdev Pvt Ltd"
 * Run this from the Laravel environment to update the database
 */

// This script should be run in the context of the Laravel app
// You can execute it via Artisan: php artisan tinker < update_invoice_branding.php
// Or manually via database tools

// Option 1: Using Laravel Artisan (recommended)
// php artisan tinker
// Then paste this into tinker:

// Find the default invoice layout (id = 1)
$layout = DB::table('invoice_layouts')->where('id', 1)->first();

if ($layout) {
    // Update with new branding
    DB::table('invoice_layouts')
        ->where('id', 1)
        ->update([
            'header_text' => '<p style="font-weight: bold; margin: 0; padding: 0;">Mahdev Pvt Ltd</p>',
            'footer_text' => '<p style="font-weight: bold; margin: 0; padding: 0;">Thank You - Mahdev Pvt Ltd</p>'
        ]);
    
    echo "Invoice layout updated successfully!\n";
} else {
    echo "Default invoice layout not found.\n";
}

// Option 2: Direct SQL query
// Run this in your database client:
/*
UPDATE invoice_layouts 
SET header_text = '<p style="font-weight: bold; margin: 0; padding: 0;">Mahdev Pvt Ltd</p>',
    footer_text = '<p style="font-weight: bold; margin: 0; padding: 0;">Thank You - Mahdev Pvt Ltd</p>'
WHERE id = 1;
*/
