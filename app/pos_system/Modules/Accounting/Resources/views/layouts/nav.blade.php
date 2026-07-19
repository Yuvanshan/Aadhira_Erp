<section class="no-print">
    <nav class="navbar navbar-default navbar-static-top accounting-navbar">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#accounting-navbar-collapse"
                    aria-expanded="false">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="{{ url('accounting/dashboard') }}">
                    <i class="fas fa-book"></i>
                    <span>{{ __('accounting::lang.accounting') }}</span>
                </a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="accounting-navbar-collapse">
                <ul class="nav navbar-nav navbar-menu-items">

                    <li @if (request()->segment(1) == 'accounting' && request()->segment(2) == 'chart_of_account') class="active" @endif>
                        <a href="{{ url('accounting/chart_of_account') }}">
                            <i class="fas fa-sitemap"></i>
                            @lang('accounting::lang.view_charts_of_accounts')
                        </a>
                    </li>

                    <li @if (request()->segment(1) == 'accounting' && request()->segment(2) == 'journal_entry') class="active" @endif>
                        <a href="{{ url('accounting/journal_entry') }}">
                            <i class="fas fa-book-open"></i>
                            @lang('accounting::lang.journal_of_entries')
                        </a>
                    </li>

                    <li @if (request()->segment(1) == 'accounting' && request()->segment(2) == 'transfers') class="active" @endif>
                        <a href="{{ url('accounting/transfers') }}">
                            <i class="fas fa-exchange-alt"></i>
                            {{ trans_choice('accounting::lang.transfer', 2) }}
                        </a>
                    </li>

                    <li @if (request()->segment(1) == 'accounting' && request()->segment(2) == 'transactions') class="active" @endif>
                        <a href="{{ url('accounting/transactions/sales?type=payment') }}">
                            <i class="fas fa-coins"></i>
                            @lang('accounting::lang.transactions')
                        </a>
                    </li>

                    <li @if (request()->segment(1) == 'accounting' && request()->segment(2) == 'reconcile') class="active" @endif>
                        <a href="{{ url('accounting/reconcile') }}">
                            <i class="fas fa-check-double"></i>
                            @lang('accounting::lang.reconcile')
                        </a>
                    </li>

                    <li @if (request()->segment(1) == 'accounting' && request()->segment(2) == 'budget') class="active" @endif>
                        <a href="{{ url('accounting/budget?view=monthly&year=' . get_financial_year()) }}">
                            <i class="fas fa-chart-pie"></i>
                            {{ trans('accounting::general.budgeting') }}
                        </a>
                    </li>

                    @if (auth()->user()->can('brand.view'))
                        <li @if (request()->segment(1) == 'report') class="active" @endif>
                            <a href="{{ url('report/accounting') }}">
                                <i class="fas fa-chart-bar"></i>
                                @lang('accounting::lang.reports')
                            </a>
                        </li>
                    @endif

                    <li @if (request()->segment(1) == 'accounting' && request()->segment(2) == 'settings') class="active" @endif>
                        <a href="{{ url('accounting/settings/account_subtypes') }}">
                            <i class="fas fa-cog"></i>
                            @lang('accounting::lang.settings')
                        </a>
                    </li>

                </ul>

            </div><!-- /.navbar-collapse -->
        </div><!-- /.container-fluid -->
    </nav>
</section>
