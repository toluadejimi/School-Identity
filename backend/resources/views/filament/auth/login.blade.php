<div class="passa-login-root">
    <style>
        .passa-login-root {
            width: 100%;
        }

        .fi-simple-main:has(.passa-login-root) {
            max-width: none !important;
            width: 100%;
            padding: 0;
        }

        .fi-simple-page:has(.passa-login-root) .fi-simple-page-content {
            padding: 0;
        }

        .passa-login-shell {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px;
            background:
                radial-gradient(circle at 20% 10%, rgba(34, 197, 94, .26), transparent 28rem),
                radial-gradient(circle at 82% 28%, rgba(14, 165, 233, .16), transparent 24rem),
                linear-gradient(135deg, #f8fafc 0%, #ecfdf5 54%, #f8fafc 100%);
        }

        .passa-login-panel {
            width: min(1040px, 100%);
            display: grid;
            grid-template-columns: 1.05fr .95fr;
            overflow: hidden;
            border-radius: 34px;
            background: rgba(255, 255, 255, .78);
            border: 1px solid rgba(15, 23, 42, .10);
            box-shadow: 0 34px 90px rgba(15, 23, 42, .16);
            backdrop-filter: blur(22px);
        }

        .passa-login-story {
            position: relative;
            min-height: 620px;
            padding: 42px;
            color: white;
            background:
                linear-gradient(145deg, rgba(6, 95, 70, .98), rgba(15, 23, 42, .96)),
                radial-gradient(circle at 10% 10%, rgba(134, 239, 172, .35), transparent 22rem);
        }

        .passa-login-story::after {
            content: '';
            position: absolute;
            width: 340px;
            height: 340px;
            right: -120px;
            bottom: -110px;
            border-radius: 999px;
            background: rgba(34, 197, 94, .22);
            border: 1px solid rgba(255, 255, 255, .14);
        }

        .passa-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800;
            letter-spacing: -.03em;
        }

        .passa-brand-mark {
            display: grid;
            place-items: center;
            width: 48px;
            height: 48px;
            border-radius: 18px;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            box-shadow: 0 18px 38px rgba(34, 197, 94, .28);
        }

        .passa-login-story h1 {
            max-width: 440px;
            margin: 96px 0 18px;
            font-size: clamp(40px, 5vw, 64px);
            line-height: .96;
            letter-spacing: -.07em;
        }

        .passa-login-story p {
            max-width: 460px;
            margin: 0;
            color: #cbd5e1;
            font-size: 16px;
            line-height: 1.8;
        }

        .passa-process {
            display: grid;
            gap: 12px;
            margin-top: 44px;
            max-width: 460px;
        }

        .passa-process-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px;
            border-radius: 20px;
            background: rgba(255, 255, 255, .08);
            border: 1px solid rgba(255, 255, 255, .10);
        }

        .passa-process-number {
            display: grid;
            place-items: center;
            width: 38px;
            height: 38px;
            border-radius: 14px;
            color: #bbf7d0;
            background: rgba(34, 197, 94, .18);
            font-weight: 800;
        }

        .passa-process-item span:last-child {
            color: #e2e8f0;
            font-weight: 700;
        }

        .passa-login-form-wrap {
            display: grid;
            align-content: center;
            padding: 48px;
        }

        .passa-login-card {
            width: 100%;
            max-width: 410px;
            margin-inline: auto;
        }

        .passa-login-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            margin-bottom: 18px;
            border-radius: 999px;
            color: #047857;
            background: #dcfce7;
            border: 1px solid rgba(22, 163, 74, .18);
            font-size: 12px;
            font-weight: 800;
        }

        .passa-login-card h2 {
            margin: 0;
            color: #0f172a;
            font-size: 34px;
            line-height: 1.08;
            letter-spacing: -.05em;
        }

        .passa-login-card .subtitle {
            margin: 10px 0 28px;
            color: #64748b;
            line-height: 1.65;
        }

        .passa-login-card .fi-sc {
            gap: 1.15rem;
        }

        .passa-login-card .fi-btn {
            border-radius: 999px;
            min-height: 46px;
        }

        .passa-login-card .fi-input-wrp {
            border-radius: 16px;
        }

        .passa-demo {
            margin-top: 26px;
            padding: 16px;
            border-radius: 20px;
            background: #f8fafc;
            border: 1px solid rgba(15, 23, 42, .08);
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
        }

        .passa-demo strong {
            color: #0f172a;
        }

        @media (max-width: 900px) {
            .passa-login-shell {
                padding: 18px;
            }

            .passa-login-panel {
                grid-template-columns: 1fr;
            }

            .passa-login-story {
                min-height: auto;
                padding: 30px;
            }

            .passa-login-story h1 {
                margin-top: 48px;
            }

            .passa-login-form-wrap {
                padding: 32px 22px;
            }
        }
    </style>

    <div class="passa-login-shell">
        <div class="passa-login-panel">
            <section class="passa-login-story" aria-label="School Identity Passa overview">
                <div class="passa-brand">
                    <span class="passa-brand-mark">PC</span>
                    <span>School Identity Passa</span>
                </div>
                <h1>Manage every Passa Card process from one secure console.</h1>
                <p>Register students, issue cards, monitor scans, review wallet activity, and keep clinic, lecture, transport, exam, and security teams aligned.</p>
                <div class="passa-process">
                    <div class="passa-process-item">
                        <span class="passa-process-number">1</span>
                        <span>Register student profile and photo</span>
                    </div>
                    <div class="passa-process-item">
                        <span class="passa-process-number">2</span>
                        <span>Bind and activate the Passa Card UID</span>
                    </div>
                    <div class="passa-process-item">
                        <span class="passa-process-number">3</span>
                        <span>Track operations with audit-ready records</span>
                    </div>
                </div>
            </section>

            <section class="passa-login-form-wrap" aria-label="Admin login form">
                <div class="passa-login-card">
                    <div class="passa-login-kicker">Secure backend access</div>
                    <h2>{{ $this->getHeading() }}</h2>
                    <p class="subtitle">{{ $this->getSubheading() }}</p>

                    {{ $this->content }}

                    <div class="passa-demo">
                        <strong>Demo admin:</strong> admin@school.local<br>
                        <strong>Password:</strong> password
                    </div>
                </div>
            </section>
        </div>
    </div>

    <x-filament-actions::modals />
</div>
