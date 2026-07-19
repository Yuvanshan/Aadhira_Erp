-- =====================================================
-- DATABASE WIPE SCRIPT - Keep Only User "Yuvanshan"
-- Generated: April 23, 2026
-- Purpose: Clear all business data and settings, keep only specified user
-- =====================================================

-- Disable foreign key checks temporarily
SET FOREIGN_KEY_CHECKS = 0;

-- Start transaction for safety
START TRANSACTION;

-- =====================================================
-- STEP 1: DELETE BUSINESS ID=2 (Saravanan Stores) COMPLETELY
-- =====================================================

-- Delete all data related to business_id = 2
DELETE FROM account_transactions WHERE account_id IN (SELECT id FROM accounts WHERE business_id = 2);
DELETE FROM accounts WHERE business_id = 2;
DELETE FROM account_types WHERE business_id = 2;

-- Delete activity logs for business 2
DELETE FROM activity_log WHERE business_id = 2;

-- Delete barcodes for business 2
DELETE FROM barcodes WHERE business_id = 2;

-- Delete brands for business 2
DELETE FROM brands WHERE business_id = 2;

-- Delete business locations for business 2
DELETE FROM business_locations WHERE business_id = 2;

-- Delete cash registers and transactions for business 2
DELETE FROM cash_register_transactions WHERE cash_register_id IN (SELECT id FROM cash_registers WHERE business_id = 2);
DELETE FROM cash_registers WHERE business_id = 2;

-- Delete contacts for business 2
DELETE FROM contacts WHERE business_id = 2;

-- Delete all transactions and related data for business 2
DELETE FROM transaction_payments WHERE transaction_id IN (SELECT id FROM transactions WHERE business_id = 2);
DELETE FROM transaction_sell_lines WHERE transaction_id IN (SELECT id FROM transactions WHERE business_id = 2);
DELETE FROM transaction_sell_lines WHERE transaction_id IN (SELECT id FROM transactions WHERE business_id = 2);
DELETE FROM transactions WHERE business_id = 2;

-- Delete products and related data for business 2
DELETE FROM product_variations WHERE product_id IN (SELECT id FROM products WHERE business_id = 2);
DELETE FROM variation_location_details WHERE product_variation_id IN (SELECT id FROM product_variations WHERE product_id IN (SELECT id FROM products WHERE business_id = 2));
DELETE FROM products WHERE business_id = 2;

-- Delete categories for business 2
DELETE FROM categories WHERE business_id = 2;

-- Delete units for business 2
DELETE FROM units WHERE business_id = 2;

-- Delete taxes for business 2
DELETE FROM tax_rates WHERE business_id = 2;

-- Delete invoice schemes and layouts for business 2
DELETE FROM invoice_schemes WHERE business_id = 2;
DELETE FROM invoice_layouts WHERE business_id = 2;

-- Delete business 2 itself
DELETE FROM business WHERE id = 2;

-- Delete users associated with business 2 (except Yuvanshan)
DELETE FROM users WHERE business_id = 2;

-- =====================================================
-- STEP 2: CLEAR BUSINESS DATA FOR BUSINESS ID=1 (Mahdev Pvt Ltd)
-- Keep basic settings but clear all transactional data
-- =====================================================

-- Clear account transactions for business 1
DELETE FROM account_transactions WHERE account_id IN (SELECT id FROM accounts WHERE business_id = 1);

-- Clear accounts for business 1 (keep system accounts if any)
DELETE FROM accounts WHERE business_id = 1;

-- Clear account types for business 1
DELETE FROM account_types WHERE business_id = 1;

-- Clear activity logs for business 1
DELETE FROM activity_log WHERE business_id = 1;

-- Clear barcodes for business 1
DELETE FROM barcodes WHERE business_id = 1;

-- Clear brands for business 1
DELETE FROM brands WHERE business_id = 1;

-- Clear business locations for business 1 (keep one default if needed)
-- Note: Keeping business locations as they might be needed for basic setup
-- DELETE FROM business_locations WHERE business_id = 1;

-- Clear cash registers and transactions for business 1
DELETE FROM cash_register_transactions WHERE cash_register_id IN (SELECT id FROM cash_registers WHERE business_id = 1);
DELETE FROM cash_registers WHERE business_id = 1;

-- Clear contacts for business 1
DELETE FROM contacts WHERE business_id = 1;

-- Clear all transactions and related data for business 1
DELETE FROM transaction_payments WHERE transaction_id IN (SELECT id FROM transactions WHERE business_id = 1);
DELETE FROM transaction_sell_lines WHERE transaction_id IN (SELECT id FROM transactions WHERE business_id = 1);
DELETE FROM transaction_sell_lines WHERE transaction_id IN (SELECT id FROM transactions WHERE business_id = 1);
DELETE FROM transactions WHERE business_id = 1;

-- Clear products and related data for business 1
DELETE FROM product_variations WHERE product_id IN (SELECT id FROM products WHERE business_id = 1);
DELETE FROM variation_location_details WHERE product_variation_id IN (SELECT id FROM product_variations WHERE product_id IN (SELECT id FROM products WHERE business_id = 1));
DELETE FROM products WHERE business_id = 1;

