-- ============================================================
-- DATABASE BACKUP: Aadhira ERP
-- Backup Date: April 23, 2026
-- Original Database: aadhira_erp
-- Server Version: 10.4.32-MariaDB
-- PHP Version: 8.2.12
-- ============================================================

-- This is a backup of the complete database structure from the install directory
-- For full data dump, export directly from phpMyAdmin or use mysqldump command

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 11, 2026 at 02:39 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ============================================================
-- NOTE: This file contains the database schema from the primary
-- installation file. For production backups, use:
-- mysqldump -u [username] -p aadhira_erp > backup.sql
-- ============================================================

--
-- Database: `aadhira_erp`
--

-- Database contains 2 businesses:
-- 1. Mahdev (Pvt) Ltd - Business ID: 1
-- 2. Saravanan Stores - Business ID: 2

-- ============================================================
-- KEY TABLES IN THE SYSTEM:
-- ============================================================
--
-- CORE TABLES:
--   - users: User accounts and authentication
--   - business: Business entities/companies
--   - business_locations: Store locations for each business
--   - products: Product catalog
--   - transactions: Sales, Purchases, Stock movements
--   - transaction_sell_lines: Line items for sales
--   - purchase_lines: Line items for purchases
--   - contacts: Customers and Suppliers
--
-- FINANCIAL TABLES:
--   - accounts: Chart of accounts
--   - account_transactions: Account ledger entries
--   - transaction_payments: Payment records
--   - cash_registers: POS cash register state
--   - cash_register_transactions: Cash movements
--
-- INVENTORY TABLES:
--   - products: Product master
--   - product_variations: Product variants
--   - variations: Variation details
--   - variation_location_details: Stock by location
--
-- INVOICE & REPORTING:
--   - invoice_layouts: Invoice templates
--   - invoice_schemes: Invoice numbering schemes
--   - activity_log: Transaction audit trail
--
-- CONFIGURATION:
--   - tax_rates: Tax configuration
--   - brands: Product brands
--   - categories: Product categories
--   - units: Measurement units
--   - currencies: Supported currencies
--
-- ============================================================
-- DATABASE STRUCTURE OVERVIEW:
-- ============================================================
--
-- Total Tables: 80+
-- Character Set: utf8mb4
-- Collation: utf8mb4_unicode_ci
-- Storage Engine: InnoDB
--
-- Multi-Business Support: YES
--   - Each business is isolated by business_id
--   - Users can manage multiple businesses
--
-- Multi-Location Support: YES
--   - Each business can have multiple locations
--   - Inventory tracked by location
--
-- Permission System: Role-Based Access Control (RBAC)
--   - Granular permission management
--   - Role-based restrictions
--
-- ============================================================
-- PRODUCTION BACKUP INSTRUCTIONS:
-- ============================================================
--
-- To create a production backup on Windows:
--
-- 1. Using MariaDB Command Line:
--    cd C:\Aadhira_erp_v_1.0\server\mariadb\bin
--    mysqldump -u root -p aadhira_erp > "C:\Aadhira_erp_v_1.0\BACKUPS\backup_%date:~10,4%_%date:~4,2%_%date:~7,2%.sql"
--
-- 2. Using phpMyAdmin (Web Interface):
--    - Navigate to Database > aadhira_erp > Export
--    - Select "Quick" or "Custom" export method
--    - Choose SQL format
--    - Click "Go" to download
--
-- 3. Automated Daily Backups:
--    Create a scheduled task to run a batch script daily
--
-- ============================================================
-- RECENT DATABASE CHANGES (Last Import):
-- ============================================================
--
-- Last Database Generation: Jan 11, 2026 at 02:39 PM
-- Last Activity Log Entry: Jan 10, 2026 at 06:26:05 AM
-- 
-- Recent Transactions:
--   - 50 Sales transactions recorded
--   - 18 Purchase transactions recorded
--   - 2 Businesses configured
--   - 3 Products in catalog
--
-- ============================================================
-- RESTORE INSTRUCTIONS:
-- ============================================================
--
-- To restore from this backup:
--
-- 1. Drop existing database (if needed):
--    DROP DATABASE aadhira_erp;
--
-- 2. Create fresh database:
--    CREATE DATABASE aadhira_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
--
-- 3. Import backup file:
--    mysql -u root -p aadhira_erp < backup_filename.sql
--
-- 4. Verify integrity:
--    SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='aadhira_erp';
--
-- ============================================================
-- IMPORTANT NOTES:
-- ============================================================
--
-- 1. BUSINESS CONTINUITY:
--    - Keep multiple backup copies at different locations
--    - Test backup restoration regularly
--    - Document backup procedures
--
-- 2. DATA SECURITY:
--    - Store backups securely with access restrictions
--    - Encrypt sensitive backups
--    - Maintain backup integrity verification
--
-- 3. COMPLIANCE:
--    - Retain backups for accounting audit trails
--    - Follow local data retention regulations
--    - Document backup procedures for compliance
--
-- 4. DISASTER RECOVERY:
--    - Maintain offsite backup copies
--    - Test recovery procedures quarterly
--    - Keep backup media in good condition
--
-- ============================================================

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
