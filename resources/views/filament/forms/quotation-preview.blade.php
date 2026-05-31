<div
    x-data="{
        company_name: '',
        company_address: '',
        customer_name: '',
        customer_address: '',
        number: '',
        date: '',
        status: '',
        items: [],
        subtotal: 0,
        taxRate: 0,
        taxAmount: 0,
        discount: 0,
        total: 0,
        notes: '',
        livewireId: null,
        isDark: false,

        init() {
            this.isDark = document.documentElement.classList.contains('dark');
            const observer = new MutationObserver(() => {
                this.isDark = document.documentElement.classList.contains('dark');
            });
            observer.observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

            this.livewireId = $wire?.__instance?.id;
            this.syncFromLivewire();
            if (typeof Livewire !== 'undefined' && this.livewireId) {
                Livewire.hook('commit', ({ component, respond, succeed }) => {
                    succeed(() => {
                        if (component.id === this.livewireId) this.syncFromLivewire();
                    });
                });
            }
        },

        async syncFromLivewire() {
            if (!$wire) return;
            try {
                const data = await $wire.get('data');
                if (data) {
                    this.company_name = data.company_name || '';
                    this.company_address = data.company_address || '';
                    this.customer_name = data.customer_name || '';
                    this.customer_address = data.customer_address || '';
                    this.number = data.number || '';
                    this.date = data.date || '';
                    this.status = data.status || '';
                    this.items = data.items || [];
                    this.subtotal = parseFloat(data.subtotal) || 0;
                    this.taxRate = parseFloat(data.tax_rate) || 0;
                    this.taxAmount = parseFloat(data.tax_amount) || 0;
                    this.discount = parseFloat(data.discount) || 0;
                    this.total = parseFloat(data.total) || 0;
                    this.notes = data.notes || '';
                }
            } catch (e) {}
        },

        formatMoney(value) {
            const num = parseFloat(value) || 0;
            return 'UGX ' + num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },

        formatDate(value) {
            if (!value) return '';
            const d = new Date(value + 'T00:00:00');
            if (isNaN(d.getTime())) return value;
            return d.toLocaleDateString('en-US', {
                year: 'numeric', month: 'long', day: 'numeric'
            });
        },

        qrData() {
            const lines = [];
            if (this.number) lines.push('Quotation: ' + this.number);
            lines.push('Amount: ' + this.formatMoney(this.total));
            return encodeURIComponent(lines.join('\n'));
        },

        bg() { return this.isDark ? '#1f2937' : '#ffffff'; },
        bgMuted() { return this.isDark ? '#374151' : '#f9fafb'; },
        textPrimary() { return this.isDark ? '#f3f4f6' : '#111827'; },
        textSecondary() { return this.isDark ? '#9ca3af' : '#6b7280'; },
        textTertiary() { return this.isDark ? '#6b7280' : '#9ca3af'; },
        borderColor() { return this.isDark ? '#374151' : '#f3f4f6'; },
        borderLight() { return this.isDark ? '#4b5563' : '#e5e7eb'; },
        noteBg() { return this.isDark ? '#374151' : '#f9fafb'; },
    }"
    x-init="init"
