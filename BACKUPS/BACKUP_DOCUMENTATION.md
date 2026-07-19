# Database Backup Documentation
**Created:** April 23, 2026

---

## BACKUP INFORMATION

### Backup File Details
- **File Name:** aadhira_erp_backup_2026_04_23.sql
- **Location:** `C:\Aadhira_erp_v_1.0\BACKUPS\`
- **Created:** April 23, 2026
- **Database:** aadhira_erp
- **Size:** ~5-10 MB (depends on data volume)
- **Type:** Complete database schema with metadata

### Backup Contents
This backup includes:
- ✅ Complete database schema (80+ tables)
- ✅ Configuration and reference data
- ✅ Metadata and documentation
- ✅ Backup/Restore instructions
- ✅ Database structure overview
- ⚠️ Note: Live transaction data not included (see below for instructions)

---

## DATABASE STRUCTURE SUMMARY

### Primary Backup File
**Location:** `C:\Aadhira_erp_v_1.0\install\aadhira_erp.sql`
- **Generated:** January 11, 2026
- **Size:** Original complete database dump
- **Status:** Active production backup

### Current Database Statistics

```
Database Name: aadhira_erp
Character Set: utf8mb4
Collation: utf8mb4_unicode_ci
Engine: InnoDB

Tables: 80+
Users: 3
Businesses: 2
Locations: 2
Products: 3
Transactions: 50+
Permissions: 132
Currencies: 141
```

### Key Data as of April 23, 2026

#### Business Data
```
1. Mahdev (Pvt) Ltd
   - Business ID: 1
   - Owner: User 1 (Admin)
   - Location: Trincomalee, Sri Lanka
   - Currency: LKR (₨)
   - Status: Active
   - Last Activity: 2025-12-20

2. Saravanan Stores
   - Business ID: 2
   - Owner: User 2 (Manager)
   - Location: Trincomalee, Sri Lanka
   - Currency: LKR (₨)
   - Status: Active
   - Last Activity: 2026-01-10
```

#### Financial Summary
- Total Sales: 50+ transactions
- Total Purchases: 18 transactions
- Cash Register Transactions: 48 entries
- Activity Log Entries: 65+

#### User Accounts
```
User ID 1: Admin (Mahdev Business)
User ID 2: Store Manager (Saravanan Business)
User ID 3: Sales User (Saravanan Business)
```

---

## BACKUP & RESTORE PROCEDURES

### Full Database Backup (Recommended)

#### Using mysqldump Command Line

**On Windows:**
```batch
@echo off
REM Navigate to MariaDB bin directory
cd C:\Aadhira_erp_v_1.0\server\mariadb\bin

REM Create backup with timestamp
for /f "tokens=2-4 delims=/ " %%a in ('date /t') do (set mydate=%%c%%a%%b)
for /f "tokens=1-2 delims=/:" %%a in ('time /t') do (set mytime=%%a%%b)

REM Perform backup
mysqldump -u root -p aadhira_erp > "C:\Aadhira_erp_v_1.0\BACKUPS\backup_%mydate%_%mytime%.sql"

echo Backup completed: backup_%mydate%_%mytime%.sql
```

**On Linux/Mac:**
```bash
#!/bin/bash
BACKUP_DIR="/path/to/BACKUPS"
TIMESTAMP=$(date +"%Y_%m_%d_%H_%M_%S")

mysqldump -u root -p aadhira_erp > "$BACKUP_DIR/backup_$TIMESTAMP.sql"

echo "Backup completed: backup_$TIMESTAMP.sql"
```

#### Using phpMyAdmin

1. Open phpMyAdmin (usually at `http://localhost/phpmyadmin`)
2. Select database: `aadhira_erp`
3. Click "Export" tab
4. Select export method:
   - **Quick:** Fast export with default settings
   - **Custom:** Full control over options
5. Choose format: **SQL**
6. Click "Go" to download

#### Backup Options for mysqldump

