<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Store | {{ readConfig('site_name') ?? 'V-Mart' }}</title>
    <link rel="shortcut icon" href="{{ assetImage(readconfig('site_logo')) }}" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary: #FF473D;
            --secondary: #7C5AC2;
            --accent: #FFC400;
            --text-main: #1A1A1A;
            --text-muted: #64748b;
            --glass-bg: rgba(255, 255, 255, 0.75);
            --glass-border: rgba(255, 255, 255, 0.5);
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            background: #fff;
            position: relative;
            overflow-x: hidden;
            overflow-y: auto;
            padding: 40px 20px;
        }

        /* ── Advanced Mesh Gradient Background ──────────────────────── */
        .mesh-bg {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: -1;
            background-color: #ffffff;
            background-image: 
                radial-gradient(at 0% 0%, rgba(255, 71, 61, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 0%, rgba(124, 90, 194, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(255, 196, 0, 0.1) 0px, transparent 50%),
                radial-gradient(at 0% 100%, rgba(255, 123, 115, 0.15) 0px, transparent 50%);
            filter: blur(10px);
        }

        /* ── Floating Elements ─────────────────────────────────────── */
        .floating-icon {
            position: absolute;
            z-index: -1;
            animation: float 6s ease-in-out infinite;
            font-size: 40px;
            filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));
            user-select: none;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(15deg); }
        }

        /* ── Glassmorphism Card ────────────────────────────────────── */
        .auth-card {
            width: 100%;
            max-width: 580px;
            background: var(--glass-bg);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
            position: relative;
            animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
            margin: auto;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ── Header ────────────────────────────────────────────── */
        .auth-header { text-align: center; margin-bottom: 25px; }
        .auth-logo { max-height: 50px; margin-bottom: 15px; transition: transform 0.3s ease; }
        .auth-logo:hover { transform: scale(1.08); }
        
        .badge-admin {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255, 71, 61, 0.1);
            border: 1px solid rgba(255, 71, 61, 0.2);
            border-radius: 100px;
            padding: 6px 16px;
            font-size: 12px;
            font-weight: 800;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }
        .badge-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--primary); box-shadow: 0 0 10px var(--primary); animation: pulse 2s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; transform: scale(1); } 50% { opacity: 0.5; transform: scale(0.8); } }

        .auth-title { font-size: 32px; font-weight: 800; color: var(--text-main); letter-spacing: -1px; margin-bottom: 8px; }
        .auth-subtitle { color: var(--text-muted); font-size: 15px; font-weight: 500; }

        /* ── Form Layout ────────────────────────────────────────── */
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 22px; }
        .form-label { display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px; margin-left: 4px; }
        .input-wrapper { position: relative; }
        
        .form-control {
            width: 100%;
            background: rgba(255, 255, 255, 0.85);
            border: 1.5px solid #e2e8f0;
            border-radius: 18px;
            padding: 16px 20px;
            font-size: 15px;
            font-family: inherit;
            color: var(--text-main);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
        }

        .form-control:focus {
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(255, 71, 61, 0.1);
            transform: translateY(-2px);
        }

        .eye-toggle {
            position: absolute; right: 18px; top: 50%; transform: translateY(-50%);
            cursor: pointer; color: var(--text-muted); display: flex; align-items: center;
        }

        /* ── Terms ──────────────────────────────────────────── */
        .terms-row { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 25px; }
        .terms-row input[type="checkbox"] { accent-color: var(--primary); width: 20px; height: 20px; flex-shrink: 0; cursor: pointer; margin-top: 2px; }
        .terms-row label { font-size: 14px; color: var(--text-muted); font-weight: 600; cursor: pointer; line-height: 1.5; }
        .terms-row a { color: var(--primary); text-decoration: none; font-weight: 800; }

        /* ── Button ────────────────────────────────────────────── */
        .btn-submit {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, var(--primary) 0%, #FF7B73 100%);
            color: #fff;
            border: none;
            border-radius: 18px;
            font-size: 16px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -5px rgba(255, 71, 61, 0.4);
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -5px rgba(255, 71, 61, 0.5);
            filter: brightness(1.05);
        }

        /* ── Social Login ──────────────────────────────────────── */
        .divider { display: flex; align-items: center; margin: 25px 0; gap: 15px; }
        .divider::before, .divider::after { content: ""; flex: 1; height: 1.5px; background: #e2e8f0; }
        .divider span { font-size: 12px; color: var(--text-muted); font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; }

        .btn-google {
            display: flex; align-items: center; justify-content: center; gap: 12px;
            width: 100%; padding: 15px;
            background: #fff;
            border: 1.5px solid #e2e8f0;
            border-radius: 18px;
            color: var(--text-main);
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        /* ── Footer ────────────────────────────────────────────── */
        .auth-footer { text-align: center; margin-top: 25px; font-size: 15px; color: var(--text-muted); font-weight: 500; }
        .auth-footer a { color: var(--primary); font-weight: 800; text-decoration: none; margin-left: 5px; }


        /* ── Alerts ───────────────────────────────────────────── */
        .alert { border-radius: 20px; padding: 18px; font-size: 14px; margin-bottom: 25px; border: 1.5px solid transparent; animation: shake 0.5s ease; font-weight: 600; }
        .alert-error { background: #fff1f2; border-color: #fecaca; color: #be123c; }

        @media (max-width: 600px) {
            .auth-card { padding: 40px 25px; border-radius: 28px; }
            .form-row { grid-template-columns: 1fr; gap: 0; }
            .auth-title { font-size: 28px; }
            .floating-icon { display: none; }
        }
    </style>
</head>

<body>
    <div class="mesh-bg"></div>

    <!-- Floating Cute Elements -->
    <div class="floating-icon" style="top: 10%; left: 8%; animation-delay: 0s;">✨</div>
    <div class="floating-icon" style="top: 80%; left: 12%; animation-delay: 1s;">🛍️</div>
    <div class="floating-icon" style="top: 15%; right: 10%; animation-delay: 2s;">📦</div>
    <div class="floating-icon" style="bottom: 10%; right: 12%; animation-delay: 1.5s;">🚀</div>

    <div class="auth-card">
        <div class="auth-header">
            <img src="{{ assetImage(readconfig('site_logo')) }}" alt="{{ readConfig('site_name') }}" class="auth-logo">
            
            <div class="badge-admin">
                <span class="badge-dot"></span>
                Store Owner Registration
            </div>

            <h1 class="auth-title">
                V-Mart <span style="font-weight: 300; font-size: 0.8em; color: var(--text-muted); opacity: 0.8;">Inventory</span>
            </h1>
            <p class="auth-subtitle">Get your advanced inventory & POS instantly ✨</p>
        </div>

        <!-- Alerts -->
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('signup') }}" method="POST" id="signupForm">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="name" class="form-control" placeholder="John Doe" value="{{ old('name') }}" required>
                    @error('name')<span style="color: #be123c; font-size: 11px; margin-top: 5px; display: block; font-weight: 600;">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="you@store.com" value="{{ old('email') }}" required>
                    @error('email')<span style="color: #be123c; font-size: 11px; margin-top: 5px; display: block; font-weight: 600;">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" class="form-control" placeholder="Min 6 chars" required>
                        <span class="eye-toggle" onclick="togglePass('password', this)">
                            <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm</label>
                    <div class="input-wrapper">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Repeat it" required>
                    </div>
                </div>
            </div>

            <div class="terms-row">
                <input type="checkbox" id="agree" required>
                <label for="agree">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a></label>
            </div>

            <button type="submit" class="btn-submit">Create My Store ✨</button>
        </form>

        <div class="divider">
            <span>Or join via</span>
        </div>

        <a href="{{ route('auth.google') }}" class="btn-google">
            <svg width="22" height="22" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            Google Account
        </a>

        <div class="auth-footer">
            Already a member? <a href="{{ route('login') }}">Sign in here</a>
        </div>

    </div>

    <script>
        function togglePass(inputId, icon) {
            const inp = document.getElementById(inputId);
            inp.type = inp.type === 'password' ? 'text' : 'password';
            icon.style.color = inp.type === 'text' ? 'var(--primary)' : 'var(--text-muted)';
        }
    </script>
</body>

</html>
