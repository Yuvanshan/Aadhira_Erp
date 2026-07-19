-- =====================================================
-- MANUAL COMPLETE WIPE SCRIPT
-- Delete in dependency order
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Delete transactions and related data first
DELETE FROM transaction_payments;
DELETE FROM transaction_sell_lines;
DELETE FROM transaction_sell_lines_variations;
DELETE FROM purchase_lines;
DELETE FROM purchase_lines_variations;
DELETE FROM transactions;

-- Delete products and variations
DELETE FROM variation_location_details;
DELETE FROM product_variations;
DELETE FROM products;

-- Delete inventory adjustments
DELETE FROM stock_adjustment_lines;
DELETE FROM stock_adjustments;

-- Delete contacts
DELETE FROM contacts;

-- Delete accounting
DELETE FROM account_transactions;
DELETE FROM accounts;
DELETE FROM account_types;

-- Delete categories, brands, units
DELETE FROM categories;
DELETE FROM brands;
DELETE FROM units;

-- Delete taxes
DELETE FROM group_sub_taxes;
DELETE FROM group_taxes;
DELETE FROM tax_rates;

-- Delete settings
DELETE FROM business_settings;
DELETE FROM user_settings;
DELETE FROM system_settings;

-- Delete invoice stuff
DELETE FROM invoice_schemes;
DELETE FROM invoice_layouts;

-- Delete barcodes
DELETE FROM barcodes;

-- Delete cash registers
DELETE FROM cash_registers;

-- Delete activity logs
DELETE FROM activity_log;

-- Delete notifications
DELETE FROM notifications;

-- Delete sessions
DELETE FROM sessions;

-- Delete businesses and locations
DELETE FROM business_locations;
DELETE FROM business;

-- Delete any remaining users (though should be empty)
DELETE FROM users;

-- Reset auto-increments
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE business AUTO_INCREMENT = 1;
ALTER TABLE transactions AUTO_INCREMENT = 1;
ALTER TABLE products AUTO_INCREMENT = 1;
ALTER TABLE contacts AUTO_INCREMENT = 1;
ALTER TABLE accounts AUTO_INCREMENT = 1;
ALTER TABLE categories AUTO_INCREMENT = 1;
ALTER TABLE brands AUTO_INCREMENT = 1;
ALTER TABLE units AUTO_INCREMENT = 1;
ALTER TABLE tax_rates AUTO_INCREMENT = 1;
ALTER TABLE activity_log AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Manual complete database wipe successful!' as status;