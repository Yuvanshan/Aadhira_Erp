@extends('layouts.app')

@section('title', __('eventmanagement::lang.decoration_orders'))

@section('content')
<style>
    .decoration-card {
        transition: transform 0.2s ease-in-out;
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .decoration-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    }
    .decoration-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">{{ __('eventmanagement::lang.decoration_orders') }}</h4>
                    <p class="text-muted mb-0">{{ __('eventmanagement::lang.manage_event_decorations') }}</p>
                </div>
                <a href="{{ route('eventmanagement.decoration-orders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus mr-2"></i>{{ __('eventmanagement::lang.add_decoration_order') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card decoration-card h-100">
                <div class="card-body text-center">
                    <div class="decoration-icon bg-primary text-white mx-auto mb-3">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h6 class="text-muted mb-1">{{ __('eventmanagement::lang.pending_orders') }}</h6>
                    <h4 class="mb-0 text-primary">{{ $pendingOrders ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card decoration-card h-100">
                <div class="card-body text-center">
                    <div class="decoration-icon bg-info text-white mx-auto mb-3">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h6 class="text-muted mb-1">{{ __('eventmanagement::lang.confirmed_orders') }}</h6>
                    <h4 class="mb-0 text-info">{{ $confirmedOrders ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card decoration-card h-100">
                <div class="card-body text-center">
                    <div class="decoration-icon bg-success text-white mx-auto mb-3">
                        <i class="fas fa-truck"></i>
                    </div>
                    <h6 class="text-muted mb-1">{{ __('eventmanagement::lang.delivered_orders') }}</h6>
                    <h4 class="mb-0 text-success">{{ $deliveredOrders ?? 0 }}</h4>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card decoration-card h-100">
                <div class="card-body text-center">
                    <div class="decoration-icon bg-warning text-white mx-auto mb-3">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <h6 class="text-muted mb-1">{{ __('eventmanagement::lang.total_value') }}</h6>
                    <h4 class="mb-0 text-warning">${{ number_format($totalValue ?? 0, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="row">
        <div class="col-12">
            <div class="card decoration-card">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">{{ __('eventmanagement::lang.all_decoration_orders') }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="decoration-orders-table">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 py-3 px-4">{{ __('eventmanagement::lang.event') }}</th>
                                    <th class="border-0 py-3">{{ __('eventmanagement::lang.decoration_type') }}</th>
                                    <th class="border-0 py-3">{{ __('eventmanagement::lang.decoration_name') }}</th>
                                    <th class="border-0 py-3">{{ __('eventmanagement::lang.quantity') }}</th>
                                    <th class="border-0 py-3">{{ __('eventmanagement::lang.total_price') }}</th>
                                    <th class="border-0 py-3">{{ __('eventmanagement::lang.delivery_date') }}</th>
                                    <th class="border-0 py-3">{{ __('eventmanagement::lang.status') }}</th>
                                    <th class="border-0 py-3">{{ __('eventmanagement::lang.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#decoration-orders-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("eventmanagement.decoration-orders.index") }}',
        columns: [
            { data: 'event_name', name: 'event_name' },
            { data: 'decoration_type_formatted', name: 'decoration_type' },
            { data: 'decoration_name', name: 'decoration_name' },
            { data: 'quantity', name: 'quantity' },
            { data: 'total_price_formatted', name: 'total_price' },
            { data: 'delivery_date_formatted', name: 'delivery_date' },
            { data: 'status_badge', name: 'status', orderable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        pageLength: 25,
        responsive: true,
        language: {
            processing: '<i class="fas fa-spinner fa-spin"></i> Loading...'
        }
    });

    // Delete decoration order
    $(document).on('click', '.delete-decoration-order', function() {
        var id = $(this).data('id');
        if (confirm('{{ __("eventmanagement::lang.confirm_delete") }}')) {
            $.ajax({
                url: '{{ route("eventmanagement.decoration-orders.index") }}/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('#decoration-orders-table').DataTable().ajax.reload();
                        toastr.success(response.message);
                    }
                },
                error: function() {
                    toastr.error('{{ __("eventmanagement::lang.error_occurred") }}');
                }
            });
        }
    });
});
</script>
@endpush
@endsection