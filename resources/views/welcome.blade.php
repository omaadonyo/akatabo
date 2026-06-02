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
            --bg-page: #ffffff;
            --bg-card: #fafafa;
            --text-primary: #000000;
            --text-secondary: #404040;
            --text-muted: #a3a3a3;
            --border: #e5e5e5;
            --bg-header: rgba(255,255,255,0.85);
            --nav-border: #e5e5e5;
            --input-bg: #f5f5f5;
            --input-border: #d4d4d4;
            --input-text: #000000;
            --label-color: #404040;
            --shadow: 0 1px 2px rgba(0,0,0,0.06);
            --stat-bg: #ffffff;
        }
        @media (prefers-color-scheme: dark) {
            :root {
                --bg-page: #000000; --bg-card: #1a1a1a; --text-primary: #ffffff; --text-secondary: #a3a3a3; --text-muted: #525252; --border: #262626;
                --bg-header: rgba(0,0,0,0.7); --nav-border: rgba(255,255,255,0.08);
                --input-bg: rgba(255,255,255,0.04); --input-border: rgba(255,255,255,0.1); --input-text: #ffffff; --label-color: #a3a3a3;
                --shadow: 0 1px 2px rgba(0,0,0,0.4); --stat-bg: rgba(26,26,26,0.8);
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
    <div style="position:absolute;top:0;left:0;right:0;height:1px;background:linear-gradient(90deg,transparent,var(--border),transparent)"></div>
</div>

<header style="position:relative;z-index:2;display:flex;align-items:center;justify-content:space-between;padding:0 40px;height:60px;border-bottom:1px solid var(--nav-border);backdrop-filter:blur(12px);background:var(--bg-header);flex-shrink:0">
    <div style="display:flex;align-items:center;gap:10px">
        <div style="width:32px;height:32px;border-radius:8px;background:var(--text-primary);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:15px;color:var(--bg-page)">A</div>
        <span style="font-weight:700;font-size:15px;letter-spacing:-.02em">{{ config('app.name') }}</span>
    </div>
    <div>
        <a href="{{ route('filament.app.auth.login') }}" style="padding:8px 24px;border-radius:8px;border:1px solid var(--text-primary);background:var(--text-primary);color:var(--bg-page);font-size:13px;font-weight:600;font-family:inherit;cursor:pointer;text-decoration:none;display:inline-block;transition:opacity .15s" onmouseenter="this.style.opacity='.8'" onmouseleave="this.style.opacity='1'">Sign In</a>
    </div>
</header>

<div style="position:relative;z-index:1;height:calc(100vh - 60px);display:flex;flex-direction:column;">

    <div style="flex:1;display:flex;align-items:center;gap:48px;padding:32px 48px;min-height:0">

        {{-- Hero --}}
        <div style="flex:0 0 auto;max-width:440px">
            <div style="display:inline-flex;align-items:center;gap:6px;padding:6px 16px;border-radius:100px;background:var(--bg-card);border:1px solid var(--border);font-size:12px;color:var(--text-secondary);margin-bottom:20px">
                <span style="width:5px;height:5px;border-radius:50%;background:var(--text-muted);display:inline-block"></span>
                Multi-tenant platform
            </div>
            <h1 style="font-size:clamp(28px,3.2vw,42px);font-weight:800;letter-spacing:-.03em;line-height:1.15;margin:0 0 12px;color:var(--text-primary)">Manage invoices,<br>quotations &amp; receipts</h1>
            <p style="font-size:15px;color:var(--text-secondary);line-height:1.6;margin:0 0 24px;max-width:400px">Track customers, inventory, fabric rolls, and payments in one place with real-time insights.</p>
            <a href="{{ route('filament.app.auth.login') }}" style="display:inline-block;padding:12px 32px;border-radius:10px;border:1px solid var(--text-primary);background:var(--text-primary);color:var(--bg-page);font-size:14px;font-weight:600;font-family:inherit;cursor:pointer;text-decoration:none;transition:opacity .15s" onmouseenter="this.style.opacity='.8'" onmouseleave="this.style.opacity='1'">Get Started &rarr;</a>
        </div>

        {{-- Stats grid --}}
        <div style="flex:1;display:grid;grid-template-columns:1fr 1fr;gap:12px;min-width:0">
            @php
                $stats = [
                    ['label'=>'Total Invoiced','value'=>'UGX '.number_format($totalInvoiced,0),'icon'=>'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label'=>'Pending Invoices','value'=>$pendingInvoices,'icon'=>'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['label'=>'Receipts Issued','value'=>$receiptCount,'icon'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'],
                    ['label'=>'Active Quotations','value'=>$activeQuotations,'icon'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                    ['label'=>'Customers','value'=>$customerCount,'icon'=>'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'],
                ];
            @endphp
            @foreach($stats as $s)
            <div style="background:var(--stat-bg);border-radius:12px;padding:18px 20px;border:1px solid var(--border);box-shadow:var(--shadow)">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
                    <div style="width:32px;height:32px;border-radius:8px;background:var(--bg-card);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <svg style="width:16px;height:16px;color:var(--text-secondary)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{$s['icon']}}"/></svg>
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
                ['svg'=>'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z','label'=>'Quotations'],
                ['svg'=>'M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z','label'=>'Invoices'],
                ['svg'=>'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z','label'=>'Receipts'],
                ['svg'=>'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4','label'=>'Inventory'],
            ];
        @endphp
        @foreach($features as $f)
        <div style="padding:16px 24px;display:flex;align-items:center;gap:12px;border-right:1px solid var(--border)">
            <div style="width:36px;height:36px;border-radius:10px;background:var(--bg-page);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;flex-shrink:0">
                <svg style="width:18px;height:18px;color:var(--text-secondary)" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="{{$f['svg']}}"/></svg>
            </div>
            <div>
                <div style="font-weight:600;font-size:13px;color:var(--text-primary)">{{$f['label']}}</div>
                <div style="font-size:11px;color:var(--text-muted);margin-top:1px">{{$f['label']==='Quotations'?'Create & track':($f['label']==='Invoices'?'Send & collect':($f['label']==='Receipts'?'Confirm payments':'Fabric rolls & stock'))}}</div>
            </div>
        </div>
        @endforeach
    </div>
</div>

</body>
</html>
