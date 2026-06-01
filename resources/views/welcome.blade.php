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
    <style>
        :root {
            --bg-page: #f8fafc;
            --bg-card: #ffffff;
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --text-muted: #94a3b8;
            --border: #e2e8f0;
            --bg-header: rgba(255,255,255,0.8);
            --nav-border: #e2e8f0;
            --overlay: rgba(15,23,42,0.3);
            --input-bg: #f1f5f9;
            --input-border: #e2e8f0;
            --input-text: #0f172a;
            --label-color: #475569;
            --shadow: 0 1px 2px rgba(0,0,0,0.04);
            --stat-bg: #ffffff;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg-page: #0f172a; --bg-card: #1e293b; --text-primary: #f1f5f9; --text-secondary: #94a3b8; --text-muted: #64748b; --border: #334155;
                --bg-header: rgba(15,23,42,0.7); --nav-border: rgba(255,255,255,0.06); --overlay: rgba(0,0,0,0.5);
                --input-bg: rgba(255,255,255,0.04); --input-border: rgba(255,255,255,0.08); --input-text: #f1f5f9; --label-color: #94a3b8;
                --shadow: 0 1px 2px rgba(0,0,0,0.3); --stat-bg: rgba(30,41,59,0.8);
            }
        }
        *,*:before,*:after{box-sizing:border-box}
        body{margin:0;padding:0;font-family:'Instrument Sans',system-ui,-apple-system,sans-serif;background:var(--bg-page);color:var(--text-primary);height:100vh;overflow:hidden}
    </style>
</head>
<body>

@php
    use App\Models\Invoice; use App\Models\Quotation; use App\Models\Receipt; use App\Models\Customer;
    $totalInvoiced = Invoice::whereNotIn('status',['draft','cancelled'])->sum('total');
    $pendingInvoices = Invoice::whereIn('status',['sent','overdue'])->count();
    $receiptCount = Receipt::count();
    $activeQuotations = Quotation::whereNotIn('status',['draft','cancelled'])->count();
    $customerCount = Customer::count();
@endphp



<div style="position:fixed;inset:0;overflow:hidden;pointer-events:none;z-index:0">
    <div style="position:absolute;top:-50%;left:-50%;width:200%;height:200%;background:radial-gradient(ellipse at 20% 50%,rgba(59,130,246,0.06) 0%,transparent 50%),radial-gradient(ellipse at 80% 20%,rgba(168,85,247,0.04) 0%,transparent 50%);"></div>
    <div style="position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,var(--border),transparent)"></div>
</div>

<header style="position:relative;z-index:2;display:flex;align-items:center;justify-content:space-between;padding:0 40px;height:60px;border-bottom:1px solid var(--nav-border);backdrop-filter:blur(12px);background:var(--bg-header);flex-shrink:0">
    <div style="display:flex;align-items:center;gap:10px">
        <div style="width:32px;height:32px;border-radius:8px;background:linear-gradient(135deg,#3b82f6,#a855f7);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:15px;color:#fff">A</div>
        <span style="font-weight:700;font-size:15px;letter-spacing:-.02em">{{ config('app.name') }}</span>
    </div>
    <div style="display:flex;align-items:center;gap:10px">
        <button onclick="openModal()" style="padding:8px 24px;border-radius:8px;border:none;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;font-size:13px;font-weight:600;font-family:inherit;cursor:pointer;box-shadow:0 3px 12px rgba(59,130,246,.25)">Sign In</button>
    </div>
</header>

