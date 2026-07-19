-- =====================================================
-- DATABASE WIPE VERIFICATION SCRIPT
-- Check what data will be deleted before running the wipe
-- Generated: April 23, 2026
-- =====================================================

-- =====================================================
-- VERIFICATION: CURRENT DATA SUMMARY
-- =====================================================

-- Count of users by business
SELECT
    u.business_id,
    b.name as business_name,
    COUNT(u.id) as user_count,
    GROUP_CONCAT(u.username) as usernames
FROM users u
LEFT JOIN business b ON u.business_id = b.id
GROUP BY u.business_id, b.name;

-- Count of transactions by business
SELECT
    business_id,
    COUNT(*) as transaction_count,
    SUM(final_total) as total_amount
FROM transactions
GROUP BY business_id;

-- Count of products by business
SELECT
    business_id,
    COUNT(*) as product_count
FROM products
GROUP BY business_id;

-- Count of contacts by business
SELECT
    business_id,
    type,
    COUNT(*) as contact_count
FROM contacts
GROUP BY business_id, type;

-- Count of activity logs by business
SELECT
    business_id,
    COUNT(*) as activity_count
FROM activity_log
GROUP BY business_id;

-- =====================================================
-- VERIFICATION: DATA TO BE KEPT
-- =====================================================

-- User that will be kept
SELECT
    id,
    username,
    first_name,
    last_name,
    email,
    business_id,
    status
FROM users
WHERE username = 'Yuvanshan';

-- Business that will be kept (but data cleared)
SELECT
    id,
    name,
    owner_id
FROM business
WHERE id = 1;

-- =====================================================
-- VERIFICATION: DATA TO BE DELETED
-- =====================================================

-- Users to be deleted
SELECT
    id,
    username,
    first_name,
    last_name,
    email,
    business_id
FROM users
WHERE username != 'Yuvanshan';

-- Business to be deleted completely
SELECT
    id,
    name,
    owner_id
FROM business
WHERE id != 1;

-- Sample of transactions to be deleted
SELECT
    business_id,
    type,
    COUNT(*) as count,
    SUM(final_total) as total_amount
FROM transactions
GROUP BY business_id, type
ORDER BY business_id;

-- =====================================================
-- VERIFICATION: SUMMARY COUNTS
-- =====================================================

-- Total counts before wipe
SELECT
    'Users' as table_name, COUNT(*) as total_count FROM users
UNION ALL
SELECT 'Business' as table_name, COUNT(*) as total_count FROM business
UNION ALL
SELECT 'Transactions' as table_name, COUNT(*) as total_count FROM transactions
UNION ALL
SELECT 'Products' as table_name, COUNT(*) as total_count FROM products
UNION ALL
SELECT 'Contacts' as table_name, COUNT(*) as total_count FROM contacts
UNION ALL
SELECT 'Activity Logs' as table_name, COUNT(*) as total_count FROM activity_log
UNION ALL
SELECT 'Accounts' as table_name, COUNT(*) as total_count FROM accounts
UNION ALL
SELECT 'Cash Registers' as table_name, COUNT(*) as total_count FROM cash_registers
UNION ALL
SELECT 'Brands' as table_name, COUNT(*) as total_count FROM brands
UNION ALL
SELECT 'Categories' as table_name, COUNT(*) as total_count FROM categories;

-- =====================================================
-- VERIFICATION COMPLETE
-- =====================================================

/*
EXPECTED RESULTS AFTER VERIFICATION:

1. Users by business should show:
   - Business 1 (Mahdev): 1 user (Yuvanshan)
   - Business 2 (Saravanan): 2 users (saravanan, saravanan1)

2. Data to be kept:
   - 1 user: Yuvanshan
   - 1 business: Mahdev (Pvt) Ltd

3. Data to be deleted:
   - 2 users: saravanan, saravanan1
   - 1 business: Saravanan Stores
   - All transactions, products, contacts, etc. for both businesses

4. Summary counts will show current data volume.

ONLY RUN THE WIPE SCRIPT IF THESE RESULTS LOOK CORRECT!
*/