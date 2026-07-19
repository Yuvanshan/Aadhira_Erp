@extends('layouts.auth2')
@section('title', config('app.name', 'ultimatePOS'))
@inject('request', 'Illuminate\Http\Request')

@php
    $appName = config('app.name', 'UltimatePOS');
    $appTitle = env('APP_TITLE', 'Smart operations for modern retail, POS, and business management.');
@endphp

@section('content')
<div class="landing-shell">
    <section class="landing-hero">
        <div class="landing-hero__copy">
            <span class="landing-badge">Business Platform</span>
            <h1 class="landing-title">{{ $appName }}</h1>
            <p class="landing-subtitle">{{ $appTitle }}</p>
            <p class="landing-description">
                Run billing, inventory, purchasing, reporting, and customer operations from one clean workspace built for everyday speed.
            </p>

            <div class="landing-actions">
                <a href="{{ route('login') }}@if(!empty(request()->lang)){{ '?lang=' . request()->lang }}@endif" class="landing-btn landing-btn--primary">
                    Sign In
                </a>
                @if (config('constants.allow_registration'))
                    <a href="{{ route('business.getRegister') }}@if(!empty(request()->lang)){{ '?lang=' . request()->lang }}@endif" class="landing-btn landing-btn--secondary">
                        Register Business
                    </a>
                @endif
            </div>

            <div class="landing-metrics">
                <div class="landing-metric">
                    <span class="landing-metric__label">Workflow</span>
                    <strong class="landing-metric__value">POS + ERP</strong>
                </div>
                <div class="landing-metric">
                    <span class="landing-metric__label">Experience</span>
                    <strong class="landing-metric__value">Fast & Focused</strong>
                </div>
                <div class="landing-metric">
                    <span class="landing-metric__label">Control</span>
                    <strong class="landing-metric__value">Real-time Visibility</strong>
                </div>
            </div>
        </div>

        <div class="landing-hero__panel">
            <div class="landing-glow"></div>
            <div class="landing-panel">
                <div class="landing-panel__header">
                    <div>
                        <p class="landing-panel__eyebrow">Operations Snapshot</p>
                        <h2 class="landing-panel__title">Designed for busy business teams</h2>
                    </div>
                    <span class="landing-panel__status">Live</span>
                </div>

                <div class="landing-panel__grid">
                    <article class="landing-feature">
                        <div class="landing-feature__icon">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <div>
                            <h3>Retail-ready POS</h3>
                            <p>Smooth billing, product search, pricing, and payment flow for daily counters.</p>
                        </div>
                    </article>

                    <article class="landing-feature">
                        <div class="landing-feature__icon landing-feature__icon--green">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div>
                            <h3>Inventory clarity</h3>
                            <p>Track stock movement, purchasing, and product availability without the clutter.</p>
                        </div>
                    </article>

                    <article class="landing-feature">
                        <div class="landing-feature__icon landing-feature__icon--amber">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <div>
                            <h3>Decision-friendly reports</h3>
                            <p>Surface sales, dues, margins, and performance trends in one connected system.</p>
                        </div>
                    </article>
                </div>

                <div class="landing-panel__footer">
                    <div class="landing-footer-card">
                        <span>Secure access</span>
                        <strong>Role-based sign in</strong>
                    </div>
                    <div class="landing-footer-card">
                        <span>Designed for</span>
                        <strong>Stores, counters, teams</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@section('css')
