<x-filament-panels::page>
    <div class="space-y-6">
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:1rem;">
            <x-filament::section>
                <div style="font-size:0.7rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;">Total Income (12m)</div>
                <div style="margin-top:0.35rem;font-size:1.6rem;font-weight:700;color:#16a34a;">KES {{ number_format($totalIncome) }}</div>
            </x-filament::section>
            <x-filament::section>
                <div style="font-size:0.7rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;">Total Expenses (12m)</div>
                <div style="margin-top:0.35rem;font-size:1.6rem;font-weight:700;color:#dc2626;">KES {{ number_format($totalExpense) }}</div>
            </x-filament::section>
            <x-filament::section>
                <div style="font-size:0.7rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;">Net Position</div>
                <div style="margin-top:0.35rem;font-size:1.6rem;font-weight:700;color:{{ $net >= 0 ? '#16a34a' : '#dc2626' }};">KES {{ number_format($net) }}</div>
            </x-filament::section>
            <x-filament::section>
                <div style="font-size:0.7rem;font-weight:700;letter-spacing:0.08em;text-transform:uppercase;color:#64748b;">Outstanding Rent</div>
                <div style="margin-top:0.35rem;font-size:1.6rem;font-weight:700;color:#d97706;">KES {{ number_format($outstanding) }}</div>
            </x-filament::section>
        </div>

        <x-filament::section heading="Income vs expenses (last 12 months)">
            <div style="display:flex;align-items:flex-end;gap:0.5rem;height:240px;padding:0.5rem 0;">
                @foreach ($months as $m)
                    <div style="flex:1;display:flex;flex-direction:column;align-items:center;justify-content:flex-end;height:100%;gap:2px;">
                        <div style="display:flex;align-items:flex-end;gap:2px;height:100%;width:100%;justify-content:center;">
                            <div title="Income: KES {{ number_format($m['income']) }}"
                                 style="width:42%;background:linear-gradient(180deg,#34d399,#10b981);border-radius:4px 4px 0 0;height:{{ max(2, round(($m['income'] / $maxValue) * 180)) }}px;"></div>
                            <div title="Expenses: KES {{ number_format($m['expense']) }}"
                                 style="width:42%;background:linear-gradient(180deg,#f87171,#ef4444);border-radius:4px 4px 0 0;height:{{ max(2, round(($m['expense'] / $maxValue) * 180)) }}px;"></div>
                        </div>
                        <div style="font-size:0.62rem;color:#94a3b8;white-space:nowrap;">{{ $m['label'] }}</div>
                    </div>
                @endforeach
            </div>
            <div style="display:flex;gap:1.25rem;margin-top:0.75rem;font-size:0.78rem;color:#475569;">
                <span><span style="display:inline-block;width:10px;height:10px;background:#10b981;border-radius:2px;"></span> Income</span>
                <span><span style="display:inline-block;width:10px;height:10px;background:#ef4444;border-radius:2px;"></span> Expenses</span>
            </div>
        </x-filament::section>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:1.5rem;">
            <x-filament::section heading="Top expense categories">
                @if ($byCategory->isEmpty())
                    <p style="color:#94a3b8;font-size:0.875rem;">No expenses recorded yet.</p>
                @else
                    <table style="width:100%;border-collapse:collapse;font-size:0.85rem;">
                        @foreach ($byCategory as $cat)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:0.5rem 0;color:#334155;text-transform:capitalize;">{{ $cat->category ?: 'Uncategorised' }}</td>
                                <td style="padding:0.5rem 0;text-align:right;font-weight:600;color:#0f172a;">KES {{ number_format((float) $cat->total) }}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif
            </x-filament::section>

            <x-filament::section heading="Monthly breakdown">
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:0.82rem;">
                        <thead>
                            <tr style="color:#64748b;text-transform:uppercase;font-size:0.66rem;letter-spacing:0.06em;">
                                <th style="text-align:left;padding:0.4rem 0;">Month</th>
                                <th style="text-align:right;padding:0.4rem 0;">Income</th>
                                <th style="text-align:right;padding:0.4rem 0;">Expenses</th>
                                <th style="text-align:right;padding:0.4rem 0;">Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($months->reverse() as $m)
                                <tr style="border-top:1px solid #f1f5f9;">
                                    <td style="padding:0.4rem 0;color:#334155;">{{ $m['label'] }}</td>
                                    <td style="padding:0.4rem 0;text-align:right;color:#16a34a;">{{ number_format($m['income']) }}</td>
                                    <td style="padding:0.4rem 0;text-align:right;color:#dc2626;">{{ number_format($m['expense']) }}</td>
                                    <td style="padding:0.4rem 0;text-align:right;font-weight:600;color:{{ $m['net'] >= 0 ? '#16a34a' : '#dc2626' }};">{{ number_format($m['net']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