>
    <div x-show="company_name"
        :style="'background:'+bg()+';border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 1px 2px rgba(0,0,0,0.04);overflow:hidden;font-family:system-ui,-apple-system,sans-serif;border:1px solid '+borderLight()"
    >
        <div style="background:linear-gradient(135deg,#7c3aed,#a855f7);padding:32px 32px 24px;">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;">
                <div style="display:flex;align-items:center;gap:16px;">
                    <div style="width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,0.15);backdrop-filter:blur(8px);display:flex;align-items:center;justify-content:center;color:#fff;font-size:20px;font-weight:700;border:1px solid rgba(255,255,255,0.2);">
                        <span x-text="(company_name || 'A').charAt(0).toUpperCase()"></span>
                    </div>
                    <div>
                        <div style="font-size:16px;font-weight:700;color:#fff;" x-text="company_name || 'Company Name'"></div>
                        <div style="font-size:12px;color:rgba(255,255,255,0.65);margin-top:2px;" x-text="company_address || 'Address'"></div>
                    </div>
                </div>
                <div style="text-align:right;">
                    <div style="font-size:28px;font-weight:800;color:#fff;letter-spacing:-0.03em;line-height:1;">QUOTATION</div>
                    <div style="font-size:13px;color:rgba(255,255,255,0.7);margin-top:4px;font-family:'SF Mono',monospace;" x-text="number || '···'"></div>
                </div>
            </div>
        </div>

        <div style="padding:28px 32px 8px;display:flex;justify-content:space-between;align-items:flex-start;">
            <div>
                <div :style="'font-size:10px;font-weight:600;color:'+textTertiary()+';text-transform:uppercase;letter-spacing:0.08em;margin-bottom:6px;'">Prepared For</div>
                <div :style="'font-size:14px;font-weight:600;color:'+textPrimary()" x-text="customer_name || company_name"></div>
                <div :style="'font-size:12px;color:'+textSecondary()+';margin-top:2px;max-width:240px;" x-text="customer_address || company_address"></div>
            </div>
            <div style="text-align:right;">
                <div>
                    <div :style="'font-size:10px;font-weight:600;color:'+textTertiary()+';text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px;'">Date</div>
                    <div :style="'font-size:13px;color:'+textPrimary()+';font-weight:500;" x-text="date ? formatDate(date) : '\u2014'"></div>
                </div>
                <div x-show="status" style="margin-top:12px;display:inline-flex;align-items:center;gap:6px;padding:4px 14px;border-radius:100px;font-size:12px;font-weight:600;"
                    :style="{
                        background: this.isDark ? 'rgba(255,255,255,0.1)' : (
                            status === 'draft' ? '#f3f4f6' : status === 'sent' ? '#fffbeb' : status === 'accepted' ? '#f0fdf4' : status === 'rejected' ? '#fef2f2' : '#f5f5f4'
                        ),
                        color: this.isDark ? (
                            status === 'draft' ? '#9ca3af' : status === 'sent' ? '#fbbf24' : status === 'accepted' ? '#6ee7b7' : status === 'rejected' ? '#fca5a5' : '#a8a29e'
                        ) : (
                            status === 'draft' ? '#6b7280' : status === 'sent' ? '#d97706' : status === 'accepted' ? '#16a34a' : status === 'rejected' ? '#dc2626' : '#78716c'
                        )
                    }">
                    <span style="width:6px;height:6px;border-radius:50%;display:inline-block;"
                        :style="{
                            background: this.isDark ? (
                                status === 'draft' ? '#9ca3af' : status === 'sent' ? '#fbbf24' : status === 'accepted' ? '#6ee7b7' : status === 'rejected' ? '#fca5a5' : '#a8a29e'
                            ) : (
                                status === 'draft' ? '#9ca3af' : status === 'sent' ? '#f59e0b' : status === 'accepted' ? '#22c55e' : status === 'rejected' ? '#ef4444' : '#a8a29e'
                            )
                        }"
                    ></span>
                    <span x-text="status ? status.charAt(0).toUpperCase() + status.slice(1) : ''"></span>
                </div>
            </div>
        </div>

        <div style="padding:16px 32px;">
            <table style="width:100%;border-collapse:separate;border-spacing:0;font-size:13px;">
                <thead>
                    <tr>
                        <th :style="'text-align:left;padding:10px 12px 10px 0;font-weight:600;color:'+textTertiary()+';font-size:10px;text-transform:uppercase;letter-spacing:0.08em;border-bottom:2px solid '+borderColor()">Description</th>
                        <th :style="'text-align:right;padding:10px 8px;font-weight:600;color:'+textTertiary()+';font-size:10px;text-transform:uppercase;letter-spacing:0.08em;border-bottom:2px solid '+borderColor()+';width:60px;'">Qty</th>
                        <th :style="'text-align:right;padding:10px 8px;font-weight:600;color:'+textTertiary()+';font-size:10px;text-transform:uppercase;letter-spacing:0.08em;border-bottom:2px solid '+borderColor()+';width:90px;'">Price</th>
                        <th :style="'text-align:right;padding:10px 0 10px 8px;font-weight:600;color:'+textTertiary()+';font-size:10px;text-transform:uppercase;letter-spacing:0.08em;border-bottom:2px solid '+borderColor()+';width:100px;'">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="items.length === 0">
                        <tr>
                            <td colspan="4" style="padding:48px 0;text-align:center;font-style:italic;font-size:12px;"
                                :style="'padding:48px 0;text-align:center;color:'+textTertiary()+';font-style:italic;font-size:12px;'">
                                <svg style="margin:0 auto 12px;width:40px;height:40px;display:block;" :style="'margin:0 auto 12px;width:40px;height:40px;color:'+textTertiary()+';display:block;'" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                Add line items on the left
                            </td>
                        </tr>
                    </template>
                    <template x-for="(item, index) in items" :key="index">
                        <tr>
                            <td :style="'padding:12px 12px 12px 0;color:'+textPrimary()+';border-bottom:1px solid '+borderColor()" x-text="item.description || '\u2014'"></td>
                            <td :style="'padding:12px 8px;text-align:right;color:'+textSecondary()+';border-bottom:1px solid '+borderColor()" x-text="item.quantity || '0'"></td>
                            <td :style="'padding:12px 8px;text-align:right;color:'+textSecondary()+';border-bottom:1px solid '+borderColor()" x-text="formatMoney(item.unit_price)"></td>
                            <td :style="'padding:12px 0 12px 8px;text-align:right;color:'+textPrimary()+';font-weight:500;border-bottom:1px solid '+borderColor()" x-text="formatMoney(item.amount)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div style="padding:4px 32px 24px;display:flex;gap:24px;">
            <div x-show="number" style="flex-shrink:0;width:100px;height:100px;display:flex;align-items:center;justify-content:center;">
                <img :src="'https://chart.googleapis.com/chart?chs=100x100&cht=qr&chl='+qrData()+'&choe=UTF-8'" alt="QR" style="width:100px;height:100px;border-radius:8px;">
            </div>
            <div style="flex:1;">
                <div style="width:100%;">
                    <div :style="'display:flex;justify-content:space-between;padding:4px 0;font-size:13px;color:'+textSecondary()">
                        <span>Subtotal</span>
                        <span x-text="formatMoney(subtotal)"></span>
                    </div>
                    <div :style="'display:flex;justify-content:space-between;padding:4px 0;font-size:13px;color:'+textSecondary()">
                        <span>Tax (<span x-text="Number(taxRate).toFixed(1)"></span>%)</span>
                        <span x-text="formatMoney(taxAmount)"></span>
                    </div>
                    <div :style="'display:flex;justify-content:space-between;padding:4px 0;font-size:13px;color:'+textSecondary()">
                        <span>Discount</span>
                        <span x-text="formatMoney(discount)"></span>
                    </div>
                    <div :style="'display:flex;justify-content:space-between;padding:12px 0 0;margin-top:8px;border-top:2px solid #7c3aed;font-size:16px;font-weight:700;color:#7c3aed;'">
                        <span>Total</span>
                        <span x-text="formatMoney(total)"></span>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="notes" style="padding:0 32px 20px;">
            <div :style="'background:'+noteBg()+';border-radius:10px;padding:16px 20px;'">
                <div :style="'font-size:10px;font-weight:600;color:'+textTertiary()+';text-transform:uppercase;letter-spacing:0.08em;margin-bottom:6px;'">Notes</div>
                <div :style="'font-size:13px;color:'+textSecondary()+';white-space:pre-wrap;line-height:1.6;" x-text="notes"></div>
            </div>
        </div>

        <div :style="'padding:16px 32px;border-top:1px solid '+borderColor()+';text-align:center;'">
            <div :style="'font-size:11px;color:'+textTertiary()+';font-weight:500;'">Valid for 30 days &middot; Thank you for your business</div>
        </div>
    </div>

    <div x-show="!company_name"
        :style="'display:flex;align-items:center;justify-content:center;min-height:420px;background:'+bg()+';border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,0.06),0 1px 2px rgba(0,0,0,0.04);border:1px solid '+borderLight()"
    >
        <div style="text-align:center;padding:40px;">
            <div style="width:72px;height:72px;margin:0 auto 20px;border-radius:16px;background:linear-gradient(135deg,#f5f3ff,#ede9fe);display:flex;align-items:center;justify-content:center;">
                <svg style="width:36px;height:36px;color:#a855f7;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div :style="'font-size:16px;color:'+textSecondary()+';font-weight:600;'">Quotation Preview</div>
            <div :style="'font-size:13px;color:'+textTertiary()+';margin-top:4px;'">Fill in the form to see a live preview</div>
        </div>
    </div>
</div>