<div style="position:relative;z-index:1;height:calc(100vh - 60px);display:flex;flex-direction:column;">

    <div style="flex:1;display:flex;align-items:center;gap:48px;padding:32px 48px;min-height:0">

        {{-- Hero --}}
        <div style="flex:0 0 auto;max-width:440px">
            <div style="display:inline-flex;align-items:center;gap:6px;padding:6px 16px;border-radius:100px;background:rgba(59,130,246,.08);border:1px solid rgba(59,130,246,.15);font-size:12px;color:#3b82f6;margin-bottom:20px">
                <span style="width:5px;height:5px;border-radius:50%;background:#22c55e;display:inline-block"></span>
                Multi-tenant platform
            </div>
            <h1 style="font-size:clamp(28px,3.2vw,42px);font-weight:800;letter-spacing:-.03em;line-height:1.15;margin:0 0 12px">Manage invoices,<br>quotations &amp; receipts</h1>
            <p style="font-size:15px;color:var(--text-secondary);line-height:1.6;margin:0 0 24px;max-width:400px">Track customers, inventory, fabric rolls, and payments in one place with real-time insights.</p>
            <button onclick="openModal()" style="padding:12px 32px;border-radius:10px;border:none;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;font-size:14px;font-weight:600;font-family:inherit;cursor:pointer;box-shadow:0 6px 24px rgba(59,130,246,.25)">Get Started &rarr;</button>
        </div>

        {{-- Stats grid --}}
        <div style="flex:1;display:grid;grid-template-columns:1fr 1fr;gap:12px;min-width:0">
            @php
                $stats = [
                    ['label'=>'Total Invoiced','value'=>'UGX '.number_format($totalInvoiced,0),'icon'=>'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z','color'=>'#3b82f6'],
                    ['label'=>'Pending Invoices','value'=>$pendingInvoices,'icon'=>'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z','color'=>'#f59e0b'],
                    ['label'=>'Receipts Issued','value'=>$receiptCount,'icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','color'=>'#10b981'],
                    ['label'=>'Active Quotations','value'=>$activeQuotations,'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','color'=>'#a855f7'],
                    ['label'=>'Customers','value'=>$customerCount,'icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z','color'=>'#06b6d4'],
                ];
            @endphp
            @foreach($stats as $s)
            <div style="background:var(--stat-bg);border-radius:12px;padding:18px 20px;border:1px solid var(--border);box-shadow:var(--shadow)">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                    <div style="width:32px;height:32px;border-radius:8px;background:{{$s['color']}}12;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:16px;height:16px;color:{{$s['color']}}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{$s['icon']}}"/></svg>
                    </div>
                    <span style="font-size:12px;font-weight:500;color:var(--text-secondary);line-height:1.2">{{$s['label']}}</span>
                </div>
                <div style="font-size:clamp(20px,1.8vw,28px);font-weight:800;letter-spacing:-.02em;color:var(--text-primary);line-height:1.2">{{$s['value']}}</div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Feature bar --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);border-top:1px solid var(--border);background:var(--bg-card);flex-shrink:0">
        @php
            $features = [
                ['svg'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','label'=>'Quotations','color'=>'#a855f7'],
                ['svg'=>'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z','label'=>'Invoices','color'=>'#3b82f6'],
                ['svg'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','label'=>'Receipts','color'=>'#10b981'],
                ['svg'=>'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4','label'=>'Inventory','color'=>'#f59e0b'],
            ];
        @endphp
        @foreach($features as $f)
        <div style="padding:16px 24px;display:flex;align-items:center;gap:12px;border-right:1px solid var(--border);&:last-child{border-right:none}">
            <div style="width:36px;height:36px;border-radius:10px;background:{{$f['color']}}12;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg style="width:18px;height:18px;color:{{$f['color']}}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{$f['svg']}}"/></svg>
            </div>
            <div>
                <div style="font-weight:600;font-size:13px;color:var(--text-primary)">{{$f['label']}}</div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:1px">{{$f['label']==='Quotations'?'Create & track':($f['label']==='Invoices'?'Send & collect':($f['label']==='Receipts'?'Confirm payments':'Fabric rolls & stock'))}}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Blur overlay --}}
<div id="overlay-bg" onclick="closeModal(event)" style="display:none;position:fixed;inset:0;z-index:10;backdrop-filter:blur(24px) saturate(.8);-webkit-backdrop-filter:blur(24px) saturate(.8);background:var(--overlay)"></div>

{{-- LOGIN MODAL --}}
<div id="login-modal" onclick="if(event.target===this)closeModal(event)" style="display:none;position:fixed;inset:0;z-index:20;align-items:center;justify-content:center;padding:24px">
    <div style="width:100%;max-width:400px;background:var(--bg-card);border-radius:16px;border:1px solid var(--border);box-shadow:0 32px 64px rgba(0,0,0,.3);overflow:hidden;animation:slideUp .3s ease-out">
        <style>@keyframes slideUp{from{opacity:0;transform:translateY(16px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}</style>
        <div style="padding:28px 28px 0;text-align:center">
            <div style="width:48px;height:48px;margin:0 auto 12px;border-radius:12px;background:linear-gradient(135deg,#3b82f6,#a855f7);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:20px;color:#fff;box-shadow:0 6px 20px rgba(59,130,246,.2)">A</div>
            <h2 style="font-size:20px;font-weight:700;margin:0 0 4px;color:var(--text-primary)">Welcome back</h2>
            <p style="font-size:13px;color:var(--text-secondary);margin:0 0 24px">Sign in to your account</p>
        </div>
        <form method="POST" action="{{ route('filament.app.auth.login') }}" style="padding:0 28px 28px">
            @csrf
            <div style="margin-bottom:16px">
                <label for="email" style="display:block;font-size:12px;font-weight:600;color:var(--label-color);margin-bottom:5px">Email</label>
                <input type="email" name="login" id="email" required autocomplete="email" autofocus value="{{ old('login') }}"
                    style="width:100%;padding:11px 14px;border-radius:8px;border:1px solid var(--input-border);background:var(--input-bg);color:var(--input-text);font-size:14px;font-family:inherit;outline:none;transition:border-color .15s"
                    onfocus="this.style.borderColor='rgba(59,130,246,.5)'" onblur="this.style.borderColor='var(--input-border)'">
                @error('login')<div style="color:#f87171;font-size:11px;margin-top:3px">{{$message}}</div>@enderror
            </div>
            <div style="margin-bottom:20px">
                <label for="password" style="display:block;font-size:12px;font-weight:600;color:var(--label-color);margin-bottom:5px">Password</label>
                <input type="password" name="password" id="password" required autocomplete="current-password"
                    style="width:100%;padding:11px 14px;border-radius:8px;border:1px solid var(--input-border);background:var(--input-bg);color:var(--input-text);font-size:14px;font-family:inherit;outline:none;transition:border-color .15s"
                    onfocus="this.style.borderColor='rgba(59,130,246,.5)'" onblur="this.style.borderColor='var(--input-border)'">
                @error('password')<div style="color:#f87171;font-size:11px;margin-top:3px">{{$message}}</div>@enderror
            </div>
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
                <label style="display:flex;align-items:center;gap:6px;font-size:12px;color:var(--text-secondary);cursor:pointer">
                    <input type="checkbox" name="remember" style="width:14px;height:14px;accent-color:#3b82f6"> Remember me
                </label>
                @if(Route::has('filament.app.auth.password-reset.request'))
                <a href="{{ route('filament.app.auth.password-reset.request') }}" style="font-size:12px;color:#3b82f6;text-decoration:none">Forgot password?</a>
                @endif
            </div>
            <button type="submit" style="width:100%;padding:12px;border-radius:8px;border:none;background:linear-gradient(135deg,#3b82f6,#2563eb);color:#fff;font-size:14px;font-weight:600;font-family:inherit;cursor:pointer;box-shadow:0 3px 12px rgba(59,130,246,.2)">Sign In</button>
            @if(Route::has('filament.app.auth.register'))
            <p style="text-align:center;margin:16px 0 0;font-size:12px;color:var(--text-secondary)">Don't have an account? <a href="{{ route('filament.app.auth.register') }}" style="color:#3b82f6;text-decoration:none;font-weight:600">Register</a></p>
            @endif
        </form>
    </div>
</div>

<script>
function openModal(){
    document.getElementById('login-modal').style.display='flex';
    document.getElementById('overlay-bg').style.display='block';
    document.body.style.overflow='hidden';
}
function closeModal(e){
    if(e&&e.target!==e.currentTarget&&e.target.closest('#login-modal>div'))return;
    document.getElementById('login-modal').style.display='none';
    document.getElementById('overlay-bg').style.display='none';
    document.body.style.overflow='';
}
@if($errors->any())
document.addEventListener('DOMContentLoaded',function(){openModal()});
@endif
</script>

</body>
</html>