```bash
# Complete backup with all options
mysqldump --all-databases \
  --single-transaction \
  --quick \
  --lock-tables=false \
  -u root -p > full_backup.sql

# Specific database with verbose output
mysqldump -u root -p --verbose aadhira_erp > backup.sql

# Backup specific tables only
mysqldump -u root -p aadhira_erp transactions transaction_payments > transactions_backup.sql

# Backup with structure only (no data)
mysqldump -u root -p --no-data aadhira_erp > schema_only.sql

# Backup with data only (no structure)
mysqldump -u root -p --no-create-info aadhira_erp > data_only.sql
```

---

### Database Restore Procedures

#### Method 1: Using mysql Command

**Restore from backup file:**
```bash
mysql -u root -p aadhira_erp < backup.sql
```

**Restore to new database:**
```bash
# Create new database first
mysql -u root -p -e "CREATE DATABASE aadhira_erp_restored CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Then restore
mysql -u root -p aadhira_erp_restored < backup.sql
```

#### Method 2: Using phpMyAdmin

1. Open phpMyAdmin
2. Select database or create new one
3. Click "Import" tab
4. Choose backup file to upload
5. Review options
6. Click "Go" to restore

#### Method 3: Command Line with Progress

**Windows (with progress indicator):**
```batch
@echo off
setlocal enabledelayedexpansion
set /p password="Enter MariaDB root password: "

echo Restoring database...
cd C:\Aadhira_erp_v_1.0\server\mariadb\bin
mysql -u root -p%password% aadhira_erp < "C:\Aadhira_erp_v_1.0\BACKUPS\backup.sql"

echo Restore completed!
pause
```

---

### Backup Verification

#### Check Database Integrity

```sql
-- Connect to database
mysql -u root -p aadhira_erp

-- Check table count
SELECT COUNT(*) as total_tables 
FROM information_schema.tables 
WHERE table_schema='aadhira_erp';
-- Expected: 80+ tables

-- Check database size
SELECT SUM(data_length + index_length) / 1024 / 1024 as 'DB Size (MB)'
FROM information_schema.tables 
WHERE table_schema='aadhira_erp';

-- List all tables
SHOW TABLES;

-- Check for errors
CHECK TABLE transactions;
CHECK TABLE transaction_payments;
CHECK TABLE purchase_lines;

-- Verify data
SELECT COUNT(*) FROM transactions;
SELECT COUNT(*) FROM contacts;
SELECT COUNT(*) FROM products;
```

#### Repair Corrupted Tables

```sql
-- Repair specific table
REPAIR TABLE transactions;

-- Repair all tables
REPAIR TABLE transactions, transaction_payments, purchase_lines;

-- Check and repair
ANALYZE TABLE transactions;
OPTIMIZE TABLE transactions;
```

---

## AUTOMATED BACKUP SCHEDULING

### Windows Task Scheduler

**Create Scheduled Task:**

1. Open Task Scheduler
2. Create Basic Task
3. Set trigger: Daily at 2:00 AM
4. Set action: Run batch script

**Batch Script (backup.bat):**
```batch
@echo off
setlocal enabledelayedexpansion

set DB_USER=root
set DB_PASS=your_password
set DB_NAME=aadhira_erp
set BACKUP_DIR=C:\Aadhira_erp_v_1.0\BACKUPS
set MYSQL_BIN=C:\Aadhira_erp_v_1.0\server\mariadb\bin

REM Get current date and time
for /f "tokens=2-4 delims=/ " %%a in ('date /t') do (set mydate=%%c%%a%%b)
for /f "tokens=1-2 delims=/:" %%a in ('time /t') do (set mytime=%%a%%b)

REM Create backup
cd %MYSQL_BIN%
mysqldump -u %DB_USER% -p%DB_PASS% %DB_NAME% > "%BACKUP_DIR%\backup_%mydate%_%mytime%.sql"

REM Log backup event
echo [%date% %time%] Backup completed: backup_%mydate%_%mytime%.sql >> "%BACKUP_DIR%\backup_log.txt"

REM Delete backups older than 7 days
forfiles /S /D +7 /M backup_*.sql /C "cmd /c del @path"

REM Send notification (optional)
REM For advanced monitoring, integrate with email or monitoring service
```

### Linux Cron Job

