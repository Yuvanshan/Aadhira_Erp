@extends('layouts.app')

@section('title', __('eventmanagement::lang.decoration_order_details'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <a href="{{ route('eventmanagement.decoration-orders.index') }}" class="btn btn-outline-secondary mr-3">
                        <i class="fas fa-arrow-left mr-1"></i>{{ __('eventmanagement::lang.back') }}
                    </a>
                    <div>
                        <h4 class="mb-1">{{ $decorationOrder->decoration_name }}</h4>
                        <p class="text-muted mb-0">{{ __('eventmanagement::lang.decoration_order_details') }}</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('eventmanagement.decoration-orders.edit', $decorationOrder->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit mr-1"></i>{{ __('eventmanagement::lang.edit') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Details -->
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ __('eventmanagement::lang.decoration_order_information') }}</h5>
                    <span class="badge badge-{{ $decorationOrder->status_badge }}">{{ ucfirst($decorationOrder->status) }}</span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">{{ __('eventmanagement::lang.event') }}</label>
                                <p class="mb-0 font-weight-bold">{{ $decorationOrder->event->name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">{{ __('eventmanagement::lang.decoration_type') }}</label>
                                <p class="mb-0">{{ $decorationTypes[$decorationOrder->decoration_type] ?? ucfirst($decorationOrder->decoration_type) }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">{{ __('eventmanagement::lang.decoration_name') }}</label>
                                <p class="mb-0">{{ $decorationOrder->decoration_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">{{ __('eventmanagement::lang.quantity') }}</label>
                                <p class="mb-0">{{ $decorationOrder->quantity }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">{{ __('eventmanagement::lang.unit_price') }}</label>
                                <p class="mb-0">${{ number_format($decorationOrder->unit_price, 2) }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">{{ __('eventmanagement::lang.total_price') }}</label>
                                <p class="mb-0 font-weight-bold text-primary">${{ number_format($decorationOrder->total_price, 2) }}</p>
                            </div>
                        </div>
                    </div>

                    @if($decorationOrder->supplier_name)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">{{ __('eventmanagement::lang.supplier_name') }}</label>
                                <p class="mb-0">{{ $decorationOrder->supplier_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">{{ __('eventmanagement::lang.supplier_contact') }}</label>
                                <p class="mb-0">{{ $decorationOrder->supplier_contact ?: 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($decorationOrder->delivery_date)
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted small">{{ __('eventmanagement::lang.delivery_date') }}</label>
                                <p class="mb-0">{{ $decorationOrder->delivery_date->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($decorationOrder->description)
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="text-muted small">{{ __('eventmanagement::lang.description') }}</label>
                                <p class="mb-0">{{ $decorationOrder->description }}</p>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if($decorationOrder->notes)
                    <div class="row">
                        <div class="col-12">
                            <div class="mb-3">
                                <label class="text-muted small">{{ __('eventmanagement::lang.notes') }}</label>
                                <p class="mb-0">{{ $decorationOrder->notes }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">{{ __('eventmanagement::lang.order_summary') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">{{ __('eventmanagement::lang.quantity') }}</small>
                            <p class="mb-2 font-weight-bold">{{ $decorationOrder->quantity }}</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('eventmanagement::lang.unit_price') }}</small>
                            <p class="mb-2">${{ number_format($decorationOrder->unit_price, 2) }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <strong>{{ __('eventmanagement::lang.total_price') }}</strong>
                                <strong class="text-primary">${{ number_format($decorationOrder->total_price, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-history mr-1"></i>{{ __('eventmanagement::lang.order_timeline') }}</h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <small class="text-muted">{{ __('eventmanagement::lang.created') }}</small>
                                <p class="mb-0">{{ $decorationOrder->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>

                        @if($decorationOrder->updated_at != $decorationOrder->created_at)
                        <div class="timeline-item mb-3">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <small class="text-muted">{{ __('eventmanagement::lang.last_updated') }}</small>
                                <p class="mb-0">{{ $decorationOrder->updated_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif

                        @if($decorationOrder->delivery_date)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <small class="text-muted">{{ __('eventmanagement::lang.delivery_scheduled') }}</small>
                                <p class="mb-0">{{ $decorationOrder->delivery_date->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">{{ __('eventmanagement::lang.quick_actions') }}</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('eventmanagement.decoration-orders.edit', $decorationOrder->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit mr-1"></i>{{ __('eventmanagement::lang.edit_order') }}
                        </a>
                        <a href="{{ route('eventmanagement.events.show', $decorationOrder->event_id) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-calendar mr-1"></i>{{ __('eventmanagement::lang.view_event') }}
                        </a>
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="confirmDelete()">
                            <i class="fas fa-trash mr-1"></i>{{ __('eventmanagement::lang.delete_order') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e9ecef;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    .timeline-marker {
        position: absolute;
        left: -22px;
        top: 5px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #e9ecef;
    }
    .timeline-content {
        background: #f8f9fa;
        padding: 10px 15px;
        border-radius: 8px;
        border-left: 3px solid #007bff;
    }
</style>

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('{{ __("eventmanagement::lang.confirm_delete") }}')) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("eventmanagement.decoration-orders.destroy", $decorationOrder->id) }}';

        var token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = '{{ csrf_token() }}';
        form.appendChild(token);

        var method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(method);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection