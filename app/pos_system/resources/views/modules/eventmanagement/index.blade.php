@extends('layouts.app')

@section('title', __('eventmanagement::lang.event_management'))

@section('content')
<style>
    .stats-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border-radius: 12px;
        border: none;
        overflow: hidden;
    }
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .stats-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }
    .stats-number {
        font-size: 2rem;
        font-weight: bold;
        margin: 0;
    }
    .stats-label {
        font-size: 0.9rem;
        opacity: 0.8;
        margin: 0;
    }
    .recent-events-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    .event-status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .status-active { background-color: #28a745; color: white; }
    .status-upcoming { background-color: #ffc107; color: #212529; }
    .status-completed { background-color: #6c757d; color: white; }
    .status-cancelled { background-color: #dc3545; color: white; }
</style>

<div class="container-fluid">
    <!-- Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm" style="border-radius: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="d-flex align-items-center">
                    <i class="fas fa-calendar-alt fa-2x mr-3"></i>
                    <div>
                        <h5 class="mb-1">{{ __('eventmanagement::lang.event_management') }}</h5>
                        <p class="mb-0">{{ __('eventmanagement::lang.welcome_message') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body text-center p-4">
                    <div class="stats-icon text-primary mb-3">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stats-number text-primary">{{ $events->count() }}</div>
                    <div class="stats-label">{{ __('eventmanagement::lang.events') }}</div>
                    <a href="{{ route('eventmanagement.events.index') }}" class="btn btn-outline-primary btn-sm mt-3 w-100">
                        <i class="fas fa-eye mr-1"></i>{{ __('eventmanagement::lang.view_all') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body text-center p-4">
                    <div class="stats-icon text-success mb-3">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <div class="stats-number text-success">{{ $venues }}</div>
                    <div class="stats-label">{{ __('eventmanagement::lang.venues') }}</div>
                    <a href="{{ route('eventmanagement.venues.index') }}" class="btn btn-outline-success btn-sm mt-3 w-100">
                        <i class="fas fa-cog mr-1"></i>{{ __('eventmanagement::lang.manage') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body text-center p-4">
                    <div class="stats-icon text-warning mb-3">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                    <div class="stats-number text-warning">{{ $bookings }}</div>
                    <div class="stats-label">{{ __('eventmanagement::lang.bookings') }}</div>
                    <a href="{{ route('eventmanagement.bookings.index') }}" class="btn btn-outline-warning btn-sm mt-3 w-100">
                        <i class="fas fa-list mr-1"></i>{{ __('eventmanagement::lang.manage') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body text-center p-4">
                    <div class="stats-icon text-danger mb-3">
                        <i class="fas fa-check-double"></i>
                    </div>
                    <div class="stats-number text-danger">{{ $reservations }}</div>
                    <div class="stats-label">{{ __('eventmanagement::lang.reservations') }}</div>
                    <a href="{{ route('eventmanagement.reservations.index') }}" class="btn btn-outline-danger btn-sm mt-3 w-100">
                        <i class="fas fa-list mr-1"></i>{{ __('eventmanagement::lang.manage') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body text-center p-4">
                    <div class="stats-icon text-info mb-3">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div class="stats-number text-info">{{ $invoices }}</div>
                    <div class="stats-label">{{ __('eventmanagement::lang.invoices') }}</div>
                    <a href="{{ route('eventmanagement.invoices.index') }}" class="btn btn-outline-info btn-sm mt-3 w-100">
                        <i class="fas fa-list mr-1"></i>{{ __('eventmanagement::lang.manage') }}
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-3">
            <div class="card stats-card h-100">
                <div class="card-body text-center p-4">
                    <div class="stats-icon text-secondary mb-3">
                        <i class="fas fa-palette"></i>
                    </div>
                    <div class="stats-number text-secondary">{{ $decorationOrders }}</div>
                    <div class="stats-label">{{ __('eventmanagement::lang.decoration_orders') }}</div>
                    <a href="{{ route('eventmanagement.decoration-orders.index') }}" class="btn btn-outline-secondary btn-sm mt-3 w-100">
                        <i class="fas fa-list mr-1"></i>{{ __('eventmanagement::lang.manage') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Events and Quick Actions -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card recent-events-card h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center py-3">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-calendar-week mr-2 text-primary"></i>
                        {{ __('eventmanagement::lang.recent_events') }}
                    </h5>
                    <a href="{{ route('eventmanagement.events.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-1"></i>{{ __('eventmanagement::lang.create_event') }}
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-3 px-4">{{ __('eventmanagement::lang.name') }}</th>
                                    <th class="border-0 py-3">{{ __('eventmanagement::lang.start_date') }}</th>
                                    <th class="border-0 py-3">{{ __('eventmanagement::lang.end_date') }}</th>
                                    <th class="border-0 py-3">{{ __('eventmanagement::lang.status') }}</th>
                                    <th class="border-0 py-3">{{ __('eventmanagement::lang.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($events as $event)
                                    <tr>
                                        <td class="py-3 px-4">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-circle bg-primary text-white mr-3" style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                                    {{ strtoupper(substr($event->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold">{{ $event->name }}</div>
                                                    <small class="text-muted">{{ $event->venue->name ?? 'No Venue' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <i class="fas fa-calendar-alt text-muted mr-1"></i>
                                            {{ \Carbon\Carbon::parse($event->start_date)->format('M d, Y H:i') }}
                                        </td>
                                        <td class="py-3">
                                            @if($event->end_date)
                                                <i class="fas fa-calendar-check text-muted mr-1"></i>
                                                {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y H:i') }}
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td class="py-3">
                                            @php
                                                $statusClass = match($event->status) {
                                                    'active' => 'status-active',
                                                    'upcoming' => 'status-upcoming',
                                                    'completed' => 'status-completed',
                                                    'cancelled' => 'status-cancelled',
                                                    default => 'status-upcoming'
                                                };
                                            @endphp
                                            <span class="event-status-badge {{ $statusClass }}">
                                                {{ ucfirst($event->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3">
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('eventmanagement.events.show', $event->id) }}" class="btn btn-outline-primary btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('eventmanagement.events.edit', $event->id) }}" class="btn btn-outline-secondary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                                <h6 class="text-muted">{{ __('eventmanagement::lang.no_events_found') }}</h6>
                                                <a href="{{ route('eventmanagement.events.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus mr-1"></i>{{ __('eventmanagement::lang.create_first_event') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions Sidebar -->
        <div class="col-lg-4">
            <div class="card recent-events-card h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0 text-dark">
                        <i class="fas fa-bolt mr-2 text-warning"></i>
                        {{ __('eventmanagement::lang.quick_actions') }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('eventmanagement.events.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus-circle mr-2"></i>{{ __('eventmanagement::lang.new_event') }}
                        </a>
                        <a href="{{ route('eventmanagement.venues.create') }}" class="btn btn-success">
                            <i class="fas fa-map-marker-alt mr-2"></i>{{ __('eventmanagement::lang.add_venue') }}
                        </a>
                        <a href="{{ route('eventmanagement.bookings.index') }}" class="btn btn-warning">
                            <i class="fas fa-ticket-alt mr-2"></i>{{ __('eventmanagement::lang.manage_bookings') }}
                        </a>
                        <a href="{{ route('eventmanagement.invoices.create') }}" class="btn btn-info">
                            <i class="fas fa-file-invoice-dollar mr-2"></i>{{ __('eventmanagement::lang.create_invoice') }}
                        </a>
                        <a href="{{ route('eventmanagement.quotations.create') }}" class="btn btn-secondary">
                            <i class="fas fa-file-alt mr-2"></i>{{ __('eventmanagement::lang.create_quotation') }}
                        </a>
                        <a href="{{ route('eventmanagement.decoration-orders.create') }}" class="btn btn-info">
                            <i class="fas fa-palette mr-2"></i>{{ __('eventmanagement::lang.add_decoration') }}
                        </a>
                    </div>

                    <!-- Recent Activity -->
                    <hr class="my-4">
                    <h6 class="text-muted mb-3">
                        <i class="fas fa-history mr-1"></i>{{ __('eventmanagement::lang.recent_activity') }}
                    </h6>
                    <div class="activity-list">
                        @if($events->count() > 0)
                            <div class="activity-item mb-2">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-plus text-success mr-1"></i>
                                    Latest event: <strong>{{ $events->first()->name }}</strong> created
                                </small>
                            </div>
                        @endif
                        <div class="activity-item mb-2">
                            <small class="text-muted">
                                <i class="fas fa-chart-line text-info mr-1"></i>
                                {{ $bookings }} active bookings
                            </small>
                        </div>
                        <div class="activity-item mb-2">
                            <small class="text-muted">
                                <i class="fas fa-building text-primary mr-1"></i>
                                {{ $venues }} venues available
                            </small>
                        </div>
                        <div class="activity-item">
                            <small class="text-muted">
                                <i class="fas fa-palette text-warning mr-1"></i>
                                {{ $decorationOrders }} decoration orders
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
