# 🚀 Step-by-Step Guide: Hosting Mahdev / Aadhira ERP on Vercel with Cloud Database

This document explains how to deploy your Laravel ERP system on **Vercel** and migrate your existing data (`aadhira_erp_old.sql`) to a **Cloud MySQL Database**.

---

## 📋 Overview of Files Created for Vercel

1. **[`api/index.php`](file:///c:/Aadhira_erp_v_1.0/api/index.php)**: Serverless entry point for Vercel. Automatically creates writable `/tmp/storage` folders for sessions, caches, and logs.
2. **[`vercel.json`](file:///c:/Aadhira_erp_v_1.0/vercel.json)**: Vercel routing configuration routing web requests to Laravel while serving static assets (`public/`).
3. **[`bootstrap/app.php`](file:///c:/Aadhira_erp_v_1.0/bootstrap/app.php)**: Configured to dynamically redirect Laravel storage path to `/tmp/storage` when executing on Vercel.
4. **[`.vercelignore`](file:///c:/Aadhira_erp_v_1.0/.vercelignore)**: Excludes heavy backups, SQL files, and unnecessary local files to keep the Vercel build lightweight.
5. **[`.env.example`](file:///c:/Aadhira_erp_v_1.0/.env.example)**: Reference environment configuration for Vercel settings.

---

## 🗄️ Step 1: Set Up Cloud MySQL Database

Your ERP application requires a **MySQL Cloud Database** accessible over the internet. Here are recommended Cloud DB providers with free/low-cost options:

### **Recommended Free / Managed MySQL Cloud Providers**:
* **Option A: Aiven for MySQL** (Free Tier available — Full native MySQL feature support)
  * Site: [https://aiven.io](https://aiven.io)
  * Create a free MySQL database instance and note your connection details (`DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).
* **Option B: TiDB Cloud** (Free Serverless Tier — 100% MySQL Compatible)
  * Site: [https://tidbcloud.com](https://tidbcloud.com)
  * Instant setup, compatible with standard MySQL dumps.
* **Option C: Railway / Render / DigitalOcean Managed MySQL**
  * Great performance and one-click database hosting options.

---

## 📤 Step 2: Migrate Your Current Data (`aadhira_erp_old.sql`) to Cloud DB

Your current ERP database dump is located at:
`c:\Aadhira_erp_v_1.0\aadhira_erp_old.sql`

To import your current data into the Cloud DB, choose one of these simple methods:

### **Method A: Using MySQL Command Line (Easiest & Fastest)**
Run this command in your PowerShell / Terminal:
```bash
mysql -h YOUR_CLOUD_DB_HOST -P YOUR_CLOUD_DB_PORT -u YOUR_CLOUD_DB_USER -p YOUR_CLOUD_DB_NAME < "c:\Aadhira_erp_v_1.0\aadhira_erp_old.sql"
```

### **Method B: Using DBeaver or TablePlus (GUI Tool)**
1. Open **DBeaver** or **TablePlus** or **HeidiSQL**.
2. Connect to your Cloud MySQL database using your host, port, username, password, and database name.
3. Right-click on your cloud database -> select **Execute SQL Script** / **Import SQL File**.
4. Select `aadhira_erp_old.sql` and run it. All tables and current data will be uploaded to your Cloud DB.

---

## 🌐 Step 3: Deploy to Vercel

1. **Push your code to GitHub / GitLab**:
   Ensure all changes (including `api/index.php`, `vercel.json`, `bootstrap/app.php`) are pushed to your GitHub repository.

2. **Import Project into Vercel**:
   * Go to [https://vercel.com/dashboard](https://vercel.com/dashboard).
   * Click **Add New Project** -> **Import Git Repository**.
   * Select your ERP repository (`Aadhira_Erp`).

3. **Configure Environment Variables in Vercel**:
   Under **Environment Variables**, add the following keys:

   | Key | Example / Recommended Value |
   |---|---|
   | `APP_NAME` | `Mahdev ERP` |
   | `APP_ENV` | `production` |
   | `APP_KEY` | `base64:x9/Za2kh5VHs+AWAP+weOJC8Y1aGL7WyOC546iwpGIQ=` |
   | `APP_DEBUG` | `false` |
   | `APP_URL` | `https://your-project.vercel.app` |
   | `DB_CONNECTION` | `mysql` |
   | `DB_HOST` | `YOUR_CLOUD_DB_HOST` |
   | `DB_PORT` | `3306` |
   | `DB_DATABASE` | `YOUR_CLOUD_DB_DATABASE_NAME` |
   | `DB_USERNAME` | `YOUR_CLOUD_DB_USER` |
   | `DB_PASSWORD` | `YOUR_CLOUD_DB_PASSWORD` |
   | `CACHE_DRIVER` | `cookie` |
   | `SESSION_DRIVER` | `cookie` |
   | `LOG_CHANNEL` | `stderr` |

4. **Click Deploy**:
   * Vercel will automatically build the project using the serverless PHP runtime.
   * Once finished, your ERP system will be live on Vercel with your current data!
