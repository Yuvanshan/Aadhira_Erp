<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt — {{$receipt_details->invoice_no}}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:ital,wght@0,300;0,400;0,500;1,300&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --sp: 22px;        /* screen side padding */
            --pp: 6mm;         /* print side padding — safe inside Epson TM-T81III non-print zone */
            --ink:   #1a1a1a;
            --ink2:  #444444;
            --ink3:  #888888;
            --ink4:  #bbbbbb;
            --paper: #ffffff;
            --bg:    #d8d4ce;
            --rule:  #d0cdc8;
            --rule2: #1a1a1a;
            --accent:#1a1a1a;
        }

        @page {
            margin: 0;
        }

        body {
            background: var(--bg);
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: auto;
            margin: 0;
            padding: 0;
            font-family: 'DM Mono', 'Courier New', monospace;
            font-size: 10.5px;
            color: var(--ink);
            line-height: 1.45;
            -webkit-font-smoothing: antialiased;
        }

        /* ══════════════════════════════
           RECEIPT PAPER
           Epson TM-T81III: 80mm roll
           ~72mm printable ≈ 272px @96dpi
        ══════════════════════════════ */
        .receipt {
            background: var(--paper);
            width: 300px;
            display: flex;
            flex-direction: column;
            box-shadow:
                0 1px 2px rgba(0,0,0,.06),
                0 4px 12px rgba(0,0,0,.10),
                0 12px 32px rgba(0,0,0,.08);
        }

        /* ══ PERFORATED EDGE — TOP ══ */
        .edge-top {
            display: block; width: 100%; height: 10px; flex-shrink: 0;
            background: radial-gradient(circle at 5px -2px, var(--bg) 5.5px, var(--paper) 6px);
            background-size: 9px 10px;
            background-repeat: repeat-x;
        }

        /* ══ PERFORATED EDGE — BOTTOM ══ */
        .edge-bottom {
            display: block; width: 100%; height: 10px; flex-shrink: 0;
            background: radial-gradient(circle at 5px 12px, var(--bg) 5.5px, var(--paper) 6px);
            background-size: 9px 10px;
            background-repeat: repeat-x;
        }

        /* ══ INNER WRAPPER ══ */
        .inner { padding: 5px 0 4px; }

        /* ══ SECTION PADDING ══ */
        .s  { padding: 0 var(--sp); }
        .st { padding: 0 var(--sp); padding-top: 2px; }
        .sb { padding: 0 var(--sp); padding-bottom: 2px; }

        /* ══ DIVIDERS ══ */
        .rule-thick {
            height: 2px;
            background: var(--rule2);
            margin: 4px var(--sp);
        }
        .rule-thin {
            height: 1px;
            background: var(--rule);
            margin: 3px var(--sp);
        }
        .rule-dashed {
            border: none;
            border-top: 1px dashed var(--rule4, #ccc);
            margin: 3px var(--sp);
        }
        .rule-double {
            border: none;
            border-top: 3px double var(--rule2);
            margin: 4px var(--sp);
        }

        /* ══ STORE HEADER ══ */
        .header {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 2px var(--sp) 4px;
        }

        .logo-ring {
            flex-shrink: 0;
            width: 48px; height: 48px;
            border-radius: 50%;
            border: 2px solid var(--ink);
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
            background: var(--paper);
        }
        .logo-ring img { width: 100%; height: 100%; object-fit: cover; }
        .logo-letter {
            font-family: 'DM Sans', sans-serif;
            font-size: 22px; font-weight: 700;
            color: var(--ink);
            line-height: 1;
        }

        .store-meta { flex: 1; min-width: 0; text-align: right; }

        .store-name {
            font-family: 'DM Sans', sans-serif;
            font-size: 13px; font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--ink);
            line-height: 1.2;
            margin-bottom: 2px;
            word-break: break-word;
        }

        .store-detail {
            font-size: 8.5px;
            color: var(--ink2);
            line-height: 1.7;
            word-break: break-word;
        }
        .store-detail .tax-id {
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
            color: var(--ink);
            letter-spacing: 0.3px;
        }

        /* ══ RECEIPT BADGE — invoice no + date ══ */
        .receipt-meta {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            padding: 4px var(--sp);
        }

        .inv-block {}
        .inv-label {
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--ink3);
            margin-bottom: 2px;
        }
        .inv-number {
            font-family: 'DM Sans', sans-serif;
            font-size: 12px; font-weight: 700;
            color: var(--ink);
            letter-spacing: 0.3px;
            word-break: break-all;
        }
        .inv-sub {
            font-size: 8.5px;
            color: var(--ink2);
            line-height: 1.7;
            margin-top: 1px;
        }

        .date-block { text-align: right; flex-shrink: 0; }
        .date-label {
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--ink3);
            margin-bottom: 2px;
        }
        .date-value {
            font-family: 'DM Sans', sans-serif;
            font-size: 10px; font-weight: 600;
            color: var(--ink);
            line-height: 1.4;
        }

        /* ══ ITEMS TABLE ══ */
        .items-wrap {
            padding: 0 var(--sp);
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 9.5px;
        }

        .items-table thead tr {
            border-bottom: 1.5px solid var(--ink);
        }
        .items-table thead th {
            padding: 2px 2px 2px 0;
            font-family: 'DM Sans', sans-serif;
            font-size: 8px; font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--ink3);
        }
        .items-table thead th:nth-child(1) { text-align: left;   width: 7%;  }
        .items-table thead th:nth-child(2) { text-align: left;   width: 39%; padding-left: 4px; }
        .items-table thead th:nth-child(3) { text-align: center; width: 12%; }
        .items-table thead th:nth-child(4) { text-align: right;  width: 19%; }
        .items-table thead th:nth-child(5) { text-align: right;  width: 23%; }

        .items-table tbody tr {
            border-bottom: 1px solid var(--rule);
        }
        .items-table tbody tr:last-child { border-bottom: none; }

        .items-table tbody td {
            padding: 2px 2px 2px 0;
            vertical-align: top;
            color: var(--ink);
        }
        .items-table tbody td:nth-child(1) {
            text-align: left; color: var(--ink4); font-size: 8px;
        }
        .items-table tbody td:nth-child(2) {
            text-align: left; font-weight: 500;
            word-break: break-word; padding-left: 4px;
        }
        .items-table tbody td:nth-child(3) { text-align: center; color: var(--ink2); }
        .items-table tbody td:nth-child(4) { text-align: right;  color: var(--ink2); }
        .items-table tbody td:nth-child(5) {
            text-align: right;
            font-family: 'DM Sans', sans-serif;
            font-weight: 600;
        }

        .item-note {
            display: block;
            font-size: 8px; font-weight: 400;
            color: var(--ink3);
            margin-top: 0;
            font-style: italic;
        }

        /* ══ TOTALS SECTION ══ */
        .totals { padding: 0 var(--sp); }

        .t-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            padding: 1px 0;
            font-size: 9.5px;
        }
        .t-row .tl { color: var(--ink2); flex: 1; min-width: 0; word-break: break-word; }
        .t-row .tv { font-weight: 500; flex-shrink: 0; white-space: nowrap; color: var(--ink); }
        .t-row.dim .tl,
        .t-row.dim .tv { color: var(--ink3); }

        /* ══ GRAND TOTAL BAR ══ */
        .grand-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            background: var(--ink);
            color: var(--paper);
            padding: 6px var(--sp);
            margin: 5px 0 3px;
        }
        .grand-bar .gl {
            font-family: 'DM Sans', sans-serif;
            font-size: 9px; font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }
        .grand-bar .gv {
            font-family: 'DM Sans', sans-serif;
            font-size: 15px; font-weight: 700;
            flex-shrink: 0;
        }

        /* ══ PAYMENT ROWS ══ */
        .paid-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            padding: 1px 0;
            font-size: 9.5px;
        }
        .paid-header .tl {
            font-family: 'DM Sans', sans-serif;
            font-weight: 600; color: var(--ink);
        }
        .paid-header .tv {
            font-family: 'DM Sans', sans-serif;
            font-weight: 600; color: var(--ink);
            flex-shrink: 0; white-space: nowrap;
        }

        .pay-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 8px;
            padding: 1px 0 1px 12px;
            font-size: 9px;
        }
        .pay-row .pl { color: var(--ink2); flex: 1; min-width: 0; word-break: break-word; }
        .pay-row .pv { font-weight: 500; color: var(--ink); flex-shrink: 0; white-space: nowrap; }

        /* Tiny arrow prefix on each payment line */
        .pay-row .pl::before {
            content: "↳ ";
            color: var(--ink4);
            font-size: 8px;
        }

        /* ══ BALANCE BOX ══ */
        .balance-box {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
            border: 1.5px solid var(--ink);
            padding: 4px 7px;
            margin-top: 3px;
        }
        .balance-box .bl {
            font-family: 'DM Sans', sans-serif;
            font-size: 10px; font-weight: 600;
            color: var(--ink);
            flex: 1; min-width: 0;
            word-break: break-word;
        }
        .balance-box .bv {
            font-family: 'DM Sans', sans-serif;
            font-size: 11px; font-weight: 700;
            color: var(--ink);
            flex-shrink: 0; white-space: nowrap;
        }

        /* ══ TOTAL IN WORDS ══ */
        .words-note {
            text-align: right;
            font-size: 7.5px;
            color: var(--ink3);
            font-style: italic;
            margin-bottom: 0;
            padding-right: 1px;
        }

        /* ══ TAX SUMMARY ══ */
        .tax-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        .tax-table th {
            text-align: center;
            padding: 1px 0 2px;
            font-family: 'DM Sans', sans-serif;
            font-size: 8px; font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--ink3);
        }
        .tax-table td {
            padding: 0.5px 0;
            color: var(--ink2);
        }
        .tax-table td:last-child {
            text-align: right;
            font-weight: 500;
            color: var(--ink);
        }

        /* ══ BARCODE / QR ══ */
        .code-wrap {
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            text-align: center;
            padding: 5px var(--sp) 2px;
        }
        .code-wrap img { display: block; margin: 0 auto; max-width: 100%; height: auto; }
        .code-no {
            font-size: 8px; letter-spacing: 2.5px;
            color: var(--ink3); margin-top: 1px;
        }

        /* ══ THANK YOU ══ */
        .footer-msg {
            text-align: center;
            padding: 5px var(--sp) 3px;
        }
        .ty-line {
            display: flex; align-items: center; gap: 8px;
            justify-content: center; margin-bottom: 3px;
        }
        .ty-rule { flex: 1; height: 1px; background: var(--rule); }
        .ty-text {
            font-family: 'DM Sans', sans-serif;
            font-size: 9.5px; font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            color: var(--ink);
            white-space: nowrap;
        }
        .ty-note {
            font-size: 8.5px;
            color: var(--ink3);
            line-height: 1.6;
        }

        /* ══ SYSTEM CREDIT ══ */
        .sys-credit {
            text-align: center;
            padding: 4px var(--sp) 2px;
            border-top: 1px solid var(--rule);
            margin-top: 3px;
        }
        .sys-by   { font-size: 7.5px; color: var(--ink4); letter-spacing: 0.5px; }
        .sys-name {
            font-family: 'DM Sans', sans-serif;
            font-size: 9.5px; font-weight: 700;
            color: var(--ink); margin: 2px 0 1px;
            letter-spacing: 0.3px;
        }
        .sys-tel  { font-size: 8.5px; color: var(--ink2); }

        /* ══ PRINT OVERRIDES ══ */
        @media print {
            body {
                background: none;
                padding: 0; margin: 0;
            }
            .receipt {
                width: 72mm;
                box-shadow: none;
            }
            .edge-top, .edge-bottom { display: none; }
            .inner { padding: 1.5mm 0; }

            /* Swap every spacing reference to print variable */
            .s, .st, .sb        { padding-left: var(--pp); padding-right: var(--pp); }
            .header             { padding-left: var(--pp); padding-right: var(--pp); }
            .receipt-meta       { padding-left: var(--pp); padding-right: var(--pp); }
            .items-wrap         { padding-left: var(--pp); padding-right: var(--pp); }
            .totals             { padding-left: var(--pp); padding-right: var(--pp); }
            .grand-bar          { padding-left: var(--pp); padding-right: var(--pp); }
            .code-wrap          { padding-left: var(--pp); padding-right: var(--pp); }
            .footer-msg         { padding-left: var(--pp); padding-right: var(--pp); }
            .sys-credit         { padding-left: var(--pp); padding-right: var(--pp); }

            .rule-thick         { margin-left: var(--pp); margin-right: var(--pp); }
            .rule-thin          { margin-left: var(--pp); margin-right: var(--pp); }
            .rule-dashed        { margin-left: var(--pp); margin-right: var(--pp); }
            .rule-double        { margin-left: var(--pp); margin-right: var(--pp); }

            /* Balance box: reset to zero extra margin inside .s on print */
            .balance-box        { margin-top: 2px; padding: 3px 4px; }

            /* Keep grand bar flush */
            .grand-bar          { margin: 3px 0 2px; }
        }
    </style>
