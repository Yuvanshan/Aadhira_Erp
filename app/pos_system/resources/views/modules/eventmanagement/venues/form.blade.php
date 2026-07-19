@extends('layouts.app')

@section('title', isset($venue) ? __('eventmanagement::lang.edit_venue') : __('eventmanagement::lang.new_venue'))

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h4>{{ isset($venue) ? __('eventmanagement::lang.edit_venue') : __('eventmanagement::lang.new_venue') }}</h4>
            <a href="{{ route('eventmanagement.venues.index') }}" class="btn btn-secondary">{{ __('eventmanagement::lang.back_to_venues') }}</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ isset($venue) ? route('eventmanagement.venues.update', $venue->id) : route('eventmanagement.venues.store') }}" method="post">
                        @csrf
                        @if(isset($venue))
                            @method('put')
                        @endif

                        <div class="form-group mb-3">
                            <label for="name">{{ __('eventmanagement::lang.name') }}</label>
                            <input type="text" name="name" id="name" value="{{ old('name', $venue->name ?? '') }}" class="form-control" required>
                        </div>
                        <div class="form-group mb-3">
                            <label for="address">{{ __('eventmanagement::lang.address') }}</label>
                            <textarea name="address" id="address" class="form-control" rows="3">{{ old('address', $venue->address ?? '') }}</textarea>
                        </div>
                        <div class="form-group mb-3">
                            <label for="capacity">{{ __('eventmanagement::lang.capacity') }}</label>
                            <input type="number" name="capacity" id="capacity" value="{{ old('capacity', $venue->capacity ?? '') }}" class="form-control">
                        </div>
                        <div class="form-group mb-3">
                            <label for="notes">{{ __('eventmanagement::lang.notes') }}</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3">{{ old('notes', $venue->notes ?? '') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success">{{ __('eventmanagement::lang.save') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
