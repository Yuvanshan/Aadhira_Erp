-- =====================================================
-- COMPLETE DATABASE WIPE SCRIPT (Dynamic)
-- Generated: April 23, 2026
-- Purpose: Delete ALL data from ALL tables
-- =====================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Get all table names and truncate them
SET @tables = NULL;
SELECT GROUP_CONCAT('`', table_name, '`') INTO @tables
FROM information_schema.tables
WHERE table_schema = 'aadhira_erp' AND table_type = 'BASE TABLE';

-- Create truncate statements
SET @truncate_stmt = CONCAT('TRUNCATE TABLE ', @tables);

-- Execute the truncate
PREPARE stmt FROM @truncate_stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Reset auto-increments for all tables
SET @reset_auto = NULL;
SELECT GROUP_CONCAT('ALTER TABLE `', table_name, '` AUTO_INCREMENT = 1') INTO @reset_auto
FROM information_schema.tables
WHERE table_schema = 'aadhira_erp' AND table_type = 'BASE TABLE'
AND table_name IN (
    SELECT table_name FROM information_schema.columns
    WHERE table_schema = 'aadhira_erp' AND column_name = 'id' AND extra LIKE '%auto_increment%'
);

-- Execute auto-increment reset
PREPARE stmt FROM @reset_auto;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET FOREIGN_KEY_CHECKS = 1;

SELECT 'Complete database wipe successful!' as status;