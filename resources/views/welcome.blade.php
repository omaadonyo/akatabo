<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body style="margin:0;padding:0;font-family:'Instrument Sans',system-ui,-apple-system,sans-serif;background:#0f172a;color:#fff;min-height:100vh;overflow-x:hidden;">

{{-- Background pattern --}}
<div style="position:fixed;inset:0;overflow:hidden;pointer-events:none;z-index:0;">
    <div style="position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(ellipse at 20% 50%,rgba(59,130,246,0.08) 0%,transparent 50%),radial-gradient(ellipse at 80% 20%,rgba(168,85,247,0.06) 0%,transparent 50%),radial-gradient(ellipse at 40% 80%,rgba(16,185,129,0.04) 0%,transparent 50%);"></div>
    <div style="position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.06),transparent);"></div>
</div>

{{-- Main content (blurred behind modal) --}}
<div id="page-content" style="position:relative;z-index:1;min-height:100vh;display:flex;flex-direction:column;">

    {{-- TOP NAV --}}
    <header style="display:flex;align-items:center;justify-content:space-between;padding:20px 40px;border-bottom:1px solid rgba(255,255,255,0.06);backdrop-filter:blur(12px);background:rgba(15,23,42,0.6);">
        <div style="display:flex;align-items:center;gap:12px;">
            <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#3b82f6,#a855f7);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:16px;color:#fff;">A</div>
            <span style="font-weight:700;font-size:16px;letter-spacing:-0.02em;">{{ config('app.name') }}</span>
        </div>
        <button onclick="document.getElementById('login-modal').style.display='flex';document.getElementById('overlay-bg').style.display='block';document.body.style.overflow='hidden';" style="padding:10px 28px;border-radius:10px;border:none;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;font-size:14px;font-weight:600;font-family:inherit;cursor:pointer;box-shadow:0 4px 16px rgba(59,130,246,0.3);transition:transform 0.15s,box-shadow 0.15s;">Sign In</button>
    </header>

    {{-- HERO --}}
    <section style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:80px 40px 60px;text-align:center;">
        <div style="display:inline-flex;align-items:center;gap:8px;padding:8px 20px;border-radius:100px;background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.2);font-size:13px;color:#93c5fd;margin-bottom:32px;">
            <span style="width:6px;height:6px;border-radius:50%;background:#22c55e;display:inline-block;"></span>
            Multi-tenant business management platform
        </div>
        <h1 style="font-size:clamp(36px,6vw,64px);font-weight:800;letter-spacing:-0.03em;line-height:1.1;margin:0 0 20px;background:linear-gradient(135deg,#f8fafc,#94a3b8);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;">Manage invoices,<br>quotations &amp; receipts</h1>
        <p style="font-size:18px;color:#64748b;max-width:520px;line-height:1.6;margin:0 0 40px;">Track customers, inventory, fabric rolls, and payments — all in one place with real-time insights.</p>
        <button onclick="document.getElementById('login-modal').style.display='flex';document.getElementById('overlay-bg').style.display='block';document.body.style.overflow='hidden';" style="padding:14px 40px;border-radius:12px;border:none;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;font-size:16px;font-weight:600;font-family:inherit;cursor:pointer;box-shadow:0 8px 32px rgba(59,130,246,0.3);transition:transform 0.15s,box-shadow 0.15s;">Get Started &rarr;</button>
    </section>

    {{-- FEATURES --}}
    <section style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1px;background:rgba(255,255,255,0.04);border-top:1px solid rgba(255,255,255,0.06);border-bottom:1px solid rgba(255,255,255,0.06);">
        @php
            $features = [
                ['svg' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Quotations', 'desc' => 'Create & track', 'color' => '#a855f7'],
                ['svg' => 'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Invoices', 'desc' => 'Send & collect', 'color' => '#3b82f6'],
                ['svg' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'label' => 'Receipts', 'desc' => 'Confirm payments', 'color' => '#10b981'],
                ['svg' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'label' => 'Inventory', 'desc' => 'Fabric rolls & stock', 'color' => '#f59e0b'],
            ];
        @endphp
        @foreach($features as $f)
        <div style="padding:32px 28px;background:rgba(15,23,42,0.8);display:flex;align-items:center;gap:16px;">
            <div style="width:44px;height:44px;border-radius:12px;background:{{ $f['color'] }}20;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg style="width:22px;height:22px;color:{{ $f['color'] }};" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $f['svg'] }}"/></svg>
            </div>
            <div>
                <div style="font-weight:600;font-size:14px;color:#f1f5f9;">{{ $f['label'] }}</div>
                <div style="font-size:12px;color:#64748b;margin-top:2px;">{{ $f['desc'] }}</div>
            </div>
        </div>
        @endforeach
    </section>

    {{-- FOOTER --}}
    <footer style="padding:20px 40px;text-align:center;font-size:13px;color:#475569;border-top:1px solid rgba(255,255,255,0.04);">
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </footer>
</div>

{{-- Blur overlay background --}}
<div id="overlay-bg" style="display:none;position:fixed;inset:0;z-index:10;backdrop-filter:blur(24px) saturate(0.8);-webkit-backdrop-filter:blur(24px) saturate(0.8);background:rgba(15,23,42,0.5);transition:opacity 0.3s;" onclick="closeModal(event)"></div>

{{-- LOGIN MODAL --}}
<div id="login-modal" style="display:none;position:fixed;inset:0;z-index:20;align-items:center;justify-content:center;padding:24px;" onclick="if(event.target===this)closeModal(event)">
    <div style="width:100%;max-width:420px;background:linear-gradient(135deg,#1e293b,#0f172a);border-radius:20px;border:1px solid rgba(255,255,255,0.08);box-shadow:0 32px 64px rgba(0,0,0,0.5);overflow:hidden;animation:slideUp 0.3s ease-out;">
        <style>@keyframes slideUp{from{opacity:0;transform:translateY(20px) scale(0.97)}to{opacity:1;transform:translateY(0) scale(1)}}</style>

        {{-- Modal header --}}
        <div style="padding:32px 32px 0;text-align:center;">
            <div style="width:56px;height:56px;margin:0 auto 16px;border-radius:16px;background:linear-gradient(135deg,#3b82f6,#a855f7);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:24px;color:#fff;box-shadow:0 8px 24px rgba(59,130,246,0.25);">A</div>
            <h2 style="font-size:22px;font-weight:700;margin:0 0 6px;color:#f1f5f9;">Welcome back</h2>
            <p style="font-size:14px;color:#64748b;margin:0 0 28px;">Sign in to your account to continue</p>
        </div>

        {{-- Login form --}}
        <form method="POST" action="{{ route('filament.app.auth.login') }}" style="padding:0 32px 32px;">
            @csrf

            <div style="margin-bottom:20px;">
                <label for="email" style="display:block;font-size:13px;font-weight:600;color:#94a3b8;margin-bottom:6px;">Email</label>
                <input type="email" name="login" id="email" required autocomplete="email" autofocus value="{{ old('login') }}"
                    style="width:100%;padding:12px 16px;border-radius:10px;border:1px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.04);color:#f1f5f9;font-size:15px;font-family:inherit;outline:none;box-sizing:border-box;transition:border-color 0.15s;"
                    onfocus="this.style.borderColor='rgba(59,130,246,0.5)'" onblur="this.style.borderColor='rgba(255,255,255,0.08)'">
                @error('login') <div style="color:#f87171;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
            </div>

            <div style="margin-bottom:24px;">
                <label for="password" style="display:block;font-size:13px;font-weight:600;color:#94a3b8;margin-bottom:6px;">Password</label>
                <input type="password" name="password" id="password" required autocomplete="current-password"
                    style="width:100%;padding:12px 16px;border-radius:10px;border:1px solid rgba(255,255,255,0.08);background:rgba(255,255,255,0.04);color:#f1f5f9;font-size:15px;font-family:inherit;outline:none;box-sizing:border-box;transition:border-color 0.15s;"
                    onfocus="this.style.borderColor='rgba(59,130,246,0.5)'" onblur="this.style.borderColor='rgba(255,255,255,0.08)'">
                @error('password') <div style="color:#f87171;font-size:12px;margin-top:4px;">{{ $message }}</div> @enderror
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;">
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:#64748b;cursor:pointer;">
                    <input type="checkbox" name="remember" style="width:16px;height:16px;accent-color:#3b82f6;">
                    Remember me
                </label>
                @if (Route::has('filament.app.auth.password-reset.request'))
                <a href="{{ route('filament.app.auth.password-reset.request') }}" style="font-size:13px;color:#3b82f6;text-decoration:none;">Forgot password?</a>
                @endif
            </div>

            <button type="submit" style="width:100%;padding:14px;border-radius:10px;border:none;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;font-size:15px;font-weight:600;font-family:inherit;cursor:pointer;box-shadow:0 4px 16px rgba(59,130,246,0.25);transition:transform 0.15s,box-shadow 0.15s;"
                onmouseenter="this.style.transform='translateY(-1px)';this.style.boxShadow='0 8px 24px rgba(59,130,246,0.35)'" 
                onmouseleave="this.style.transform='translateY(0)';this.style.boxShadow='0 4px 16px rgba(59,130,246,0.25)'">
                Sign In
            </button>

            @if (Route::has('filament.app.auth.register'))
            <p style="text-align:center;margin:20px 0 0;font-size:13px;color:#64748b;">
                Don't have an account?
                <a href="{{ route('filament.app.auth.register') }}" style="color:#3b82f6;text-decoration:none;font-weight:600;">Register</a>
            </p>
            @endif
        </form>
    </div>
</div>

<script>
function closeModal(e) {
    if (e && e.target !== e.currentTarget && e.target.closest('#login-modal > div')) return;
    document.getElementById('login-modal').style.display = 'none';
    document.getElementById('overlay-bg').style.display = 'none';
    document.body.style.overflow = '';
}
@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('login-modal').style.display = 'flex';
    document.getElementById('overlay-bg').style.display = 'block';
    document.body.style.overflow = 'hidden';
});
@endif
</script>

</body>
</html>
