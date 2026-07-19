# Aadhira ERP - Code Analysis Report
**Generated:** April 23, 2026

---

## 1. PROJECT OVERVIEW

### System Information
- **Application Name:** Aadhira ERP v1.8.2 (Mahdev Pvt Ltd)
- **Framework:** Laravel 9.51
- **PHP Version:** 8.0+
- **Server:** Apache + MariaDB (10.4.32)
- **Database:** aadhira_erp (Multi-business, Multi-location)
- **License:** MIT

### Technology Stack
```
Frontend:
  - Laravel Blade Templates
  - Bootstrap/Custom CSS
  - jQuery/JavaScript
  - DataTables (yajra/laravel-datatables-oracle v9.19)

Backend:
  - Laravel Framework (9.51)
  - PHP 8.0+
  - Laravel Passport (OAuth2)
  - Laravel Permissions (Spatie)

Database:
  - MariaDB 10.4.32
  - InnoDB Engine
  - UTF8MB4 Encoding

Key Packages:
  - maatwebsite/excel (v3.1.8) - Excel import/export
  - barryvdh/laravel-dompdf (v2.0) - PDF generation
  - mpdf/mpdf (v8.1) - Alternative PDF
  - laravel/backup (v8.0) - Backup functionality
  - spatie/laravel-permission (v5.5) - RBAC
  - nwidart/laravel-modules (v9.0) - Module system
  - guzzlehttp/guzzle (v7.2) - HTTP client
  - automattic/woocommerce (v3.0) - WooCommerce API
```

---

## 2. PROJECT STRUCTURE

```
Aadhira_erp_v_1.0/
│
├── app/pos_system/                    # Laravel Application Root
│   ├── app/                           # Application Logic
│   │   ├── Http/                      # Controllers, Requests, Middleware
│   │   ├── Models/                    # Eloquent Models (48 models)
│   │   ├── Events/                    # Event Listeners
│   │   ├── Exports/                   # Export Classes
│   │   ├── Console/                   # Artisan Commands
│   │   └── Providers/                 # Service Providers
│   │
│   ├── Modules/                       # Modular Feature Separation
│   │   ├── Accounting/                # Financial Management
│   │   ├── AiAssistance/              # AI Integration
│   │   ├── AssetManagement/           # Fixed Assets
│   │   ├── Cms/                       # Content Management
│   │   ├── Connector/                 # Third-party APIs
│   │   ├── Crm/                       # Customer Relations
│   │   ├── Essentials/                # Core Features
│   │   ├── Manufacturing/             # Production Management
│   │   ├── Project/                   # Project Tracking
│   │   ├── Repair/                    # Warranty/Repairs
│   │   ├── Superadmin/                # Admin Panel
│   │   ├── Woocommerce/               # WooCommerce Integration
│   │   └── Others...                  # Additional Modules
│   │
│   ├── database/                      # Database Files
│   │   ├── migrations/                # Schema Migrations
│   │   ├── seeders/                   # Initial Data
│   │   └── factories/                 # Model Factories
│   │
│   ├── resources/                     # Frontend Assets
│   │   ├── views/                     # Blade Templates
│   │   ├── css/                       # Stylesheets
│   │   └── js/                        # JavaScript Files
│   │
│   ├── routes/                        # Route Definitions
│   ├── config/                        # Configuration Files
│   ├── bootstrap/                     # Bootstrap Configuration
│   ├── storage/                       # File Storage (uploads, logs)
│   ├── public/                        # Web Root
│   ├── vendor/                        # Composer Dependencies
│   ├── composer.json                  # PHP Dependencies
│   ├── phpunit.xml                    # Testing Configuration
│   └── artisan                        # Artisan CLI
│
├── server/                            # Embedded Server
│   ├── apache/                        # Apache Web Server
│   ├── php/                           # PHP Interpreter
│   └── mariadb/                       # Database Server
│
├── electron/                          # Desktop Application
│   ├── main.js                        # Main Process
│   ├── preload.js                     # Preload Scripts
│   ├── start-server.bat               # Server Startup Script
│   └── package.json                   # Electron Dependencies
│
├── build/                             # Application Build Output
│   └── win-unpacked/                  # Windows Executable
│
├── install/                           # Installation Files
│   ├── aadhira_erp.sql                # Database Schema (MAIN BACKUP)
│   └── update_branding.sql            # Branding Updates
│
└── BACKUPS/                           # Backup Directory (Created)
    └── aadhira_erp_backup_2026_04_23.sql  # Database Backup with Notes
```

---

## 3. DATABASE STRUCTURE

### Core Tables (80+)

