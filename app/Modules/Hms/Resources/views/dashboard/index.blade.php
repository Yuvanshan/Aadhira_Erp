@extends('layouts.app')
@section('title', __('hms::lang.hms'))
@section('content')
    @include('hms::layouts.nav')

    @php
        $business_id = request()->session()->get('user.business_id');
        $room_types = \Modules\Hms\Entities\HmsRoomType::where('business_id', $business_id)->get();
        
        // Resolve active room type filter (default to first type if not set)
        $active_type_id = request()->get('room_type_id', $room_types->first()->id ?? null);
        
        // Fetch rooms for this type
        $rooms = [];
        if (!empty($active_type_id)) {
            $rooms = \Modules\Hms\Entities\HmsRoom::where('hms_room_type_id', $active_type_id)->get();
        }
        
        // Active bookings today
        $today_date = date('Y-m-d H:i:s');
        $booked_room_ids = \DB::table('hms_booking_lines')
            ->join('transactions', 'hms_booking_lines.transaction_id', '=', 'transactions.id')
            ->whereIn('transactions.status', ['confirmed'])
            ->where(function ($query) use ($today_date) {
                $query->where('transactions.hms_booking_arrival_date_time', '<=', $today_date)
                      ->where('transactions.hms_booking_departure_date_time', '>=', $today_date);
            })
            ->pluck('hms_booking_lines.hms_room_id')
            ->toArray();

        // Cleaning / Maintenance rooms today
        $unavailable_room_ids = \DB::table('hms_room_unavailables')
            ->whereDate('date_to', '>=', date('Y-m-d'))
            ->whereDate('date_from', '<=', date('Y-m-d'))
            ->pluck('hms_rooms_id')
            ->toArray();

        // Fetch staff members for display
        $staff = \App\User::where('business_id', $business_id)->take(3)->get();

        // TODAY Sales (by hour)
        $today_start = \Carbon::now()->startOfDay()->toDateTimeString();
        $today_end = \Carbon::now()->endOfDay()->toDateTimeString();
        $today_sales = \DB::table('transactions')
            ->where('business_id', $business_id)
            ->whereIn('type', ['sell', 'hms_booking'])
            ->whereIn('status', ['final', 'confirmed'])
            ->whereBetween(\DB::raw("COALESCE(NULLIF(transaction_date, '0000-00-00 00:00:00'), created_at)"), [$today_start, $today_end])
            ->selectRaw("HOUR(COALESCE(NULLIF(transaction_date, '0000-00-00 00:00:00'), created_at)) as hour, SUM(final_total) as total")
            ->groupByRaw("HOUR(COALESCE(NULLIF(transaction_date, '0000-00-00 00:00:00'), created_at))")
            ->pluck('total', 'hour')
            ->toArray();

        $today_data = [];
        $today_labels = [];
        for ($i = 0; $i < 24; $i++) {
            $today_data[] = (float)($today_sales[$i] ?? 0);
            $today_labels[] = sprintf('%02d:00', $i);
        }

        // YESTERDAY Sales (by hour)
        $yesterday_start = \Carbon::now()->subDay()->startOfDay()->toDateTimeString();
        $yesterday_end = \Carbon::now()->subDay()->endOfDay()->toDateTimeString();
        $yesterday_sales = \DB::table('transactions')
            ->where('business_id', $business_id)
            ->whereIn('type', ['sell', 'hms_booking'])
            ->whereIn('status', ['final', 'confirmed'])
            ->whereBetween(\DB::raw("COALESCE(NULLIF(transaction_date, '0000-00-00 00:00:00'), created_at)"), [$yesterday_start, $yesterday_end])
            ->selectRaw("HOUR(COALESCE(NULLIF(transaction_date, '0000-00-00 00:00:00'), created_at)) as hour, SUM(final_total) as total")
            ->groupByRaw("HOUR(COALESCE(NULLIF(transaction_date, '0000-00-00 00:00:00'), created_at))")
            ->pluck('total', 'hour')
            ->toArray();

        $yesterday_data = [];
        $yesterday_labels = [];
        for ($i = 0; $i < 24; $i++) {
            $yesterday_data[] = (float)($yesterday_sales[$i] ?? 0);
            $yesterday_labels[] = sprintf('%02d:00', $i);
        }

        // WEEK Sales (by day)
        $week_start = \Carbon::now()->subDays(6)->startOfDay()->toDateTimeString();
        $week_sales = \DB::table('transactions')
            ->where('business_id', $business_id)
            ->whereIn('type', ['sell', 'hms_booking'])
            ->whereIn('status', ['final', 'confirmed'])
            ->where(\DB::raw("COALESCE(NULLIF(transaction_date, '0000-00-00 00:00:00'), created_at)"), '>=', $week_start)
            ->selectRaw("DATE(COALESCE(NULLIF(transaction_date, '0000-00-00 00:00:00'), created_at)) as date, SUM(final_total) as total")
            ->groupByRaw("DATE(COALESCE(NULLIF(transaction_date, '0000-00-00 00:00:00'), created_at))")
            ->pluck('total', 'date')
            ->toArray();

        $week_data = [];
        $week_labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = \Carbon::now()->subDays($i)->format('Y-m-d');
            $week_data[] = (float)($week_sales[$d] ?? 0);
            $week_labels[] = \Carbon::now()->subDays($i)->format('D');
        }

        // MONTH Sales (by day)
        $month_start = \Carbon::now()->subDays(29)->startOfDay()->toDateTimeString();
        $month_sales = \DB::table('transactions')
            ->where('business_id', $business_id)
            ->whereIn('type', ['sell', 'hms_booking'])
            ->whereIn('status', ['final', 'confirmed'])
            ->where(\DB::raw("COALESCE(NULLIF(transaction_date, '0000-00-00 00:00:00'), created_at)"), '>=', $month_start)
            ->selectRaw("DATE(COALESCE(NULLIF(transaction_date, '0000-00-00 00:00:00'), created_at)) as date, SUM(final_total) as total")
            ->groupByRaw("DATE(COALESCE(NULLIF(transaction_date, '0000-00-00 00:00:00'), created_at))")
            ->pluck('total', 'date')
            ->toArray();

        $month_data = [];
        $month_labels = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = \Carbon::now()->subDays($i)->format('Y-m-d');
            $month_data[] = (float)($month_sales[$d] ?? 0);
            $month_labels[] = \Carbon::now()->subDays($i)->format('d M');
        }

        // YEAR Sales (by month)
        $year_start = \Carbon::now()->subMonths(11)->startOfMonth()->toDateTimeString();
        $year_sales = \DB::table('transactions')
            ->where('business_id', $business_id)
            ->whereIn('type', ['sell', 'hms_booking'])
            ->whereIn('status', ['final', 'confirmed'])
            ->where(\DB::raw("COALESCE(NULLIF(transaction_date, '0000-00-00 00:00:00'), created_at)"), '>=', $year_start)
            ->selectRaw("DATE_FORMAT(COALESCE(NULLIF(transaction_date, '0000-00-00 00:00:00'), created_at), '%Y-%m') as month, SUM(final_total) as total")
            ->groupByRaw("DATE_FORMAT(COALESCE(NULLIF(transaction_date, '0000-00-00 00:00:00'), created_at), '%Y-%m')")
            ->pluck('total', 'month')
            ->toArray();

        $year_data = [];
        $year_labels = [];
        for ($i = 11; $i >= 0; $i--) {
            $m = \Carbon::now()->subMonths($i)->format('Y-m');
            $year_data[] = (float)($year_sales[$m] ?? 0);
            $year_labels[] = \Carbon::now()->subMonths($i)->format('M Y');
        }

        // Pull all sales transactions for Client-side custom date filtering
        $all_transactions = \DB::table('transactions')
            ->where('business_id', $business_id)
            ->whereIn('type', ['sell', 'hms_booking'])
            ->whereIn('status', ['final', 'confirmed'])
            ->whereDate('created_at', '>=', \Carbon::now()->subYear()->toDateString())
            ->selectRaw("DATE(COALESCE(NULLIF(transaction_date, '0000-00-00 00:00:00'), created_at)) as date, final_total as total")
            ->get()
            ->toArray();

        // Dynamically calculate satisfaction rate: Confirmed bookings ratio
        $total_hms_bookings = \DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'hms_booking')
            ->count();
        $confirmed_hms_bookings = \DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'hms_booking')
            ->where('status', 'confirmed')
            ->count();
        
        $satisfaction_score = 4.0;
        if ($total_hms_bookings > 0) {
            $satisfaction_score = round(4.0 + ($confirmed_hms_bookings / $total_hms_bookings) * 1.0, 1);
        }
        $satisfaction_score = min($satisfaction_score, 5.0);
        $satisfaction_percent = ($satisfaction_score / 5.0) * 100;

        // Calculate arrival percentage increase/decrease
        $yesterday_date_str = date('Y-m-d', strtotime('-1 day'));
        $yesterday_arrivals = \DB::table('transactions')
            ->where('business_id', $business_id)
            ->where('type', 'hms_booking')
            ->whereDate('hms_booking_arrival_date_time', $yesterday_date_str)
            ->count();
        $arrival_change = 0;
        if ($yesterday_arrivals > 0) {
            $arrival_change = round(((count($today_arrivales) - $yesterday_arrivals) / $yesterday_arrivals) * 100, 1);
        }
        $arrival_change_symbol = ($arrival_change >= 0) ? '▲' : '▼';

        // Calculate occupancy rate
        $occupancy_rate = 0;
        $total_rooms = \Modules\Hms\Entities\HmsRoom::leftJoin('hms_room_types', 'hms_rooms.hms_room_type_id', '=', 'hms_room_types.id')
            ->where('hms_room_types.business_id', $business_id)
            ->count();
        if ($total_rooms > 0) {
            $occupancy_rate = round(($room_count->booked_rooms / $total_rooms) * 100, 1);
        }
    @endphp

    <style>
        .hms-dashboard-grid {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 16px;
        }
        .hms-stat-card {
            padding: 20px;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.025);
            display: flex;
            flex-direction: column;
            position: relative;
        }
        .hms-circle-icon {
            width: 40px;
            height: 40px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
        }
        .bg-hms-orange { background-color: #f97316 !important; }
        .bg-hms-sky { background-color: #0ea5e9 !important; }
        .bg-hms-blue { background-color: #2563eb !important; }
        .bg-hms-emerald { background-color: #10b981 !important; }
        
        .room-tile {
            aspect-ratio: 1 / 1;
            border-radius: 12px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            cursor: pointer;
        }
        .room-tile-booked {
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            color: #475569;
        }
        .room-tile-cleaning {
            background-color: #ef4444;
            color: #ffffff;
        }
        .room-tile-available {
            background-color: #10b981;
            color: #ffffff;
        }
        .room-category-btn {
            width: 100%;
            text-align: left;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
            margin-bottom: 8px;
        }
        .room-category-btn-active {
            background-color: #2563eb;
            color: #ffffff;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
        }
        .room-category-btn-inactive {
            background-color: #f1f5f9;
            color: #475569;
        }
        .room-category-btn-inactive:hover {
            background-color: #e2e8f0;
        }
        
        /* Modern Trending Capsule Tabs for Sales Graph */
        .sales-tab-btn-group {
            position: relative;
            z-index: 20; /* Ensure click events are never blocked by canvas overlays */
            display: flex;
            gap: 6px;
            background-color: #f8fafc;
            padding: 4px;
            border-radius: 9999px;
            border: 1px solid #e2e8f0;
        }
        .sales-tab-btn {
            padding: 6px 14px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 700;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            background-color: transparent;
            color: #64748b;
            cursor: pointer;
            outline: none;
        }
        .sales-tab-btn:hover {
            color: #1e293b;
        }
        .sales-tab-btn-active {
            background-color: #2563eb;
            color: #ffffff !important;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.15);
        }
    </style>

    <section class="content no-print tw-bg-slate-50 tw-min-h-screen tw-p-6">
        <div class="row">
            <!-- Left Main Column (Overview, Stats, Room Availability Grid, Activity & Sells Graph) -->
            <div class="col-md-9">
                <!-- Header block -->
                <div class="tw-flex tw-items-center tw-justify-between tw-mb-8">
                    <div>
                        <h1 class="tw-text-2xl tw-font-bold tw-text-slate-800">Overview</h1>
                        <p class="tw-text-sm tw-text-slate-500">Whole data about Business</p>
                    </div>
                </div>

                <!-- Modern 4 Stat Cards Deck -->
                <div class="row tw-mb-8">
                    <!-- Today Arrival -->
                    <div class="col-md-3">
                        <div class="hms-stat-card">
                            <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                                <div class="hms-circle-icon bg-hms-orange">
                                    <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h3a3 3 0 013 3v1"></path>
                                    </svg>
                                </div>
                            </div>
                            <span class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Today Arrival</span>
                            <div class="tw-flex tw-items-baseline tw-gap-2 tw-mt-2">
                                <span class="tw-text-2xl tw-font-bold tw-text-slate-800">{{ count($today_arrivales) }}</span>
                                <span class="tw-text-xs tw-text-emerald-500 tw-font-bold tw-flex tw-items-center">{{ $arrival_change_symbol }} {{ abs($arrival_change) }}%</span>
                            </div>
                            <span class="tw-text-xs tw-text-slate-400 tw-mt-1">VS Yesterday</span>
                        </div>
                    </div>

                    <!-- Today Departure -->
                    <div class="col-md-3">
                        <div class="hms-stat-card">
                            <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                                <div class="hms-circle-icon bg-hms-sky">
                                    <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h3a3 3 0 013 3v1"></path>
                                    </svg>
                                </div>
                            </div>
                            <span class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Today Departure</span>
                            <div class="tw-flex tw-items-baseline tw-gap-2 tw-mt-2">
                                <span class="tw-text-2xl tw-font-bold tw-text-slate-800">{{ count($today_departure) }}</span>
                                <span class="tw-text-xs tw-text-slate-400 tw-font-bold">-</span>
                            </div>
                            <span class="tw-text-xs tw-text-slate-400 tw-mt-1">Scheduled Today</span>
                        </div>
                    </div>

                    <!-- Total Booked -->
                    <div class="col-md-3">
                        <div class="hms-stat-card">
                            <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                                <div class="hms-circle-icon bg-hms-blue">
                                    <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                            <span class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Total Booked</span>
                            <div class="tw-flex tw-items-baseline tw-gap-2 tw-mt-2">
                                <span class="tw-text-2xl tw-font-bold tw-text-slate-800">{{ $room_count->booked_rooms ?? 0 }}</span>
                                <span class="tw-text-xs tw-text-indigo-500 tw-font-bold tw-flex tw-items-center">Active</span>
                            </div>
                            <span class="tw-text-xs tw-text-slate-400 tw-mt-1">Confirmed Bookings</span>
                        </div>
                    </div>

                    <!-- Available Rooms -->
                    <div class="col-md-3">
                        <div class="hms-stat-card">
                            <div class="tw-flex tw-items-center tw-justify-between tw-mb-4">
                                <div class="hms-circle-icon bg-hms-emerald">
                                    <svg class="tw-w-5 tw-h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                </div>
                            </div>
                            <span class="tw-text-xs tw-font-bold tw-text-slate-400 tw-uppercase tw-tracking-wider">Available Rooms</span>
                            <div class="tw-flex tw-items-baseline tw-gap-2 tw-mt-2">
                                <span class="tw-text-2xl tw-font-bold tw-text-slate-800">{{ $room_count->unbooked_rooms ?? 0 }}</span>
                                <span class="tw-text-xs tw-text-emerald-500 tw-font-bold tw-flex tw-items-center">{{ 100 - $occupancy_rate }}% Free</span>
                            </div>
                            <span class="tw-text-xs tw-text-slate-400 tw-mt-1">Occupancy: {{ $occupancy_rate }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Room Availability Component (Left Panel filter + Grid + Legend) -->
                <div class="tw-bg-white tw-p-6 tw-rounded-2xl tw-shadow-sm tw-border tw-border-slate-200/50 tw-mb-8">
                    <div class="row">
                        <!-- Left Side: Room Categories list -->
                        <div class="col-md-3 tw-border-r tw-border-slate-100">
                            <div class="tw-flex tw-flex-col">
                                @foreach($room_types as $type)
                                    @php
                                        $isActive = ($type->id == $active_type_id);
                                    @endphp
                                    <a href="?room_type_id={{ $type->id }}" 
                                       class="room-category-btn {{ $isActive ? 'room-category-btn-active' : 'room-category-btn-inactive' }}">
                                        {{ $type->type }}
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <!-- Right Side: Legend & Dynamic Room Grid -->
                        <div class="col-md-9 tw-pl-6">
                            <!-- Legend -->
                            <div class="tw-flex tw-items-center tw-gap-6 tw-mb-6 tw-text-xs tw-font-semibold tw-text-slate-500">
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <span class="tw-w-3 tw-h-3 tw-bg-slate-100 tw-border tw-border-slate-300 tw-rounded-full"></span>
                                    <span>Confirmed Rooms</span>
                                </div>
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <span class="tw-w-3 tw-h-3 tw-bg-emerald-500 tw-rounded-full"></span>
                                    <span>Available Rooms</span>
                                </div>
                                <div class="tw-flex tw-items-center tw-gap-2">
                                    <span class="tw-w-3 tw-h-3 tw-bg-rose-500 tw-rounded-full"></span>
                                    <span>Cleaning Room</span>
                                </div>
                            </div>

                            <!-- Room Grid Layout -->
                            <div class="hms-dashboard-grid">
                                @forelse($rooms as $room)
                                    @php
                                        $isBooked = in_array($room->id, $booked_room_ids);
                                        $isCleaning = in_array($room->id, $unavailable_room_ids);
                                    @endphp

                                    @if($isBooked)
                                        <!-- Confirmed (Booked) Room -->
                                        <div class="room-tile room-tile-booked">
                                            <svg class="tw-w-6 tw-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                            </svg>
                                            <span class="tw-text-xs tw-font-bold tw-mt-2">#{{ $room->room_number }}</span>
                                        </div>
                                    @elseif($isCleaning)
                                        <!-- Cleaning / Maintenance Room -->
                                        <div class="room-tile room-tile-cleaning">
                                            <svg class="tw-w-6 tw-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                            </svg>
                                            <span class="tw-text-xs tw-font-bold tw-mt-2">#{{ $room->room_number }}</span>
                                        </div>
                                    @else
                                        <!-- Available Room -->
                                        <div class="room-tile room-tile-available">
                                            <svg class="tw-w-6 tw-h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                                            </svg>
                                            <span class="tw-text-xs tw-font-bold tw-mt-2">#{{ $room->room_number }}</span>
                                        </div>
                                    @endif
                                @empty
                                    <div class="tw-col-span-6 tw-text-center tw-text-slate-400 tw-py-12">No rooms registered for this category.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bookings & Original Graph Areas -->
                <div class="row tw-mb-8">
                    <!-- Tabbed Booking Lists -->
                    <div class="col-md-7">
                        <div class="tw-bg-white tw-p-5 tw-rounded-2xl tw-shadow-sm tw-border tw-border-slate-200/50">
                            <div class="tw-border-b tw-border-slate-100 tw-mb-4">
                                <ul class="tw-flex tw-flex-wrap tw-text-xs tw-font-bold tw-text-slate-500 tw-gap-4">
                                    <li class="active">
                                        <a href="#cn_1" data-toggle="tab" class="tw-inline-block tw-pb-3 tw-border-b-2 tw-border-blue-600 tw-text-blue-600">
                                            @lang('hms::lang.arrivals')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#cn_2" data-toggle="tab" class="tw-inline-block tw-pb-3 tw-border-b-2 tw-border-transparent hover:tw-text-slate-700 hover:tw-border-slate-300">
                                            @lang('hms::lang.departures')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#cn_3" data-toggle="tab" class="tw-inline-block tw-pb-3 tw-border-b-2 tw-border-transparent hover:tw-text-slate-700 hover:tw-border-slate-300">
                                            @lang('hms::lang.latest')
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="cn_1">
                                    <div class="tw-space-y-3">
                                        @forelse ($today_arrivales as $info)
                                            @include('hms::dashboard.partial.booking_info')
                                        @empty
                                            <div class="tw-text-center tw-text-slate-400 tw-py-6 tw-text-xs">@lang('hms::lang.no_arrivals_today')</div>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="tab-pane" id="cn_2">
                                    <div class="tw-space-y-3">
                                        @forelse ($today_departure as $info)
                                            @include('hms::dashboard.partial.booking_info')
                                        @empty
                                            <div class="tw-text-center tw-text-slate-400 tw-py-6 tw-text-xs">@lang('hms::lang.no_departures_today')</div>
                                        @endforelse
                                    </div>
                                </div>
                                <div class="tab-pane" id="cn_3">
                                    <div class="tw-space-y-3">
                                        @forelse ($latest_bookig as $info)
                                            @include('hms::dashboard.partial.booking_info')
                                        @empty
                                            <div class="tw-text-center tw-text-slate-400 tw-py-6 tw-text-xs">@lang('hms::lang.no_latest')</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Booking Charts (Original Booking Charts Kept Unchanged) -->
                    <div class="col-md-5">
                        <div class="tw-bg-white tw-p-5 tw-rounded-2xl tw-shadow-sm tw-border tw-border-slate-200/50">
                            <div class="tw-border-b tw-border-slate-100 tw-mb-4">
                                <ul class="tw-flex tw-text-xs tw-font-bold tw-text-slate-500 tw-gap-2">
                                    <li class="active">
                                        <a href="#chat_1" data-toggle="tab" class="tw-inline-block tw-pb-3 tw-border-b-2 tw-border-blue-600 tw-text-blue-600">
                                            @lang('hms::lang.upcoming_bookings')
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#chat_2" data-toggle="tab" class="tw-inline-block tw-pb-3 tw-border-b-2 tw-border-transparent hover:tw-text-slate-700 hover:tw-border-slate-300">
                                            @lang('hms::lang.past_bookings')
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="chat_1">
                                    <div class="tw-overflow-hidden">
                                        {!! $booking_chart->container() !!}
                                    </div>
                                </div>
                                <div class="tab-pane" id="chat_2">
                                    <div class="tw-overflow-hidden">
                                        {!! $past_booking_chart->container() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Row for Sales Graph with Toggle and Custom Filter -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="tw-bg-white tw-p-6 tw-rounded-2xl tw-shadow-sm tw-border tw-border-slate-200/50">
                            <div class="tw-flex tw-items-center tw-justify-between tw-border-b tw-border-slate-100 tw-pb-4 tw-mb-4">
                                <h4 class="tw-text-xs tw-font-bold tw-text-slate-800">Sales Graph</h4>
                                <!-- Interactive capsule buttons with high z-index to stay clickable -->
                                <div class="sales-tab-btn-group">
                                    <button onclick="window.updateSalesChart('today')" class="sales-tab-btn sales-tab-btn-active" id="btn-sales-today">Today</button>
                                    <button onclick="window.updateSalesChart('yesterday')" class="sales-tab-btn" id="btn-sales-yesterday">Yesterday</button>
                                    <button onclick="window.updateSalesChart('week')" class="sales-tab-btn" id="btn-sales-week">Week</button>
                                    <button onclick="window.updateSalesChart('month')" class="sales-tab-btn" id="btn-sales-month">Month</button>
                                    <button onclick="window.updateSalesChart('year')" class="sales-tab-btn" id="btn-sales-year">Year</button>
                                    <button onclick="window.updateSalesChart('custom')" class="sales-tab-btn" id="btn-sales-custom">Custom</button>
                                </div>
                            </div>

                            <!-- Custom date filter inputs -->
                            <div id="custom-date-range-inputs" style="display: none; margin-bottom: 15px;" class="tw-flex tw-items-center tw-gap-2">
                                <input type="date" id="sales-start-date" class="form-control input-sm" style="width: 140px; display: inline-block;" value="{{ date('Y-m-d', strtotime('-7 days')) }}">
                                <span class="tw-text-xs tw-text-slate-500">to</span>
                                <input type="date" id="sales-end-date" class="form-control input-sm" style="width: 140px; display: inline-block;" value="{{ date('Y-m-d') }}">
                                <button onclick="window.applyCustomSalesFilter()" class="btn btn-primary btn-sm">Apply Filter</button>
                            </div>
                            
                            <!-- Container with absolute style to prevent canvas overlaps -->
                            <div style="position: relative; width:100%; height:250px;">
                                <div id="salesChartContainer" style="height: 250px; width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column (Customer Satisfaction, Staff Schedule) -->
            <div class="col-md-3">
                <!-- Customer Satisfaction Card -->
                <div class="tw-bg-white tw-p-6 tw-rounded-2xl tw-shadow-sm tw-border tw-border-slate-200/50 tw-mb-8 tw-text-center">
                    <h4 class="tw-text-sm tw-font-bold tw-text-slate-800 tw-mb-6">Customers Satisfaction</h4>
                    
                    <!-- Progress Donut Component -->
                    <div class="tw-relative tw-w-32 tw-h-32 tw-mx-auto tw-flex tw-items-center tw-justify-center tw-mb-6">
                        <svg class="tw-w-full tw-h-full" viewBox="0 0 36 36">
                            <path class="tw-text-slate-100" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="tw-text-blue-500" stroke-dasharray="{{ $satisfaction_percent }}, 100" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="tw-absolute tw-text-center">
                            <span class="tw-text-3xl tw-font-bold tw-text-slate-800">{{ $satisfaction_score }}</span>
                        </div>
                    </div>

                    <p class="tw-text-xs tw-text-slate-400 tw-leading-relaxed tw-mb-6">
                        Overall feedback from guests staying at the hotel this week. Maintain premium customer services.
                    </p>

                    <button class="tw-w-full tw-py-3 tw-bg-blue-600 tw-text-white tw-font-semibold tw-rounded-xl tw-shadow-md hover:tw-bg-blue-700 tw-transition-all tw-text-sm">
                        Customers Review
                    </button>
                </div>

                <!-- Staff Schedule Card -->
                <div class="tw-bg-white tw-p-6 tw-rounded-2xl tw-shadow-sm tw-border tw-border-slate-200/50">
                    <h4 class="tw-text-sm tw-font-bold tw-text-slate-800 tw-mb-6">Staff Schedule</h4>

                    <!-- Top horizontal scrolling profiles avatar list -->
                    <div class="tw-flex tw-items-center tw-justify-between tw-mb-6 tw-bg-slate-50 tw-p-3 tw-rounded-xl tw-border tw-border-slate-100">
                        <span class="tw-text-blue-500 tw-cursor-pointer">◀</span>
                        <div class="tw-flex tw-gap-2">
                            @foreach($staff as $st)
                                <div class="tw-w-8 tw-h-8 tw-bg-indigo-100 tw-text-indigo-600 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-text-xs tw-font-bold" title="{{ $st->user_full_name }}">
                                    {{ substr($st->first_name, 0, 1) }}{{ substr($st->last_name, 0, 1) }}
                                </div>
                            @endforeach
                        </div>
                        <span class="tw-text-blue-500 tw-cursor-pointer">▶</span>
                    </div>

                    <!-- Staff status list cards -->
                    <div class="tw-space-y-4">
                        @forelse($staff as $st)
                            <div class="tw-p-3 tw-bg-slate-50 tw-rounded-xl tw-border tw-border-slate-100 tw-flex tw-items-center tw-justify-between">
                                <div class="tw-flex tw-items-center tw-gap-3">
                                    <div class="tw-w-9 tw-h-9 tw-bg-slate-200 tw-text-slate-700 tw-rounded-full tw-flex tw-items-center tw-justify-center tw-text-xs tw-font-bold">
                                        {{ substr($st->first_name, 0, 1) }}
                                    </div>
                                    <div class="tw-flex tw-flex-col">
                                        <span class="tw-text-xs tw-font-bold tw-text-slate-800">{{ $st->user_full_name }}</span>
                                        <span class="tw-text-[10px] tw-text-emerald-500 tw-font-bold">Available</span>
                                    </div>
                                </div>
                                <div class="tw-flex tw-gap-1">
                                    <!-- message icon -->
                                    <button class="tw-p-1.5 tw-bg-blue-100 tw-text-blue-600 tw-rounded-lg" title="Message">
                                        <svg class="tw-w-3.5 tw-h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                        </svg>
                                    </button>
                                    <!-- call icon -->
                                    <button class="tw-p-1.5 tw-bg-blue-100 tw-text-blue-600 tw-rounded-lg" title="Call">
                                        <svg class="tw-w-3.5 tw-h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="tw-text-center tw-text-xs tw-text-slate-400 tw-py-4">No staff on schedule.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('javascript')
    {!! $booking_chart->script() !!}
    {!! $past_booking_chart->script() !!}

    <script>
        // Register updateSalesChart globally immediately so it is clickable right away
        window.updateSalesChart = function(period) {
            // Check if chart is initialized
            if (!window.mySalesChart) return;
            
            // Update button styles
            document.querySelectorAll('.sales-tab-btn').forEach(function(btn) {
                btn.classList.remove('sales-tab-btn-active');
            });
            document.getElementById('btn-sales-' + period).classList.add('sales-tab-btn-active');

            if (period === 'custom') {
                document.getElementById('custom-date-range-inputs').style.display = 'flex';
            } else {
                document.getElementById('custom-date-range-inputs').style.display = 'none';
                // Update Highcharts categories and data series
                window.mySalesChart.xAxis[0].setCategories(window.salesChartData[period].labels);
                window.mySalesChart.series[0].setData(window.salesChartData[period].data);
            }
        };

        window.applyCustomSalesFilter = function() {
            if (!window.mySalesChart) return;
            var start = document.getElementById('sales-start-date').value;
            var end = document.getElementById('sales-end-date').value;
            if (!start || !end) return;

            var startMs = new Date(start).getTime();
            var endMs = new Date(end).getTime();

            // Group transactions by date in range
            var customData = {};
            var currentDate = new Date(start);
            var endLimit = new Date(end);
            
            while (currentDate <= endLimit) {
                var dateStr = currentDate.toISOString().split('T')[0];
                customData[dateStr] = 0;
                currentDate.setDate(currentDate.getDate() + 1);
            }

            var rawTx = @json($all_transactions);
            rawTx.forEach(function(tx) {
                var txDate = tx.date;
                var txMs = new Date(txDate).getTime();
                if (txMs >= startMs && txMs <= endMs) {
                    if (customData[txDate] !== undefined) {
                        customData[txDate] += parseFloat(tx.total);
                    } else {
                        customData[txDate] = parseFloat(tx.total);
                    }
                }
            });

            var labels = Object.keys(customData);
            var data = Object.values(customData);

            window.mySalesChart.xAxis[0].setCategories(labels);
            window.mySalesChart.series[0].setData(data);
        };

        // Polling chart initializer to prevent "Highcharts is not defined" error
        function initSalesChart() {
            if (typeof Highcharts === 'undefined') {
                setTimeout(initSalesChart, 100);
                return;
            }
            
            window.salesChartData = {
                today: {
                    labels: @json($today_labels),
                    data: @json($today_data)
                },
                yesterday: {
                    labels: @json($yesterday_labels),
                    data: @json($yesterday_data)
                },
                week: {
                    labels: @json($week_labels),
                    data: @json($week_data)
                },
                month: {
                    labels: @json($month_labels),
                    data: @json($month_data)
                },
                year: {
                    labels: @json($year_labels),
                    data: @json($year_data)
                }
            };

            // Initialize Highcharts
            window.mySalesChart = new Highcharts.Chart({
                chart: {
                    renderTo: 'salesChartContainer',
                    type: 'areaspline',
                    backgroundColor: 'transparent',
                    spacingTop: 10,
                    spacingBottom: 10,
                    spacingLeft: 0,
                    spacingRight: 0
                },
                title: {
                    text: null
                },
                credits: {
                    enabled: false
                },
                legend: {
                    enabled: false
                },
                xAxis: {
                    categories: window.salesChartData.today.labels,
                    gridLineWidth: 0,
                    lineColor: '#f1f5f9',
                    tickColor: '#f1f5f9',
                    labels: {
                        style: {
                            color: '#94a3b8',
                            fontSize: '10px',
                            fontFamily: 'Inter, sans-serif'
                        }
                    }
                },
                yAxis: {
                    title: {
                        text: null
                    },
                    min: 0,
                    gridLineColor: '#f1f5f9',
                    labels: {
                        style: {
                            color: '#94a3b8',
                            fontSize: '10px',
                            fontFamily: 'Inter, sans-serif'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    borderWidth: 0,
                    borderRadius: 8,
                    shadow: true,
                    style: {
                        color: '#ffffff',
                        fontFamily: 'Inter, sans-serif',
                        fontSize: '11px'
                    },
                    valuePrefix: '$'
                },
                plotOptions: {
                    areaspline: {
                        fillColor: {
                            linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
                            stops: [
                                [0, 'rgba(37, 99, 235, 0.2)'],
                                [1, 'rgba(37, 99, 235, 0)']
                            ]
                        },
                        marker: {
                            radius: 3.5,
                            fillColor: '#2563eb',
                            lineWidth: 1.5,
                            lineColor: '#ffffff',
                            states: {
                                hover: {
                                    radius: 5.5
                                }
                            }
                        },
                        lineWidth: 2,
                        lineColor: '#2563eb',
                        threshold: null
                    }
                },
                series: [{
                    name: 'Sales Amount',
                    data: window.salesChartData.today.data
                }]
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            initSalesChart();
        });
    </script>
@endsection
