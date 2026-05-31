@php
    $roll = $roll ?? $this->record;
    $company = $company ?? $roll->company;
    $remainingPct = $roll->remaining_percentage;
    $usedMeters = $roll->used_meters;
@endphp

<div style="padding: 4px 0; font-family: system-ui, -apple-system, sans-serif;">
    <div style="background: #fff; border-radius: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.06); overflow: hidden;">
        <div style="background: linear-gradient(135deg, #b45309, #f59e0b); padding: 28px 28px 20px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div style="display: flex; align-items: center; gap: 14px;">
                    <div style="width: 46px; height: 46px; border-radius: 12px; background: rgba(255,255,255,0.15); backdrop-filter: blur(8px); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 700; font-size: 20px; border: 1px solid rgba(255,255,255,0.2);">
                        {{ strtoupper(substr($roll->fabric_name ?? 'F', 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-size: 16px; font-weight: 700; color: #fff;">{{ $roll->roll_code }}</div>
                        <div style="font-size: 11px; color: rgba(255,255,255,0.65); margin-top: 1px;">{{ $roll->fabric_name }} &bull; {{ $roll->color }}</div>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 24px; font-weight: 800; color: #fff; letter-spacing: -0.02em;">FABRIC ROLL</div>
                    <div style="display: inline-flex; align-items: center; gap: 6px; margin-top: 10px; padding: 3px 14px; border-radius: 100px; font-size: 10px; font-weight: 700; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.2);">
                        <span style="width: 5px; height: 5px; border-radius: 50%; display: inline-block; background: {{ match($roll->status) { 'in_stock' => '#34d399', 'partially_used' => '#fcd34d', 'depleted' => '#9ca3af', 'damaged' => '#f87171', default => '#9ca3af' } }};"></span>
                        <span style="color: #fff;">{{ str_replace('_', ' ', ucfirst($roll->status)) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div style="padding: 24px 28px; display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <div style="padding: 16px 20px; background: #fffbeb; border-radius: 10px; border: 1px solid #fde68a;">
                <div style="font-size: 9px; font-weight: 700; color: #d97706; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px;">Fabric Information</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; font-size: 12px;">
                    <div><span style="color: #6b7280;">Fabric:</span> <span style="color: #374151; font-weight: 500;">{{ $roll->fabric_name }}</span></div>
                    <div><span style="color: #6b7280;">Color:</span> <span style="color: #374151; font-weight: 500;">{{ $roll->color }}</span></div>
                    <div><span style="color: #6b7280;">Supplier:</span> <span style="color: #374151; font-weight: 500;">{{ $roll->supplier }}</span></div>
                    <div><span style="color: #6b7280;">Width:</span> <span style="color: #374151; font-weight: 500;">{{ $roll->fabric_width ? $roll->fabric_width . ' cm' : 'N/A' }}</span></div>
                    <div><span style="color: #6b7280;">Received:</span> <span style="color: #374151; font-weight: 500;">{{ $roll->date_received?->format('M d, Y') }}</span></div>
                    <div><span style="color: #6b7280;">Code:</span> <span style="color: #374151; font-weight: 500; font-family: monospace;">{{ $roll->roll_code }}</span></div>
                </div>
            </div>

            <div style="padding: 16px 20px; background: #fffbeb; border-radius: 10px; border: 1px solid #fde68a;">
                <div style="font-size: 9px; font-weight: 700; color: #d97706; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px;">Measurements</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; font-size: 12px;">
                    <div><span style="color: #6b7280;">Claimed:</span> <span style="color: #374151; font-weight: 500;">{{ number_format($roll->claimed_meters, 2) }} m</span></div>
                    <div><span style="color: #6b7280;">Verified:</span> <span style="color: #374151; font-weight: 500;">{{ number_format($roll->verified_meters, 2) }} m</span></div>
                    <div><span style="color: #6b7280;">Remaining:</span> 
                        <span style="font-weight: 600; color: {{ $remainingPct <= 10 ? '#dc2626' : ($remainingPct <= 30 ? '#d97706' : '#16a34a') }};">
                            {{ number_format($roll->remaining_meters, 2) }} m
                        </span>
                    </div>
                    <div><span style="color: #6b7280;">Used:</span> <span style="color: #374151; font-weight: 500;">{{ number_format($usedMeters, 2) }} m</span></div>
                </div>
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

            <div style="padding: 16px 20px; background: #fffbeb; border-radius: 10px; border: 1px solid #fde68a;">
                <div style="font-size: 9px; font-weight: 700; color: #d97706; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px;">Pricing</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px 16px; font-size: 12px;">
                    <div><span style="color: #6b7280;">Buying/m:</span> <span style="color: #374151; font-weight: 500;">UGX {{ number_format($roll->buying_price_per_meter, 2) }}</span></div>
                    <div><span style="color: #6b7280;">Selling/m:</span> <span style="color: #374151; font-weight: 500;">UGX {{ number_format($roll->selling_price_per_meter, 2) }}</span></div>
                    <div><span style="color: #6b7280;">Total Cost:</span> <span style="color: #374151; font-weight: 500;">UGX {{ number_format($roll->buying_price_per_meter * $roll->verified_meters, 2) }}</span></div>
                    <div><span style="color: #6b7280;">Potential Value:</span> <span style="color: #374151; font-weight: 500;">UGX {{ number_format($roll->selling_price_per_meter * $roll->remaining_meters, 2) }}</span></div>
                </div>
            </div>

            <div style="padding: 16px 20px; background: #fffbeb; border-radius: 10px; border: 1px solid #fde68a;">
                <div style="font-size: 9px; font-weight: 700; color: #d97706; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 12px;">Usage History</div>
                @php $usages = $roll->usages()->latest()->limit(10)->get(); @endphp
                @if($usages->count())
                <div style="font-size: 11px;">
                    <div style="display: grid; grid-template-columns: 70px 1fr 55px 70px; gap: 4px 10px; font-weight: 600; color: #6b7280; padding-bottom: 6px; border-bottom: 1px solid #fde68a; margin-bottom: 6px;">
                        <span>Date</span><span>Customer</span><span>Used</span><span>Left</span>
                    </div>
                    @foreach($usages as $usage)
                    <div style="display: grid; grid-template-columns: 70px 1fr 55px 70px; gap: 4px 10px; padding: 4px 0; border-bottom: 1px solid #fef3c7;">
                        <span style="color: #6b7280;">{{ $usage->date?->format('M d') }}</span>
                        <span style="color: #374151;">{{ $usage->customer?->name ?? '—' }}</span>
                        <span style="color: #dc2626; font-weight: 600;">-{{ number_format($usage->meters_used, 1) }}m</span>
                        <span style="color: #6b7280;">{{ number_format($usage->remaining_after, 1) }}m</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div style="font-size: 12px; color: #9ca3af; font-style: italic;">No usage recorded yet.</div>
                @endif
            </div>
        </div>

        @if($roll->notes)
        <div style="padding: 0 28px 24px;">
            <div style="padding: 14px 18px; background: #f9fafb; border-radius: 10px; border-left: 3px solid #d1d5db;">
                <div style="font-size: 9px; font-weight: 700; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 6px;">Notes</div>
                <div style="font-size: 12px; color: #6b7280; line-height: 1.6; white-space: pre-wrap;">{{ $roll->notes }}</div>
            </div>
        </div>
        @endif

        <div style="text-align: center; padding: 16px 28px; border-top: 1px solid #f3f4f6;">
            <div style="display: inline-block; padding: 6px 20px; background: #fffbeb; border: 1px solid #fde68a; border-radius: 20px; font-size: 10px; color: #d97706; font-weight: 600;">
                Registered {{ $roll->created_at?->format('M d, Y \a\t h:i A') }}
                @if($roll->deleted_at)
                    &bull; Deleted {{ $roll->deleted_at->format('M d, Y') }}
                @endif
            </div>
        </div>
    </div>
</div>
