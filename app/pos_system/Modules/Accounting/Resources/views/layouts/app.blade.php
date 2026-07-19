@extends('layouts.app')

@section('title')
    @yield('page-title') - {{ __('accounting::lang.accounting') }}
@endsection

@section('content')
    @include('accounting::layouts.nav')

    @yield('accounting-content')
@endsection

@section('css')
    {{-- Accounting Module Specific CSS --}}
    <link rel="stylesheet" href="{{ Module::asset('accounting:css/navbar-reset.css') }}" />
    <link rel="stylesheet" href="{{ Module::asset('accounting:css/navbar.css') }}" />
    <link rel="stylesheet" href="{{ Module::asset('accounting:css/accounting-theme.css') }}" />
    <link rel="stylesheet" href="{{ Module::asset('accounting:css/forms.css') }}" />
    <link rel="stylesheet" href="{{ Module::asset('accounting:css/animations.css') }}" />
@endsection


