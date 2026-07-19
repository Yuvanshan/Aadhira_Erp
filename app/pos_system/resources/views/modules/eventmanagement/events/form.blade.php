@extends('layouts.app')

@section('title', isset($event) ? __('eventmanagement::lang.edit_event') : __('eventmanagement::lang.create_event'))

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h4>{{ isset($event) ? __('eventmanagement::lang.edit_event') : __('eventmanagement::lang.create_event') }}</h4>
            <a href="{{ route('eventmanagement.events.index') }}" class="btn btn-secondary">{{ __('eventmanagement::lang.back_to_events') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ isset($event) ? route('eventmanagement.events.update', $event->id) : route('eventmanagement.events.store') }}" method="post">
                        @csrf
                        @if(isset($event))
                            @method('put')
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name">{{ __('eventmanagement::lang.name') }}</label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $event->name ?? '') }}" class="form-control" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="venue_id">{{ __('eventmanagement::lang.venue') }}</label>
                                    <select name="venue_id" id="venue_id" class="form-control">
                                        <option value="">{{ __('eventmanagement::lang.select_venue') }}</option>
                                        @foreach($venues as $venue)
                                            <option value="{{ $venue->id }}" {{ old('venue_id', $event->venue_id ?? '') == $venue->id ? 'selected' : '' }}>{{ $venue->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="start_date">{{ __('eventmanagement::lang.start_date') }}</label>
                                    <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date', isset($event->start_date) ? \Carbon\Carbon::parse($event->start_date)->format('Y-m-d\TH:i') : '') }}" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="end_date">{{ __('eventmanagement::lang.end_date') }}</label>
                                    <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date', isset($event->end_date) ? \Carbon\Carbon::parse($event->end_date)->format('Y-m-d\TH:i') : '') }}" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="capacity">{{ __('eventmanagement::lang.capacity') }}</label>
                                    <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $event->capacity ?? '') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="price">{{ __('eventmanagement::lang.price') }}</label>
                                    <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $event->price ?? '') }}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="status">{{ __('eventmanagement::lang.status') }}</label>
                                    <select name="status" id="status" class="form-control" required>
                                        @foreach(['draft', 'open', 'closed', 'cancelled'] as $status)
                                            <option value="{{ $status }}" {{ old('status', $event->status ?? 'draft') === $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">{{ __('eventmanagement::lang.description') }}</label>
                            <textarea name="description" id="description" class="form-control" rows="4">{{ old('description', $event->description ?? '') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-success">{{ __('eventmanagement::lang.save') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
