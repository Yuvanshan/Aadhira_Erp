-- Update Invoice Layouts to "Mahdev Pvt Ltd" Branding
-- Run this SQL in your database to update both invoice layouts

-- Update Layout 1 (Default for business_id 1)
UPDATE invoice_layouts 
SET 
    header_text = 'Mahdev Pvt Ltd',
    footer_text = '<p style="font-weight: bold; text-align: center; margin: 0;">Thank You - Mahdev Pvt Ltd</p>'
WHERE id = 1;

-- Update Layout 2 (Default for business_id 2)  
UPDATE invoice_layouts 
SET 
    header_text = 'Mahdev Pvt Ltd',
    footer_text = '<p style="font-weight: bold; text-align: center; margin: 0;">Thank You - Mahdev Pvt Ltd</p>'
WHERE id = 2;

-- Verify the updates
SELECT id, header_text, footer_text FROM invoice_layouts;

-- Clear Laravel cache tables (if they exist)
DELETE FROM cache WHERE 1=1;
DELETE FROM cache_locks WHERE 1=1;
