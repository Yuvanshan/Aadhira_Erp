DELIMITER //

CREATE PROCEDURE wipe_all_tables()
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE table_name VARCHAR(255);
    DECLARE cur CURSOR FOR
        SELECT TABLE_NAME
        FROM information_schema.TABLES
        WHERE TABLE_SCHEMA = 'aadhira_erp' AND TABLE_TYPE = 'BASE TABLE';
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

    SET FOREIGN_KEY_CHECKS = 0;

    OPEN cur;

    read_loop: LOOP
        FETCH cur INTO table_name;
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Truncate the table
        SET @sql = CONCAT('TRUNCATE TABLE `', table_name, '`');
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;

        -- Reset auto-increment if the table has an id column with auto_increment
        SET @check_auto = CONCAT('SELECT COUNT(*) INTO @has_auto FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ''aadhira_erp'' AND TABLE_NAME = ''', table_name, ''' AND COLUMN_NAME = ''id'' AND EXTRA LIKE ''%auto_increment%''');
        PREPARE stmt FROM @check_auto;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;

        IF @has_auto > 0 THEN
            SET @reset_sql = CONCAT('ALTER TABLE `', table_name, '` AUTO_INCREMENT = 1');
            PREPARE stmt FROM @reset_sql;
            EXECUTE stmt;
            DEALLOCATE PREPARE stmt;
        END IF;

    END LOOP;

    CLOSE cur;

    SET FOREIGN_KEY_CHECKS = 1;

END //

DELIMITER ;

-- Execute the procedure
CALL wipe_all_tables();

-- Drop the procedure
DROP PROCEDURE wipe_all_tables;

SELECT 'Complete database wipe successful!' as status;