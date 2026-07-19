-- =====================================================
-- MINIMAL COMPLETE WIPE SCRIPT
-- Only delete from tables that exist
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Delete in dependency order
DELETE FROM transaction_payments;
DELETE FROM transaction_sell_lines;
DELETE FROM transactions;

DELETE FROM product_variations;
DELETE FROM products;

DELETE FROM contacts;

DELETE FROM account_transactions;
DELETE FROM accounts;

DELETE FROM categories;
DELETE FROM brands;
DELETE FROM units;

DELETE FROM tax_rates;

DELETE FROM business_settings;

DELETE FROM invoice_schemes;

DELETE FROM barcodes;

DELETE FROM cash_registers;

DELETE FROM activity_log;

DELETE FROM notifications;

DELETE FROM business_locations;
DELETE FROM business;

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

SELECT 'Minimal complete database wipe successful!' as status;