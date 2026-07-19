<style>
    body.lockscreen {
        background:
            radial-gradient(circle at top left, rgba(14, 165, 233, 0.10), transparent 24%),
            radial-gradient(circle at top right, rgba(37, 99, 235, 0.10), transparent 20%),
            linear-gradient(180deg, #f8fafc 0%, #eef4ff 40%, #f8fafc 100%);
    }

    .pos-header > div:first-child {
        border: 1px solid rgba(148, 163, 184, 0.18);
        box-shadow: 0 20px 44px rgba(15, 23, 42, 0.08) !important;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.96)) !important;
    }

    .pos-header p,
    .pos-header strong,
    .pos-header span,
    .pos-header label {
        color: #0f172a;
    }

    .pos-header .curr_datetime {
        letter-spacing: 0.01em;
    }

    .pos-header a,
    .pos-header button {
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease, background-color 0.18s ease;
    }

    .pos-header a:hover,
    .pos-header button:hover {
        transform: translateY(-1px);
        box-shadow: 0 12px 24px rgba(15, 23, 42, 0.10);
        border-color: rgba(59, 130, 246, 0.24) !important;
    }

    #add_pos_sell_form > .row.mb-12 > .col-md-12 > .row > div:first-child > div,
    #edit_pos_sell_form > .row.mb-12 > .col-md-12 > .row > div:first-child > div {
        border: 1px solid rgba(148, 163, 184, 0.16);
        box-shadow: 0 24px 56px rgba(15, 23, 42, 0.08) !important;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%) !important;
    }

    .pos-form-actions {
        border: 1px solid rgba(148, 163, 184, 0.16);
        box-shadow: 0 20px 44px rgba(15, 23, 42, 0.08) !important;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.96)) !important;
    }

    .pos-form-actions button {
        transition: transform 0.18s ease, filter 0.18s ease, box-shadow 0.18s ease;
    }

    .pos-form-actions button:hover {
        transform: translateY(-1px);
        filter: saturate(1.05);
    }

    #customer_id,
    #search_product,
    #transaction_date,
    #commission_agent,
    #price_group,
    #invoice_layout_id,
    #invoice_scheme_id,
    #types_of_service_id,
    #select_location_id,
    .pos-header .form-control,
    .pos-header .select2-selection,
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple,
    .form-control {
        border-color: #d7deea;
        border-radius: 14px !important;
        box-shadow: none !important;
    }

    .form-control,
    .select2-container--default .select2-selection--single,
    .select2-container--default .select2-selection--multiple {
        min-height: 42px;
        background: #fff;
    }

    .input-group-addon,
    .input-group-btn > .btn {
        border-color: #d7deea;
        background: #f8fafc;
    }

    .input-group-addon:first-child {
        border-top-left-radius: 14px;
        border-bottom-left-radius: 14px;
    }

    .input-group .form-control:last-child,
    .input-group-btn:last-child > .btn {
        border-top-right-radius: 14px;
        border-bottom-right-radius: 14px;
    }

    .form-control:focus,
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #60a5fa !important;
        box-shadow: 0 0 0 4px rgba(96, 165, 250, 0.14) !important;
    }

    .pos_product_div {
        margin-top: 8px;
    }

    #pos_table {
        border-collapse: separate;
        border-spacing: 0;
        overflow: hidden;
        border-radius: 18px;
        border: 1px solid #e2e8f0;
        background: #fff;
    }

    #pos_table thead th {
        background: linear-gradient(180deg, #eff6ff 0%, #e8f0ff 100%);
        color: #0f172a;
        border-bottom: 1px solid #dbe5f3;
        padding-top: 14px !important;
        padding-bottom: 14px !important;
    }

    #pos_table tbody td {
        border-top-color: #edf2f7 !important;
        vertical-align: middle !important;
        background: #fff;
    }

    #pos_table tbody tr:nth-child(even) td {
        background: #fcfdff;
    }

    .pos_form_totals table {
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        overflow: hidden;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
    }

    .pos_form_totals td {
        padding-top: 14px !important;
        padding-bottom: 14px !important;
        border-top-color: #e9eef5 !important;
    }

    #product_list_body {
        margin-top: 14px;
        padding: 6px;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.94), rgba(248, 250, 252, 0.92));
        min-height: 220px;
    }

    .product_list {
        margin-bottom: 14px;
    }

    .product_box {
        height: 100%;
        padding: 12px;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        background: linear-gradient(180deg, #ffffff 0%, #f8fbff 100%);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.05);
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }

    .product_box:hover {
        transform: translateY(-2px);
        border-color: rgba(59, 130, 246, 0.28);
        box-shadow: 0 16px 30px rgba(15, 23, 42, 0.10);
    }

    .product_box .image-container {
        height: 110px;
        margin-bottom: 12px;
        border-radius: 14px;
        background-color: #f8fafc;
    }

    .product_box .text_div {
        line-height: 1.45;
    }

    .product_box .text_div .text {
        color: #0f172a !important;
        font-weight: 600;
    }

    .product_box .text_div .text-muted {
        color: #64748b !important;
    }

    #featured_products_box {
        margin-bottom: 12px;
        padding: 10px 8px 0;
        border: 1px solid #e2e8f0;
        border-radius: 22px;
        background: rgba(255, 255, 255, 0.88);
    }

    .tw-dw-drawer-content > label,
    #show_featured_products {
        box-shadow: 0 12px 24px rgba(37, 99, 235, 0.12);
    }

    .modal-content {
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 22px;
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.16);
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
        border-bottom-color: #e2e8f0;
    }

    @media (max-width: 991px) {
        #product_list_body {
            padding: 4px;
        }

        .product_box .image-container {
            height: 92px;
        }
    }
</style>
