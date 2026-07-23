<div class="tw-p-4 tw-bg-slate-50 tw-rounded-xl tw-border tw-border-slate-100 tw-transition-all hover:tw-bg-slate-100/70">
    <div class="tw-flex tw-items-start tw-justify-between">
        <div>
            <h5 class="tw-font-bold tw-text-gray-900 tw-text-sm">
                @lang('hms::lang.id'): <span class="tw-text-indigo-600 tw-font-semibold">{{ $info->ref_no }}</span>
            </h5>
            <p class="tw-text-sm tw-font-semibold tw-text-gray-800 tw-mt-1">{{ $info->contact->name }}</p>
            <p class="tw-text-xs tw-text-gray-500 tw-flex tw-items-center tw-gap-1 tw-mt-0.5">
                <svg class="tw-w-3.5 tw-h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                {{ $info->contact->mobile }}
            </p>
        </div>
        <div>
            @if($info->status == 'confirmed')
                <span class="tw-text-xs tw-font-bold tw-bg-emerald-100 tw-text-emerald-800 tw-px-2.5 tw-py-1 tw-rounded-full">{{ ucfirst($info->status) }}</span>
            @elseif($info->status == 'pending')
                <span class="tw-text-xs tw-font-bold tw-bg-amber-100 tw-text-amber-800 tw-px-2.5 tw-py-1 tw-rounded-full">{{ ucfirst($info->status) }}</span>
            @elseif($info->status == 'cancelled')
                <span class="tw-text-xs tw-font-bold tw-bg-rose-100 tw-text-rose-800 tw-px-2.5 tw-py-1 tw-rounded-full">{{ ucfirst($info->status) }}</span>
            @endif
        </div>
    </div>
    <div class="tw-mt-3 tw-pt-3 tw-border-t tw-border-slate-200/50 tw-flex tw-items-center tw-gap-1.5 tw-text-xs tw-text-slate-500">
        <svg class="tw-w-4 tw-h-4 tw-text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <span class="tw-font-medium">@lang('hms::lang.stay'):</span>
        <span>{{ @format_datetime($info->hms_booking_arrival_date_time) }}</span>
        <span>-</span>
        <span>{{ @format_datetime($info->hms_booking_departure_date_time) }}</span>
    </div>
</div>