#### Users & Authorization
- `users` - User accounts (ID, name, email, business_id, etc.)
- `roles` - User roles (Admin, Manager, Cashier, etc.)
- `permissions` - Granular action permissions
- `model_has_roles` - User-Role mapping
- `model_has_permissions` - Direct permissions

#### Business & Organization
- `business` - Business entities
  - `id` INT, `name` VARCHAR, `currency_id` INT
  - `owner_id`, `time_zone`, `logo`, `enabled_modules`
  - Settings: tax, profit %, pricing, POS config
  - **Count:** 2 businesses (Mahdev, Saravanan Stores)

- `business_locations` - Store/Branch locations
  - `id`, `business_id`, `name`, `city`, `state`, `country`
  - `invoice_scheme_id`, `invoice_layout_id`
  - **Count:** 2 locations total

- `currencies` - Supported currencies
  - **Count:** 141 currencies (USD, LKR, EUR, INR, etc.)

#### Products & Inventory
- `products` - Product master
  - `id`, `name`, `sku`, `barcode_type`, `enable_stock`
  - `brand_id`, `category_id`, `unit_id`
  - `type` ENUM (single, variable, modifier, combo)
  - **Count:** 3 products

- `product_variations` - Product variants
  - `id`, `product_id`, `variation_template_id`

- `variations` - Variation details
  - `id`, `name`, `product_id`

- `variation_location_details` - Stock by location
  - `id`, `variation_id`, `location_id`, `qty_on_hand`

- `brands` - Product brands
  - **Count:** 1 brand (Wireman - Bulb)

- `categories` - Product categories

- `units` - Measurement units

#### Financial & Transactions
- `transactions` - All transaction types
  - `id`, `type` ENUM (purchase, sell, stock_transfer, etc.)
  - `status`, `payment_status`, `business_id`, `location_id`
  - `date`, `total_before_tax`, `final_total`
  - **Count:** 50+ sales, 18 purchases recorded

- `transaction_sell_lines` - Sales line items
  - `id`, `transaction_id`, `product_id`, `quantity`
  - `unit_price`, `discount`, `tax`

- `purchase_lines` - Purchase line items
  - `id`, `transaction_id`, `product_id`, `quantity`
  - `purchase_price`, `tax_id`, `lot_number`

- `transaction_payments` - Payment records
  - `id`, `transaction_id`, `method` (cash, card, cheque, bank_transfer)
  - `amount`, `paid_on`, `account_id`

- `accounts` - Chart of accounts
  - Hierarchical account structure

- `account_transactions` - Account ledger
  - `id`, `account_id`, `type` (debit/credit)

#### POS & Cash Management
- `cash_registers` - POS registers
  - `id`, `business_id`, `user_id`, `status` (open/close)
  - **Count:** 4 registers

- `cash_register_transactions` - Cash movements
  - **Count:** 48 transactions

- `cash_denominations` - Cash breakdown

#### Customers & Contacts
- `contacts` - Customers and Suppliers
  - `id`, `business_id`, `type` (customer/supplier)
  - `name`, `mobile`, `email`, `address`
  - `balance`, `total_rp` (reward points)
  - **Count:** 2 walk-in customers

#### Invoice & Reporting
- `invoice_layouts` - Invoice templates
  - Header/footer text, field visibility settings
  - **Count:** 2 layouts configured

- `invoice_schemes` - Invoice numbering
  - **Count:** 2 schemes

- `activity_log` - Transaction audit trail
  - **Count:** 65+ activity entries

#### Miscellaneous
- `permissions` - Role permissions (132 permissions)
- `notification_templates` - Email/SMS templates
- `media` - File uploads and attachments
- `discounts` - Promotional discounts
- `customer_groups` - Customer segmentation
- `bookings` - Restaurant reservations
- `tax_rates` - Tax configuration
- `printers` - Network printer definitions

### Database Statistics

| Metric | Value |
|--------|-------|
| Total Tables | 80+ |
| Character Set | utf8mb4 |
| Collation | utf8mb4_unicode_ci |
| Engine | InnoDB |
| Businesses | 2 |
| Users | 3 |
| Products | 3 |
| Transactions | 50+ |
| Permissions | 132 |

---

## 4. KEY APPLICATION MODELS (Eloquent)

