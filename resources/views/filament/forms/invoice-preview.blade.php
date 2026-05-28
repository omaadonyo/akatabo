<div
    x-data="{
        company_name: '',
        company_address: '',
        number: '',
        date: '',
        due_date: '',
        status: '',
        items: [],
        subtotal: 0,
        taxRate: 0,
        taxAmount: 0,
        discount: 0,
        total: 0,
        notes: '',
        livewireId: null,

        init() {
            this.livewireId = $wire?.__instance?.id;
            this.syncFromLivewire();

            if (typeof Livewire !== 'undefined' && this.livewireId) {
                Livewire.hook('commit', ({ component, respond, succeed }) => {
                    succeed(() => {
                        if (component.id === this.livewireId) {
                            this.syncFromLivewire();
                        }
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
                    this.number = data.number || '';
                    this.date = data.date || '';
                    this.due_date = data.due_date || '';
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
            return '$' + num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        },

        formatDate(value) {
            if (!value) return '';
            const d = new Date(value + 'T00:00:00');
            if (isNaN(d.getTime())) return value;
            return d.toLocaleDateString('en-US', {
                year: 'numeric', month: 'long', day: 'numeric'
            });
        }
    }"
    x-init="init"
>


    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: hidden;">
        <div style="background: #f9fafb; padding: 12px 24px; border-bottom: 1px solid #e5e7eb;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div style="display: flex; align-items: center; gap: 6px;">
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444; display: inline-block;"></span>
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #eab308; display: inline-block;"></span>
                    <span style="width: 10px; height: 10px; border-radius: 50%; background: #22c55e; display: inline-block;"></span>
                </div>
                <span style="font-size: 11px; color: #9ca3af; font-weight: 500;">Preview</span>
            </div>
        </div>

        <div style="padding: 24px;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <div style="width: 40px; height: 40px; border-radius: 8px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: bold; font-size: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.15);">
                        A
                    </div>
                    <div>
                        <div style="font-size: 14px; font-weight: 700; color: #111827;">Your Company</div>
                        <div style="font-size: 11px; color: #9ca3af;">123 Business Ave, Suite 100</div>
                    </div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 22px; font-weight: 800; color: #111827; letter-spacing: -0.025em;">INVOICE</div>
                    <div style="font-size: 13px; color: #6b7280; margin-top: 2px; font-family: monospace;" x-text="number || '...'"></div>
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: flex-start; padding-top: 16px; border-top: 1px solid #f3f4f6;">
                <div>
                    <div style="font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 4px;">Bill To</div>
                    <div style="font-size: 13px; font-weight: 600; color: #111827;" x-text="company_name"></div>
                    <div style="font-size: 11px; color: #6b7280;" x-text="company_address"></div>
                </div>
                <div style="text-align: right;">
                    <div style="font-size: 11px; color: #6b7280;">
                        <span style="font-weight: 500; color: #374151;">Date:</span>
                        <span x-text="date ? formatDate(date) : '&mdash;'"></span>
                    </div>
                    <div style="font-size: 11px; color: #6b7280; margin-top: 4px;" x-show="due_date">
                        <span style="font-weight: 500; color: #374151;">Due:</span>
                        <span x-text="formatDate(due_date)"></span>
                    </div>
                    <span x-show="status"
                        style="display: inline-block; margin-top: 8px; padding: 2px 12px; font-size: 11px; font-weight: 600; border-radius: 999px;"
                        :style="{
                            background: status === 'draft' ? '#f3f4f6' : status === 'sent' ? '#dbeafe' : status === 'paid' ? '#d1fae5' : status === 'overdue' ? '#fee2e2' : '#f3f4f6',
                            color: status === 'draft' ? '#6b7280' : status === 'sent' ? '#2563eb' : status === 'paid' ? '#16a34a' : status === 'overdue' ? '#dc2626' : '#6b7280',
                        }"
                        x-text="status ? status.charAt(0).toUpperCase() + status.slice(1) : ''">
                    </span>
                </div>
            </div>

            <div style="margin-top: 16px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <th style="text-align: left; padding: 8px 8px 8px 0; font-weight: 600; color: #9ca3af; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">Description</th>
                            <th style="text-align: right; padding: 8px 8px; font-weight: 600; color: #9ca3af; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; width: 60px;">Qty</th>
                            <th style="text-align: right; padding: 8px 8px; font-weight: 600; color: #9ca3af; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; width: 80px;">Price</th>
                            <th style="text-align: right; padding: 8px 0 8px 8px; font-weight: 600; color: #9ca3af; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; width: 100px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-if="items.length === 0">
                            <tr>
                                <td colspan="4" style="padding: 40px 0; text-align: center; color: #d1d5db; font-style: italic; font-size: 12px;">
                                    Add items on the left
                                </td>
                            </tr>
                        </template>
                        <template x-for="(item, index) in items" :key="index">
                            <tr style="border-bottom: 1px solid #f9fafb;">
                                <td style="padding: 8px 8px 8px 0; color: #374151;" x-text="item.description || '&mdash;'"></td>
                                <td style="padding: 8px; text-align: right; color: #6b7280;" x-text="item.quantity || '0'"></td>
                                <td style="padding: 8px; text-align: right; color: #6b7280;" x-text="formatMoney(item.unit_price)"></td>
                                <td style="padding: 8px 0 8px 8px; text-align: right; color: #111827; font-weight: 500;" x-text="formatMoney(item.amount)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 16px;">
                <div style="width: 260px;">
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #6b7280; padding: 4px 0;">
                        <span>Subtotal</span>
                        <span x-text="formatMoney(subtotal)"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #6b7280; padding: 4px 0;">
                        <span>Tax (<span x-text="taxRate.toFixed(1)"></span>%)</span>
                        <span x-text="formatMoney(taxAmount)"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 13px; color: #6b7280; padding: 4px 0;">
                        <span>Discount</span>
                        <span x-text="formatMoney(discount)"></span>
                    </div>
                    <div style="display: flex; justify-content: space-between; font-size: 15px; font-weight: 700; color: #111827; padding-top: 10px; margin-top: 8px; border-top: 2px solid #e5e7eb;">
                        <span>Total</span>
                        <span x-text="formatMoney(total)"></span>
                    </div>
                </div>
            </div>

            <div x-show="notes" style="padding-top: 16px; margin-top: 16px; border-top: 1px solid #f3f4f6;">
                <div style="font-size: 11px; font-weight: 600; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px;">Notes</div>
                <div style="font-size: 13px; color: #6b7280; white-space: pre-wrap;" x-text="notes"></div>
            </div>

            <div style="padding-top: 16px; margin-top: 16px; border-top: 1px solid #f3f4f6; text-align: center;">
                <div style="font-size: 10px; color: #d1d5db;">Thank you for your business</div>
            </div>
        </div>
    </div>

    <div x-show="!company_name" style="display: flex; align-items: center; justify-content: center; min-height: 400px;">
        <div style="text-align: center;">
            <svg style="margin: 0 auto; width: 64px; height: 64px; color: #e5e7eb;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <div style="margin-top: 16px; font-size: 14px; color: #d1d5db; font-weight: 500;">Invoice Preview</div>
            <div style="font-size: 12px; color: #e5e7eb; margin-top: 4px;">Select a company to get started</div>
        </div>
    </div>
</div>
