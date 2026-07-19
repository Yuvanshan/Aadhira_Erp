-- =====================================================
-- COMPLETE DATABASE WIPE SCRIPT
-- Generated: April 23, 2026
-- Purpose: Delete ALL data, keep only schema
-- =====================================================

-- =====================================================
-- SAFETY WARNING
-- =====================================================

/*
THIS SCRIPT WILL PERMANENTLY DELETE ALL DATA FROM THE DATABASE!
- All users
- All businesses
- All transactions
- All products
- All contacts
- All settings
- Everything

ONLY the table structures will remain.

BACKUP YOUR DATABASE BEFORE RUNNING THIS!
*/

-- =====================================================
-- STEP 1: DISABLE FOREIGN KEY CHECKS
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================
-- STEP 2: TRUNCATE ALL TABLES (Safe order)
-- =====================================================

-- User and authentication related
TRUNCATE TABLE users;
TRUNCATE TABLE password_resets;
TRUNCATE TABLE personal_access_tokens;

-- Business related
TRUNCATE TABLE business;
TRUNCATE TABLE business_locations;

-- Transaction related
TRUNCATE TABLE transactions;
TRUNCATE TABLE transaction_payments;
TRUNCATE TABLE transaction_sell_lines;
TRUNCATE TABLE transaction_sell_lines_variations;
TRUNCATE TABLE purchase_lines;
TRUNCATE TABLE purchase_lines_variations;

-- Product related
TRUNCATE TABLE products;
TRUNCATE TABLE product_variations;
TRUNCATE TABLE variation_location_details;
TRUNCATE TABLE variation_templates;
TRUNCATE TABLE variation_value_templates;

-- Inventory and stock
TRUNCATE TABLE stock_adjustment_lines;
TRUNCATE TABLE stock_adjustments;

-- Contact related
TRUNCATE TABLE contacts;

-- Accounting
TRUNCATE TABLE accounts;
TRUNCATE TABLE account_transactions;
TRUNCATE TABLE account_types;

-- Categories and brands
TRUNCATE TABLE categories;
TRUNCATE TABLE brands;
TRUNCATE TABLE units;

-- Taxes
TRUNCATE TABLE tax_rates;
TRUNCATE TABLE group_sub_taxes;
TRUNCATE TABLE group_taxes;

-- Settings and configurations
TRUNCATE TABLE business_settings;
TRUNCATE TABLE system_settings;
TRUNCATE TABLE user_settings;

-- Invoice and printing
TRUNCATE TABLE invoice_schemes;
TRUNCATE TABLE invoice_layouts;

-- Barcodes
TRUNCATE TABLE barcodes;

-- Cash registers
TRUNCATE TABLE cash_registers;

-- Activity logs
TRUNCATE TABLE activity_log;

-- Notifications
TRUNCATE TABLE notifications;

-- Sessions
TRUNCATE TABLE sessions;

-- Job related (if exist)
-- TRUNCATE TABLE failed_jobs;
-- TRUNCATE TABLE job_batches;

-- =====================================================
-- STEP 3: RESET AUTO-INCREMENT VALUES
-- =====================================================

-- Reset auto-increment for all tables
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

-- =====================================================
-- STEP 4: RE-ENABLE FOREIGN KEY CHECKS
-- =====================================================

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- WIPE COMPLETE
-- =====================================================

SELECT 'Database wipe completed successfully!' as status;