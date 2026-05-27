<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>School Identity Passa</title>
    <style>
        :root {
            color-scheme: light;
            --ink: #0f172a;
            --muted: #64748b;
            --line: rgba(15, 23, 42, .10);
            --green: #16a34a;
            --green-dark: #047857;
            --mint: #dcfce7;
            --gold: #f59e0b;
            --bg: #f8fafc;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 15% 5%, rgba(34, 197, 94, .20), transparent 30rem),
                radial-gradient(circle at 88% 20%, rgba(14, 165, 233, .16), transparent 26rem),
                linear-gradient(135deg, #f8fafc 0%, #ecfdf5 48%, #f8fafc 100%);
        }

        a { color: inherit; text-decoration: none; }
        .shell { width: min(1180px, calc(100% - 32px)); margin: 0 auto; }
        .nav { display: flex; align-items: center; justify-content: space-between; padding: 28px 0; }
        .brand { display: inline-flex; align-items: center; gap: 12px; font-weight: 800; letter-spacing: -.03em; }
        .brand-mark { display: grid; place-items: center; width: 44px; height: 44px; border-radius: 16px; color: white; background: linear-gradient(135deg, #16a34a, #065f46); box-shadow: 0 18px 34px rgba(22, 163, 74, .28); }
        .nav-actions { display: flex; gap: 12px; align-items: center; }
        .pill { display: inline-flex; align-items: center; justify-content: center; min-height: 42px; padding: 0 18px; border-radius: 999px; border: 1px solid var(--line); font-weight: 700; font-size: 14px; background: rgba(255, 255, 255, .72); backdrop-filter: blur(18px); }
        .pill.primary { color: white; border: 0; background: linear-gradient(135deg, var(--green), var(--green-dark)); box-shadow: 0 18px 32px rgba(22, 163, 74, .24); }

        .hero { display: grid; grid-template-columns: 1.03fr .97fr; gap: 44px; align-items: center; padding: 54px 0 72px; }
        .eyebrow { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 999px; color: #047857; background: rgba(220, 252, 231, .82); border: 1px solid rgba(22, 163, 74, .18); font-size: 13px; font-weight: 800; }
        h1 { margin: 20px 0 18px; font-size: clamp(46px, 7vw, 78px); line-height: .94; letter-spacing: -.07em; }
        .lead { max-width: 620px; margin: 0; color: var(--muted); font-size: 18px; line-height: 1.75; }
        .cta { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 30px; }
        .cta .pill { min-height: 52px; padding: 0 24px; }
        .metrics { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-top: 34px; }
        .metric { padding: 18px; border-radius: 24px; background: rgba(255,255,255,.72); border: 1px solid var(--line); box-shadow: 0 20px 60px rgba(15, 23, 42, .07); }
        .metric strong { display: block; font-size: 28px; letter-spacing: -.04em; }
        .metric span { color: var(--muted); font-size: 13px; font-weight: 700; }

        .console { position: relative; padding: 20px; border-radius: 36px; background: rgba(255,255,255,.62); border: 1px solid rgba(255,255,255,.82); box-shadow: 0 30px 90px rgba(15, 23, 42, .14); backdrop-filter: blur(22px); }
        .console::before { content: ''; position: absolute; inset: 18px -10px -16px 28px; border-radius: 34px; background: linear-gradient(135deg, rgba(22, 163, 74, .18), rgba(14, 165, 233, .10)); z-index: -1; }
        .screen { overflow: hidden; border-radius: 28px; background: #0f172a; color: white; }
        .screen-top { display: flex; align-items: center; justify-content: space-between; padding: 18px 20px; border-bottom: 1px solid rgba(255,255,255,.10); }
        .dots { display: flex; gap: 7px; }
        .dot { width: 10px; height: 10px; border-radius: 999px; background: #22c55e; opacity: .9; }
        .dot:nth-child(2) { background: #f59e0b; }
        .dot:nth-child(3) { background: #38bdf8; }
        .scan-card { margin: 22px; padding: 24px; border-radius: 26px; background: linear-gradient(135deg, rgba(22, 163, 74, .22), rgba(15, 23, 42, .84)); border: 1px solid rgba(255,255,255,.12); }
        .scan-card h2 { margin: 0 0 10px; font-size: 28px; letter-spacing: -.04em; }
        .status-row { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-top: 22px; }
        .status { padding: 16px; border-radius: 20px; background: rgba(255,255,255,.08); border: 1px solid rgba(255,255,255,.10); }
        .status b { display: block; font-size: 22px; }
        .process { display: grid; gap: 12px; margin: 22px; }
        .step { display: flex; gap: 14px; align-items: center; padding: 14px; border-radius: 18px; background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.08); }
        .step-icon { display: grid; place-items: center; width: 42px; height: 42px; border-radius: 14px; color: #bbf7d0; background: rgba(34,197,94,.16); }
        .step span { color: #cbd5e1; font-size: 13px; }

        .modules { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; padding-bottom: 64px; }
        .module { padding: 22px; min-height: 150px; border-radius: 28px; background: rgba(255,255,255,.76); border: 1px solid var(--line); box-shadow: 0 20px 50px rgba(15, 23, 42, .07); }
        .module strong { display: block; margin-bottom: 10px; font-size: 16px; }
        .module p { margin: 0; color: var(--muted); line-height: 1.55; font-size: 14px; }

        @media (max-width: 900px) {
            .hero { grid-template-columns: 1fr; padding-top: 24px; }
            .modules { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 620px) {
            .nav { align-items: flex-start; gap: 18px; flex-direction: column; }
            .metrics, .status-row, .modules { grid-template-columns: 1fr; }
            .console { padding: 10px; border-radius: 28px; }
        }
    </style>
</head>
<body>
    <header class="shell nav">
        <a class="brand" href="{{ url('/') }}" aria-label="School Identity Passa home">
            <span class="brand-mark">PC</span>
            <span>School Identity Passa</span>
        </a>
        <nav class="nav-actions" aria-label="Primary navigation">
            <a class="pill" href="{{ url('/api/v1/health') }}">API status</a>
            <a class="pill primary" href="{{ url('/admin') }}">Admin login</a>
        </nav>
    </header>

    <main class="shell">
        <section class="hero">
            <div>
                <span class="eyebrow">Live Passa Card identity platform</span>
                <h1>One card for identity, access, care, exams, and transport.</h1>
                <p class="lead">Manage student registration, Passa Card issuance, secure scans, clinic visits, attendance, exam clearance, and campus payments from one modern backend.</p>
                <div class="cta">
                    <a class="pill primary" href="{{ url('/admin') }}">Open management dashboard</a>
                    <a class="pill" href="{{ url('/api/v1/health') }}">Check API health</a>
                </div>
                <div class="metrics" aria-label="Platform highlights">
                    <div class="metric"><strong>8</strong><span>Campus workflows</span></div>
                    <div class="metric"><strong>UID</strong><span>Server-backed lookup</span></div>
                    <div class="metric"><strong>24/7</strong><span>Audit-ready operations</span></div>
                </div>
            </div>

            <div class="console" aria-label="Passa Card process preview">
                <div class="screen">
                    <div class="screen-top">
                        <div class="dots"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
                        <strong>Process Control</strong>
                    </div>
                    <div class="scan-card">
                        <h2>Passa Card verified</h2>
                        <p class="lead" style="color:#cbd5e1;font-size:15px;line-height:1.6">Student profile, card state, wallet, medical flags, attendance, and eligibility are resolved securely by the backend.</p>
                        <div class="status-row">
                            <div class="status"><b>Active</b><span>Card status</span></div>
                            <div class="status"><b>Synced</b><span>Device audit</span></div>
                        </div>
                    </div>
                    <div class="process">
                        <div class="step"><span class="step-icon">1</span><div><strong>Register student</strong><br><span>Capture profile, class, guardian, and photo.</span></div></div>
                        <div class="step"><span class="step-icon">2</span><div><strong>Issue Passa Card</strong><br><span>Bind UID to the student account.</span></div></div>
                        <div class="step"><span class="step-icon">3</span><div><strong>Operate modules</strong><br><span>Clinic, lecture, exam, security, transport, and vendors.</span></div></div>
                    </div>
                </div>
            </div>
        </section>

        <section class="modules" aria-label="Backend modules">
            <article class="module"><strong>Registration</strong><p>Create student identities with academic, guardian, medical, and profile-photo records.</p></article>
            <article class="module"><strong>Passa Cards</strong><p>Issue, activate, replace, or deactivate UID-only cards from the admin panel.</p></article>
            <article class="module"><strong>Operations</strong><p>Manage clinic visits, attendance sessions, exam eligibility, and transport fares.</p></article>
            <article class="module"><strong>Wallets</strong><p>Review balances, transactions, bus payments, and finance activity in one place.</p></article>
        </section>
    </main>
</body>
</html>
