# 🔄 DATABASE WIPE INSTRUCTIONS
**Date:** April 23, 2026
**Purpose:** Clear all settings and data, keep only user "Yuvanshan"

---

## ⚠️  CRITICAL WARNING

**This process will PERMANENTLY DELETE all business data, transactions, products, contacts, and settings.**

**Only the user "Yuvanshan" with password "Yuvan@1709" will be preserved.**

**BACKUP YOUR DATABASE BEFORE PROCEEDING!**

---

## 📋 WHAT WILL BE DELETED

### ❌ Complete Removal
- **Business:** Saravanan Stores (ID: 2) - completely deleted
- **Users:** saravanan, saravanan1 - completely deleted
- **All transactional data:** Sales, purchases, payments
- **All products and inventory**
- **All contacts (customers/suppliers)**
- **All financial accounts and transactions**
- **All activity logs and audit trails**
- **All system settings and configurations**

### ✅ What Will Be Kept
- **User:** Yuvanshan (username: Yuvanshan, password: Yuvan@1709)
- **Business:** Mahdev (Pvt) Ltd (ID: 1) - structure only, all data cleared
- **Database schema:** All tables and relationships preserved
- **System framework:** Laravel application remains intact

---

## 📁 GENERATED SCRIPTS

| File | Purpose | When to Run |
|------|---------|-------------|
| `database_wipe_verification.sql` | Check current data before wipe | **FIRST** - Always run first |
| `database_wipe_script.sql` | Main wipe script (safe - transaction commented) | **SECOND** - After verification |
| `database_wipe_execute.sql` | Execution wrapper with safety checks | **THIRD** - Final execution |

---

## 🚀 STEP-BY-STEP EXECUTION

### Step 1: Backup Your Database
```bash
# Option 1: Using mysqldump (Command Line)
mysqldump -u [username] -p aadhira_erp > backup_before_wipe_2026_04_23.sql

# Option 2: Use existing backup procedures
# See: BACKUPS/BACKUP_DOCUMENTATION.md
```

### Step 2: Verify Current Data
```sql
-- Run this script to see what will be deleted
SOURCE database_wipe_verification.sql;
```

**Expected Results:**
- Users: 3 total (Yuvanshan + 2 others to be deleted)
- Businesses: 2 total (Mahdev + Saravanan to be deleted)
- Transactions: 50+ sales + 18 purchases (all to be deleted)
- Products: 3+ items (all to be deleted)

### Step 3: Execute the Wipe
```sql
-- Run the verification script first, then:
SOURCE database_wipe_execute.sql;

-- Inside that script, uncomment this line to actually run the wipe:
-- SOURCE database_wipe_script.sql;
```

### Step 4: Verify Wipe Success
```sql
-- After wipe, run these queries to confirm:

-- Should return 1 user
SELECT id, username, first_name FROM users;

-- Should return 1 business
SELECT id, name FROM business;

-- Should return 0 transactions
SELECT COUNT(*) FROM transactions;

-- Should return 0 products
SELECT COUNT(*) FROM products;
```

---

## 🔐 POST-WIPE LOGIN

After successful wipe, you can log in with:

```
Username: Yuvanshan
Password: Yuvan@1709
```

---

## 🛠️ POST-WIPE RECONFIGURATION

### Required Setup Steps:
1. **Business Locations** - Add your store locations
2. **Products** - Re-add your inventory items
3. **Tax Rates** - Configure tax settings
4. **Payment Methods** - Set up cash/card payment options
5. **Customers/Suppliers** - Re-add contact information
6. **Users** - Add additional staff accounts if needed

### Optional Configuration:
- Payment gateways (Razorpay, Stripe, PayPal)
- Email/SMS settings
- Barcode/Printer settings
- Loyalty program settings
- Invoice templates

---

## 🔄 RECOVERY OPTIONS

### If Wipe Goes Wrong:
```sql
-- The main wipe script uses transactions
-- If something fails, you can rollback:
ROLLBACK;

-- Or restore from backup:
mysql -u [username] -p aadhira_erp < backup_before_wipe_2026_04_23.sql
```

### Emergency Restore:
1. Stop the application
2. Restore database from backup
3. Restart the application
4. Verify all data is restored

---

## 📊 EXPECTED RESULTS

### Before Wipe:
- **Users:** 3 (Yuvanshan, saravanan, saravanan1)
- **Businesses:** 2 (Mahdev Pvt Ltd, Saravanan Stores)
- **Transactions:** 68+ (50+ sales, 18+ purchases)
- **Products:** 3+ items
- **Contacts:** Multiple customers/suppliers

### After Wipe:
- **Users:** 1 (Yuvanshan only)
- **Businesses:** 1 (Mahdev Pvt Ltd - empty)
- **Transactions:** 0
- **Products:** 0
- **Contacts:** 0

---

## ⚡ EXECUTION TIME

- **Verification:** 2-5 seconds
- **Main Wipe:** 30-60 seconds (depending on data volume)
- **Verification:** 2-5 seconds

---

## 🛡️ SAFETY FEATURES

### Built-in Protections:
- ✅ **Transaction-wrapped:** Can rollback if needed
- ✅ **Verification first:** See exactly what will be deleted
- ✅ **Commented execution:** Must manually uncomment to run
- ✅ **Foreign key safe:** Handles constraints properly
- ✅ **Auto-increment reset:** Clean ID sequences

### Manual Safety Steps:
- ✅ Backup database first
- ✅ Test on development environment
- ✅ Verify user credentials preserved
- ✅ Check business structure remains

---

## 📞 SUPPORT

### If You Need Help:
1. **Check the verification results** - ensure they match expectations
2. **Review the scripts** - all operations are logged
3. **Test on a copy first** - never run on production without testing
4. **Have backup ready** - restore option available

### Troubleshooting:
- **Script fails:** Check foreign key constraints
- **Wrong data deleted:** Restore from backup
- **User not preserved:** Check username spelling
- **Business missing:** Verify business_id=1 exists

---

## 📋 CHECKLIST

### Pre-Wipe:
- [ ] Database backup completed
- [ ] Verification script run and results reviewed
- [ ] Understanding of what will be deleted
- [ ] Confirmation that Yuvanshan user will be preserved

### During Wipe:
- [ ] Scripts executed in correct order
- [ ] No errors reported
- [ ] Transaction committed (if uncommented)

### Post-Wipe:
- [ ] User Yuvanshan can log in
- [ ] Business structure exists
- [ ] No old data remains
- [ ] System functions normally

---

## 🎯 FINAL NOTES

- **This is irreversible** - backup is your only safety net
- **Test first** - run on a development copy if possible
- **Take your time** - review verification results carefully
- **Document changes** - note what was configured after wipe

**Success will result in a clean system with only your user account preserved.**

---

**Generated:** April 23, 2026
**Scripts Location:** `C:\Aadhira_erp_v_1.0\`
**Contact:** System Administrator