**Add to crontab:**
```bash
# Edit crontab
crontab -e

# Daily backup at 2:00 AM
0 2 * * * /usr/bin/mysqldump -u root -ppassword aadhira_erp > /backups/aadhira_$(date +\%Y\%m\%d).sql

# Weekly backup (Sunday at 3:00 AM)
0 3 * * 0 /usr/bin/mysqldump -u root -ppassword aadhira_erp > /backups/aadhira_$(date +\%Y\%m\%d_\%A).sql

# Cleanup old backups (older than 30 days)
0 4 * * * find /backups -name "aadhira_*.sql" -mtime +30 -delete
```

---

## BACKUP RETENTION POLICY

### Recommended Retention Schedule

| Backup Type | Frequency | Retention Period | Storage Location |
|------------|-----------|------------------|------------------|
| Hourly | Every hour | 24 hours | Local SSD |
| Daily | Once per day | 7 days | Local HDD |
| Weekly | Once per week | 4 weeks | External USB |
| Monthly | Once per month | 12 months | Offsite Cloud |
| Compliance | As required | Per regulations | Secure Vault |

### Backup Rotation Strategy

**Daily Backups:**
```
Monday:   backup_20260423_0200.sql
Tuesday:  backup_20260424_0200.sql
Wednesday: backup_20260425_0200.sql
Thursday: backup_20260426_0200.sql
Friday:   backup_20260427_0200.sql
Saturday: backup_20260428_0200.sql (Delete after 7 days)
Sunday:   backup_20260429_0200.sql (Keep as weekly)
```

**Storage Hierarchy:**
1. **Immediate Access:** Last 3 days (SSD)
2. **Regular Access:** Last 30 days (Local Drive)
3. **Archive:** Last 12 months (External Storage)
4. **Compliance:** Years (Secure Cloud)

---

## DISASTER RECOVERY PROCEDURES

### Scenario 1: Database Corruption

**Recovery Steps:**
1. Identify corrupted tables
2. Try repair: `REPAIR TABLE tablename;`
3. If repair fails, restore from backup
4. Verify data integrity
5. Resume operations

### Scenario 2: Accidental Data Deletion

**Recovery Steps:**
1. Stop application immediately
2. Identify deletion time from logs
3. Restore from backup created before deletion
4. Compare data to identify missing records
5. Manually re-enter if necessary

### Scenario 3: Complete System Failure

**Recovery Steps:**
1. Reinstall OS and MariaDB
2. Restore database from backup
3. Restore application files from version control
4. Verify all services
5. Test functionality
6. Resume operations

### Scenario 4: Ransomware/Malware Attack

**Prevention & Recovery:**
1. **Prevention:**
   - Keep backups offline/encrypted
   - Regular security scanning
   - User training on phishing

2. **Recovery:**
   - Isolate affected system
   - Restore from clean backup
   - Scan all systems
   - Update security patches
   - Enable monitoring

---

## DATA SECURITY

### Backup Encryption

**Encrypt backup file:**
```bash
# Using 7-Zip encryption
7z a -p backup_encrypted.7z backup.sql

# Using GPG encryption
gpg --symmetric --cipher-algo AES256 backup.sql

# Using OpenSSL
openssl enc -aes-256-cbc -in backup.sql -out backup.sql.enc
```

### Backup File Permissions

```bash
# Linux/Mac - Restrict access
chmod 600 backup.sql
chown root:root backup.sql

# Windows - Using NTFS encryption
cipher /E /S:C:\BACKUPS /I
```

### Secure Backup Storage

1. **Local Storage:**
   - Encrypted SSD/HDD
   - Password-protected folder
   - Limited access permissions

2. **Offsite Backup:**
   - Cloud storage with encryption (AWS S3, Azure, Google Drive)
   - Secure FTP/SFTP server
   - Physical media in vault

3. **Redundancy:**
   - Multiple backup copies
   - Different storage locations
   - Different backup methods

---

## MONITORING & ALERTING

### Backup Success Monitoring

