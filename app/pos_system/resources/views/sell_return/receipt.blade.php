<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Receipt - {{$receipt_details->invoice_no}}</title>
    <style>
        /*
         Responsive thermal receipt style for 80mm and 58mm inline printers.
         - Primary widths used:
           * 80mm -> target printable width: 384px
           * 58mm -> target printable width: 288px
         - Uses percentage column widths and media queries to adjust typography & spacing.
         - No JS, no function changes — only CSS & markup alignment.
        */

        :root{
            --font-family: "Helvetica", "Arial", sans-serif;
            --color-text: #000;
            --muted: #666;
            --accent-bg: #357ca5;
            --accent-color: #fff;
        }

        html,body{
            margin:0;
            padding:0;
            color:var(--color-text);
            font-family: var(--font-family);
            background: #fff;
        }

        /* Ticket container: default set to 384px (80mm). Will scale down for 58mm. */
        .ticket {
            width: 100%;
            max-width: 384px; /* 80mm target */
            margin: 0 auto;
            box-sizing: border-box;
            padding: 6px 8px; /* small padding for inline printers */
            font-size: 12px;
            line-height: 1.1;
            word-break: break-word;
        }

        /* Utility */
        .center { text-align:center; }
        .right { text-align:right; }
        .left { text-align:left; }
        .muted { color: var(--muted); }
        .bold { font-weight:700; }
        .small { font-size: 10px; }
        .xsmall { font-size: 9px; }
        .gap-sm { margin-top:6px; margin-bottom:6px; }

        img.logo {
            max-height:70px;
            width:auto;
            display:block;
            margin-bottom:6px;
        }

        /* Header area */
        .header-row { display:flex; justify-content:space-between; align-items:flex-start; gap:8px; }
        .header-left { width: 60%; }
        .header-right { width: 40%; text-align:right; }

        /* Product table */
        .products {
            width:100%;
            border-collapse: collapse;
            margin-top:6px;
            margin-bottom:6px;
            font-size: 12px;
        }

        .products thead tr {
            background: var(--accent-bg);
            color: var(--accent-color);
        }

        .products th, .products td {
            padding: 4px 3px;
            vertical-align: top;
            box-sizing: border-box;
            word-break: break-word;
        }

        /* Column widths tuned for 80mm */
        .col-no { width:6%; max-width: 6%; text-align:center; }
        .col-desc { width:48%; max-width:48%; text-align:left; }
        .col-price { width:16%; max-width:16%; text-align:right; }
        .col-qty { width:10%; max-width:10%; text-align:center; }
        .col-total { width:20%; max-width:20%; text-align:right; }

        /* Table header specific style */
        .products thead th { padding:6px 4px; font-size:13px; }

        /* Row helper for product details smaller text */
        .product-meta { display:block; color:var(--muted); font-size:10px; margin-top:2px; }

        /* Footer totals area */
        .totals { width:100%; margin-top:6px; border-top:1px solid #ddd; padding-top:6px; }
        .totals .row { display:flex; justify-content:space-between; margin-bottom:4px; }
        .totals .label { color:var(--muted); }
        .totals .value { font-weight:600; }

        /* Barcode / QR centering */
        .center-block { display:block; margin:8px auto; max-width:100%; }

        /* Dotted separator and small bottom spacing */
        .separator { border-bottom:1px dotted #ccc; margin:6px 0; }

        /*
         58mm adjustments:
         Many 58mm printers render at 288px width. We reduce font size, collapse paddings,
         and change column proportions to preserve readability.
        */
        @media (max-width: 320px), (max-device-width: 320px) {
            .ticket { max-width:288px; padding:6px 6px; font-size:11px; }
            .products th, .products td { padding:3px 2px; font-size:11px; }
            .products thead th { font-size:12px; padding:5px 2px; }
            .col-no { width:7%; }
            .col-desc { width:50%; }
            .col-price { width:18%; }
            .col-qty { width:8%; }
            .col-total { width:17%; }
            .product-meta { font-size:9px; }
            .totals .row { margin-bottom:3px; }
        }

        /* Print specific rules */
        @media print {
            .ticket { max-width:384px; padding:0; font-size:12px; }
            body,html { -webkit-print-color-adjust:exact; }
            a[href]:after { content: ""; } /* suppress url printing */
            .no-print { display:none !important; }
        }

        /* Keep long description wrapping nicely */
        .desc-wrap { white-space: normal; word-break: break-word; }

        /* Small visual improvements */
        .bg-accent {
            background: var(--accent-bg);
            color: var(--accent-color);
            padding:4px 6px;
            font-weight:700;
        }
    </style>
</head>
<body>
    <div class="ticket">
        <!-- Header -->
        <div class="header-row">
            <div class="header-left">
                @if(!empty($receipt_details->logo))
                    <img src="{{$receipt_details->logo}}" alt="Logo" class="logo">
                @endif

                @if(!empty($receipt_details->display_name))
                    <div class="bold">{{$receipt_details->display_name}}</div>
                @endif

                @if(!empty($receipt_details->address))
                    <div class="small muted">{!! $receipt_details->address !!}</div>
                @endif

                @if(!empty($receipt_details->contact) || !empty($receipt_details->website))
                    <div class="small muted gap-sm">
                        @if(!empty($receipt_details->contact)){!! $receipt_details->contact !!}@endif
                        @if(!empty($receipt_details->contact) && !empty($receipt_details->website)), @endif
                        @if(!empty($receipt_details->website)){{$receipt_details->website}}@endif
                    </div>
                @endif

                @if(!empty($receipt_details->location_custom_fields))
                    <div class="small muted">{!! $receipt_details->location_custom_fields !!}</div>
                @endif
            </div>

            <div class="header-right">
                @if(!empty($receipt_details->invoice_heading))
                    <div class="bold" style="font-size:16px;">{!! $receipt_details->invoice_heading !!}</div>
                @endif

                <div class="gap-sm">
                    <div class="small muted left">{!! $receipt_details->invoice_no_prefix ?? '' !!}</div>
                    <div class="bold" style="font-size:16px;">{{$receipt_details->invoice_no}}</div>
                </div>

                @if(!empty($receipt_details->invoice_date))
                    <div class="small muted"> {{$receipt_details->date_label ?? ''}} {{$receipt_details->invoice_date}}</div>
                @endif
            </div>
        </div>

        <div class="separator"></div>

        <!-- Customer & meta -->
        <div style="display:flex; gap:8px; margin-bottom:6px;">
            <div style="flex:1;">
                @if(!empty($receipt_details->customer_label))
                    <div class="small bold">{{$receipt_details->customer_label}}</div>
                @endif

                @if(!empty($receipt_details->customer_name))
                    <div class="small desc-wrap">{!! $receipt_details->customer_name !!}</div>
                @endif

                @if(!empty($receipt_details->customer_info))
                    <div class="small muted desc-wrap">{!! $receipt_details->customer_info !!}</div>
                @endif

                @if(!empty($receipt_details->client_id))
                    <div class="small muted">{{$receipt_details->client_id_label ?? ''}} {{$receipt_details->client_id}}</div>
                @endif
            </div>

            <div style="flex:0 0 38%; text-align:right;">
                @if(!empty($receipt_details->total_due))
                    <div class="bg-accent" style="display:inline-block; padding:4px 8px;">{!! $receipt_details->total_due_label !!} {{$receipt_details->total_due}}</div>
                @endif
            </div>
        </div>

        <!-- Products table header -->
        <table class="products" aria-label="Products">
            <thead>
                <tr>
                    <th class="col-no">No</th>
                    <th class="col-desc left">{{$receipt_details->table_product_label ?? 'Description'}}</th>
                    <th class="col-price">Price</th>
                    <th class="col-qty">Qty</th>
                    <th class="col-total">Total</th>
                </tr>
            </thead>

            <tbody>
                @foreach($receipt_details->lines as $line)
                    <tr>
                        <td class="col-no center">{{$loop->iteration}}</td>

                        <td class="col-desc left desc-wrap">
                            <div class="bold">{{$line['name']}} @if(!empty($line['variation'])) <span class="small muted"> - {{$line['variation']}}</span> @endif</div>

                            @if(!empty($line['product_description']))
                                <div class="product-meta">{!! $line['product_description'] !!}</div>
                            @endif

                            @if(!empty($line['sub_sku']))<div class="product-meta">SKU: {{$line['sub_sku']}}</div>@endif
                            @if(!empty($line['brand']))<div class="product-meta">Brand: {{$line['brand']}}</div>@endif

                            @if($receipt_details->show_cat_code == 1 && !empty($line['cat_code']))
                                <div class="product-meta">{{$receipt_details->cat_code_label}}: {{$line['cat_code']}}</div>
                            @endif

                            @if(!empty($line['sell_line_note']))
                                <div class="product-meta">{!! $line['sell_line_note'] !!}</div>
                            @endif

                            @if(!empty($line['lot_number']) || !empty($line['product_expiry']))
                                <div class="product-meta">
                                    @if(!empty($line['lot_number'])) {{$line['lot_number_label']}}: {{$line['lot_number']}} @endif
                                    @if(!empty($line['product_expiry'])) @if(!empty($line['lot_number'])) | @endif {{$line['product_expiry_label']}}: {{$line['product_expiry']}} @endif
                                </div>
                            @endif

                            @if($receipt_details->show_base_unit_details && $line['quantity'] && $line['base_unit_multiplier'] !== 1)
                                <div class="product-meta">
                                    1 {{$line['units']}} = {{$line['base_unit_multiplier']}} {{$line['base_unit_name']}} |
                                    {{$line['quantity']}} x {{$line['base_unit_multiplier']}} = {{$line['orig_quantity']}} {{$line['base_unit_name']}}
                                </div>
                            @endif
                        </td>

                        <td class="col-price right">
                            {{$line['unit_price_exc_tax'] ?? $line['unit_price_before_discount'] ?? ''}}
                            @if(!empty($line['total_line_discount']) && $line['total_line_discount'] != 0)
                                <div class="xsmall muted">- {{$line['total_line_discount']}}</div>
                            @endif
                        </td>

                        <td class="col-qty center">
                            {{$line['quantity']}} @if(!empty($line['units']))<div class="xsmall muted">{{$line['units']}}</div>@endif
                        </td>

                        <td class="col-total right">
                            {{$line['line_total']}}
                        </td>
                    </tr>

                    {{-- Modifiers (indented) --}}
                    @if(!empty($line['modifiers']))
                        @foreach($line['modifiers'] as $modifier)
                            <tr>
                                <td class="col-no"></td>
                                <td class="col-desc left">
                                    <div class="small bold" style="padding-left:6px;">{{$modifier['name']}}</div>
                                    @if(!empty($modifier['sell_line_note']))<div class="product-meta" style="padding-left:6px;">{!! $modifier['sell_line_note'] !!}</div>@endif
                                </td>
                                <td class="col-price right">{{$modifier['unit_price_inc_tax'] ?? ''}}</td>
                                <td class="col-qty center">{{$modifier['quantity']}}</td>
                                <td class="col-total right">{{$modifier['line_total']}}</td>
                            </tr>
                        @endforeach
                    @endif
                @endforeach
            </tbody>
        </table>

        <div class="separator"></div>

        <!-- Totals -->
        <div class="totals">
            <div class="row">
                <div class="label">{!! $receipt_details->subtotal_label !!}</div>
                <div class="value">{{$receipt_details->subtotal}}</div>
            </div>

            @if(!empty($receipt_details->discount))
                <div class="row">
                    <div class="label">{!! $receipt_details->discount_label !!}</div>
                    <div class="value">(-) {{$receipt_details->discount}}</div>
                </div>
            @endif

            @if(!empty($receipt_details->total_line_discount))
                <div class="row">
                    <div class="label">{!! $receipt_details->line_discount_label !!}</div>
                    <div class="value">(-) {{$receipt_details->total_line_discount}}</div>
                </div>
            @endif

            <!-- Taxes (grouped or single) -->
            @if(!empty($receipt_details->group_tax_details))
                @foreach($receipt_details->group_tax_details as $k => $v)
                    <div class="row">
                        <div class="label">{!! $k !!}</div>
                        <div class="value">(+) {{$v}}</div>
                    </div>
                @endforeach
            @else
                @if(!empty($receipt_details->tax))
                    <div class="row">
                        <div class="label">{!! $receipt_details->tax_label !!}</div>
                        <div class="value">(+) {{$receipt_details->tax}}</div>
                    </div>
                @endif
            @endif

            @if(!empty($receipt_details->shipping_charges))
                <div class="row">
                    <div class="label">{!! $receipt_details->shipping_charges_label !!}</div>
                    <div class="value">+ {{$receipt_details->shipping_charges}}</div>
                </div>
            @endif

            @if(!empty($receipt_details->packing_charge))
                <div class="row">
                    <div class="label">{!! $receipt_details->packing_charge_label !!}</div>
                    <div class="value">+ {{$receipt_details->packing_charge}}</div>
                </div>
            @endif

            @if($receipt_details->round_off_amount > 0)
                <div class="row">
                    <div class="label">{!! $receipt_details->round_off_label !!}</div>
                    <div class="value">{{$receipt_details->round_off}}</div>
                </div>
            @endif

            <div class="row" style="font-size:14px; margin-top:6px;">
                <div class="label bold">{!! $receipt_details->total_label !!}</div>
                <div class="value bold">{{$receipt_details->total}}</div>
            </div>

            @if(!empty($receipt_details->total_in_words))
                <div class="small muted right">({{$receipt_details->total_in_words}})</div>
            @endif

            {{-- Payments list --}}
            @if(!empty($receipt_details->payments))
                @foreach($receipt_details->payments as $payment)
                    <div class="row small">
                        <div class="label">{{$payment['method']}} ({{$payment['date']}})</div>
                        <div class="value">{{$payment['amount']}}</div>
                    </div>
                @endforeach
            @endif

            @if(!empty($receipt_details->total_paid))
                <div class="row">
                    <div class="label">{!! $receipt_details->total_paid_label !!}</div>
                    <div class="value">{{$receipt_details->total_paid}}</div>
                </div>
            @endif

            @if(!empty($receipt_details->total_due))
                <div class="row">
                    <div class="label">{!! $receipt_details->total_due_label !!}</div>
                    <div class="value">{{$receipt_details->total_due}}</div>
                </div>
            @endif
        </div>

        <div class="separator"></div>

        <!-- Notes & barcode/qr -->
        @if(!empty($receipt_details->additional_notes))
            <div class="small muted">{!! nl2br($receipt_details->additional_notes) !!}</div>
        @endif

        @if($receipt_details->show_barcode)
            <img class="center-block" src="data:image/png;base64,{{DNS1D::getBarcodePNG($receipt_details->invoice_no, 'C128', 2,30,array(39, 48, 54), true)}}" alt="barcode">
        @endif

        @if($receipt_details->show_qr_code && !empty($receipt_details->qr_code_text))
            <img class="center-block" src="data:image/png;base64,{{DNS2D::getBarcodePNG($receipt_details->qr_code_text, 'QRCODE', 3, 3, [39, 48, 54])}}" alt="qrcode">
        @endif

        @if(!empty($receipt_details->footer_text))
            <div class="small muted center gap-sm">{!! $receipt_details->footer_text !!}</div>
        @endif
    </div>
</body>
</html>