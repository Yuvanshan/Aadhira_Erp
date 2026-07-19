@extends('layouts.app')

@section('title', __('eventmanagement::lang.quotations'))

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4>{{ __('eventmanagement::lang.quotations') }}</h4>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('eventmanagement::lang.new_quotation') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('eventmanagement.quotations.store') }}" method="post">
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
                                <label>{{ __('eventmanagement::lang.quotation_number') }}</label>
                                <input type="text" name="quotation_number" class="form-control" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>{{ __('eventmanagement::lang.total_amount') }}</label>
                                <input type="number" step="0.01" name="total_amount" class="form-control" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>{{ __('eventmanagement::lang.valid_until') }}</label>
                                <input type="date" name="valid_until" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>{{ __('eventmanagement::lang.status') }}</label>
                                <select name="status" class="form-control" required>
                                    <option value="draft">{{ __('eventmanagement::lang.draft') }}</option>
                                    <option value="open">{{ __('eventmanagement::lang.open') }}</option>
                                    <option value="accepted">{{ __('eventmanagement::lang.accepted') }}</option>
                                    <option value="rejected">{{ __('eventmanagement::lang.rejected') }}</option>
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
                                    <th>{{ __('eventmanagement::lang.quotation_number') }}</th>
                                    <th>{{ __('eventmanagement::lang.total_amount') }}</th>
                                    <th>{{ __('eventmanagement::lang.valid_until') }}</th>
                                    <th>{{ __('eventmanagement::lang.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($quotations as $quotation)
                                    <tr>
                                        <td>{{ optional($quotation->event)->name }}</td>
                                        <td>{{ $quotation->quotation_number }}</td>
                                        <td>{{ number_format($quotation->total_amount, 2) }}</td>
                                        <td>{{ $quotation->valid_until ? \Carbon\Carbon::parse($quotation->valid_until)->format('Y-m-d') : '-' }}</td>
                                        <td>{{ ucfirst($quotation->status) }}</td>
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
                    {{ $quotations->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
