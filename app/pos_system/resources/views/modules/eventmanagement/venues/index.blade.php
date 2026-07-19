@extends('layouts.app')

@section('title', __('eventmanagement::lang.venues'))

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <h4>{{ __('eventmanagement::lang.venues') }}</h4>
            <a href="{{ route('eventmanagement.venues.create') }}" class="btn btn-primary">{{ __('eventmanagement::lang.new_venue') }}</a>
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
                                    <th>{{ __('eventmanagement::lang.address') }}</th>
                                    <th>{{ __('eventmanagement::lang.capacity') }}</th>
                                    <th>{{ __('eventmanagement::lang.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($venues as $venue)
                                    <tr>
                                        <td>{{ $venue->name }}</td>
                                        <td>{{ $venue->address }}</td>
                                        <td>{{ $venue->capacity ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('eventmanagement.venues.edit', $venue->id) }}" class="btn btn-outline-primary btn-sm">{{ __('eventmanagement::lang.edit') }}</a>
                                            <form action="{{ route('eventmanagement.venues.destroy', $venue->id) }}" method="post" class="d-inline-block" onsubmit="return confirm('{{ __('eventmanagement::lang.confirm_delete') }}');">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">{{ __('eventmanagement::lang.delete') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">{{ __('eventmanagement::lang.no_records_found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer">
                    {{ $venues->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
