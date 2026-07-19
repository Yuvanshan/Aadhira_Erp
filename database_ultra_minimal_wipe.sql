-- =====================================================
-- ULTRA MINIMAL WIPE SCRIPT
-- Only delete from confirmed existing tables
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Delete transactions first
DELETE FROM transactions;

-- Delete products
DELETE FROM products;

-- Delete contacts
DELETE FROM contacts;

-- Delete activity logs
DELETE FROM activity_log;

-- Delete cash registers
DELETE FROM cash_registers;

-- Delete brands
DELETE FROM brands;

-- Delete businesses last
DELETE FROM business;

-- Delete users (should be empty already)
DELETE FROM users;

-- Reset auto-increments
ALTER TABLE users AUTO_INCREMENT = 1;
ALTER TABLE business AUTO_INCREMENT = 1;
ALTER TABLE transactions AUTO_INCREMENT = 1;
ALTER TABLE products AUTO_INCREMENT = 1;
ALTER TABLE contacts AUTO_INCREMENT = 1;
ALTER TABLE activity_log AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Ultra minimal database wipe successful!' as status;