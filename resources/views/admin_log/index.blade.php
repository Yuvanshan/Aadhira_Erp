@extends('layouts.app')
@section('title', __('lang_v1.admin_log'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1 class="tw-text-2xl tw-font-bold tw-text-gray-900">@lang('lang_v1.admin_log')</h1>
</section>

<!-- Main content -->
<section class="content">
    <div class="box box-solid tw-p-6">
        <div class="box-header tw-pb-4 tw-mb-6 tw-border-b tw-border-gray-100">
            <div class="row">
                <div class="col-md-4">
                    <form method="GET" action="{{ action([\App\Http\Controllers\AdminLogController::class, 'index']) }}" id="log_filter_form">
                        <div class="form-group">
                            {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                            {!! Form::select('location_id', $locations, request()->input('location_id'), ['class' => 'form-control select2', 'placeholder' => __('lang_v1.all'), 'style' => 'width:100%', 'onchange' => 'this.form.submit()']) !!}
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-hover tw-w-full">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.date')</th>
                            <th>@lang('lang_v1.by')</th>
                            <th>@lang('purchase.business_location')</th>
                            <th>@lang('messages.action')</th>
                            <th>@lang('brand.note')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                            <tr>
                                <td class="tw-whitespace-nowrap">
                                    <span class="tw-font-medium tw-text-gray-900">{{ @format_datetime($activity->created_at) }}</span>
                                </td>
                                <td>
                                    @if($activity->causer)
                                        <div class="tw-flex tw-flex-col">
                                            <span class="tw-font-semibold tw-text-gray-900">{{ $activity->causer->user_full_name }}</span>
                                            <span class="tw-text-xs tw-text-gray-500">{{ $activity->causer->username }}</span>
                                        </div>
                                    @else
                                        <span class="tw-text-gray-400">@lang('lang_v1.automatic')</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($activity->location_id))
                                        @php
                                            $loc = \App\BusinessLocation::find($activity->location_id);
                                        @endphp
                                        <span class="label bg-blue">{{ $loc->name ?? 'Unknown Shop' }}</span>
                                    @else
                                        <span class="label bg-gray">@lang('lang_v1.all')</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="label bg-green tw-text-sm">{{ ucfirst($activity->description) }}</span>
                                    @if(!empty($activity->subject_type))
                                        <span class="tw-text-xs tw-text-gray-500 tw-block tw-mt-1">
                                            Target: {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $properties = $activity->properties;
                                        $update_note = $activity->getExtraProperty('update_note');
                                    @endphp

                                    @if(!empty($update_note))
                                        <p class="tw-text-sm tw-text-gray-700 tw-font-medium">{{ $update_note }}</p>
                                    @endif

                                    @if(!empty($properties) && count($properties) > 0)
                                        <details class="tw-cursor-pointer tw-text-xs tw-mt-2">
                                            <summary class="tw-text-primary-600 hover:tw-text-primary-800 tw-font-medium">
                                                @lang('lang_v1.view_changes')
                                            </summary>
                                            <div class="tw-bg-gray-50 tw-p-3 tw-rounded-lg tw-mt-2 tw-border tw-border-gray-100 tw-max-w-md tw-overflow-x-auto">
                                                <pre class="tw-text-xs tw-text-gray-800 tw-font-mono">{{ json_encode($properties, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        </details>
                                    @else
                                        <span class="tw-text-gray-400 tw-text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center tw-text-gray-500 tw-py-8">
                                    @lang('purchase.no_records_found')
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="tw-mt-6 tw-flex tw-justify-end">
                {!! $activities->appends(request()->query())->links() !!}
            </div>
        </div>
    </div>
</section>

@endsection
