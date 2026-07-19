@php
    $arrival = \Carbon\Carbon::parse($transaction->hms_booking_arrival_date_time);
    $departure = \Carbon\Carbon::parse($transaction->hms_booking_departure_date_time);
    $nights = $arrival->diffInDays($departure);
    if ($nights == 0) {
        $nights = 1;
    }
    
    $total_adults = 0;
    $total_kids = 0;
    foreach($booking_rooms as $room) {
        $total_adults += $room->adults;
        $total_kids += $room->childrens;
    }
    
    $settings = json_decode($business->hms_settings);
    
    $payment_methods = [];
    if (!empty($transaction->payment_lines)) {
        foreach ($transaction->payment_lines as $payment) {
            if (!empty($payment->method)) {
                $payment_methods[] = ucfirst(str_replace('_', ' ', $payment->method));
            }
        }
    }
    $payment_method_str = !empty($payment_methods) ? implode(', ', array_unique($payment_methods)) : 'Credit Card';

    // Math breakdown for matching user's invoice mockup taxes (10% Service Charge & 15% Gov Tax)
    $final_total = $transaction->final_total;
    
    // We assume the final_total includes 10% Service Charge and 15% Gov Tax (total 25% addition on base total after discount)
    $subtotal_after_discount = $final_total / 1.25;
    
    // Discount amount:
    $discount = 0;
    if ($transaction->discount_amount > 0) {
        if ($transaction->discount_type == 'percentage') {
            $discount = ($subtotal_after_discount * $transaction->discount_amount) / (100 - $transaction->discount_amount);
        } else {
            $discount = $transaction->discount_amount;
        }
    }
    
    $subtotal = $subtotal_after_discount + $discount;
    $service_charge = $subtotal_after_discount * 0.10;
    $government_tax = $subtotal_after_discount * 0.15;
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hotel Booking Invoice – {{ $format }}</title>
  <style>
    /* ── Reset & base ── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    @page {
      size: auto;
      margin: {{ $format == 'A5' ? '8mm 10mm' : '12mm 15mm' }};
    }

    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      font-size: {{ $format == 'A5' ? '10px' : '11.5px' }};
      color: #1a1a1a;
      background: #fff;
      line-height: 1.4;
    }

    .page {
      width: 100%;
      max-width: {{ $format == 'A5' ? '148mm' : '210mm' }};
      margin: 0 auto;
      padding: 0;
    }

    /* ── Header ── */
    .header {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: {{ $format == 'A5' ? '12px' : '20px' }};
      padding-bottom: {{ $format == 'A5' ? '12px' : '18px' }};
      border-bottom: 2px solid #1a1a1a;
      margin-bottom: {{ $format == 'A5' ? '12px' : '20px' }};
    }

    .header-left {
      display: flex;
      align-items: flex-start;
      gap: {{ $format == 'A5' ? '10px' : '16px' }};
    }

    .logo-box {
      width: {{ $format == 'A5' ? '52px' : '68px' }};
      height: {{ $format == 'A5' ? '52px' : '68px' }};
      border: {{ $format == 'A5' ? '1.5px' : '2px' }} solid #1a1a1a;
      border-radius: {{ $format == 'A5' ? '8px' : '10px' }};
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      font-size: {{ $format == 'A5' ? '22px' : '30px' }};
      overflow: hidden;
    }

    .hotel-name {
      font-size: {{ $format == 'A5' ? '15px' : '20px' }};
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 2px;
    }

    .hotel-tagline {
      font-size: {{ $format == 'A5' ? '8px' : '10px' }};
      color: #666;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 4px;
    }

    .hotel-contacts {
      display: {{ $format == 'A5' ? 'block' : 'grid' }};
      grid-template-columns: 1fr 1fr;
      gap: 2px 20px;
      font-size: {{ $format == 'A5' ? '9px' : '10px' }};
      color: #555;
      line-height: {{ $format == 'A5' ? '1.6' : 'inherit' }};
    }

    .contact-line { display: flex; align-items: center; gap: 5px; }

    .invoice-meta { text-align: right; flex-shrink: 0; padding-top: 4px; }

    .invoice-label {
      font-size: {{ $format == 'A5' ? '18px' : '28px' }};
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: {{ $format == 'A5' ? '2px' : '3px' }};
      color: #1a1a1a;
      line-height: 1;
    }

    .invoice-no {
      font-size: {{ $format == 'A5' ? '11px' : '13px' }};
      color: #444;
      margin-top: 4px;
    }

    .invoice-date { font-size: {{ $format == 'A5' ? '9px' : '10px' }}; color: #888; margin-top: 3px; }

    /* ── Info grid ── */
    .info-grid {
      display: grid;
      grid-template-columns: {{ $format == 'A5' ? '1fr 1fr' : 'repeat(3, 1fr)' }};
      gap: 0;
      border: 1px solid #ccc;
      border-radius: {{ $format == 'A5' ? '6px' : '8px' }};
      overflow: hidden;
      margin-bottom: {{ $format == 'A5' ? '12px' : '20px' }};
    }

    .info-cell {
      padding: {{ $format == 'A5' ? '8px 10px' : '12px 14px' }};
      border-right: 1px solid #ccc;
      border-bottom: 1px solid #ccc;
    }

    @if ($format == 'A5')
      .info-cell:nth-child(even) { border-right: none; }
      .info-cell:nth-last-child(1),
      .info-cell:nth-last-child(2) { border-bottom: none; }
    @else
      .info-cell:nth-child(3n) { border-right: none; }
      .info-cell:nth-last-child(1),
      .info-cell:nth-last-child(2),
      .info-cell:nth-last-child(3) { border-bottom: none; }
    @endif

    .info-label {
      font-size: {{ $format == 'A5' ? '8px' : '8.5px' }};
      text-transform: uppercase;
      letter-spacing: 0.6px;
      color: #999;
      margin-bottom: 4px;
    }

    .info-value {
      font-size: {{ $format == 'A5' ? '11px' : '13px' }};
      font-weight: 600;
      color: #1a1a1a;
    }

    .info-sub {
      font-size: {{ $format == 'A5' ? '9px' : '10px' }};
      color: #777;
      margin-top: 2px;
    }

    /* ── Room badge ── */
    .room-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      background: #f0f0f0;
      border: 1px solid #ccc;
      border-radius: 4px;
      padding: 4px 10px;
      font-size: 10px;
      font-weight: 600;
      color: #1a1a1a;
      margin-bottom: 10px;
    }

    /* ── Section heading ── */
    .section-heading {
      font-size: 9px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #888;
      border-bottom: 1px solid #e0e0e0;
      padding-bottom: 5px;
      margin-bottom: 12px;
    }

    /* ── Charges table ── */
    .charges-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 10px;
    }

    .charges-table thead tr {
      background: #f5f5f5;
    }

    .charges-table thead tr th {
      padding: {{ $format == 'A5' ? '7px 8px' : '9px 10px' }};
      font-size: {{ $format == 'A5' ? '8px' : '9px' }};
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #666;
      border-bottom: 1.5px solid #ccc;
      text-align: left;
    }

    .charges-table thead tr th.right { text-align: right; }
    .charges-table thead tr th.center { text-align: center; }

    .charges-table tbody tr {
      border-bottom: 1px solid #ebebeb;
    }

    .charges-table tbody tr:last-child { border-bottom: none; }

    .charges-table td {
      padding: {{ $format == 'A5' ? '8px 8px' : '10px 10px' }};
      font-size: {{ $format == 'A5' ? '10px' : '11px' }};
      color: #1a1a1a;
      vertical-align: top;
    }

    .charges-table td.right { text-align: right; }
    .charges-table td.center { text-align: center; }
    .charges-table td.muted { color: #777; }

    .item-name { font-weight: 500; }
    .item-sub { font-size: {{ $format == 'A5' ? '8px' : '9.5px' }}; color: #999; margin-top: 2px; }

    /* ── Totals ── */
    .totals-wrapper {
      display: flex;
      justify-content: flex-end;
      margin-top: 0;
      border-top: 1.5px solid #ccc;
      padding-top: {{ $format == 'A5' ? '8px' : '12px' }};
    }

    .totals-inner { min-width: {{ $format == 'A5' ? '220px' : '280px' }}; }

    .totals-row {
      display: flex;
      justify-content: space-between;
      font-size: {{ $format == 'A5' ? '10px' : '11px' }};
      color: #555;
      margin-bottom: 5px;
      gap: 32px;
    }

    .totals-row.grand {
      font-size: {{ $format == 'A5' ? '13px' : '15px' }};
      font-weight: 700;
      color: #1a1a1a;
      border-top: 2px solid #1a1a1a;
      padding-top: {{ $format == 'A5' ? '7px' : '9px' }};
      margin-top: 5px;
    }

    /* ── Payment section ── */
    .payment-section {
      margin-top: {{ $format == 'A5' ? '12px' : '24px' }};
      display: grid;
      grid-template-columns: {{ $format == 'A5' ? '1fr 1fr' : '1fr 1fr 1fr' }};
      gap: {{ $format == 'A5' ? '8px' : '12px' }};
      margin-bottom: 10px;
    }

    .pay-box {
      border-radius: {{ $format == 'A5' ? '6px' : '8px' }};
      padding: {{ $format == 'A5' ? '9px 10px' : '14px 16px' }};
    }

    .pay-box.paid { background: #e6f5ed; border: 1px solid #9fd4b4; }
    .pay-box.balance { background: #f5f5f5; border: 1px solid #ddd; }
    .pay-box.method { background: #f5f5f5; border: 1px solid #ddd; }

    .pay-box-label {
      font-size: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #888;
      margin-bottom: 5px;
    }

    .pay-box.paid .pay-box-label { color: #2d7a4a; }

    .pay-box-amount {
      font-size: {{ $format == 'A5' ? '12px' : '14px' }};
      font-weight: 700;
      color: #1a1a1a;
    }

    .pay-box.paid .pay-box-amount { color: #1a5c35; }

    .pay-box-detail {
      font-size: 9px;
      color: #888;
      margin-top: 3px;
    }

    .pay-box.paid .pay-box-detail { color: #3a8055; }

    /* ── Terms ── */
    .terms-box {
      margin-top: 20px;
      padding: 14px 16px;
      background: #fafafa;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
    }

    .terms-title {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #444;
      margin-bottom: 5px;
    }

    .terms-text {
      font-size: 10px;
      color: #666;
      line-height: 1.7;
    }

    /* ── Signature row ── */
    .signature-row {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 20px;
      margin-top: 28px;
    }

    .sig-block { text-align: center; }

    .sig-line {
      border-bottom: 1px solid #ccc;
      margin-bottom: 5px;
      height: 28px;
    }

    .sig-label {
      font-size: 9px;
      color: #999;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* ── Footer ── */
    .bill-footer {
      border-top: 1px solid #ccc;
      padding-top: 14px;
      margin-top: 24px;
      display: flex;
      justify-content: space-between;
      align-items: flex-end;
      gap: 16px;
    }

    .footer-thanks {
      font-size: 11px;
      color: #555;
      line-height: 1.7;
    }

    .footer-auto {
      font-size: 9px;
      color: #aaa;
      text-align: right;
      line-height: 1.6;
    }

    /* ── Print ── */
    @media print {
      body { margin: 0; background: #fff; }
      .page { width: 100%; max-width: 100%; min-height: 0; }
      .no-print { display: none !important; }
      .charges-table tbody tr:hover { background: none; }
    }

    /* ── Screen preview ── */
    @media screen {
      body { background: #e8e8e8; padding: 20px; }
      .page {
        background: #fff;
        padding: {{ $format == 'A5' ? '14mm' : '18mm' }};
        box-shadow: 0 4px 24px rgba(0,0,0,.15);
        border-radius: 4px;
      }
    }
  </style>
</head>
<body>
<div class="page">

  <!-- HEADER -->
  <div class="header">
    <div class="header-left">
      <div class="logo-box">
        @if (!empty($business->logo))
          <img src="{{ asset('uploads/business_logos/' . $business->logo) }}" alt="Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
        @elseif (!empty(Session::get('business.logo')))
          <img src="{{ asset('uploads/business_logos/' . Session::get('business.logo')) }}" alt="Logo" style="max-width: 100%; max-height: 100%; object-fit: contain;">
        @else
          🏨
        @endif
      </div>
      <div>
        <div class="hotel-name">{{ $business->name }}</div>
        @if(!empty($business->landmark))
          <div class="hotel-tagline">{{ $business->landmark }}</div>
        @endif
        
        <div class="hotel-contacts">
          @php
            $location = $business->locations->first();
            $address_parts = [];
            if (!empty($location)) {
                if (!empty($location->landmark)) $address_parts[] = $location->landmark;
                if (!empty($location->city)) $address_parts[] = $location->city;
                if (!empty($location->state)) $address_parts[] = $location->state;
                if (!empty($location->zip_code)) $address_parts[] = $location->zip_code;
                if (!empty($location->country)) $address_parts[] = $location->country;
            }
            $address_str = !empty($address_parts) ? implode(', ', $address_parts) : '';
            $phone_str = !empty($location->mobile) ? $location->mobile : ($settings->booking_pdf->phone ?? '');
            $email_str = !empty($location->email) ? $location->email : ($settings->booking_pdf->email ?? '');
            $website_str = !empty($location->website) ? $location->website : ($settings->booking_pdf->website ?? '');
          @endphp

          @if(!empty($address_str))
            <span class="contact-line">&#128205; {{ $address_str }}</span>
          @endif
          @if(!empty($phone_str))
            <span class="contact-line">&#128222; {{ $phone_str }}</span>
          @endif
          @if(!empty($email_str))
            <span class="contact-line">&#128231; {{ $email_str }}</span>
          @endif
          @if(!empty($website_str))
            <span class="contact-line">&#127758; {{ $website_str }}</span>
          @endif
        </div>
      </div>
    </div>
    <div class="invoice-meta">
      <div class="invoice-label">Invoice</div>
      <div class="invoice-no">#BK-{{ $transaction->ref_no }}</div>
      <div class="invoice-date">Issued: {{ @format_date($transaction->created_at) }}</div>
    </div>
  </div>

  <!-- GUEST & STAY DETAILS -->
  <div class="info-grid">
    @if ($format == 'A5')
      <!-- A5 2-Column Info Grid -->
      <div class="info-cell">
        <div class="info-label">Guest name</div>
        <div class="info-value">{{ $transaction->contact->name }}</div>
        <div class="info-sub">Mobile: {{ $transaction->contact->mobile ?? 'N/A' }}</div>
      </div>
      <div class="info-cell">
        <div class="info-label">Booking reference</div>
        <div class="info-value">BK-{{ $transaction->ref_no }}</div>
        <div class="info-sub">Confirmed</div>
      </div>
      <div class="info-cell">
        <div class="info-label">Check-in</div>
        <div class="info-value">{{ @format_date($transaction->check_in ?? $transaction->hms_booking_arrival_date_time) }}</div>
        <div class="info-sub">14:00 hrs</div>
      </div>
      <div class="info-cell">
        <div class="info-label">Check-out</div>
        <div class="info-value">{{ @format_date($transaction->check_out ?? $transaction->hms_booking_departure_date_time) }}</div>
        <div class="info-sub">12:00 hrs &nbsp;·&nbsp; {{ $nights }} nights</div>
      </div>
      <div class="info-cell">
        <div class="info-label">Room</div>
        <div class="info-value">
          @foreach($booking_rooms as $room)
            Room {{ $room->room_number }} – {{ $room->type }}@if(!$loop->last), @endif
          @endforeach
        </div>
      </div>
      <div class="info-cell">
        <div class="info-label">Guests</div>
        <div class="info-value">{{ $total_adults }} Adults @if($total_kids), {{ $total_kids }} Kids @endif</div>
      </div>
    @else
      <!-- A4 3-Column Info Grid -->
      <div class="info-cell">
        <div class="info-label">Guest name</div>
        <div class="info-value">{{ $transaction->contact->name }}</div>
        <div class="info-sub">Mobile: {{ $transaction->contact->mobile ?? 'N/A' }}</div>
      </div>
      <div class="info-cell">
        <div class="info-label">Booking reference</div>
        <div class="info-value">BK-{{ $transaction->ref_no }}</div>
        <div class="info-sub">Confirmed</div>
      </div>
      <div class="info-cell">
        <div class="info-label">No. of nights</div>
        <div class="info-value">{{ $nights }} {{ $nights == 1 ? 'night' : 'nights' }}</div>
        <div class="info-sub">{{ @format_date($transaction->hms_booking_arrival_date_time) }} – {{ @format_date($transaction->hms_booking_departure_date_time) }}</div>
      </div>
      <div class="info-cell">
        <div class="info-label">Check-in</div>
        <div class="info-value">{{ @format_date($transaction->check_in ?? $transaction->hms_booking_arrival_date_time) }}</div>
        <div class="info-sub">14:00 hrs</div>
      </div>
      <div class="info-cell">
        <div class="info-label">Check-out</div>
        <div class="info-value">{{ @format_date($transaction->check_out ?? $transaction->hms_booking_departure_date_time) }}</div>
        <div class="info-sub">12:00 hrs</div>
      </div>
      <div class="info-cell">
        <div class="info-label">Room &amp; guests</div>
        <div class="info-value">
          @foreach($booking_rooms as $room)
            Room {{ $room->room_number }} – {{ $room->type }}@if(!$loop->last), @endif
          @endforeach
        </div>
        <div class="info-sub">{{ $total_adults }} Adults @if($total_kids), {{ $total_kids }} Kids @endif</div>
      </div>
    @endif
  </div>

  <!-- ROOM BADGE (For A5) -->
  @if ($format == 'A5')
    <div class="room-badge">
      &#127968; 
      @foreach($booking_rooms as $room)
        Room {{ $room->room_number }} – {{ $room->type }}@if(!$loop->last), @endif
      @endforeach
      &nbsp;&middot;&nbsp; 
      {{ $total_adults }} Adults @if($total_kids), {{ $total_kids }} Kids @endif
    </div>
  @endif

  <!-- CHARGES TABLE -->
  <div class="section-heading">Charges &amp; services</div>
  <table class="charges-table">
    <thead>
      <tr>
        <th style="width:40%;">Description</th>
        <th class="center" style="width:13%;">Qty</th>
        <th class="right" style="width:20%;">Unit rate</th>
        <th class="right" style="width:27%;">Amount</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($booking_rooms as $room)
        <tr>
          <td>
            <div class="item-name">{{ $room->type }} Room</div>
            <div class="item-sub">Room {{ $room->room_number }} &nbsp;·&nbsp; Adults: {{ $room->adults }}@if($room->childrens), Kids: {{ $room->childrens }}@endif</div>
          </td>
          <td class="center muted">{{ $nights }} {{ $nights == 1 ? 'night' : 'nights' }}</td>
          <td class="right muted">@format_currency($room->total_price / ($nights > 0 ? $nights : 1))</td>
          <td class="right">@format_currency($room->total_price)</td>
        </tr>
      @endforeach

      @foreach ($extras as $extra)
        @if (in_array($extra->id, $extras_id))
          <tr>
            <td>
              <div class="item-name">{{ $extra->name }}</div>
              <div class="item-sub">Service: {{ str_replace('_', ' ', $extra->price_per) }}</div>
            </td>
            <td class="center muted">1</td>
            <td class="right muted">@format_currency($extra->price)</td>
            <td class="right">@format_currency($extra->price)</td>
          </tr>
        @endif
      @endforeach
    </tbody>
  </table>

  <!-- TOTALS -->
  <div class="totals-wrapper">
    <div class="totals-inner">
      <div class="totals-row">
        <span>Subtotal</span>
        <span>@format_currency($subtotal)</span>
      </div>
      
      @if ($discount > 0)
        <div class="totals-row">
          <span>
            Discount
            @if ($transaction->discount_type == 'percentage')
              ({{ number_format($transaction->discount_amount, 2) }}%)
            @endif
          </span>
          <span style="color: #c5221f;">
            -@format_currency($discount)
          </span>
        </div>
      @endif

      <div class="totals-row">
        <span>Service charge (10%)</span>
        <span>@format_currency($service_charge)</span>
      </div>

      <div class="totals-row">
        <span>Government tax (15%)</span>
        <span>@format_currency($government_tax)</span>
      </div>
      
      <div class="totals-row grand">
        <span>Total due</span>
        <span>@format_currency($final_total)</span>
      </div>
    </div>
  </div>

  <!-- PAYMENT -->
  <div class="payment-section">
    <div class="pay-box paid">
      <div class="pay-box-label">Payment received</div>
      <div class="pay-box-amount">@format_currency($transaction->total_paid)</div>
      <div class="pay-box-detail">{{ $payment_method_str }} &nbsp;·&nbsp; {{ @format_date($transaction->created_at) }}</div>
    </div>
    <div class="pay-box balance">
      <div class="pay-box-label">Balance due</div>
      <div class="pay-box-amount">@format_currency($transaction->final_total - $transaction->total_paid)</div>
      <div class="pay-box-detail">
        @if($transaction->final_total - $transaction->total_paid <= 0)
          Fully settled
        @else
          Outstanding
        @endif
      </div>
    </div>
    @if ($format != 'A5')
      <div class="pay-box method">
        <div class="pay-box-label">Payment method</div>
        <div class="pay-box-amount" style="font-size:12px;">{{ $payment_method_str }}</div>
        <div class="pay-box-detail">Processed at check-in</div>
      </div>
    @endif
  </div>

  <!-- TERMS (A4 Only) -->
  @if ($format == 'A4')
    <div class="terms-box">
      <div class="terms-title">Terms &amp; conditions</div>
      <div class="terms-text">
        Late checkout after 12:00 hrs is subject to an additional half-day charge. All amounts are inclusive of applicable taxes.
        Disputes must be raised within 7 days of checkout.
        @if (!empty($settings->booking_pdf->footer_text))
          <br>{!! $settings->booking_pdf->footer_text !!}
        @endif
      </div>
    </div>
  @endif

  <!-- SIGNATURE (A4 Only) -->
  @if ($format == 'A4')
    <div class="signature-row">
      <div class="sig-block">
        <div class="sig-line"></div>
        <div class="sig-label">Guest signature</div>
      </div>
      <div class="sig-block">
        <div class="sig-line"></div>
        <div class="sig-label">Cashier / Front desk</div>
      </div>
      <div class="sig-block">
        <div class="sig-line"></div>
        <div class="sig-label">Authorised by</div>
      </div>
    </div>
  @endif

  <!-- FOOTER -->
  <div class="bill-footer">
    <div class="footer-thanks">
      Thank you for choosing {{ $business->name }}.<br>
      We look forward to welcoming you back very soon.
    </div>
    <div class="footer-auto">
      Computer-generated invoice. No signature required for digital copy.<br>
      {{ $business->name }} · {{ $phone_str }}
    </div>
  </div>

</div>
</body>
</html>
