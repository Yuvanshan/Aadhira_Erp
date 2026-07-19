# Invoice Branding Update Instructions

## Objective
Change invoice header and footer to display "Mahdev Pvt Ltd" professionally and update the receipt template for better appearance.

## Changes Made

### 1. **Receipt Template Updates (classic.blade.php)**
   - **Logo Size**: Reduced from 120px to 80px for better proportions
   - **Header Text**: Added professional styling (bold, centered, 13px font, proper spacing)
   - **Shop Name**: Improved styling with proper margins and font size (16px, bold)
   - **Footer Section**: Added separator line (border-top) and improved styling (centered, bold, 12px font)

### 2. **What You Need to Do: Update Default Invoice Layout**

You have TWO options to update the invoice branding:

#### **OPTION A: Using Invoice Settings UI (Easiest)**
1. Login to your Aadhira ERP system
2. Go to **Settings → Invoice Settings**
3. Click **Edit** on the default invoice layout
4. Update the fields:
   - **Header Text**: `Mahdev Pvt Ltd`
   - **Footer Text**: `Thank You - Mahdev Pvt Ltd`
5. Click **Save**
6. Test by creating/printing an invoice

#### **OPTION B: Using Database SQL (Direct)**
1. Open your database client (MySQL Workbench, PhpMyAdmin, etc.)
2. Run the following SQL query:

```sql
UPDATE invoice_layouts 
SET 
    header_text = '<p style="font-weight: bold; margin: 0; padding: 0; text-align: center;">Mahdev Pvt Ltd</p>',
    footer_text = '<p style="margin: 0; padding: 0; text-align: center;">Thank You - Mahdev Pvt Ltd</p>'
WHERE id = 1;
```

3. Verify the update:
```sql
SELECT id, header_text, footer_text FROM invoice_layouts WHERE id = 1;
```

#### **OPTION C: Using Laravel Artisan Tinker**
1. Open terminal/command prompt in the POS system directory:
   ```
   cd c:\Aadhira_erp_v_1.0\app\pos_system
   ```

2. Run Artisan tinker:
   ```
   php artisan tinker
   ```

3. Execute these commands:
   ```php
   $layout = DB::table('invoice_layouts')->where('id', 1)->first();
   DB::table('invoice_layouts')->where('id', 1)->update(['header_text' => '<p style="font-weight: bold; margin: 0; padding: 0; text-align: center;">Mahdev Pvt Ltd</p>', 'footer_text' => '<p style="margin: 0; padding: 0; text-align: center;">Thank You - Mahdev Pvt Ltd</p>']);
   exit;
   ```

## Invoice Layout Database Structure

The invoice layouts are stored in the `invoice_layouts` table:

```
Column Name          | Type   | Purpose
---------------------|--------|----------------------------
id                   | INT    | Unique identifier
header_text          | TEXT   | Custom header (supports HTML)
footer_text          | TEXT   | Custom footer (supports HTML)
display_name         | VARCHAR| Business/Shop name
address              | TEXT   | Business address
contact              | VARCHAR| Contact information
website              | VARCHAR| Website URL
logo                 | LONGBLOB| Logo image
... (other fields)   |        |
```

## How the Invoice Works

1. **Form Input**: Header & Footer fields in Settings → Invoice Settings (now fully editable)
2. **Database Storage**: Saved in `invoice_layouts` table as TEXT fields
3. **Template Rendering**: Receipt templates retrieve data and display via `{!! $receipt_details->header_text !!}`
4. **Final Display**: User sees formatted invoice with professional branding

## Expected Result After Update

When you create/print an invoice, you should see:
```
[Logo if configured]

Mahdev Pvt Ltd

[Shop Display Name]
[Address]
[Contact Info]

[Invoice Details]

...invoice items...

---
Thank You - Mahdev Pvt Ltd
---
[Barcode/QR Code if enabled]
```

## Styling Options

The footer/header text supports HTML and CSS. Here are some professional options:

### Simple Professional:
```html
<p style="margin: 0; padding: 0;">Thank You - Mahdev Pvt Ltd</p>
```

### With Line Breaks:
```html
<p style="margin: 0; padding: 0;">Thank You</p>
<p style="margin: 5px 0; padding: 0; font-weight: bold;">Mahdev Pvt Ltd</p>
```

### With Contact:
```html
<p style="margin: 0; padding: 0; font-weight: bold;">Mahdev Pvt Ltd</p>
<p style="margin: 5px 0; padding: 0; font-size: 11px;">076 89 88 970 / 047 509 28 078</p>
```

### With Horizontal Line:
```html
<div style="border-top: 1px solid #000; margin: 5px 0; padding-top: 5px;">
    <p style="margin: 0; padding: 0; font-weight: bold;">Mahdev Pvt Ltd</p>
</div>
```

## Files Modified

- `c:\Aadhira_erp_v_1.0\app\pos_system\resources\views\sale_pos\receipts\classic.blade.php`
  - Updated header section styling
  - Updated footer section styling with border separator

## Next Steps

1. **Update Invoice Layout** - Choose one of the options (A, B, or C) above
2. **Test Invoice** - Create a test transaction and view/print the invoice
3. **Adjust if Needed** - If you want different styling, edit the footer_text HTML in the form
4. **Deploy to Other Layouts** - If using other receipt templates (slim, elegant, etc.), repeat for those

## Troubleshooting

**Issue**: Header/Footer not showing in invoice
- **Solution**: Make sure you click Save after editing in Settings → Invoice Settings

**Issue**: HTML tags showing as plain text
- **Solution**: The template already uses `{!! !!}` tags to render HTML, ensure you're not escaping the content

**Issue**: Old text still showing
- **Solution**: Clear browser cache or use hard refresh (Ctrl+Shift+R in most browsers)

**Issue**: Multiple invoice layouts exist
- **Solution**: If you have multiple layouts (id > 1), update those as well using the same SQL query but different WHERE clause

---
Last Updated: Based on Invoice Layout Form Editability Fix
Version: 1.8.2
