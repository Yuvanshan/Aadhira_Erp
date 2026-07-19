@extends('layouts.app')

@section('title', __('eventmanagement::lang.invoices'))

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4>{{ __('eventmanagement::lang.invoices') }}</h4>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('eventmanagement::lang.new_invoice') }}</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('eventmanagement.invoices.store') }}" method="post">
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
                                <label>{{ __('eventmanagement::lang.reference_no') }}</label>
                                <input type="text" name="reference_no" class="form-control" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label>{{ __('eventmanagement::lang.total_amount') }}</label>
                                <input type="number" step="0.01" name="total_amount" class="form-control" required>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label>{{ __('eventmanagement::lang.paid_amount') }}</label>
                                <input type="number" step="0.01" name="paid_amount" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>{{ __('eventmanagement::lang.due_date') }}</label>
                                <input type="date" name="due_date" class="form-control">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>{{ __('eventmanagement::lang.status') }}</label>
                                <select name="status" class="form-control" required>
                                    <option value="draft">{{ __('eventmanagement::lang.draft') }}</option>
                                    <option value="open">{{ __('eventmanagement::lang.open') }}</option>
                                    <option value="paid">{{ __('eventmanagement::lang.paid') }}</option>
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
                                    <th>{{ __('eventmanagement::lang.reference_no') }}</th>
                                    <th>{{ __('eventmanagement::lang.total_amount') }}</th>
                                    <th>{{ __('eventmanagement::lang.paid_amount') }}</th>
                                    <th>{{ __('eventmanagement::lang.status') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoices as $invoice)
                                    <tr>
                                        <td>{{ optional($invoice->event)->name }}</td>
                                        <td>{{ $invoice->reference_no }}</td>
                                        <td>{{ number_format($invoice->total_amount, 2) }}</td>
                                        <td>{{ number_format($invoice->paid_amount, 2) }}</td>
                                        <td>{{ ucfirst($invoice->status) }}</td>
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
                    {{ $invoices->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
