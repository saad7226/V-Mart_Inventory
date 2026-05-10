@if (isset($errors) && $errors->any())
    <div class="custom-toast glass-toast error" id="errorToast">
        <div class="toast-content">
            <div class="toast-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="toast-message">
                @foreach ($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            </div>
            <button class="toast-close" onclick="closeToast('errorToast')">&times;</button>
        </div>
        <div class="toast-progress"></div>
    </div>
@endif

@if (session()->has('success'))
    <div class="custom-toast glass-toast success" id="successToast">
        <div class="toast-content">
            <div class="toast-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="toast-message">{{ session('success') }}</div>
            <button class="toast-close" onclick="closeToast('successToast')">&times;</button>
        </div>
        <div class="toast-progress"></div>
    </div>
@endif

@if (session()->has('error'))
    <div class="custom-toast glass-toast error" id="sessErrorToast">
        <div class="toast-content">
            <div class="toast-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="toast-message">{{ session('error') }}</div>
            <button class="toast-close" onclick="closeToast('sessErrorToast')">&times;</button>
        </div>
        <div class="toast-progress"></div>
    </div>
@endif

<style>
    .custom-toast {
        position: fixed;
        top: 25px;
        right: 25px;
        z-index: 10000;
        min-width: 320px;
        max-width: 450px;
        border-radius: 20px;
        overflow: hidden;
        animation: toastSlideIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .glass-toast {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.4);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
    }

    .toast-content {
        padding: 20px 25px;
        display: flex;
        align-items: center;
        gap: 15px;
        position: relative;
    }

    .toast-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .success .toast-icon { background: rgba(16, 185, 129, 0.1); color: #10B981; }
    .error .toast-icon { background: rgba(239, 68, 68, 0.1); color: #EF4444; }

    .toast-message {
        color: #1f2937;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.5;
        flex-grow: 1;
    }

    .toast-close {
        background: transparent;
        border: none;
        font-size: 22px;
        color: #9ca3af;
        cursor: pointer;
        padding: 0;
        transition: color 0.2s;
    }
    .toast-close:hover { color: #4b5563; }

    .toast-progress {
        height: 4px;
        width: 100%;
        background: rgba(0, 0, 0, 0.05);
        position: relative;
    }

    .toast-progress::after {
        content: '';
        position: absolute;
        top: 0; left: 0; height: 100%; width: 100%;
        animation: toastProgress 4s linear forwards;
    }

    .success .toast-progress::after { background: #10B981; }
    .error .toast-progress::after { background: #EF4444; }

    @keyframes toastSlideIn {
        from { transform: translateX(100px) scale(0.9); opacity: 0; }
        to { transform: translateX(0) scale(1); opacity: 1; }
    }

    @keyframes toastFadeOut {
        from { transform: scale(1); opacity: 1; }
        to { transform: scale(0.9); opacity: 0; }
    }

    @keyframes toastProgress {
        from { width: 100%; }
        to { width: 0%; }
    }
</style>

<script>
    function closeToast(id) {
        const toast = document.getElementById(id);
        if (toast) {
            toast.style.animation = 'toastFadeOut 0.4s ease forwards';
            setTimeout(() => toast.remove(), 400);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const toasts = document.querySelectorAll('.custom-toast');
        toasts.forEach(toast => {
            setTimeout(() => {
                if (document.getElementById(toast.id)) closeToast(toast.id);
            }, 4000);
        });
    });
</script>