### Core Models (app/Models/)
```
User                    - Authentication & User Management
Business                - Business entity
BusinessLocation        - Store/Branch locations
Product                 - Product master
ProductVariation        - Product variants
Variation               - Variation details
Transaction             - Sales/Purchase/Stock transactions
TransactionPayment      - Payment records
TransactionSellLine     - Sales line items
PurchaseLine            - Purchase line items
Contact                 - Customers & Suppliers
Account                 - Chart of accounts
AccountTransaction      - General ledger
CashRegister            - POS register
CashRegisterTransaction - Cash movements
InvoiceLayout           - Invoice templates
InvoiceScheme           - Invoice numbering
Brand                   - Product brands
Category                - Product categories
Unit                    - Measurement units
Currency                - Currency definitions
TaxRate                 - Tax configuration
Discount                - Promotional discounts
ActivityLog             - Audit trail
```

---

## 5. KEY FEATURES & FUNCTIONALITY

### Sales Module
- ✅ Point of Sale (POS)
- ✅ Online Sales Orders
- ✅ Sales Returns/Credit Notes
- ✅ Invoice Generation
- ✅ Quotations & Drafts
- ✅ Payment Management (Cash, Card, Cheque, Bank Transfer)
- ✅ Customer Tracking & Balances
- ✅ Reward Points System

### Purchase Module
- ✅ Purchase Orders
- ✅ Purchase Receipts
- ✅ Purchase Returns
- ✅ Supplier Management
- ✅ Payment Terms
- ✅ Multi-currency Support

### Inventory Management
- ✅ Stock Tracking by Location
- ✅ Stock Transfers Between Locations
- ✅ Stock Adjustments
- ✅ Product Variations
- ✅ Barcode Management
- ✅ Expiry Date Tracking
- ✅ Lot Number Management

### Financial Management
- ✅ Chart of Accounts
- ✅ General Ledger
- ✅ Transaction Payments
- ✅ Cash Register Management
- ✅ Account Reconciliation
- ✅ Financial Reports

### Reporting
- ✅ Profit & Loss Report
- ✅ Stock Report
- ✅ Sales Report
- ✅ Purchase Report
- ✅ Tax Report
- ✅ Contacts Report
- ✅ Register Report
- ✅ Expense Report

### Administrative
- ✅ Multi-Business Support
- ✅ Multi-Location Support
- ✅ Role-Based Access Control (132 permissions)
- ✅ Activity Logging
- ✅ User Management
- ✅ Email/SMS Notifications
- ✅ Custom Settings

### Integration
- ✅ WooCommerce Integration
- ✅ OpenAI Integration (AI Assistance)
- ✅ Payment Gateway Integration (Razorpay, Stripe, PayPal)
- ✅ SMS Services (Twilio, Nexmo)
- ✅ Third-party API Connectors

---

## 6. CURRENT DATA SNAPSHOT

### Businesses
```
ID 1: Mahdev (Pvt) Ltd
  - Currency: LKR (₨)
  - Location: Trincomalee, Sri Lanka
  - Owner: User ID 1
  - Status: Active
  - Last Activity: 2025-12-20

ID 2: Saravanan Stores
  - Currency: LKR (₨)
  - Location: Trincomalee, Sri Lanka
  - Owner: User ID 2
  - Status: Active
  - Last Activity: 2026-01-10
```

### Users
```
ID 1: Admin (System Admin)
  - Role: Super Admin
  - Business: Mahdev
  - Status: Active

ID 2: Store Manager
  - Role: Store Manager
  - Business: Saravanan Stores
  - Status: Active

ID 3: Sales User (Saravanan)
  - Role: Cashier
  - Business: Saravanan Stores
  - Status: Active
```

### Recent Transactions
- **Total Sales:** 50+ transactions
- **Total Purchases:** 18 transactions
- **Total Revenue:** ~LKR 200,000+
- **Last Sale:** 2026-01-10 06:26 AM

---

## 7. CONFIGURATION & SETTINGS

### Enabled Modules
- Purchases
- Sales (POS)
- Stock Transfers
- Stock Adjustments
- Expenses
- Accounting

### Business Settings
- Default Profit Margin: 25%
- Accounting Method: FIFO (First In, First Out)
- Stock Expiry Alerts: 30 days before expiry
- Transaction Edit Window: 30 days
- Date Format: MM/DD/YYYY
- Time Format: 24-hour

### Invoice Settings
- Multiple layouts configured
- Custom header/footer text
- Logo display enabled
- Business name display
- Location details display
- Payment information

---

## 8. BACKUP STRATEGY

### Current Backups
- **Primary:** `install/aadhira_erp.sql` (Generated: Jan 11, 2026)
- **Update File:** `install/update_branding.sql` (Latest updates)
- **New Backup:** `BACKUPS/aadhira_erp_backup_2026_04_23.sql` (Created today)

### Backup Recommendations