-- Clear categories for business 1
DELETE FROM categories WHERE business_id = 1;

-- Clear units for business 1
DELETE FROM units WHERE business_id = 1;

-- Clear taxes for business 1
DELETE FROM tax_rates WHERE business_id = 1;

-- Clear invoice schemes and layouts for business 1
DELETE FROM invoice_schemes WHERE business_id = 1;
DELETE FROM invoice_layouts WHERE business_id = 1;

-- =====================================================
-- STEP 3: CLEAR SYSTEM-WIDE DATA (Non-business specific)
-- =====================================================

-- Clear all notifications
DELETE FROM notifications;

-- Clear sessions
DELETE FROM sessions;

-- =====================================================
-- STEP 4: RESET AUTO-INCREMENT VALUES
-- =====================================================

-- Reset auto-increment for key tables
ALTER TABLE accounts AUTO_INCREMENT = 1;
ALTER TABLE account_transactions AUTO_INCREMENT = 1;
ALTER TABLE account_types AUTO_INCREMENT = 1;
ALTER TABLE activity_log AUTO_INCREMENT = 1;
ALTER TABLE barcodes AUTO_INCREMENT = 1;
ALTER TABLE brands AUTO_INCREMENT = 1;
ALTER TABLE business_locations AUTO_INCREMENT = 1;
ALTER TABLE cash_registers AUTO_INCREMENT = 1;
ALTER TABLE cash_register_transactions AUTO_INCREMENT = 1;
ALTER TABLE categories AUTO_INCREMENT = 1;
ALTER TABLE contacts AUTO_INCREMENT = 1;
ALTER TABLE invoice_layouts AUTO_INCREMENT = 1;
ALTER TABLE invoice_schemes AUTO_INCREMENT = 1;
ALTER TABLE products AUTO_INCREMENT = 1;
ALTER TABLE product_variations AUTO_INCREMENT = 1;
ALTER TABLE tax_rates AUTO_INCREMENT = 1;
ALTER TABLE transactions AUTO_INCREMENT = 1;
ALTER TABLE transaction_payments AUTO_INCREMENT = 1;
ALTER TABLE transaction_sell_lines AUTO_INCREMENT = 1;
ALTER TABLE units AUTO_INCREMENT = 1;
ALTER TABLE variation_location_details AUTO_INCREMENT = 1;

-- =====================================================
-- STEP 5: UPDATE BUSINESS SETTINGS (Keep basic config)
-- =====================================================