<style>
    .landing-shell {
        max-width: 1320px;
        margin: 0 auto;
        padding: 6rem 1.5rem 2rem;
    }

    .landing-hero {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1.1fr) minmax(360px, 0.9fr);
        gap: 2rem;
        align-items: stretch;
    }

    .landing-hero__copy,
    .landing-panel {
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(148, 163, 184, 0.22);
        border-radius: 2rem;
        background: rgba(255, 255, 255, 0.9);
        box-shadow: 0 28px 60px rgba(15, 23, 42, 0.12);
        backdrop-filter: blur(16px);
    }

    .landing-hero__copy {
        padding: 3rem;
        background:
            radial-gradient(circle at top left, rgba(37, 99, 235, 0.18), transparent 32%),
            linear-gradient(145deg, rgba(255, 255, 255, 0.98), rgba(241, 245, 249, 0.96));
    }

    .landing-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 0.95rem;
        border-radius: 999px;
        background: rgba(37, 99, 235, 0.1);
        color: #1d4ed8;
        font-size: 1.2rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .landing-title {
        margin: 1.5rem 0 0.75rem;
        font-size: clamp(3.4rem, 6vw, 6.2rem);
        line-height: 0.95;
        font-weight: 900;
        color: #0f172a;
        letter-spacing: -0.04em;
    }

    .landing-subtitle {
        margin: 0;
        font-size: 2rem;
        line-height: 1.4;
        font-weight: 700;
        color: #1e3a8a;
    }

    .landing-description {
        max-width: 60rem;
        margin: 1.5rem 0 0;
        font-size: 1.7rem;
        line-height: 1.8;
        color: #475569;
    }

    .landing-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 2rem;
    }

    .landing-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 180px;
        padding: 1.2rem 1.6rem;
        border-radius: 1rem;
        font-size: 1.5rem;
        font-weight: 700;
        text-decoration: none !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .landing-btn:hover,
    .landing-btn:focus {
        transform: translateY(-1px);
        text-decoration: none !important;
    }

    .landing-btn--primary {
        background: linear-gradient(135deg, #1d4ed8, #2563eb);
        color: #fff !important;
        box-shadow: 0 18px 40px rgba(37, 99, 235, 0.24);
    }

    .landing-btn--secondary {
        border: 1px solid rgba(148, 163, 184, 0.38);
        background: rgba(255, 255, 255, 0.88);
        color: #0f172a !important;
    }

    .landing-metrics {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        margin-top: 2.25rem;
    }

    .landing-metric {
        padding: 1.35rem 1.4rem;
        border: 1px solid rgba(148, 163, 184, 0.18);
        border-radius: 1.2rem;
        background: rgba(248, 250, 252, 0.92);
    }

    .landing-metric__label {
        display: block;
        font-size: 1.15rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #64748b;
    }

    .landing-metric__value {
        display: block;
        margin-top: 0.45rem;
        font-size: 1.7rem;
        line-height: 1.35;
        color: #0f172a;
    }

    .landing-hero__panel {
        position: relative;
        display: flex;
        align-items: stretch;
    }

    .landing-glow {
        position: absolute;
        inset: auto 6% 5% auto;
        width: 220px;
        height: 220px;
        border-radius: 999px;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.28), transparent 68%);
        pointer-events: none;
    }

    .landing-panel {
        width: 100%;
        padding: 2.2rem;
        background:
            radial-gradient(circle at top right, rgba(14, 165, 233, 0.14), transparent 26%),
            linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.96));
    }

    .landing-panel__header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.8rem;
    }

    .landing-panel__eyebrow {
        margin: 0 0 0.4rem;
        font-size: 1.1rem;
        font-weight: 800;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #0284c7;
    }

    .landing-panel__title {
        margin: 0;
        font-size: 2.4rem;
        line-height: 1.25;
        font-weight: 800;
        color: #0f172a;
    }

    .landing-panel__status {
        display: inline-flex;
        align-items: center;
        padding: 0.55rem 0.9rem;
        border-radius: 999px;
        background: rgba(34, 197, 94, 0.12);
        color: #15803d;
        font-size: 1.2rem;
        font-weight: 700;
    }

    .landing-panel__grid {
        display: grid;
        gap: 1rem;
    }

    .landing-feature {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 1rem;
        align-items: flex-start;
        padding: 1.3rem;
        border: 1px solid rgba(148, 163, 184, 0.16);
        border-radius: 1.3rem;
        background: rgba(255, 255, 255, 0.88);
    }

    .landing-feature__icon {
        width: 4.4rem;
        height: 4.4rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 1.1rem;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.16), rgba(59, 130, 246, 0.28));
        color: #1d4ed8;
        font-size: 1.9rem;
    }

    .landing-feature__icon--green {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.16), rgba(34, 197, 94, 0.24));
        color: #15803d;
    }

    .landing-feature__icon--amber {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.16), rgba(251, 191, 36, 0.24));
        color: #b45309;
    }

    .landing-feature h3 {
        margin: 0 0 0.35rem;
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
    }

    .landing-feature p {
        margin: 0;
        font-size: 1.35rem;
        line-height: 1.65;
        color: #475569;
    }

    .landing-panel__footer {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
        margin-top: 1.2rem;
    }

    .landing-footer-card {
        padding: 1.2rem 1.3rem;
        border-radius: 1.1rem;
        background: #0f172a;
        color: #e2e8f0;
    }

    .landing-footer-card span {
        display: block;
        font-size: 1.1rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #93c5fd;
    }

    .landing-footer-card strong {
        display: block;
        margin-top: 0.35rem;
        font-size: 1.5rem;
        line-height: 1.45;
        color: #fff;
    }

    @media (max-width: 991px) {
        .landing-shell {
            padding-top: 7rem;
        }

        .landing-hero {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .landing-shell {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .landing-hero__copy,
        .landing-panel {
            padding: 1.6rem;
            border-radius: 1.5rem;
        }

        .landing-title {
            font-size: 3.6rem;
        }

        .landing-subtitle {
            font-size: 1.65rem;
        }

        .landing-description,
        .landing-feature p {
            font-size: 1.3rem;
        }

        .landing-metrics,
        .landing-panel__footer {
            grid-template-columns: 1fr;
        }

        .landing-actions {
            flex-direction: column;
        }

        .landing-btn {
            width: 100%;
        }

        .landing-panel__header {
            flex-direction: column;
        }
    }
</style>
@endsection
