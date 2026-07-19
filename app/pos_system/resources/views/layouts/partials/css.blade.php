<link href="{{ asset('css/tailwind/app.css?v='.$asset_v) }}" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/vendor.css?v='.$asset_v) }}">

@if( in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) )
	<link rel="stylesheet" href="{{ asset('css/rtl.css?v='.$asset_v) }}">
@endif

@yield('css')

<!-- app css -->
<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">

<style>
    :root {
        --brand: #2563eb;
        --brand-dark: #1d4ed8;
        --surface: #ffffff;
        --surface-alt: #f8fafc;
        --border: #e5e7eb;
        --text-primary: #0f172a;
        --text-secondary: #475569;
        --shadow-soft: 0 20px 50px rgba(15, 23, 42, 0.08);
    }

    body {
        background: radial-gradient(circle at top left, rgba(59, 130, 246, 0.12), transparent 28%), #f8fafc;
        color: var(--text-primary);
    }

    .btn, .btn-flat, .btn-app, .btn-primary, .btn-success, .btn-warning, .btn-danger, .btn-info {
        border-radius: 0.85rem !important;
        transition: all 0.2s ease !important;
    }

    .btn, .btn-flat {
        box-shadow: 0 12px 32px rgba(15, 23, 42, 0.08) !important;
    }

    .btn-primary {
        background-color: var(--brand) !important;
        border-color: var(--brand) !important;
        color: #fff !important;
    }

    .btn-primary:hover, .btn-primary:focus {
        background-color: var(--brand-dark) !important;
        border-color: var(--brand-dark) !important;
        color: #fff !important;
    }

    .card, .box, .panel, .modal-content, .info-box, .small-box {
        background: var(--surface) !important;
        border: 1px solid var(--border) !important;
        border-radius: 1rem !important;
        box-shadow: var(--shadow-soft) !important;
    }

    .table thead th {
        background: #f8fafc !important;
        border-bottom: 1px solid var(--border) !important;
        color: var(--text-primary) !important;
    }

    .table tbody tr:hover {
        background: #f1f5f9 !important;
    }

    .form-control, input, select, textarea, .select2-container .select2-selection--single, .select2-dropdown {
        border-radius: 0.85rem !important;
        border-color: var(--border) !important;
        background: #ffffff !important;
        color: var(--text-primary) !important;
        box-shadow: none !important;
    }

    .form-control:focus, .select2-selection--single:focus {
        border-color: var(--brand) !important;
        box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.15) !important;
    }

    .content, .content-wrapper, .content-header, .box-body, .main-footer {
        background: transparent !important;
    }

    .navbar, .main-header {
        border-bottom: 1px solid rgba(148, 163, 184, 0.16) !important;
    }

    .content-header h1, .page-title {
        color: var(--text-primary) !important;
    }

    .card-header, .box-header {
        border-bottom: 1px solid rgba(148, 163, 184, 0.16) !important;
        background: transparent !important;
    }

    .tw-bg-gray-100 {
        background-color: #f8fafc !important;
    }
</style>

@if(isset($pos_layout) && $pos_layout)
	<style type="text/css">
		.content{
			padding-bottom: 0px !important;
		}
	</style>
@endif
<style type="text/css">
	/*
	* Pattern lock css
	* Pattern direction
	* http://ignitersworld.com/lab/patternLock.html
	*/
	.patt-wrap {
	  z-index: 10;
	}
	.patt-circ.hovered {
	  background-color: #cde2f2;
	  border: none;
	}
	.patt-circ.hovered .patt-dots {
	  display: none;
	}
	.patt-circ.dir {
	  background-image: url("{{asset('/img/pattern-directionicon-arrow.png')}}");
	  background-position: center;
	  background-repeat: no-repeat;
	}
	.patt-circ.e {
	  -webkit-transform: rotate(0);
	  transform: rotate(0);
	}
	.patt-circ.s-e {
	  -webkit-transform: rotate(45deg);
	  transform: rotate(45deg);
	}
	.patt-circ.s {
	  -webkit-transform: rotate(90deg);
	  transform: rotate(90deg);
	}
	.patt-circ.s-w {
	  -webkit-transform: rotate(135deg);
	  transform: rotate(135deg);
	}
	.patt-circ.w {
	  -webkit-transform: rotate(180deg);
	  transform: rotate(180deg);
	}
	.patt-circ.n-w {
	  -webkit-transform: rotate(225deg);
	   transform: rotate(225deg);
	}
	.patt-circ.n {
	  -webkit-transform: rotate(270deg);
	  transform: rotate(270deg);
	}
	.patt-circ.n-e {
	  -webkit-transform: rotate(315deg);
	  transform: rotate(315deg);
	}
</style>
@if(!empty($__system_settings['additional_css']))
    {!! $__system_settings['additional_css'] !!}
@endif