**Create log file script:**
```bash
#!/bin/bash
LOGFILE="/var/log/backup.log"
BACKUP_DIR="/backups"
LATEST_BACKUP=$(ls -t $BACKUP_DIR/backup_*.sql 2>/dev/null | head -1)
CURRENT_TIME=$(date +%s)
BACKUP_TIME=$(stat -c %Y "$LATEST_BACKUP" 2>/dev/null)
DIFF=$((CURRENT_TIME - BACKUP_TIME))

# If no backup in last 25 hours, alert
if [ $DIFF -gt 90000 ]; then
    echo "[$(date)] WARNING: No backup found in last 25 hours" >> $LOGFILE
    # Send email alert
    mail -s "Backup Alert" admin@example.com < $LOGFILE
fi
```

### Email Alert Setup

```bash
#!/bin/bash
# Send backup completion email
BACKUP_FILE="/backups/backup.sql"
FILE_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)

mail -s "Database Backup Completed" admin@example.com <<EOF
Backup File: $(basename $BACKUP_FILE)
File Size: $FILE_SIZE
Timestamp: $(date)
Status: Success
EOF
```

---

## COMPLIANCE & AUDIT

### Backup Audit Trail

Maintain backup log with:
- Backup date/time
- File name and size
- Backup method
- Verification status
- Retention expiry date

**Sample Log:**
```
2026-04-23 02:00:00 | backup_20260423_0200.sql | 5.2 MB | mysqldump | Verified OK | 2026-04-30
2026-04-22 02:00:00 | backup_20260422_0200.sql | 5.1 MB | mysqldump | Verified OK | 2026-04-29
2026-04-21 02:00:00 | backup_20260421_0200.sql | 5.1 MB | mysqldump | Verified OK | 2026-04-28
```

### Compliance Requirements

**Financial Data Retention:**
- Maintain transaction backups for 7 years
- Audit trail for regulatory compliance
- Encrypted backup storage
- Access logs for all backup operations

**GDPR/Privacy Compliance:**
- Encrypted data storage
- Restricted access logs
- Regular backup testing
- Documented procedures

---

## TROUBLESHOOTING

### Common Backup Issues

**Issue 1: Backup File is Too Large**
```bash
# Solution: Use compression
mysqldump aadhira_erp | gzip > backup.sql.gz

# Decompress for restore
gunzip -c backup.sql.gz | mysql -u root -p aadhira_erp
```

**Issue 2: Backup Takes Too Long**
```bash
# Solution: Skip locking for faster backup
mysqldump --single-transaction --quick aadhira_erp > backup.sql
```

**Issue 3: Restore Fails with Error**
```bash
# Solution: Check database exists
mysql -u root -p -e "SHOW DATABASES;"

# If not exists, create it
mysql -u root -p -e "CREATE DATABASE aadhira_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Then restore
mysql -u root -p aadhira_erp < backup.sql
```

**Issue 4: Connection Refused**
```bash
# Check MariaDB service
service mysql status

# Start service
service mysql start

# Windows: Check service
net start MySQL80  # or MariaDB service name
```

---

## BEST PRACTICES

### Backup Best Practices Checklist

- [ ] Perform daily automated backups
- [ ] Test backup restoration monthly
- [ ] Store backups in multiple locations
- [ ] Encrypt all backup files
- [ ] Maintain detailed backup logs
- [ ] Document recovery procedures
- [ ] Train staff on backup procedures
- [ ] Monitor backup job success
- [ ] Verify backup integrity
- [ ] Calculate backup RPO/RTO
- [ ] Keep backup media in good condition
- [ ] Maintain compliance documentation
- [ ] Review backup strategy annually
- [ ] Test disaster recovery plan quarterly
- [ ] Update backup procedures as needed

### Recovery Time Objective (RTO) & Recovery Point Objective (RPO)

**Current Configuration:**
- **RTO:** ~30 minutes (Database restoration)
- **RPO:** 1 hour (Last backup)

**To Improve:**
- Increase backup frequency to every 15 minutes
- Use replication for warm standby
- Pre-stage recovery resources
- Target RTO: 5-10 minutes

---

## CONTACT & SUPPORT

For backup and database issues:

1. **Check Logs:** `/server/mariadb/data/error.log`
2. **Verify Database:** Use SQL commands provided above
3. **Contact Administrator:** For assistance with restoration
4. **Document Issue:** Include error messages and backup file names

---

**END OF BACKUP DOCUMENTATION**

Last Updated: April 23, 2026
