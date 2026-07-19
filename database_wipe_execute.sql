-- =====================================================
-- DATABASE WIPE EXECUTION SCRIPT
-- Run this AFTER verifying with database_wipe_verification.sql
-- Generated: April 23, 2026
-- =====================================================

-- =====================================================
-- SAFETY CHECKS
-- =====================================================

-- Verify Yuvanshan user exists and will be kept
SELECT id, username, password, business_id FROM users WHERE username = 'Yuvanshan';

-- Verify other users that will be deleted
SELECT id, username, business_id FROM users WHERE username != 'Yuvanshan';

-- =====================================================
-- FINAL CONFIRMATION PROMPT
-- =====================================================

/*
BEFORE RUNNING THIS SCRIPT:

1. ✅ Backup your database using one of these methods:
   - mysqldump command
   - phpMyAdmin export
   - The backup script from BACKUPS/ folder

2. ✅ Run the verification script first:
   - Execute: database_wipe_verification.sql
   - Review the results carefully

3. ✅ Confirm you want to delete:
   - All users except Yuvanshan
   - All business data (transactions, products, contacts, etc.)
   - All settings and configurations
   - Keep only: User Yuvanshan + basic business structure

4. ⚠️  THIS ACTION CANNOT BE UNDONE ⚠️

ONLY PROCEED IF YOU ARE ABSOLUTELY SURE!
*/

-- =====================================================
-- EXECUTE WIPE (UNCOMMENT TO RUN)
-- =====================================================

/*
-- To execute the wipe, uncomment the line below and run this script:
-- SOURCE database_wipe_script.sql;
*/

-- =====================================================
-- POST-WIPE VERIFICATION
-- =====================================================

-- After running the wipe, verify these queries return expected results:

-- Should return 1 row (only Yuvanshan)
-- SELECT id, username, first_name, last_name FROM users;

-- Should return 1 row (only Mahdev business)
-- SELECT id, name FROM business;

-- Should return 0 rows (all transactions cleared)
-- SELECT COUNT(*) as transaction_count FROM transactions;

-- Should return 0 rows (all products cleared)
-- SELECT COUNT(*) as product_count FROM products;

-- =====================================================
-- POST-WIPE SETUP INSTRUCTIONS
-- =====================================================

/*
AFTER SUCCESSFUL WIPE:

1. Log in with:
   - Username: Yuvanshan
   - Password: Yuvan@1709

2. Reconfigure your business:
   - Set up business locations
   - Add products and inventory
   - Configure tax rates
   - Add customers and suppliers
   - Set up payment methods

3. The system will be like a fresh installation but with your user account preserved.

4. Consider setting up automated backups to prevent future data loss.
*/