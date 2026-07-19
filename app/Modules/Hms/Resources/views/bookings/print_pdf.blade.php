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
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Hotel Booking Invoice – {{ $format }}</title>
  <style>
    /* Reset & base */
    *, *::before, *::after { box-sizing: border-box; }

    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      font-size: {{ $format == 'A5' ? '11px' : '12px' }};
      color: #1a1a1a;
      background: #fff;
      line-height: 1.5;
    }

    .page {
      width: 100%;
      margin: 0;
      padding: 0;
    }

    /* Header */
    .header-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: {{ $format == 'A5' ? '12px' : '20px' }};
      border-bottom: 2px solid #1a1a1a;
      padding-bottom: {{ $format == 'A5' ? '10px' : '15px' }};
    }

    .logo-box {
      width: {{ $format == 'A5' ? '52px' : '68px' }};
      height: {{ $format == 'A5' ? '52px' : '68px' }};
      border: {{ $format == 'A5' ? '1.5px' : '2px' }} solid #1a1a1a;
      border-radius: {{ $format == 'A5' ? '8px' : '10px' }};
      text-align: center;
      vertical-align: middle;
      font-size: {{ $format == 'A5' ? '22px' : '30px' }};
      padding-top: {{ $format == 'A5' ? '6px' : '10px' }};
    }

    .hotel-name {
      font-size: {{ $format == 'A5' ? '15px' : '20px' }};
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 3px;
    }

    .hotel-tagline {
      font-size: {{ $format == 'A5' ? '8px' : '10px' }};
      color: #666;
      text-transform: uppercase;
      letter-spacing: 1.5px;
      margin-bottom: 6px;
    }

    .hotel-contacts {
      font-size: {{ $format == 'A5' ? '8.5px' : '9.5px' }};
      color: #555;
      line-height: 1.4;
    }

    .invoice-label {
      font-size: {{ $format == 'A5' ? '18px' : '28px' }};
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 3px;
      color: #1a1a1a;
      line-height: 1;
    }

    .invoice-no {
      font-size: {{ $format == 'A5' ? '11px' : '13px' }};
      color: #444;
      margin-top: 4px;
    }

    .invoice-date { 
      font-size: {{ $format == 'A5' ? '8.5px' : '10px' }}; 
      color: #888; 
      margin-top: 3px; 
    }

    /* Info grid */
    .info-grid-table {
      width: 100%;
      border: 1px solid #ccc;
      border-radius: 6px;
      border-collapse: collapse;
      margin-bottom: {{ $format == 'A5' ? '12px' : '20px' }};
    }

    .info-cell {
      padding: {{ $format == 'A5' ? '8px 10px' : '12px 14px' }};
      vertical-align: top;
    }

    .border-rb { border-right: 1px solid #ccc; border-bottom: 1px solid #ccc; }
    .border-r { border-right: 1px solid #ccc; }
    .border-b { border-bottom: 1px solid #ccc; }

    .info-label {
      font-size: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #888;
      margin-bottom: 4px;
    }

    .info-value {
      font-size: {{ $format == 'A5' ? '11px' : '13px' }};
      font-weight: 600;
      color: #1a1a1a;
    }

    .info-sub {
      font-size: {{ $format == 'A5' ? '8.5px' : '9.5px' }};
      color: #777;
      margin-top: 2px;
    }

    /* Room badge */
    .room-badge {
      background: #f0f0f0;
      border: 1px solid #ccc;
      border-radius: 4px;
      padding: 4px 10px;
      font-size: 10px;
      font-weight: 600;
      color: #1a1a1a;
      margin-bottom: 10px;
    }

    /* Section heading */
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

    /* Charges table */
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
      font-size: 8.5px;
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
    .item-sub { font-size: 8.5px; color: #999; margin-top: 2px; }

    /* Totals */
    .totals-row {
      font-size: 11px;
      color: #555;
    }

    .totals-row.grand td {
      font-size: 15px;
      font-weight: 700;
      color: #1a1a1a;
      border-top: 2px solid #1a1a1a;
      padding-top: 9px;
      margin-top: 7px;
    }

    /* Payment boxes */
    .pay-box, .payment-box {
      border-radius: 8px;
      padding: {{ $format == 'A5' ? '9px 10px' : '14px 16px' }};
    }

    .pay-box.paid, .payment-box.paid { background: #e6f5ed; border: 1px solid #9fd4b4; }
    .pay-box.balance, .payment-box.balance { background: #f5f5f5; border: 1px solid #ddd; }
    .pay-box.method, .payment-box.method { background: #f5f5f5; border: 1px solid #ddd; }

    .pay-box-label, .payment-box-label {
      font-size: 8px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #888;
      margin-bottom: 5px;
    }

    .pay-box.paid .pay-box-label, .payment-box.paid .payment-box-label { color: #2d7a4a; }

    .pay-box-amount, .payment-box-amount {
      font-size: {{ $format == 'A5' ? '12px' : '14px' }};
      font-weight: 700;
      color: #1a1a1a;
    }

    .pay-box.paid .pay-box-amount, .payment-box.paid .payment-box-amount { color: #1a5c35; }

    .pay-box-detail, .payment-box-detail {
      font-size: 9px;
      color: #888;
      margin-top: 3px;
    }

    .pay-box.paid .pay-box-detail, .payment-box.paid .payment-box-detail { color: #3a8055; }

    /* Terms */
    .terms-box {
      margin-top: 15px;
      padding: 10px 12px;
      background: #fafafa;
      border: 1px solid #e0e0e0;
      border-radius: 8px;
    }

    .terms-title {
      font-size: 9.5px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      color: #444;
      margin-bottom: 4px;
    }

    .terms-text {
      font-size: 9px;
      color: #666;
      line-height: 1.6;
    }

    /* Signature */
    .signature-row {
      margin-top: 25px;
    }

    .sig-label {
      font-size: 8.5px;
      color: #999;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      text-align: center;
      margin-top: 5px;
    }

    /* Footer */
    .bill-footer {
      border-top: 1px solid #ccc;
      padding-top: 12px;
      margin-top: 20px;
    }
  </style>
</head>
<body>
<div class="page">

  <!-- HEADER -->
  <table class="header-table" style="width: 100%; border: none; border-collapse: collapse;">
    <tr>
      <td style="width: 65%; vertical-align: top; text-align: left;">
        <table style="width: 100%; border: none; border-collapse: collapse;">
          <tr>
            <td style="width: {{ $format == 'A5' ? '60px' : '75px' }}; vertical-align: top;">
              <div class="logo-box">
                @if (!empty($business->logo))
                  <img src="{{ asset('uploads/business_logos/' . $business->logo) }}" style="max-height: {{ $format == 'A5' ? '46px' : '62px' }}; width: auto; border-radius: 6px;">
                @elseif (!empty(Session::get('business.logo')))
                  <img src="{{ asset('uploads/business_logos/' . Session::get('business.logo')) }}" style="max-height: {{ $format == 'A5' ? '46px' : '62px' }}; width: auto; border-radius: 6px;">
                @else
                  🏨
                @endif
              </div>
            </td>
            <td style="vertical-align: top; padding-left: 10px;">
              <div class="hotel-name">{{ $business->name }}</div>
              <div class="hotel-tagline">Luxury Hotel &amp; Spa</div>
              <div class="hotel-contacts">
                @if (!empty($settings->booking_pdf->address))
                  {!! nl2br(e($settings->booking_pdf->address)) !!}<br>
                @endif
                @if (!empty($settings->booking_pdf->phone))
                  Tel: {{ $settings->booking_pdf->phone }} &nbsp;|&nbsp;
                @endif
                @if (!empty($settings->booking_pdf->email))
                  Email: {{ $settings->booking_pdf->email }}<br>
                @endif
                @if (!empty($settings->booking_pdf->website))
                  Web: {{ $settings->booking_pdf->website }}
                @endif
              </div>
            </td>
          </tr>
        </table>
      </td>
      <td style="width: 35%; vertical-align: top; text-align: right; padding-top: 4px;">
        <div class="invoice-label">Invoice</div>
        <div class="invoice-no">#BK-{{ $transaction->ref_no }}</div>
        <div class="invoice-date">Issued: {{ @format_datetime($transaction->created_at) }}</div>
      </td>
    </tr>
  </table>

  <!-- GUEST & STAY DETAILS -->
  @if($format == 'A5')
    <!-- A5 2-Column Info Grid -->
    <table class="info-grid-table">
      <tr>
        <td class="info-cell border-rb" style="width: 50%;">
          <div class="info-label">Guest name</div>
          <div class="info-value">{{ $transaction->contact->name }}</div>
          <div class="info-sub">Mobile: {{ $transaction->contact->mobile ?? 'N/A' }}</div>
        </td>
        <td class="info-cell border-b" style="width: 50%;">
          <div class="info-label">Booking reference</div>
          <div class="info-value">BK-{{ $transaction->ref_no }}</div>
          <div class="info-sub">Confirmed Booking</div>
        </td>
      </tr>
      <tr>
        <td class="info-cell border-rb" style="width: 50%;">
          <div class="info-label">Check-in</div>
          <div class="info-value">{{ @format_datetime($transaction->check_in ?? $transaction->hms_booking_arrival_date_time) }}</div>
          <div class="info-sub">14:00 hrs</div>
        </td>
        <td class="info-cell border-b" style="width: 50%;">
          <div class="info-label">Check-out</div>
          <div class="info-value">{{ @format_datetime($transaction->check_out ?? $transaction->hms_booking_departure_date_time) }}</div>
          <div class="info-sub">12:00 hrs &nbsp;·&nbsp; {{ $nights }} {{ $nights == 1 ? 'night' : 'nights' }}</div>
        </td>
      </tr>
      <tr>
        <td class="info-cell border-r" style="width: 50%;">
          <div class="info-label">Room</div>
          <div class="info-value">
            @foreach($booking_rooms as $room)
              Room {{ $room->room_number }} – {{ $room->type }}@if(!$loop->last), @endif
            @endforeach
          </div>
          <div class="info-sub">Confirmed</div>
        </td>
        <td class="info-cell" style="width: 50%;">
          <div class="info-label">Guests</div>
          <div class="info-value">{{ $total_adults }} Adults @if($total_kids), {{ $total_kids }} Kids @endif</div>
          <div class="info-sub">Non-smoking room</div>
        </td>
      </tr>
    </table>
    
    <!-- ROOM BADGE (For A5) -->
    <div class="room-badge">
      &#127968; 
      @foreach($booking_rooms as $room)
        Room {{ $room->room_number }} – {{ $room->type }}@if(!$loop->last), @endif
      @endforeach
      &nbsp;&middot;&nbsp; 
      {{ $total_adults }} Adults @if($total_kids), {{ $total_kids }} Kids @endif
    </div>
  @else
    <!-- A4 3-Column Info Grid -->
    <table class="info-grid-table">
      <tr>
        <td class="info-cell border-rb" style="width: 33%;">
          <div class="info-label">Guest name</div>
          <div class="info-value">{{ $transaction->contact->name }}</div>
          <div class="info-sub">Mobile: {{ $transaction->contact->mobile ?? 'N/A' }}</div>
        </td>
        <td class="info-cell border-rb" style="width: 33%;">
          <div class="info-label">Booking reference</div>
          <div class="info-value">BK-{{ $transaction->ref_no }}</div>
          <div class="info-sub">Confirmed Booking</div>
        </td>
        <td class="info-cell border-b" style="width: 34%;">
          <div class="info-label">No. of nights</div>
          <div class="info-value">{{ $nights }} {{ $nights == 1 ? 'night' : 'nights' }}</div>
          <div class="info-sub">{{ @format_datetime($transaction->hms_booking_arrival_date_time) }} – {{ @format_datetime($transaction->hms_booking_departure_date_time) }}</div>
        </td>
      </tr>
      <tr>
        <td class="info-cell border-r" style="width: 33%;">
          <div class="info-label">Check-in</div>
          <div class="info-value">{{ @format_datetime($transaction->check_in ?? $transaction->hms_booking_arrival_date_time) }}</div>
          <div class="info-sub">14:00 hrs</div>
        </td>
        <td class="info-cell border-r" style="width: 33%;">
          <div class="info-label">Check-out</div>
          <div class="info-value">{{ @format_datetime($transaction->check_out ?? $transaction->hms_booking_departure_date_time) }}</div>
          <div class="info-sub">12:00 hrs</div>
        </td>
        <td class="info-cell" style="width: 34%;">
          <div class="info-label">Room &amp; guests</div>
          <div class="info-value">
            @foreach($booking_rooms as $room)
              Room {{ $room->room_number }} – {{ $room->type }}@if(!$loop->last), @endif
            @endforeach
          </div>
          <div class="info-sub">{{ $total_adults }} Adults @if($total_kids), {{ $total_kids }} Kids @endif</div>
        </td>
      </tr>
    </table>
  @endif

  <!-- CHARGES TABLE -->
  <div class="section-heading">Charges &amp; services</div>
  <table class="charges-table">
    <thead>
      <tr>
        <th style="width: 40%;">Description</th>
        <th class="center" style="width: 15%;">Qty</th>
        <th class="right" style="width: 20%;">Unit rate</th>
        <th class="right" style="width: 25%;">Amount</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($booking_rooms as $room)
        <tr>
          <td>
            <div class="item-name">{{ $room->type }} Room</div>
            <div class="item-sub">Room {{ $room->room_number }} &nbsp;·&nbsp; King bed, standard occupancy</div>
          </td>
          <td class="center muted">{{ $nights }} {{ $nights == 1 ? 'night' : 'nights' }}</td>
          <td class="right muted">
            @format_currency($room->total_price / ($nights > 0 ? $nights : 1))
          </td>
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
  <table style="width: 100%; border-collapse: collapse; margin-top: 12px; border-top: 1.5px solid #ccc; padding-top: 12px;">
    <tr>
      <td style="width: 50%;"></td>
      <td style="width: 50%; vertical-align: top;">
        <table style="width: 100%; border-collapse: collapse;">
          <tr class="totals-row">
            <td style="text-align: left; padding: 4px 0; color: #555;">Subtotal</td>
            <td style="text-align: right; padding: 4px 0; font-weight: bold; color: #1a1a1a;">
              @format_currency($transaction->room_price + $transaction->extra_price)
            </td>
          </tr>
          
          @php
            $discount_percent_disable = 0;
            if (!empty($transaction->hms_coupon_id)) {
                $discount_percent_disable = 1;
            }
          @endphp
          
          @if ($transaction->discount_amount > 0)
            <tr class="totals-row">
              <td style="text-align: left; padding: 4px 0; color: #555;">
                Discount
                @if ($discount_percent_disable == 0)
                  ({{ number_format($transaction->discount_amount, 2) }}%)
                @endif
              </td>
              <td style="text-align: right; padding: 4px 0; font-weight: bold; color: #c5221f;">
                -@if ($discount_percent_disable == 0)
                  @format_currency(($transaction->discount_amount * ($transaction->room_price + $transaction->extra_price)) / 100)
                @else
                  @format_currency($transaction->discount_amount)
                @endif
              </td>
            </tr>
          @endif
          <tr class="totals-row grand">
            <td style="text-align: left; padding: 8px 0; font-size: 15px; font-weight: 700; color: #1a1a1a; border-top: 2px solid #1a1a1a;">Total due</td>
            <td style="text-align: right; padding: 8px 0; font-size: 15px; font-weight: 700; color: #1a1a1a; border-top: 2px solid #1a1a1a;">
              @format_currency($transaction->final_total)
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>

  <!-- PAYMENT -->
  @if($format == 'A5')
    <!-- A5 2-Column Payment Boxes -->
    <table class="payment-grid" style="width: 100%; border-collapse: collapse; margin-top: 15px;">
      <tr>
        <td style="width: 48%; padding-right: 8px; vertical-align: top;">
          <div class="payment-box paid">
            <div class="payment-box-label">Payment received</div>
            <div class="payment-box-amount">@format_currency($transaction->total_paid)</div>
            <div class="payment-box-detail">{{ $payment_method_str }} &nbsp;·&nbsp; {{ @format_datetime($transaction->created_at) }}</div>
          </div>
        </td>
        <td style="width: 48%; vertical-align: top;">
          <div class="payment-box balance">
            <div class="payment-box-label">Balance due</div>
            <div class="payment-box-amount">@format_currency($transaction->final_total - $transaction->total_paid)</div>
            <div class="payment-box-detail">
              @if($transaction->final_total - $transaction->total_paid <= 0)
                Fully settled
              @else
                Outstanding
              @endif
            </div>
          </div>
        </td>
      </tr>
    </table>
  @else
    <!-- A4 3-Column Payment Boxes -->
    <table class="charges-table" style="width: 100%; border-collapse: collapse; margin-top: 20px; border-bottom: none;">
      <tr>
        <td style="width: 32%; padding: 0 12px 0 0; vertical-align: top;">
          <div class="pay-box paid">
            <div class="pay-box-label">Payment received</div>
            <div class="pay-box-amount">@format_currency($transaction->total_paid)</div>
            <div class="pay-box-detail">{{ $payment_method_str }} &nbsp;·&nbsp; {{ @format_datetime($transaction->created_at) }}</div>
          </div>
        </td>
        <td style="width: 32%; padding: 0 12px 0 12px; vertical-align: top;">
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
        </td>
        <td style="width: 32%; padding: 0 0 0 12px; vertical-align: top;">
          <div class="pay-box method">
            <div class="pay-box-label">Payment method</div>
            <div class="pay-box-amount" style="font-size:12px;">{{ $payment_method_str }}</div>
            <div class="pay-box-detail">Processed at check-in</div>
          </div>
        </td>
      </tr>
    </table>
  @endif

  <!-- TERMS (A4 Only) -->
  @if($format == 'A4')
    <div class="terms-box">
      <div class="terms-title">Terms &amp; conditions</div>
      <div class="terms-text">
        Late checkout after 12:00 hrs is subject to an additional half-day charge. All amounts are in base currency inclusive of applicable taxes.
        Disputes must be raised within 7 days of checkout. 
        @if (!empty($settings->booking_pdf->footer_text))
          {!! $settings->booking_pdf->footer_text !!}
        @endif
      </div>
    </div>
  @endif

  <!-- SIGNATURE (A4 Only) -->
  @if($format == 'A4')
    <table class="charges-table" style="width: 100%; border-collapse: collapse; margin-top: 28px; border-bottom: none;">
      <tr>
        <td style="width: 32%; text-align: center; padding: 0 15px 0 0;">
          <div style="border-bottom: 1px solid #ccc; height: 28px;"></div>
          <div class="sig-label">Guest signature</div>
        </td>
        <td style="width: 32%; text-align: center; padding: 0 15px 0 15px;">
          <div style="border-bottom: 1px solid #ccc; height: 28px;"></div>
          <div class="sig-label">Cashier / Front desk</div>
        </td>
        <td style="width: 32%; text-align: center; padding: 0 0 0 15px;">
          <div style="border-bottom: 1px solid #ccc; height: 28px;"></div>
          <div class="sig-label">Authorised by</div>
        </td>
      </tr>
    </table>
  @endif

  <!-- FOOTER -->
  <table class="bill-footer" style="width: 100%; border-collapse: collapse; border-bottom: none;">
    <tr>
      <td style="text-align: left; font-size: 11px; color: #555; line-height: 1.7; vertical-align: bottom; padding: 0;">
        Thank you for choosing {{ $business->name }}.<br>
        We look forward to welcoming you back very soon.
      </td>
      <td style="text-align: right; font-size: 9px; color: #aaa; line-height: 1.6; vertical-align: bottom; padding: 0;">
        Computer-generated invoice. No signature required for digital copy.<br>
        {{ $business->name }} · {{ $settings->booking_pdf->phone ?? '' }}
      </td>
    </tr>
  </table>

</div>
</body>
</html>
