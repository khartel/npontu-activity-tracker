<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Npontu Activity Tracker</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --green:   #1a6b3a;
            --green-l: #22883f;
            --gold:    #f4a800;
            --surface: #f0f4f1;
            --border:  #d4ddd7;
            --text:    #1a1f2e;
            --muted:   #6b7280;
            --error:   #dc2626;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--surface);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        /* Split layout */
        .login-wrap {
            display: flex;
            width: 100%;
            max-width: 880px;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,.12);
        }

        /* Brand panel */
        .brand-panel {
            width: 45%;
            background: var(--green);
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }
        .brand-panel::before {
            content: '';
            position: absolute;
            bottom: -60px; right: -60px;
            width: 260px; height: 260px;
            border-radius: 50%;
            background: rgba(255,255,255,.06);
        }
        .brand-panel::after {
            content: '';
            position: absolute;
            top: -40px; left: -40px;
            width: 180px; height: 180px;
            border-radius: 50%;
            background: rgba(244,168,0,.1);
        }
        .brand-logo {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 26px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -.4px;
            position: relative;
            z-index: 1;
        }
        .brand-logo span { color: var(--gold); }
        .brand-tagline {
            font-size: 11px;
            color: rgba(255,255,255,.5);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 4px;
        }
        .brand-hero {
            position: relative;
            z-index: 1;
        }
        .brand-hero h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px;
            font-weight: 600;
            color: #fff;
            line-height: 1.3;
            margin-bottom: 12px;
        }
        .brand-hero p {
            font-size: 13.5px;
            color: rgba(255,255,255,.65);
            line-height: 1.6;
        }
        .brand-dots {
            display: flex;
            gap: 8px;
            position: relative;
            z-index: 1;
        }
        .brand-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,.25);
        }
        .brand-dot.active { background: var(--gold); }

        /* Form panel */
        .form-panel {
            flex: 1;
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-heading {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 22px;
            font-weight: 700;
            color: var(--text);
            margin-bottom: 4px;
        }
        .form-subheading {
            font-size: 13.5px;
            color: var(--muted);
            margin-bottom: 32px;
        }

        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: var(--text);
            margin-bottom: 6px;
        }
        .form-control {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid var(--border);
            border-radius: 8px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
            color: var(--text);
            outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .form-control:focus {
            border-color: var(--green);
            box-shadow: 0 0 0 3px rgba(26,107,58,.15);
        }
        .form-control.is-invalid { border-color: var(--error); }
        .invalid-feedback { font-size: 12px; color: var(--error); margin-top: 5px; }

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .remember-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            color: var(--muted);
            cursor: pointer;
        }
        .remember-label input { accent-color: var(--green); }

        .btn-login {
            width: 100%;
            padding: 11px;
            background: var(--green);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: background .15s;
        }
        .btn-login:hover { background: var(--green-l); }

        .demo-box {
            margin-top: 24px;
            padding: 14px 16px;
            background: #f0f9f4;
            border: 1px solid #c3e6cb;
            border-radius: 8px;
            font-size: 12.5px;
            color: var(--green);
        }
        .demo-box strong { font-weight: 600; }

        @media (max-width: 640px) {
            .brand-panel { display: none; }
            .form-panel { padding: 36px 28px; }
        }
    </style>
</head>
<body>

<div class="login-wrap">
    {{-- Brand side --}}
    <div class="brand-panel">
        <div>
            <div class="brand-logo">NP<span>O</span>NTU</div>
            <div class="brand-tagline">Technologies</div>
        </div>

        <div class="brand-hero">
            <h2>Application Support Activity Tracker</h2>
            <p>Log, track, and hand over daily support activities with full audit history and team visibility.</p>
        </div>

        <div class="brand-dots">
            <div class="brand-dot active"></div>
            <div class="brand-dot"></div>
            <div class="brand-dot"></div>
        </div>
    </div>

    {{-- Form side --}}
    <div class="form-panel">
        <div class="form-heading">Welcome back</div>
        <div class="form-subheading">Sign in to your team account</div>

        @if($errors->any())
            <div style="padding:12px 14px;background:#fee2e2;border:1px solid #fecaca;border-radius:8px;font-size:13px;color:#dc2626;margin-bottom:20px;">
                {{ $errors->first() }}
            </div>
        @endif

        @if(session('success'))
            <div style="padding:12px 14px;background:#dcfce7;border:1px solid #bbf7d0;border-radius:8px;font-size:13px;color:#15803d;margin-bottom:20px;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
                    value="{{ old('email') }}"
                    placeholder="you@npontu.com"
                    autocomplete="email"
                    autofocus
                    required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
                    placeholder="••••••••"
                    autocomplete="current-password"
                    required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-footer">
                <label class="remember-label">
                    <input type="checkbox" name="remember" value="1"> Remember me
                </label>
            </div>

            <button type="submit" class="btn-login">Sign In</button>
        </form>

        <div class="demo-box">
            <strong>Demo credentials:</strong><br>
            Admin: admin@npontu.com / password<br>
            Staff: ama.asante@npontu.com / password
        </div>
    </div>
</div>

</body>
</html>
