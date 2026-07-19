-- Update invoice layout branding to "Mahdev Pvt Ltd"
-- Run this SQL query in your database

UPDATE invoice_layouts 
SET 
    header_text = '<p style="font-weight: bold; margin: 0; padding: 0; text-align: center;">Mahdev Pvt Ltd</p>',
    footer_text = '<p style="margin: 0; padding: 0; text-align: center;">Thank You - Mahdev Pvt Ltd</p>'
WHERE id = 1;

-- Verify the update
SELECT id, header_text, footer_text FROM invoice_layouts WHERE id = 1;
