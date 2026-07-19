-- =====================================================
-- SALES DATA WIPE SCRIPT
-- Wipe all sales transactions, invoices, and related data
-- Keep users, businesses, settings, and other important data
-- Generated: May 5, 2026
-- =====================================================

-- =====================================================
-- SAFETY BACKUP REMINDER
-- =====================================================

/*
IMPORTANT: BACKUP YOUR DATABASE BEFORE RUNNING THIS SCRIPT!

This script will permanently delete:
- All sales transactions (type = 'sell')
- All transaction sell lines
- All transaction payments related to sales
- All invoices and related documents
- Stock adjustments (if related to sales)
- Any other sales-related data

It will KEEP:
- Users and their login details
- Business information
- System settings and configurations
- Products, categories, brands, etc.
- Purchase data
- Other non-sales data

Run this only if you are sure about the deletion.
*/

-- =====================================================
-- START WIPE PROCESS
-- =====================================================

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Delete transaction payments for sales transactions
DELETE tp FROM transaction_payments tp
INNER JOIN transactions t ON tp.transaction_id = t.id
WHERE t.type = 'sell';

-- Delete transaction sell lines
DELETE tsl FROM transaction_sell_lines tsl
INNER JOIN transactions t ON tsl.transaction_id = t.id
WHERE t.type = 'sell';

-- Delete transaction sell lines purchase lines relationships
DELETE tslpl FROM transaction_sell_lines_purchase_lines tslpl
INNER JOIN transactions t ON tslpl.sell_line_id IN (
    SELECT tsl2.id FROM transaction_sell_lines tsl2
    INNER JOIN transactions t2 ON tsl2.transaction_id = t2.id
    WHERE t2.type = 'sell'
);

-- Delete sell line warranties
DELETE slw FROM sell_line_warranties slw
INNER JOIN transaction_sell_lines tsl ON slw.sell_line_id = tsl.id
INNER JOIN transactions t ON tsl.transaction_id = t.id
WHERE t.type = 'sell';

-- Delete document and notes for sales transactions
DELETE dn FROM document_and_notes dn
WHERE dn.notable_type = 'App\\Transaction'
AND dn.notable_id IN (
    SELECT id FROM transactions WHERE type = 'sell'
);

-- Delete activity log for sales transactions
DELETE al FROM activity_log al
WHERE al.subject_type = 'App\\Transaction' AND al.subject_id IN (
    SELECT id FROM transactions WHERE type = 'sell'
);

-- Delete notifications for sales transactions
DELETE n FROM notifications n
WHERE n.data LIKE '%transaction_id%' AND JSON_EXTRACT(n.data, '$.transaction_id') IN (
    SELECT id FROM transactions WHERE type = 'sell'
);

-- Delete stock adjustments (assuming they are sales-related)
-- Note: This might be too broad, adjust if needed
DELETE FROM stock_adjustment_lines;
DELETE FROM stock_adjustments_temp;

-- Delete cash register transactions for sales
DELETE crt FROM cash_register_transactions crt
INNER JOIN transactions t ON crt.transaction_id = t.id
WHERE t.type = 'sell';

-- Delete account transactions for sales
DELETE at FROM account_transactions at
INNER JOIN transactions t ON at.transaction_id = t.id
WHERE t.type = 'sell';

-- Finally, delete the sales transactions themselves
DELETE FROM transactions WHERE type = 'sell';

-- Delete any orphaned records (if any)
-- This is optional and depends on your data integrity

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- VERIFICATION
-- =====================================================

-- Check remaining transactions (should only have purchases, etc.)
SELECT type, COUNT(*) as count FROM transactions GROUP BY type;

-- Check if any sales data remains
SELECT COUNT(*) as remaining_sales FROM transactions WHERE type = 'sell';

-- =====================================================
-- END OF WIPE SCRIPT
-- =====================================================

/*
After running this script:
1. Clear any caches: php artisan cache:clear
2. Clear config: php artisan config:clear
3. Optimize: php artisan optimize:clear
4. Restart the application
*/