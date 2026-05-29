@php
    $roll = $roll ?? $this->record;
    $company = $company ?? $roll->company;
    $remainingPct = $roll->remaining_percentage;
    $usedMeters = $roll->used_meters;
@endphp

<div style="padding: 4px 0;">
    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 3px solid #f59e0b;">
        <div style="display: flex; align-items: center; gap: 14px;">
            <div style="width: 46px; height: 46px; border-radius: 10px; background: linear-gradient(135deg, #f59e0b, #d97706); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 20px; box-shadow: 0 2px 6px rgba(217,119,6,0.25);">
                {{ strtoupper(substr($roll->fabric_name ?? 'F', 0, 1)) }}
            </div>
            <div>
                <div style="font-size: 16px; font-weight: 700;">{{ $roll->roll_code }}</div>
                <div style="font-size: 11px; margin-top: 1px;">{{ $roll->fabric_name }} &middot; {{ $roll->color }}</div>
            </div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 24px; font-weight: 800; color: #d97706; letter-spacing: -0.02em;">FABRIC ROLL</div>
            <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px; margin-top: 8px;">
                <span style="font-size: 11px;">Received {{ $roll->date_received?->format('M d, Y') }}</span>
                <span style="display: inline-block; padding: 2px 12px; font-size: 10px; font-weight: 700; border-radius: 20px; background: {{ match($roll->status) { 'in_stock' => '#ecfdf5', 'partially_used' => '#fef3c7', 'depleted' => '#f3f4f6', 'damaged' => '#fef2f2', default => '#f3f4f6' } }}; color: {{ match($roll->status) { 'in_stock' => '#16a34a', 'partially_used' => '#d97706', 'depleted' => '#6b7280', 'damaged' => '#dc2626', default => '#6b7280' } }};">
                    {{ str_replace('_', ' ', ucfirst($roll->status)) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Fabric Information --}}
    <div style="display: flex; gap: 14px; margin-bottom: 20px;">
        <div style="flex: 1; padding: 14px 18px; border: 1px solid #fde68a; border-radius: 10px;">
            <div style="font-size: 9px; font-weight: 700; color: #d97706; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px;">Fabric Information</div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; font-size: 12px;">
                <div><span style="font-weight: 600; color: #6b7280;">Fabric:</span> {{ $roll->fabric_name }}</div>
                <div><span style="font-weight: 600; color: #6b7280;">Color:</span> {{ $roll->color }}</div>
                <div><span style="font-weight: 600; color: #6b7280;">Supplier:</span> {{ $roll->supplier }}</div>
                <div><span style="font-weight: 600; color: #6b7280;">Width:</span> {{ $roll->fabric_width ? $roll->fabric_width . ' cm' : 'N/A' }}</div>
                <div><span style="font-weight: 600; color: #6b7280;">Date Received:</span> {{ $roll->date_received?->format('M d, Y') }}</div>
                <div><span style="font-weight: 600; color: #6b7280;">Roll Code:</span> {{ $roll->roll_code }}</div>
            </div>
        </div>
    </div>

    {{-- Measurements & Pricing --}}
    <div style="display: flex; gap: 14px; margin-bottom: 20px;">
        <div style="flex: 1; padding: 14px 18px; border: 1px solid #fde68a; border-radius: 10px;">
            <div style="font-size: 9px; font-weight: 700; color: #d97706; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px;">Measurements</div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; font-size: 12px;">
                <div><span style="font-weight: 600; color: #6b7280;">Claimed:</span> {{ number_format($roll->claimed_meters, 2) }} m</div>
                <div><span style="font-weight: 600; color: #6b7280;">Verified:</span> {{ number_format($roll->verified_meters, 2) }} m</div>
                <div><span style="font-weight: 600; color: #6b7280;">Remaining:</span> 
                    <span style="color: {{ $remainingPct <= 10 ? '#dc2626' : ($remainingPct <= 30 ? '#d97706' : '#16a34a') }}; font-weight: 600;">
                        {{ number_format($roll->remaining_meters, 2) }} m
                    </span>
                </div>
                <div><span style="font-weight: 600; color: #6b7280;">Used:</span> {{ number_format($usedMeters, 2) }} m</div>
            </div>
            {{-- Progress bar --}}
            @if($roll->verified_meters > 0)
                <div style="margin-top: 12px;">
                    <div style="display: flex; justify-content: space-between; font-size: 10px; color: #9ca3af; margin-bottom: 4px;">
                        <span>Remaining: {{ number_format($remainingPct, 1) }}%</span>
                        <span>Used: {{ number_format(100 - $remainingPct, 1) }}%</span>
                    </div>
                    <div style="width: 100%; height: 8px; background: #f3f4f6; border-radius: 4px; overflow: hidden;">
                        <div style="height: 100%; width: {{ $remainingPct }}%; border-radius: 4px; background: {{ $remainingPct <= 10 ? '#dc2626' : ($remainingPct <= 30 ? '#f59e0b' : '#16a34a') }}; transition: width 0.3s;"></div>
                    </div>
                </div>
            @endif
        </div>
        <div style="flex: 1; padding: 14px 18px; border: 1px solid #fde68a; border-radius: 10px;">
            <div style="font-size: 9px; font-weight: 700; color: #d97706; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px;">Pricing</div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; font-size: 12px;">
                <div><span style="font-weight: 600; color: #6b7280;">Buying Price/m:</span> ${{ number_format($roll->buying_price_per_meter, 2) }}</div>
                <div><span style="font-weight: 600; color: #6b7280;">Selling Price/m:</span> ${{ number_format($roll->selling_price_per_meter, 2) }}</div>
                <div><span style="font-weight: 600; color: #6b7280;">Total Cost:</span> ${{ number_format($roll->buying_price_per_meter * $roll->verified_meters, 2) }}</div>
                <div><span style="font-weight: 600; color: #6b7280;">Potential Value:</span> ${{ number_format($roll->selling_price_per_meter * $roll->remaining_meters, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Notes --}}
    @if($roll->notes)
        <div style="padding: 14px 18px; border: 1px solid #e5e7eb; border-radius: 10px; margin-bottom: 20px;">
            <div style="font-size: 9px; font-weight: 700; color: #d97706; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px;">Notes</div>
            <div style="font-size: 12px; line-height: 1.6; white-space: pre-wrap;">{{ $roll->notes }}</div>
        </div>
    @endif

    {{-- Usage History --}}
    <div style="padding: 14px 18px; border: 1px solid #fde68a; border-radius: 10px; margin-bottom: 20px;">
        <div style="font-size: 9px; font-weight: 700; color: #d97706; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px;">Usage History</div>
        @php $usages = $roll->usages()->latest()->limit(10)->get(); @endphp
        @if($usages->count())
            <div style="font-size: 11px;">
                <div style="display: grid; grid-template-columns: 80px 1fr 60px 80px; gap: 4px 12px; font-weight: 600; color: #6b7280; padding-bottom: 6px; border-bottom: 1px solid #f3f4f6; margin-bottom: 6px;">
                    <span>Date</span>
                    <span>Customer</span>
                    <span>Used</span>
                    <span>Remaining</span>
                </div>
                @foreach($usages as $usage)
                    <div style="display: grid; grid-template-columns: 80px 1fr 60px 80px; gap: 4px 12px; padding: 4px 0; border-bottom: 1px solid #f9fafb;">
                        <span>{{ $usage->date?->format('M d') }}</span>
                        <span>{{ $usage->customer?->name ?? '—' }}</span>
                        <span style="color: #dc2626; font-weight: 600;">-{{ number_format($usage->meters_used, 1) }}m</span>
                        <span style="color: #6b7280;">{{ number_format($usage->remaining_after, 1) }}m</span>
                    </div>
                @endforeach
            </div>
        @else
            <div style="font-size: 12px; color: #9ca3af; font-style: italic;">No usage recorded yet.</div>
        @endif
    </div>

    {{-- Footer --}}
    <div style="text-align: center; padding-top: 14px; border-top: 1px solid #f3f4f6;">
        <div style="display: inline-block; padding: 6px 20px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 20px; font-size: 10px; color: #d97706; font-weight: 600;">
            Registered {{ $roll->created_at?->format('M d, Y \a\t h:i A') }}
            @if($roll->deleted_at)
                &middot; Deleted {{ $roll->deleted_at->format('M d, Y') }}
            @endif
        </div>
    </div>
</div>
