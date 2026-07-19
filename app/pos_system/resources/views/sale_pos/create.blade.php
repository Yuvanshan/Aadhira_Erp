@extends('layouts.app')

@section('title', __('sale.pos_sale'))

@section('content')
    <section class="content no-print pos-fullscreen-layout">
        <input type="hidden" id="amount_rounding_method" value="{{ $pos_settings['amount_rounding_method'] ?? '' }}">
        @if (!empty($pos_settings['allow_overselling']))
            <input type="hidden" id="is_overselling_allowed">
        @endif
        @if (session('business.enable_rp') == 1)
            <input type="hidden" id="reward_point_enabled">
        @endif
        @php
            $is_discount_enabled = $pos_settings['disable_discount'] != 1 ? true : false;
            $is_rp_enabled = session('business.enable_rp') == 1 ? true : false;
        @endphp
        {!! Form::open([
            'url' => action([\App\Http\Controllers\SellPosController::class, 'store']),
            'method' => 'post',
            'id' => 'add_pos_sell_form',
        ]) !!}
        <div class="row mb-12 pos-main-row">
            <div class="col-md-12 tw-pt-0 tw-mb-14 pos-main-column">
                <div class="row tw-flex lg:tw-flex-row md:tw-flex-col sm:tw-flex-col tw-flex-col tw-items-start md:tw-gap-4 pos-main-panels">
                    {{-- <div class="@if (empty($pos_settings['hide_product_suggestion'])) col-md-7 @else col-md-10 col-md-offset-1 @endif no-padding pr-12"> --}}
                    <div class="tw-px-3 tw-w-full  lg:tw-px-0 lg:tw-pr-0 pos-left-panel @if(empty($pos_settings['hide_product_suggestion'])) lg:tw-w-[60%]  @else lg:tw-w-[100%] @endif">

                        <div class="tw-shadow-[rgba(17,_17,_26,_0.1)_0px_0px_16px] tw-rounded-2xl tw-bg-white tw-mb-2 md:tw-mb-8 tw-p-2 pos-left-card">

                            {{-- <div class="box box-solid mb-12 @if (!isMobile()) mb-40 @endif"> --}}
                                <div class="box-body pb-0 pos-left-card-body">
                                    {!! Form::hidden('location_id', $default_location->id ?? null, [
                                        'id' => 'location_id',
                                        'data-receipt_printer_type' => !empty($default_location->receipt_printer_type)
                                            ? $default_location->receipt_printer_type
                                            : 'browser',
                                        'data-default_payment_accounts' => $default_location->default_payment_accounts ?? '',
                                    ]) !!}
                                    <!-- sub_type -->
                                    {!! Form::hidden('sub_type', isset($sub_type) ? $sub_type : null) !!}
                                    <input type="hidden" id="item_addition_method"
                                        value="{{ $business_details->item_addition_method }}">
                                    @include('sale_pos.partials.pos_form')

                                    @include('sale_pos.partials.pos_form_totals')

                                    @include('sale_pos.partials.payment_modal')

                                    @if (empty($pos_settings['disable_suspend']))
                                        @include('sale_pos.partials.suspend_note_modal')
                                    @endif

                                    @if (empty($pos_settings['disable_recurring_invoice']))
                                        @include('sale_pos.partials.recurring_invoice_modal')
                                    @endif
                                </div>
                            {{-- </div> --}}
                        </div>
                    </div>
                    @if (empty($pos_settings['hide_product_suggestion']) && !isMobile())
                        <div class="md:tw-no-padding tw-w-full lg:tw-w-[40%] tw-px-5 pos-right-panel">
                            @include('sale_pos.partials.pos_sidebar')
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @include('sale_pos.partials.pos_form_actions')
        {!! Form::close() !!}
    </section>

    <!-- This will be printed -->
    <section class="invoice print_section" id="receipt_section">
    </section>
    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        @include('contact.create', ['quick_add' => true])
    </div>
    @if (empty($pos_settings['hide_product_suggestion']) && isMobile())
        @include('sale_pos.partials.mobile_product_suggestions')
    @endif
    <!-- /.content -->
    <div class="modal fade register_details_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade close_register_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <!-- quick product modal -->
    <div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>

    <div class="modal fade" id="expense_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    @include('sale_pos.partials.configure_search_modal')

    @include('sale_pos.partials.recent_transactions_modal')

    @include('sale_pos.partials.weighing_scale_modal')

