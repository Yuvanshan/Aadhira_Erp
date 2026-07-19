-- =====================================================
-- COMPLETE DATABASE WIPE SCRIPT (Safe)
-- Generated: April 23, 2026
-- Purpose: Delete ALL data, ignore missing tables
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Truncate all possible tables (ignore if not exist)
TRUNCATE TABLE users;
TRUNCATE TABLE business;
TRUNCATE TABLE business_locations;
TRUNCATE TABLE transactions;
TRUNCATE TABLE transaction_payments;
TRUNCATE TABLE transaction_sell_lines;
TRUNCATE TABLE transaction_sell_lines_variations;
TRUNCATE TABLE purchase_lines;
TRUNCATE TABLE purchase_lines_variations;
TRUNCATE TABLE products;
TRUNCATE TABLE product_variations;
TRUNCATE TABLE variation_location_details;
TRUNCATE TABLE variation_templates;
TRUNCATE TABLE variation_value_templates;
TRUNCATE TABLE stock_adjustment_lines;
TRUNCATE TABLE stock_adjustments;
TRUNCATE TABLE contacts;
TRUNCATE TABLE accounts;
TRUNCATE TABLE account_transactions;
TRUNCATE TABLE account_types;
TRUNCATE TABLE categories;
TRUNCATE TABLE brands;
TRUNCATE TABLE units;
TRUNCATE TABLE tax_rates;
TRUNCATE TABLE group_sub_taxes;
TRUNCATE TABLE group_taxes;
TRUNCATE TABLE business_settings;
TRUNCATE TABLE system_settings;
TRUNCATE TABLE user_settings;
TRUNCATE TABLE invoice_schemes;
TRUNCATE TABLE invoice_layouts;
TRUNCATE TABLE barcodes;
TRUNCATE TABLE cash_registers;
TRUNCATE TABLE activity_log;
TRUNCATE TABLE notifications;
TRUNCATE TABLE sessions;
TRUNCATE TABLE password_resets;

-- Reset auto-increments where possible
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE business AUTO_INCREMENT = 1;
ALTER TABLE business_locations AUTO_INCREMENT = 1;
ALTER TABLE transactions AUTO_INCREMENT = 1;
ALTER TABLE transaction_payments AUTO_INCREMENT = 1;
ALTER TABLE transaction_sell_lines AUTO_INCREMENT = 1;
ALTER TABLE transaction_sell_lines_variations AUTO_INCREMENT = 1;
ALTER TABLE purchase_lines AUTO_INCREMENT = 1;
ALTER TABLE purchase_lines_variations AUTO_INCREMENT = 1;
ALTER TABLE products AUTO_INCREMENT = 1;
ALTER TABLE product_variations AUTO_INCREMENT = 1;
ALTER TABLE variation_location_details AUTO_INCREMENT = 1;
ALTER TABLE variation_templates AUTO_INCREMENT = 1;
ALTER TABLE variation_value_templates AUTO_INCREMENT = 1;
ALTER TABLE stock_adjustment_lines AUTO_INCREMENT = 1;
ALTER TABLE stock_adjustments AUTO_INCREMENT = 1;
ALTER TABLE contacts AUTO_INCREMENT = 1;
ALTER TABLE accounts AUTO_INCREMENT = 1;
ALTER TABLE account_transactions AUTO_INCREMENT = 1;
ALTER TABLE account_types AUTO_INCREMENT = 1;
ALTER TABLE categories AUTO_INCREMENT = 1;
ALTER TABLE brands AUTO_INCREMENT = 1;
ALTER TABLE units AUTO_INCREMENT = 1;
ALTER TABLE tax_rates AUTO_INCREMENT = 1;
ALTER TABLE group_sub_taxes AUTO_INCREMENT = 1;
ALTER TABLE group_taxes AUTO_INCREMENT = 1;
ALTER TABLE business_settings AUTO_INCREMENT = 1;
ALTER TABLE system_settings AUTO_INCREMENT = 1;
ALTER TABLE user_settings AUTO_INCREMENT = 1;
ALTER TABLE invoice_schemes AUTO_INCREMENT = 1;
ALTER TABLE invoice_layouts AUTO_INCREMENT = 1;
ALTER TABLE barcodes AUTO_INCREMENT = 1;
ALTER TABLE cash_registers AUTO_INCREMENT = 1;
ALTER TABLE activity_log AUTO_INCREMENT = 1;
ALTER TABLE notifications AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Complete database wipe completed!' as status;