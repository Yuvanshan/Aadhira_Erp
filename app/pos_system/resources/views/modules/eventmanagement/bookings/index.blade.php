@extends('layouts.app')

@section('title', __('eventmanagement::lang.bookings'))

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4>{{ __('eventmanagement::lang.bookings') }}</h4>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('eventmanagement::lang.new_booking') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('eventmanagement.bookings.store') }}" method="post">
                        @csrf
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>{{ __('eventmanagement::lang.event') }}</label>
                                <select name="event_id" class="form-control" required>
                                    <option value="">{{ __('eventmanagement::lang.select_event') }}</option>
                                    @foreach($events as $event)
                                        <option value="{{ $event->id }}">{{ $event->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>{{ __('eventmanagement::lang.customer_name') }}</label>
                                <input type="text" name="customer_name" class="form-control" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label>{{ __('eventmanagement::lang.seats') }}</label>
                                <input type="number" name="seats" class="form-control" min="1" value="1" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label>{{ __('eventmanagement::lang.amount') }}</label>
                                <input type="number" step="0.01" name="amount" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>{{ __('eventmanagement::lang.status') }}</label>
                                <select name="status" class="form-control" required>
                                    <option value="pending">{{ __('eventmanagement::lang.pending') }}</option>
                                    <option value="confirmed">{{ __('eventmanagement::lang.confirmed') }}</option>
                                    <option value="cancelled">{{ __('eventmanagement::lang.cancelled') }}</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-success">{{ __('eventmanagement::lang.save') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>{{ __('eventmanagement::lang.event') }}</th>
                                    <th>{{ __('eventmanagement::lang.customer_name') }}</th>
                                    <th>{{ __('eventmanagement::lang.seats') }}</th>
                                    <th>{{ __('eventmanagement::lang.amount') }}</th>
                                    <th>{{ __('eventmanagement::lang.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bookings as $booking)
                                    <tr>
                                        <td>{{ optional($booking->event)->name }}</td>
                                        <td>{{ $booking->customer_name }}</td>
                                        <td>{{ $booking->seats }}</td>
                                        <td>{{ number_format($booking->amount, 2) }}</td>
                                        <td>{{ ucfirst($booking->status) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">{{ __('eventmanagement::lang.no_records_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $bookings->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
