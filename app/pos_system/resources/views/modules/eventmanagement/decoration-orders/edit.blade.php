@extends('layouts.app')

@section('title', __('eventmanagement::lang.edit_decoration_order'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <a href="{{ route('eventmanagement.decoration-orders.index') }}" class="btn btn-outline-secondary mr-3">
                    <i class="fas fa-arrow-left mr-1"></i>{{ __('eventmanagement::lang.back') }}
                </a>
                <div>
                    <h4 class="mb-1">{{ __('eventmanagement::lang.edit_decoration_order') }}</h4>
                    <p class="text-muted mb-0">{{ __('eventmanagement::lang.update_decoration_order_details') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">{{ __('eventmanagement::lang.decoration_order_details') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('eventmanagement.decoration-orders.update', $decorationOrder->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="event_id" class="form-label">{{ __('eventmanagement::lang.event') }} <span class="text-danger">*</span></label>
                                <select name="event_id" id="event_id" class="form-control @error('event_id') is-invalid @enderror" required>
                                    <option value="">{{ __('eventmanagement::lang.select_event') }}</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}" {{ old('event_id', $decorationOrder->event_id) == $event->id ? 'selected' : '' }}>
                                            {{ $event->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('event_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="decoration_type" class="form-label">{{ __('eventmanagement::lang.decoration_type') }} <span class="text-danger">*</span></label>
                                <select name="decoration_type" id="decoration_type" class="form-control @error('decoration_type') is-invalid @enderror" required>
                                    <option value="">{{ __('eventmanagement::lang.select_type') }}</option>
                                    @foreach($decorationTypes as $key => $value)
                                        <option value="{{ $key }}" {{ old('decoration_type', $decorationOrder->decoration_type) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('decoration_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="decoration_name" class="form-label">{{ __('eventmanagement::lang.decoration_name') }} <span class="text-danger">*</span></label>
                                <input type="text" name="decoration_name" id="decoration_name" class="form-control @error('decoration_name') is-invalid @enderror"
                                       value="{{ old('decoration_name', $decorationOrder->decoration_name) }}" required>
                                @error('decoration_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="quantity" class="form-label">{{ __('eventmanagement::lang.quantity') }} <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                       value="{{ old('quantity', $decorationOrder->quantity) }}" min="1" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="unit_price" class="form-label">{{ __('eventmanagement::lang.unit_price') }} <span class="text-danger">*</span></label>
                                <input type="number" name="unit_price" id="unit_price" class="form-control @error('unit_price') is-invalid @enderror"
                                       value="{{ old('unit_price', $decorationOrder->unit_price) }}" step="0.01" min="0" required>
                                @error('unit_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="supplier_name" class="form-label">{{ __('eventmanagement::lang.supplier_name') }}</label>
                                <input type="text" name="supplier_name" id="supplier_name" class="form-control @error('supplier_name') is-invalid @enderror"
                                       value="{{ old('supplier_name', $decorationOrder->supplier_name) }}">
                                @error('supplier_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="supplier_contact" class="form-label">{{ __('eventmanagement::lang.supplier_contact') }}</label>
                                <input type="text" name="supplier_contact" id="supplier_contact" class="form-control @error('supplier_contact') is-invalid @enderror"
                                       value="{{ old('supplier_contact', $decorationOrder->supplier_contact) }}">
                                @error('supplier_contact')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="delivery_date" class="form-label">{{ __('eventmanagement::lang.delivery_date') }}</label>
                                <input type="datetime-local" name="delivery_date" id="delivery_date" class="form-control @error('delivery_date') is-invalid @enderror"
                                       value="{{ old('delivery_date', $decorationOrder->delivery_date ? $decorationOrder->delivery_date->format('Y-m-d\TH:i') : '') }}">
                                @error('delivery_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">{{ __('eventmanagement::lang.status') }} <span class="text-danger">*</span></label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                    @foreach($statusOptions as $key => $value)
                                        <option value="{{ $key }}" {{ old('status', $decorationOrder->status) == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">{{ __('eventmanagement::lang.description') }}</label>
                                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                                          rows="3">{{ old('description', $decorationOrder->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="notes" class="form-label">{{ __('eventmanagement::lang.notes') }}</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                                          rows="2">{{ old('notes', $decorationOrder->notes) }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-2"></i>{{ __('eventmanagement::lang.save_decoration_order') }}
                                </button>
                                <a href="{{ route('eventmanagement.decoration-orders.index') }}" class="btn btn-outline-secondary ml-2">
                                    {{ __('eventmanagement::lang.cancel') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Summary Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">{{ __('eventmanagement::lang.order_summary') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">{{ __('eventmanagement::lang.quantity') }}</small>
                            <p class="mb-2" id="summary-quantity">{{ $decorationOrder->quantity }}</p>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">{{ __('eventmanagement::lang.unit_price') }}</small>
                            <p class="mb-2" id="summary-unit-price">${{ number_format($decorationOrder->unit_price, 2) }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <strong>{{ __('eventmanagement::lang.total_price') }}</strong>
                                <strong id="summary-total-price">${{ number_format($decorationOrder->total_price, 2) }}</strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Info -->
            <div class="card shadow-sm mt-3">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fas fa-info-circle mr-1"></i>{{ __('eventmanagement::lang.order_info') }}</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">{{ __('eventmanagement::lang.created_at') }}</small>
                        <p class="mb-1">{{ $decorationOrder->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">{{ __('eventmanagement::lang.updated_at') }}</small>
                        <p class="mb-1">{{ $decorationOrder->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                    @if($decorationOrder->supplier_name)
                        <div>
                            <small class="text-muted">{{ __('eventmanagement::lang.supplier') }}</small>
                            <p class="mb-0">{{ $decorationOrder->supplier_name }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Auto-calculate total price
    function calculateTotal() {
        var quantity = parseFloat($('#quantity').val()) || 0;
        var unitPrice = parseFloat($('#unit_price').val()) || 0;
        var total = quantity * unitPrice;

        $('#summary-quantity').text(quantity);
        $('#summary-unit-price').text('$' + unitPrice.toFixed(2));
        $('#summary-total-price').text('$' + total.toFixed(2));
    }

    $('#quantity, #unit_price').on('input', calculateTotal);
    calculateTotal(); // Initial calculation
});
</script>
@endpush
@endsection