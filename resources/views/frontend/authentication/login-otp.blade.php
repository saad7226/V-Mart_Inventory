<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        Otp Verify | {{ readConfig('site_name') }}
    </title>
    <!-- FAVICON ICON -->
    <link rel="shortcut icon" href="{{ assetImage(readconfig('site_logo')) }}" type="image/svg+xml">
    <!-- BACK-TOP CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/back-top/backToTop.css') }}">
    <!-- BOOTSTRAP CSS (5.3) -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap/bootstrap.min.css') }}">
    <!-- APP-CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.min.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #FFF5F1;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .authentications {
            display: flex;
            width: 100%;
            max-width: 1000px;
            background: #fff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(255, 71, 61, 0.1);
        }
        .left-content {
            background: linear-gradient(135deg, #FF473D 0%, #7C5AC2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
        }
        .right-content {
            padding: 60px;
            flex: 1;
        }
        .form-title {
            font-weight: 800;
            color: #2D3436;
        }
        .otp-container input {
            border: 2px solid #FFDED0;
            border-radius: 12px;
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: 700;
            margin: 0 5px;
            transition: all 0.3s ease;
        }
        .otp-container input:focus {
            border-color: #FF473D;
            box-shadow: 0 0 0 4px rgba(255, 71, 61, 0.1);
            outline: none;
        }
        .create-account-btn {
            background: #FF473D;
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 15px;
            font-weight: 700;
            box-shadow: 0 8px 15px rgba(255, 71, 61, 0.2);
            transition: all 0.3s ease;
        }
        .create-account-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(255, 71, 61, 0.3);
            background: #E63E35;
        }
        .authentication-form-footer a {
            color: #FF473D;
            font-weight: 700;
        }
    </style>
</head>

<body>
    <x-simple-alert />

    <!-- AUTHENTICATION-START (LOGIN) -->
    <section class="authentications">
        <div class="left-content">
            <figure class="">
                <img src="{{ asset('assets/images/authentication/login.svg') }}" alt="register image ">
            </figure>
        </div>
        <div class="right-content">
            <form action="{{ route('login.otp') }}" method="post" class="authentication-form px-lg-5 forgot-form">
                @csrf
                <div class="authentication-form-header">
                    <a href="{{ route('frontend.home') }}" class="logo">
                        <img src="{{ assetImage(readconfig('site_logo')) }}" width="200px" alt="brand-logo">
                    </a>
                    <h3 class="form-title">Otp Verify</h3>
                    <p class="form-des">Please enter the code we emailed you.</p>
                </div>
                <div class="authentication-form-content">
                    <div class="row g-4">
                        <div class="col-12">
                            <div class="otp-container">
                                <input type="text" maxlength="1" name="number_1" required />
                                <input type="text" maxlength="1" name="number_2" required />
                                <input type="text" maxlength="1" name="number_3" required />
                                <input type="text" maxlength="1" name="number_4" required />
                                <input type="text" maxlength="1" name="number_5" required />
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <button type="submit" class="create-account-btn w-100">Continue</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="authentication-form-footer">
                    <p>Didn’t receive the email? Click to <a href="{{ route('resend.login.otp') }}">resend </a></p>
                    <p>Back to <a href="{{ route('login') }}">Log in </a></p>
                </div>
            </form>
        </div>
    </section>
    <!-- AUTHENTICATION-END -->


    <!-- BOOTSTRAP JS (5.3) -->
    <script src="{{ asset('assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <!-- BOOTSTRAP-TOOLTIP -->
    <script src="{{ asset('assets/js/tooltip/tooltip.js') }}"></script>
    <!-- BACK-TOP JS -->
    <script src="{{ asset('assets/js/back-top/backToTop.js') }}"></script>
    <script src="{{ asset('assets/js/back-top/backtop.js') }}"></script>
    <!-- COPYRIGHT JS -->
    <script src="{{ asset('assets/js/copyright/copyright.js') }}"></script>
    <script>
        const otpInputs = document.querySelectorAll('.otp-container input[type="text"]');

        otpInputs.forEach((input, index) => {
            input.addEventListener('input', (event) => {
                const inputLength = event.target.value.length;

                if (inputLength === 1) {
                    if (index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    } else {
                        otpInputs[index].blur();
                    }
                } else if (inputLength === 0) {
                    if (index > 0) {
                        otpInputs[index - 1].focus();
                    }
                }

                updateInputStyles();
            });

            input.addEventListener('keydown', (event) => {
                const key = event.keyCode || event.charCode;

                if (key === 8 && input.value.length === 0 && index > 0) {
                    otpInputs[index - 1].focus();
                }

                updateInputStyles();
            });
        });

        function updateInputStyles() {
            otpInputs.forEach((input) => {
                if (input.value.length > 0) {
                    input.classList.add('filled');
                } else {
                    input.classList.remove('filled');
                }
            });
        }
    </script>

</body>

</html>
