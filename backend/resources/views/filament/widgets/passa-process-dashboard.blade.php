<x-filament-widgets::widget>
    <style>
        .passa-dash-hero {
            overflow: hidden;
            position: relative;
            padding: 28px;
            border-radius: 28px;
            background: linear-gradient(135deg, #064e3b, #0f172a 68%);
            color: white;
            box-shadow: 0 24px 60px rgba(15, 23, 42, .16);
        }
        .passa-dash-hero::after {
            content: '';
            position: absolute;
            inset: auto -80px -140px auto;
            width: 320px;
            height: 320px;
            border-radius: 999px;
            background: rgba(34, 197, 94, .20);
        }
        .passa-dash-hero h2 { margin: 0; max-width: 720px; font-size: clamp(28px, 4vw, 46px); line-height: 1; letter-spacing: -.05em; }
        .passa-dash-hero p { max-width: 760px; margin: 14px 0 0; color: #cbd5e1; line-height: 1.7; }
        .passa-stage-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-top: 24px; }
        .passa-stage-card { position: relative; z-index: 1; display: block; padding: 18px; border-radius: 22px; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.12); transition: transform .18s ease, background .18s ease; }
        .passa-stage-card:hover { transform: translateY(-2px); background: rgba(255,255,255,.12); }
        .passa-stage-card small { display: inline-flex; margin-bottom: 18px; padding: 6px 10px; border-radius: 999px; color: #bbf7d0; background: rgba(34,197,94,.16); font-weight: 800; }
        .passa-stage-card strong { display: block; font-size: 17px; }
        .passa-stage-card span { display: block; margin-top: 8px; color: #cbd5e1; font-size: 13px; line-height: 1.55; }
        .passa-module-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-top: 16px; }
        .passa-module-link { display: block; padding: 18px; border-radius: 22px; background: white; border: 1px solid rgba(15,23,42,.08); box-shadow: 0 16px 34px rgba(15,23,42,.06); transition: transform .18s ease, box-shadow .18s ease; }
        .dark .passa-module-link { background: rgba(15,23,42,.72); border-color: rgba(255,255,255,.08); }
        .passa-module-link:hover { transform: translateY(-2px); box-shadow: 0 20px 42px rgba(15,23,42,.10); }
        .passa-module-link strong { display: block; color: #0f172a; }
        .dark .passa-module-link strong { color: white; }
        .passa-module-link span { display: block; margin-top: 6px; color: #64748b; font-size: 13px; }
        .dark .passa-module-link span { color: #cbd5e1; }
        @media (max-width: 1100px) { .passa-stage-grid, .passa-module-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 700px) { .passa-stage-grid, .passa-module-grid { grid-template-columns: 1fr; } }
    </style>

    <div class="passa-dash-hero">
        <h2>Manage the full Passa Card process from registration to daily operations.</h2>
        <p>Use this dashboard as the control room for student onboarding, card issuing, device scanning, departmental actions, payment review, and audit-ready follow up.</p>

        <div class="passa-stage-grid">
            @foreach ($stages as $stage)
                <a class="passa-stage-card" href="{{ $stage['url'] }}">
                    <small>{{ $stage['label'] }}</small>
                    <strong>{{ $stage['title'] }}</strong>
                    <span>{{ $stage['text'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <div class="passa-module-grid">
        @foreach ($modules as $module)
            <a class="passa-module-link" href="{{ $module['url'] }}">
                <strong>{{ $module['name'] }}</strong>
                <span>{{ $module['description'] }}</span>
            </a>
        @endforeach
    </div>
</x-filament-widgets::widget>
