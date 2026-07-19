@extends('layouts.app')

@section('title', __('eventmanagement::lang.event_details'))

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h4>{{ __('eventmanagement::lang.event_details') }}</h4>
            <a href="{{ route('eventmanagement.events.index') }}" class="btn btn-secondary">{{ __('eventmanagement::lang.back_to_events') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-3">{{ __('eventmanagement::lang.name') }}</dt>
                        <dd class="col-sm-9">{{ $event->name }}</dd>

                        <dt class="col-sm-3">{{ __('eventmanagement::lang.description') }}</dt>
                        <dd class="col-sm-9">{{ $event->description ?? '-' }}</dd>

                        <dt class="col-sm-3">{{ __('eventmanagement::lang.venue') }}</dt>
                        <dd class="col-sm-9">{{ optional($event->venue)->name ?? '-' }}</dd>

                        <dt class="col-sm-3">{{ __('eventmanagement::lang.stage') }}</dt>
                        <dd class="col-sm-9">{{ optional($event->stage)->name ?? '-' }}</dd>

                        <dt class="col-sm-3">{{ __('eventmanagement::lang.start_date') }}</dt>
                        <dd class="col-sm-9">{{ \Carbon\Carbon::parse($event->start_date)->format('Y-m-d H:i') }}</dd>

                        <dt class="col-sm-3">{{ __('eventmanagement::lang.end_date') }}</dt>
                        <dd class="col-sm-9">{{ $event->end_date ? \Carbon\Carbon::parse($event->end_date)->format('Y-m-d H:i') : '-' }}</dd>

                        <dt class="col-sm-3">{{ __('eventmanagement::lang.capacity') }}</dt>
                        <dd class="col-sm-9">{{ $event->capacity ?? '-' }}</dd>

                        <dt class="col-sm-3">{{ __('eventmanagement::lang.price') }}</dt>
                        <dd class="col-sm-9">{{ $event->price ? number_format($event->price, 2) : '-' }}</dd>

                        <dt class="col-sm-3">{{ __('eventmanagement::lang.status') }}</dt>
                        <dd class="col-sm-9">{{ ucfirst($event->status) }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