@stop
@section('css')
    <!-- include module css -->
    @if (!empty($pos_module_data))
        @foreach ($pos_module_data as $key => $value)
            @if (!empty($value['module_css_path']))
                @includeIf($value['module_css_path'])
            @endif
        @endforeach
    @endif

    @include('sale_pos.partials.professional_theme')

    <style>
        :root {
            --pos-fullscreen-actions-height: 112px;
        }

        body.pos-fullscreen-active,
        html.pos-fullscreen-active {
            overflow: hidden;
        }

        .pos-fullscreen-active main {
            height: 100vh !important;
            height: 100dvh !important;
            overflow: hidden !important;
        }

        #scrollable-container.pos-fullscreen-scroll {
            height: auto !important;
            min-height: 0 !important;
            flex: 1 1 auto !important;
            overflow: hidden !important;
        }

        .pos-fullscreen-active .pos-header {
            position: sticky;
            top: 0;
            z-index: 30;
        }

        .pos-fullscreen-active .pos-form-actions {
            position: sticky;
            bottom: 0;
            z-index: 25;
        }

        .pos-fullscreen-active section.content.no-print {
            min-height: auto;
            height: 100%;
            margin-bottom: 0 !important;
            padding-bottom: 0 !important;
            overflow: hidden;
        }

        .pos-fullscreen-active #add_pos_sell_form {
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 0;
            overflow: hidden;
        }

        .pos-fullscreen-active .pos-main-row,
        .pos-fullscreen-active .pos-main-column,
        .pos-fullscreen-active .pos-main-panels,
        .pos-fullscreen-active .pos-left-panel,
        .pos-fullscreen-active .pos-left-card,
        .pos-fullscreen-active .pos-left-card-body,
        .pos-fullscreen-active .pos-right-panel {
            min-height: 0;
        }

        .pos-fullscreen-active .pos-main-row {
            flex: 1 1 auto;
            margin-bottom: 0 !important;
            overflow: hidden;
        }

        .pos-fullscreen-active .pos-main-column {
            height: 100%;
            margin-bottom: 0 !important;
        }

        .pos-fullscreen-active .pos-main-panels {
            height: 100%;
            overflow: hidden;
        }

        .pos-fullscreen-active .pos-left-panel,
        .pos-fullscreen-active .pos-right-panel {
            height: 100%;
        }

        .pos-fullscreen-active .pos-left-card {
            height: 100%;
            margin-bottom: 0 !important;
            overflow: hidden;
        }

        .pos-fullscreen-active .pos-left-card-body {
            height: 100%;
            overflow-y: auto;
            padding-bottom: calc(var(--pos-fullscreen-actions-height) + env(safe-area-inset-bottom, 0px) + 16px);
        }

        .pos-fullscreen-active .pos-right-panel {
            overflow-y: auto;
            padding-bottom: calc(var(--pos-fullscreen-actions-height) + env(safe-area-inset-bottom, 0px) + 16px);
        }

        .pos-fullscreen-active footer.no-print {
            display: none !important;
        }

        @media print {
            .no-print,
            .main-sidebar,
            .left-side,
            .main-header,
            .content-header {
                display: none !important;
            }
        }
    </style>
@stop

@section('javascript')
    <script src="{{ asset('js/pos.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
    @include('sale_pos.partials.keyboard_shortcuts')

    <!-- Call restaurant module if defined -->
    @if (in_array('tables', $enabled_modules) ||
            in_array('modifiers', $enabled_modules) ||
            in_array('service_staff', $enabled_modules))
        <script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
    <!-- include module js -->
    @if (!empty($pos_module_data))
        @foreach ($pos_module_data as $key => $value)
            @if (!empty($value['module_js_path']))
                @includeIf($value['module_js_path'], ['view_data' => $value['view_data']])
            @endif
        @endforeach
    @endif

    <script>
        $(document).ready(function() {
            const fullScreenButton = document.getElementById('full_screen');
            const scrollContainer = document.getElementById('scrollable-container');
            const posActions = document.querySelector('.pos-form-actions');

            function isFullScreenActive() {
                return Boolean(
                    document.fullscreenElement ||
                    document.webkitFullscreenElement ||
                    document.mozFullScreenElement ||
                    document.msFullscreenElement
                );
            }

            function syncPosFullScreenState() {
                const isActive = isFullScreenActive();
                const actionHeight = posActions ? Math.ceil(posActions.getBoundingClientRect().height) : 112;

                document.documentElement.classList.toggle('pos-fullscreen-active', isActive);
                document.body.classList.toggle('pos-fullscreen-active', isActive);
                document.documentElement.style.setProperty('--pos-fullscreen-actions-height', actionHeight + 'px');

                if (scrollContainer) {
                    scrollContainer.classList.toggle('pos-fullscreen-scroll', isActive);
                }

                if (fullScreenButton) {
                    const icon = fullScreenButton.querySelector('i');
                    const label = fullScreenButton.querySelector('span');

                    fullScreenButton.setAttribute('title', isActive ? 'Exit Full Screen' : 'Full Screen');

                    if (icon) {
                        icon.classList.toggle('fa-window-maximize', !isActive);
                        icon.classList.toggle('fa-window-restore', isActive);
                    }

                    if (label) {
                        label.textContent = isActive ? 'Exit Full Screen' : 'Full Screen';
                    }
                }
            }

            syncPosFullScreenState();

            [
                'fullscreenchange',
                'webkitfullscreenchange',
                'mozfullscreenchange',
                'MSFullscreenChange',
            ].forEach(function(eventName) {
                document.addEventListener(eventName, syncPosFullScreenState);
            });

            window.addEventListener('resize', syncPosFullScreenState);

            document.addEventListener('wheel', function(e) {
                if (e.ctrlKey) {
                    e.preventDefault();
                }
            }, {passive: false});
        });

        $(document).on('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && (e.keyCode === 187 || e.keyCode === 189 || e.keyCode === 48)) {
                e.preventDefault();
            }
        });

        document.addEventListener('touchmove', function(e) {
            if (e.touches.length > 1) {
                e.preventDefault();
            }
        }, {passive: false});

        document.addEventListener('gesturestart', function(e) {
            e.preventDefault();
        }, {passive: false});
    </script>
@endsection
