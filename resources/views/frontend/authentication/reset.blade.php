<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | {{ readConfig('site_name') ?? 'V-Mart' }}</title>
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

        .otp-container { display: flex; gap: 10px; justify-content: center; margin-bottom: 30px; }
        .otp-container input {
            width: 55px; height: 65px; text-align: center; font-size: 24px; font-weight: 800;
            border: 1.5px solid #e2e8f0; border-radius: 16px; background: rgba(255,255,255,0.8);
            transition: all 0.3s ease; outline: none; color: var(--text-main);
        }
        .otp-container input:focus { border-color: var(--primary); background: #fff; transform: translateY(-3px); box-shadow: 0 10px 15px rgba(255, 71, 61, 0.1); }
        .otp-container input.filled { border-color: var(--primary); background: #fff; }

        .btn-submit {
            width: 100%; padding: 18px; background: linear-gradient(135deg, var(--primary) 0%, #FF7B73 100%);
            color: #fff; border: none; border-radius: 18px; font-size: 16px; font-weight: 800;
            cursor: pointer; transition: all 0.3s ease; box-shadow: 0 10px 20px -5px rgba(255, 71, 61, 0.4);
        }
        .btn-submit:hover { transform: translateY(-3px); box-shadow: 0 15px 30px -5px rgba(255, 71, 61, 0.5); }

        .auth-footer { text-align: center; margin-top: 35px; font-size: 14px; color: var(--text-muted); font-weight: 500; }
        .auth-footer a { color: var(--primary); font-weight: 800; text-decoration: none; }

        @media (max-width: 480px) { .auth-card { padding: 40px 25px; } .otp-container input { width: 45px; height: 55px; font-size: 20px; } }
    </style>
</head>

<body>
    <div class="mesh-bg"></div>

    <div class="auth-card">
        <div class="auth-header">
            <img src="{{ assetImage(readconfig('site_logo')) }}" alt="{{ readConfig('site_name') }}" class="auth-logo">
            <h1 class="auth-title">
                V-Mart <span style="font-weight: 300; font-size: 0.8em; color: var(--text-muted); opacity: 0.8;">Inventory</span>
            </h1>
            <p class="auth-subtitle">Enter the 5-digit code sent to you âœ¨</p>
        </div>

        <form action="{{ route('password.reset') }}" method="POST">
            @csrf
            <div class="otp-container">
                <input type="text" maxlength="1" name="number_1" required autofocus />
                <input type="text" maxlength="1" name="number_2" required />
                <input type="text" maxlength="1" name="number_3" required />
                <input type="text" maxlength="1" name="number_4" required />
                <input type="text" maxlength="1" name="number_5" required />
            </div>

            <button type="submit" class="btn-submit">Verify Account</button>
        </form>

        <div class="auth-footer">
            <p>Didn't get the code? <a href="{{ route('resend.otp') }}">Resend</a></p>
            <p style="margin-top: 10px;"><a href="{{ route('login') }}">Back to Sign In</a></p>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-container input');
        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1 && index < inputs.length - 1) inputs[index + 1].focus();
                if (e.target.value.length > 0) e.target.classList.add('filled');
                else e.target.classList.remove('filled');
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) inputs[index - 1].focus();
            });
        });
    </script>
</body>

</html>

