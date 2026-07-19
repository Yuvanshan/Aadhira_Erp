@extends('layouts.app')

@section('title', __('eventmanagement::lang.reservations'))

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4>{{ __('eventmanagement::lang.reservations') }}</h4>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('eventmanagement::lang.new_reservation') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('eventmanagement.reservations.store') }}" method="post">
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
                            <div class="col-md-4 mb-3">
                                <label>{{ __('eventmanagement::lang.reserved_at') }}</label>
                                <input type="datetime-local" name="reserved_at" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>{{ __('eventmanagement::lang.seats_reserved') }}</label>
                                <input type="number" name="seats_reserved" class="form-control" min="1" value="1" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>{{ __('eventmanagement::lang.status') }}</label>
                                <select name="status" class="form-control" required>
                                    <option value="pending">{{ __('eventmanagement::lang.pending') }}</option>
                                    <option value="approved">{{ __('eventmanagement::lang.approved') }}</option>
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
                                    <th>{{ __('eventmanagement::lang.reserved_at') }}</th>
                                    <th>{{ __('eventmanagement::lang.seats_reserved') }}</th>
                                    <th>{{ __('eventmanagement::lang.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservations as $reservation)
                                    <tr>
                                        <td>{{ optional($reservation->event)->name }}</td>
                                        <td>{{ $reservation->customer_name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($reservation->reserved_at)->format('Y-m-d H:i') }}</td>
                                        <td>{{ $reservation->seats_reserved }}</td>
                                        <td>{{ ucfirst($reservation->status) }}</td>
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
                    {{ $reservations->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