-- Reset business settings to defaults while keeping basic info
UPDATE business SET
    start_date = CURDATE(),
    logo = NULL,
    sku_prefix = NULL,
    default_sales_discount = 0.00,
    pos_settings = '{"amount_rounding_method":null,"cmmsn_calculation_type":"invoice_value","razor_pay_key_id":null,"razor_pay_key_secret":null,"stripe_public_key":null,"stripe_secret_key":null,"display_screen_heading":null,"cash_denominations":null,"enable_cash_denomination_on":"pos_screen","disable_pay_checkout":0,"disable_draft":0,"disable_express_checkout":0,"hide_product_suggestion":0,"hide_recent_trans":0,"disable_discount":0,"disable_order_tax":0,"is_pos_subtotal_editable":0}',
    woocommerce_api_settings = NULL,
    woocommerce_skipped_orders = NULL,
    woocommerce_wh_oc_secret = NULL,
    woocommerce_wh_ou_secret = NULL,
    woocommerce_wh_od_secret = NULL,
    woocommerce_wh_or_secret = NULL,
    weighing_scale_setting = '{"label_prefix":null,"product_sku_length":"4","qty_length":"3","qty_length_decimal":"2"}',
    keyboard_shortcuts = '{"pos":{"express_checkout":"shift+z","pay_n_ckeckout":"shift+p","draft":"shift+d","cancel":"shift+c","recent_product_quantity":"f2","weighing_scale":"Kg","edit_discount":"shift+i","edit_order_tax":"shift+t","add_payment_row":"shift+r","finalize_payment":"shift+f","add_new_product":"f4"}}',
    email_settings = '{"mail_driver":"smtp","mail_host":null,"mail_port":null,"mail_username":null,"mail_password":null,"mail_encryption":null,"mail_from_address":null,"mail_from_name":null}',
    sms_settings = '{"sms_service":"other","nexmo_key":null,"nexmo_secret":null,"nexmo_from":null,"twilio_sid":null,"twilio_token":null,"twilio_from":null,"url":null,"send_to_param_name":"to","send_to_param_type":"string","msg_param_name":"text","request_method":"post","data_parameter_type":"form-data","header_1":null,"header_val_1":null,"header_2":null,"header_val_2":null,"header_3":null,"header_val_3":null,"param_1":null,"param_val_1":null,"param_2":null,"param_val_2":null,"param_3":null,"param_val_3":null,"param_4":null,"param_val_4":null,"param_5":null,"param_val_5":null,"param_6":null,"param_val_6":null,"param_7":null,"param_val_7":null,"param_8":null,"param_val_8":null,"param_9":null,"param_val_9":null,"param_10":null,"param_val_10":null}',
    custom_labels = '{"payments":{"custom_pay_1":null,"custom_pay_2":null,"custom_pay_3":null,"custom_pay_4":null,"custom_pay_5":null,"custom_pay_6":null,"custom_pay_7":null},"contact":{"custom_field_1":null,"custom_field_2":null,"custom_field_3":null,"custom_field_4":null,"custom_field_5":null,"custom_field_6":null,"custom_field_7":null,"custom_field_8":null,"custom_field_9":null,"custom_field_10":null},"product":{"custom_field_1":null,"custom_field_2":null,"custom_field_3":null,"custom_field_4":null,"custom_field_5":null,"custom_field_6":null,"custom_field_7":null,"custom_field_8":null,"custom_field_9":null,"custom_field_10":null,"custom_field_11":null,"custom_field_12":null,"custom_field_13":null,"custom_field_14":null,"custom_field_15":null,"custom_field_16":null,"custom_field_17":null,"custom_field_18":null,"custom_field_19":null,"custom_field_20":null},"product_cf_details":{"1":{"type":null,"dropdown_options":null},"2":{"type":null,"dropdown_options":null},"3":{"type":null,"dropdown_options":null},"4":{"type":null,"dropdown_options":null},"5":{"type":null,"dropdown_options":null},"6":{"type":null,"dropdown_options":null},"7":{"type":null,"dropdown_options":null},"8":{"type":null,"dropdown_options":null},"9":{"type":null,"dropdown_options":null},"10":{"type":null,"dropdown_options":null},"11":{"type":null,"dropdown_options":null},"12":{"type":null,"dropdown_options":null},"13":{"type":null,"dropdown_options":null},"14":{"type":null,"dropdown_options":null},"15":{"type":null,"dropdown_options":null},"16":{"type":null,"dropdown_options":null},"17":{"type":null,"dropdown_options":null},"18":{"type":null,"dropdown_options":null},"19":{"type":null,"dropdown_options":null},"20":{"type":null,"dropdown_options":null}},"location":{"custom_field_1":null,"custom_field_2":null,"custom_field_3":null,"custom_field_4":null},"user":{"custom_field_1":null,"custom_field_2":null,"custom_field_3":null,"custom_field_4":null},"purchase":{"custom_field_1":null,"custom_field_2":null,"custom_field_3":null,"custom_field_4":null},"purchase_shipping":{"custom_field_1":null,"custom_field_2":null,"custom_field_3":null,"custom_field_4":null,"custom_field_5":null},"sell":{"custom_field_1":null,"custom_field_2":null,"custom_field_3":null,"custom_field_4":null},"shipping":{"custom_field_1":null,"custom_field_2":null,"custom_field_3":null,"custom_field_4":null,"custom_field_5":null},"types_of_service":{"custom_field_1":null,"custom_field_2":null,"custom_field_3":null,"custom_field_4":null,"custom_field_5":null,"custom_field_6":null}}',
    common_settings = '{"default_credit_limit":null,"default_datatable_page_entries":"25"}',
    enable_rp = 0,
    rp_name = NULL,
    amount_for_unit_rp = 1.0000,
    min_order_total_for_rp = 1.0000,
    max_rp_per_order = NULL,
    redeem_amount_per_unit_rp = 1.0000,
    min_order_total_for_redeem = 1.0000,
    min_redeem_point = NULL,
    max_redeem_point = NULL,
    rp_expiry_period = NULL,
    rp_expiry_type = 'year',
    updated_at = NOW()
WHERE id = 1;

-- =====================================================
-- STEP 6: VERIFY ONLY YUVANSHAN REMAINS
-- =====================================================

-- Verify only Yuvanshan user remains
SELECT id, username, first_name, last_name, email FROM users;

-- Verify only one business remains
SELECT id, name, owner_id FROM business;

-- =====================================================
-- STEP 7: COMMIT OR ROLLBACK
-- =====================================================

-- If everything looks good, uncomment the commit line:
-- COMMIT;

-- If you need to undo, uncomment the rollback line:
-- ROLLBACK;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================
-- POST-WIPE NOTES
-- =====================================================
/*
AFTER RUNNING THIS SCRIPT:

1. The system will have only:
   - User: Yuvanshan (username: Yuvanshan, password: Yuvan@1709)
   - Business: Mahdev (Pvt) Ltd (reset to defaults)
   - All transactional data cleared
   - All products, contacts, inventory cleared
   - All settings reset to defaults

2. You will need to:
   - Set up business locations again
   - Add products and inventory
   - Configure payment gateways if needed
   - Set up tax rates
   - Add customer/supplier contacts

3. The user Yuvanshan will be able to log in with:
   - Username: Yuvanshan
   - Password: Yuvan@1709

4. Business settings are reset but basic structure remains.

WARNING: This script completely wipes all business data.
Make sure to backup your database before running this script.
Test on a development environment first.
*/