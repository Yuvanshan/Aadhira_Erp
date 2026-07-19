@extends('layouts.app')

@section('title', __('eventmanagement::lang.events'))

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h4>{{ __('eventmanagement::lang.events') }}</h4>
            <a href="{{ route('eventmanagement.events.create') }}" class="btn btn-primary">{{ __('eventmanagement::lang.create_event') }}</a>
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
                                    <th>{{ __('eventmanagement::lang.name') }}</th>
                                    <th>{{ __('eventmanagement::lang.venue') }}</th>
                                    <th>{{ __('eventmanagement::lang.start_date') }}</th>
                                    <th>{{ __('eventmanagement::lang.end_date') }}</th>
                                    <th>{{ __('eventmanagement::lang.status') }}</th>
                                    <th>{{ __('eventmanagement::lang.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($events as $event)
                                    <tr>
                                        <td>{{ $event->name }}</td>
                                        <td>{{ optional($event->venue)->name }}</td>
                                        <td>{{ \Carbon\Carbon::parse($event->start_date)->format('Y-m-d H:i') }}</td>
                                        <td>{{ $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('Y-m-d H:i') : '-' }}</td>
                                        <td>{{ ucfirst($event->status) }}</td>
                                        <td>
                                            <a href="{{ route('eventmanagement.events.show', $event->id) }}" class="btn btn-outline-secondary btn-sm">{{ __('eventmanagement::lang.view') }}</a>
                                            <a href="{{ route('eventmanagement.events.edit', $event->id) }}" class="btn btn-outline-primary btn-sm">{{ __('eventmanagement::lang.edit') }}</a>
                                            <form action="{{ route('eventmanagement.events.destroy', $event->id) }}" method="post" class="d-inline-block" onsubmit="return confirm('{{ __('eventmanagement::lang.confirm_delete') }}');">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">{{ __('eventmanagement::lang.delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ __('eventmanagement::lang.no_records_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $events->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