</head>
<body>
<div class="receipt">

    <span class="edge-top"></span>

    <div class="inner">

        {{-- ══════════════════════
             HEADER
        ══════════════════════ --}}
        <div class="header">
            <div class="logo-ring">
                @if(!empty($receipt_details->letter_head))
                    <img src="{{$receipt_details->letter_head}}" alt="logo">
                @elseif(!empty($receipt_details->logo))
                    <img src="{{$receipt_details->logo}}" alt="logo">
                @else
                    <span class="logo-letter">{{ strtoupper(substr($receipt_details->display_name ?? 'S', 0, 1)) }}</span>
                @endif
            </div>
            <div class="store-meta">
                @if(!empty($receipt_details->display_name))
                    <div class="store-name">{{$receipt_details->display_name}}</div>
                @endif
                <div class="store-detail">
                    @if(!empty($receipt_details->address)){!! $receipt_details->address !!}<br>@endif
                    @if(!empty($receipt_details->contact)){!! $receipt_details->contact !!}@endif
                    @if(!empty($receipt_details->contact) && !empty($receipt_details->website)), @endif
                    @if(!empty($receipt_details->website)){{$receipt_details->website}}<br>@endif
                    @if(!empty($receipt_details->sub_heading_line1)){{$receipt_details->sub_heading_line1}}<br>@endif
                    @if(!empty($receipt_details->sub_heading_line2)){{$receipt_details->sub_heading_line2}}<br>@endif
                    @if(!empty($receipt_details->sub_heading_line3)){{$receipt_details->sub_heading_line3}}<br>@endif
                    @if(!empty($receipt_details->sub_heading_line4)){{$receipt_details->sub_heading_line4}}<br>@endif
                    @if(!empty($receipt_details->sub_heading_line5)){{$receipt_details->sub_heading_line5}}<br>@endif
                    @if(!empty($receipt_details->location_custom_fields)){{$receipt_details->location_custom_fields}}<br>@endif
                    @if(!empty($receipt_details->tax_info1))
                        <span class="tax-id">{{$receipt_details->tax_label1}}: {{$receipt_details->tax_info1}}</span><br>
                    @endif
                    @if(!empty($receipt_details->tax_info2))
                        <span class="tax-id">{{$receipt_details->tax_label2}}: {{$receipt_details->tax_info2}}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="rule-thick"></div>

        {{-- ══════════════════════
             INVOICE META
        ══════════════════════ --}}
        <div class="receipt-meta">
            <div class="inv-block">
                <div class="inv-label">Receipt No.</div>
                <div class="inv-number">{!! $receipt_details->invoice_no_prefix !!}{{$receipt_details->invoice_no}}</div>
                <div class="inv-sub">
                    @if(!empty($receipt_details->sales_person))
                        {{$receipt_details->sales_person_label}}: {{$receipt_details->sales_person}}<br>
                    @endif
                    @if(!empty($receipt_details->service_staff))
                        {!! $receipt_details->service_staff_label !!}: {{$receipt_details->service_staff}}<br>
                    @endif
                    @if(!empty($receipt_details->commission_agent))
                        {{$receipt_details->commission_agent_label}}: {{$receipt_details->commission_agent}}<br>
                    @endif
                    @if(!empty($receipt_details->table))
                        {!! $receipt_details->table_label !!}: {{$receipt_details->table}}<br>
                    @endif
                    @if(!empty($receipt_details->customer_info))
                        {{$receipt_details->customer_label ?? 'Customer'}}: {!! $receipt_details->customer_info !!}<br>
                    @endif
                    @if(!empty($receipt_details->sell_custom_field_1_value))
                        {!! $receipt_details->sell_custom_field_1_label !!}: {{$receipt_details->sell_custom_field_1_value}}<br>
                    @endif
                    @if(!empty($receipt_details->sell_custom_field_2_value))
                        {!! $receipt_details->sell_custom_field_2_label !!}: {{$receipt_details->sell_custom_field_2_value}}<br>
                    @endif
                </div>
            </div>
            <div class="date-block">
                <div class="date-label">{!! $receipt_details->date_label !!}</div>
                <div class="date-value">{{$receipt_details->invoice_date}}</div>
                @if(!empty($receipt_details->due_date_label))
                <div style="margin-top:5px;">
                    <div class="date-label">{{$receipt_details->due_date_label}}</div>
                    <div class="date-value">{{$receipt_details->due_date ?? ''}}</div>
                </div>
                @endif
            </div>
        </div>

        <div class="rule-double"></div>

        {{-- ══════════════════════
             ITEMS TABLE
        ══════════════════════ --}}
        <div class="items-wrap">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receipt_details->lines as $line)
                    <tr>
                        <td>{{$loop->iteration}}</td>
                        <td>
                            {{$line['name']}}
                            @if(!empty($line['variation']))<span class="item-note">{{$line['product_variation']}} {{$line['variation']}}</span>@endif
                            @if(!empty($line['sell_line_note']))<span class="item-note">{!!$line['sell_line_note']!!}</span>@endif
                            @if(!empty($line['product_description']))<span class="item-note">{!!$line['product_description']!!}</span>@endif
                            @if(!empty($line['lot_number']))<span class="item-note">{{$line['lot_number_label']}}: {{$line['lot_number']}}</span>@endif
                            @if(!empty($line['warranty_name']))<span class="item-note">{{$line['warranty_name']}}@if(!empty($line['warranty_exp_date'])) – {{@format_date($line['warranty_exp_date'])}}@endif</span>@endif
                        </td>
                        <td>{{$line['quantity']}}</td>
                        @if(empty($receipt_details->hide_price))
                        <td>{{$line['unit_price_before_discount']}}</td>
                        <td>{{$line['line_total']}}</td>
                        @else
                        <td></td><td></td>
                        @endif
                    </tr>
                    @if(!empty($line['modifiers']))
                        @foreach($line['modifiers'] as $mod)
                        <tr>
                            <td></td>
                            <td>+ {{$mod['name']}}@if(!empty($mod['sell_line_note']))<span class="item-note">{!!$mod['sell_line_note']!!}</span>@endif</td>
                            <td>{{$mod['quantity']}}</td>
                            @if(empty($receipt_details->hide_price))
                            <td>{{$mod['unit_price_inc_tax']}}</td>
                            <td>{{$mod['line_total']}}</td>
                            @else
                            <td></td><td></td>
                            @endif
                        </tr>
                        @endforeach
                    @endif
                    @empty
                    <tr><td colspan="5" style="text-align:center;padding:10px 0;color:var(--ink4);">No items</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="rule-thick"></div>

        {{-- ══════════════════════
             TOTALS
        ══════════════════════ --}}
        @if(empty($receipt_details->hide_price))

        @php
            $customer_paid_amount = null;
            $change_return_amount = 0;
            $due_amount = 0;

            if (!empty($receipt_details->total_due)) {
                $due_amount = (float) preg_replace('/[^\d.\-]/', '', (string) $receipt_details->total_due);
            }

            if (!empty($receipt_details->payments) && is_iterable($receipt_details->payments)) {
                foreach ($receipt_details->payments as $payment) {
                    $payment_method = is_array($payment) ? ($payment['method'] ?? '') : ($payment->method ?? '');
                    $payment_amount = is_array($payment) ? ($payment['amount'] ?? 0) : ($payment->amount ?? 0);
                    $payment_amount_numeric = (float) preg_replace('/[^\d.\-]/', '', (string) $payment_amount);
                    $is_change_return = stripos(strip_tags((string) $payment_method), 'change return') !== false
                        || strpos((string) $payment_method, '(-)') !== false;

                    if ($is_change_return) {
                        $change_return_amount += $payment_amount_numeric;
                    } else {
                        $customer_paid_amount = ($customer_paid_amount ?? 0) + $payment_amount_numeric;
                    }
                }
            }

            $show_due_row = $due_amount > 0;
            $display_total_paid = !is_null($customer_paid_amount)
                ? $customer_paid_amount
                : (float) preg_replace('/[^\d.\-]/', '', (string) ($receipt_details->total_paid ?? 0));
        @endphp

        <div class="totals">
            <div class="t-row">
                <span class="tl">{!! $receipt_details->subtotal_label !!}</span>
                <span class="tv">{{$receipt_details->subtotal}}</span>
            </div>

            @if(!empty($receipt_details->discount))
            <div class="t-row dim">
                <span class="tl">{!! $receipt_details->discount_label !!}</span>
                <span class="tv">(-) {{$receipt_details->discount}}</span>
            </div>
            @endif

            @if(!empty($receipt_details->total_line_discount))
            <div class="t-row dim">
                <span class="tl">{!! $receipt_details->line_discount_label !!}</span>
                <span class="tv">(-) {{$receipt_details->total_line_discount}}</span>
            </div>
            @endif
        </div>

        {{-- ── GRAND TOTAL BAR — full bleed ── --}}
        <div class="grand-bar">
            <span class="gl">{!! $receipt_details->total_label !!}</span>
            <span class="gv">{{$receipt_details->total}}</span>
        </div>

        <div class="totals">

            @if(!empty($receipt_details->total_in_words))
            <div class="words-note">({{$receipt_details->total_in_words}})</div>
            @endif

            <div class="paid-header">
                <span class="tl">Total Paid</span>
                <span class="tv">@format_currency($display_total_paid)</span>
            </div>

            <div class="balance-box">
                <span class="bl">Balance</span>
                <span class="bv">@format_currency($change_return_amount)</span>
            </div>

            @if($show_due_row)
            <div class="t-row" style="margin-top:3px;">
                <span class="tl">Due Amount</span>
                <span class="tv">{{$receipt_details->total_due}}</span>
            </div>
            @endif

        </div>

        {{-- Tax summary --}}
        @if(!empty($receipt_details->tax_summary_label) && !empty($receipt_details->taxes))
        <div class="rule-dashed" style="margin-top:8px;"></div>
        <div class="totals" style="padding-top:2px;">
            <table class="tax-table">
                <thead>
                    <tr><th colspan="2">{{$receipt_details->tax_summary_label}}</th></tr>
                </thead>
                <tbody>
                    @foreach($receipt_details->taxes as $key => $val)
                    <tr>
                        <td>{{$key}}</td>
                        <td>{{$val}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        @endif {{-- end hide_price --}}

        <div class="rule-dashed" style="margin-top:8px;"></div>

        {{-- ══════════════════════
             BARCODE / QR
        ══════════════════════ --}}
        @if($receipt_details->show_barcode || ($receipt_details->show_qr_code && !empty($receipt_details->qr_code_text)))
        <div class="code-wrap">
            @if($receipt_details->show_barcode)
                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2, 28, array(26, 26, 26), true)}}" alt="barcode">
                <div class="code-no">{{$receipt_details->invoice_no}}</div>
            @endif
            @if($receipt_details->show_qr_code && !empty($receipt_details->qr_code_text))
                <img src="data:image/png;base64,{{DNS2D::getBarcodePNG($receipt_details->qr_code_text, 'QRCODE')}}" alt="qrcode" style="width:76px;height:76px;margin-top:6px;">
            @endif
        </div>
        <div class="rule-thin"></div>
        @endif

        {{-- ══════════════════════
             THANK YOU
        ══════════════════════ --}}
        <div class="footer-msg">
            <div class="ty-line">
                <div class="ty-rule"></div>
                <div class="ty-text">Thank You</div>
                <div class="ty-rule"></div>
            </div>
            @if(!empty($receipt_details->additional_notes))
                <div class="ty-note">{!! nl2br($receipt_details->additional_notes) !!}</div>
            @else
                <div class="ty-note">We appreciate your business. Please visit us again.</div>
            @endif
        </div>

        {{-- ══════════════════════
             SYSTEM CREDIT
        ══════════════════════ --}}
        <div class="sys-credit">
            <div class="sys-by">Powered by</div>
            <div class="sys-name">Mahdev (Pvt) Ltd</div>
            <div class="sys-tel">075 092 8078 &nbsp;·&nbsp; 076 898 8970</div>
        </div>

        <div style="height:12px;"></div>

    </div>{{-- /.inner --}}

    <span class="edge-bottom"></span>

</div>
</body>
</html>
