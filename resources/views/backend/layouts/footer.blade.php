<footer class="main-footer">
    <div class="footer-glass">
        <strong>© {{date('Y')}} <a href="{{ readConfig('site_url') }}">{{ readConfig('site_name') }} </a></strong>
        All rights reserved.
    </div>
</footer>

<style>
    .main-footer {
        background: transparent !important;
        border: none !important;
        padding: 20px 30px !important;
    }
    .footer-glass {
        background: var(--glass-bg);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 20px;
        text-align: center;
        box-shadow: 0 -10px 30px rgba(0,0,0,0.02);
        color: var(--text-muted);
    }
    .footer-glass a {
        color: var(--primary);
        font-weight: 700;
    }
</style>
