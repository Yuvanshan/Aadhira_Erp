<link href="{{ asset('css/tailwind/app.css?v='.$asset_v) }}" rel="stylesheet">

<link rel="stylesheet" href="{{ asset('css/vendor.css?v='.$asset_v) }}">

@if( in_array(session()->get('user.language', config('app.locale')), config('constants.langs_rtl')) )
	<link rel="stylesheet" href="{{ asset('css/rtl.css?v='.$asset_v) }}">
@endif

@yield('css')

<!-- app css -->
<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">

<style type="text/css">
    /* Premium Modern UI CSS Override System */
    :root {
        --primary: #6366f1;
        --primary-hover: #4f46e5;
        --primary-light: #e0e7ff;
        --success: #10b981;
        --info: #06b6d4;
        --warning: #f59e0b;
        --danger: #ef4444;
        --bg-main: #f8fafc;
        --surface: #ffffff;
        --border: #e2e8f0;
        --text-main: #0f172a;
        --text-muted: #64748b;
        --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        --radius-lg: 0.75rem;
        --radius-xl: 1rem;
        --transition-base: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
        background-color: var(--bg-main) !important;
        color: var(--text-main) !important;
    }

    /* Cards & Boxes */
    .box, .card, .panel, .modal-content, .info-box, .small-box {
        background: var(--surface) !important;
        border: 1px solid var(--border) !important;
        border-radius: var(--radius-xl) !important;
        box-shadow: var(--shadow-md) !important;
        transition: var(--transition-base);
    }

    .box:hover, .card:hover {
        box-shadow: var(--shadow-lg) !important;
    }

    /* Buttons */
    .btn {
        border-radius: var(--radius-lg) !important;
        font-weight: 500 !important;
        padding: 0.5rem 1rem !important;
        transition: var(--transition-base) !important;
        box-shadow: var(--shadow-sm) !important;
    }

    .btn:hover {
        transform: translateY(-1px);
        box-shadow: var(--shadow-md) !important;
    }

    .btn:active {
        transform: translateY(0);
    }

    .btn-primary {
        background-color: var(--primary) !important;
        border-color: var(--primary) !important;
        color: white !important;
    }

    .btn-primary:hover, .btn-primary:focus {
        background-color: var(--primary-hover) !important;
        border-color: var(--primary-hover) !important;
    }

    /* Modern Table design */
    .table {
        border-collapse: separate !important;
        border-spacing: 0 !important;
        width: 100% !important;
    }

    .table thead th {
        background-color: #f1f5f9 !important;
        color: var(--text-main) !important;
        font-weight: 600 !important;
        border-bottom: 2px solid var(--border) !important;
        padding: 12px 16px !important;
    }

    .table tbody td {
        padding: 12px 16px !important;
        border-bottom: 1px solid var(--border) !important;
        vertical-align: middle !important;
    }

    .table tbody tr {
        transition: var(--transition-base);
    }

    .table tbody tr:hover {
        background-color: #f8fafc !important;
    }

    /* Forms & Inputs */
    .form-control, input[type="text"], input[type="password"], input[type="email"], input[type="number"], select, textarea,
    .select2-container--default .select2-selection--single {
        border-radius: var(--radius-lg) !important;
        border: 1px solid var(--border) !important;
        padding: 0.5rem 0.85rem !important;
        height: auto !important;
        transition: var(--transition-base) !important;
        background-color: var(--surface) !important;
    }

    .form-control:focus, input:focus, select:focus, textarea:focus,
    .select2-container--default.select2-container--focus .select2-selection--single {
        border-color: var(--primary) !important;
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15) !important;
    }

    /* Minimal Scrollbars */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
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

