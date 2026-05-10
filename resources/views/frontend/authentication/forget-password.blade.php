<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password | {{ readConfig('site_name') ?? 'V-Mart' }}</title>
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
            align-items: center;
            justify-content: center;
            background: #fff;
            position: relative;
            overflow: hidden;
            padding: 20px;
        }

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

        .floating-icon {
            position: absolute; z-index: -1; animation: float 6s ease-in-out infinite; font-size: 40px;
        }
        @keyframes float { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-30px) rotate(15deg); } }

        .auth-card {
            width: 100%; max-width: 460px;
            background: var(--glass-bg); backdrop-filter: blur(25px); -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border); border-radius: 32px; padding: 50px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1); position: relative; animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes slideUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }

        .auth-header { text-align: center; margin-bottom: 35px; }
        .auth-logo { max-height: 55px; margin-bottom: 20px; }
        .auth-title { font-size: 28px; font-weight: 800; color: var(--text-main); letter-spacing: -1px; margin-bottom: 8px; }
        .auth-subtitle { color: var(--text-muted); font-size: 15px; font-weight: 500; }

        .form-group { margin-bottom: 22px; }
        .form-label { display: block; font-size: 13px; font-weight: 700; color: var(--text-main); margin-bottom: 8px; }
        
        .form-control {
            width: 100%; background: rgba(255, 255, 255, 0.85); border: 1.5px solid #e2e8f0;
            border-radius: 18px; padding: 16px 20px; font-size: 15px; font-family: inherit;
            color: var(--text-main); transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); outline: none;
        }
        .form-control:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px rgba(255, 71, 61, 0.1); transform: translateY(-2px); }

        .btn-submit {
            width: 100%; padding: 18px; background: linear-gradient(135deg, var(--primary) 0%, #FF7B73 100%);
            color: #fff; border: none; border-radius: 18px; font-size: 16px; font-weight: 800;
            cursor: pointer; transition: all 0.3s ease; box-shadow: 0 10px 20px -5px rgba(255, 71, 61, 0.4); margin-top: 10px;
        }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 15px 30px -5px rgba(255, 71, 61, 0.5); filter: brightness(1.05); }

        .auth-footer { text-align: center; margin-top: 35px; font-size: 15px; color: var(--text-muted); font-weight: 500; }
        .auth-footer a { color: var(--primary); font-weight: 800; text-decoration: none; margin-left: 5px; }

        @media (max-width: 480px) { .auth-card { padding: 40px 25px; } .floating-icon { display: none; } }
    </style>
</head>

<body>
    <div class="mesh-bg"></div>
    <div class="floating-icon" style="top: 15%; left: 10%; animation-delay: 0s;">🔑</div>
    <div class="floating-icon" style="bottom: 15%; right: 15%; animation-delay: 1.5s;">🔒</div>

    <div class="auth-card">
        <div class="auth-header">
            <a href="{{ route('frontend.home') }}">
                <img src="{{ assetImage(readconfig('site_logo')) }}" alt="{{ readConfig('site_name') }}" class="auth-logo">
            </a>
            <h1 class="auth-title">
                V-Mart <span style="font-weight: 300; font-size: 0.8em; color: var(--text-muted); opacity: 0.8;">Inventory</span>
            </h1>
            <p class="auth-subtitle">No worries, we'll send you reset instructions ✨</p>
        </div>

        <form action="{{ route('forget.password') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>

            <button type="submit" class="btn-submit">Send Reset Link</button>
        </form>

        <div class="auth-footer">
            Back to <a href="{{ route('login') }}">Sign In</a>
        </div>
    </div>
</body>

</html>