#### Daily Automated Backup
```batch
@echo off
cd C:\Aadhira_erp_v_1.0\server\mariadb\bin
for /f "tokens=2-4 delims=/ " %%a in ('date /t') do (set mydate=%%c%%a%%b)
for /f "tokens=1-2 delims=/:" %%a in ('time /t') do (set mytime=%%a%%b)
mysqldump -u root -p aadhira_erp > "C:\Aadhira_erp_v_1.0\BACKUPS\backup_%mydate%_%mytime%.sql"
```

#### Backup Frequency
- **Daily:** At 2:00 AM (off-peak hours)
- **Weekly:** Full backup with timestamp
- **Monthly:** Archived backup for compliance
- **Offsite:** Cloud backup of critical data

#### Backup Retention
- Daily: 7 days
- Weekly: 4 weeks
- Monthly: 12 months
- Compliance: As required by local regulations

---

## 9. SECURITY CONSIDERATIONS

### Current Security Features
- ✅ Role-Based Access Control (RBAC)
- ✅ OAuth2 Authentication (Laravel Passport)
- ✅ Activity Logging & Audit Trail
- ✅ User Permission Restrictions
- ✅ Business-level data isolation
- ✅ Location-level access control

### Recommended Security Hardening
1. **Database Security**
   - Enable SSL/TLS for database connections
   - Use strong MariaDB root password
   - Restrict database access by IP
   - Enable query logging for audit

2. **Application Security**
   - Enable HTTPS in production
   - Implement CSRF protection
   - Use environment variables for secrets
   - Regular security updates for dependencies

3. **Backup Security**
   - Encrypt backup files
   - Store backups securely
   - Verify backup integrity
   - Test restoration procedures

4. **User Management**
   - Enforce strong password policies
   - Enable two-factor authentication
   - Regular audit of user permissions
   - Revoke unused accounts

---

## 10. PERFORMANCE OPTIMIZATION

### Database Optimization
- ✅ Indexes on frequently queried columns
- ✅ Foreign key constraints
- ✅ Proper data types
- ✅ Partition strategy for large tables

### Application Optimization
- ✅ Query optimization in Eloquent
- ✅ Eager loading (with Eloquent relationships)
- ✅ DataTables server-side processing
- ✅ Pagination on large datasets

### Recommended Improvements
1. Add database query caching
2. Implement Redis for session storage
3. Optimize invoice generation (PDF caching)
4. Add full-text search indexes
5. Monitor slow query logs

---

## 11. MAINTENANCE & SUPPORT

### Regular Maintenance Tasks
- [ ] Daily: Monitor error logs, backup verification
- [ ] Weekly: Database optimization, permission audit
- [ ] Monthly: Performance analysis, dependency updates
- [ ] Quarterly: Security assessment, disaster recovery testing

### Support Contacts
- **Development:** For module enhancements
- **Database:** For schema modifications
- **Infrastructure:** For server issues
- **User Support:** For access/permission issues

---

## 12. QUICK REFERENCE

### Essential Commands

**Database Backup**
```bash
mysqldump -u root -p aadhira_erp > backup.sql
```

**Database Restore**
```bash
mysql -u root -p aadhira_erp < backup.sql
```

**Verify Database**
```sql
SELECT COUNT(*) as total_tables FROM information_schema.tables 
WHERE table_schema='aadhira_erp';
```

**Check Database Size**
```sql
SELECT SUM(data_length + index_length) / 1024 / 1024 as 'DB Size (MB)'
FROM information_schema.tables 
WHERE table_schema='aadhira_erp';
```

---

## 13. DOCUMENT HISTORY

| Date | Created/Updated By | Description |
|------|-------------------|-------------|
| 2026-04-23 | Code Analysis | Initial comprehensive analysis and backup creation |
| 2026-01-11 | System | Last database generation timestamp |

---

## 14. APPENDIX: SQL INFORMATION

### Database Connection
- **Host:** 127.0.0.1 (Local)
- **Port:** 3306 (Default MariaDB)
- **Username:** root
- **Database:** aadhira_erp
- **Charset:** utf8mb4
- **Collation:** utf8mb4_unicode_ci

### Table Distribution
```
Authorization & Users: 6 tables
Business & Organization: 4 tables
Products & Inventory: 15 tables
Financial & Transactions: 12 tables
POS & Cash: 3 tables
Customers: 1 table
Invoicing: 2 tables
Reporting: 3 tables
Integration: 8 tables
Other: 26+ tables
```

---

**END OF REPORT**

For more information or detailed analysis of specific modules, please refer to the Laravel application structure or database schema documentation.
