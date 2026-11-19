<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section
            heading="Access control overview"
            description="Which roles can access each administrative module. Tenants are restricted from the panel entirely."
        >
            <div style="overflow-x:auto;">
                <table style="width:100%;border-collapse:separate;border-spacing:0;font-size:0.875rem;">
                    <thead>
                        <tr style="text-align:left;color:#64748b;text-transform:uppercase;font-size:0.68rem;letter-spacing:0.08em;">
                            <th style="padding:0.6rem 0.75rem;">Role</th>
                            <th style="padding:0.6rem 0.75rem;">Users</th>
                            @foreach ($modules as $label)
                                <th style="padding:0.6rem 0.75rem;text-align:center;">{{ $label }}</th>
                            @endforeach
                            <th style="padding:0.6rem 0.75rem;text-align:center;">Panel</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($rows as $row)
                            <tr style="border-top:1px solid #e2e8f0;">
                                <td style="padding:0.75rem;">
                                    <div style="font-weight:600;color:#0f172a;">{{ $row['label'] }}</div>
                                    <div style="color:#94a3b8;font-size:0.75rem;">{{ $row['desc'] }}</div>
                                </td>
                                <td style="padding:0.75rem;color:#475569;">{{ $row['users'] }}</td>
                                @foreach ($modules as $key => $label)
                                    <td style="padding:0.75rem;text-align:center;">
                                        @if ($row['access'][$key])
                                            <span style="color:#16a34a;font-size:1.05rem;font-weight:700;">&#10003;</span>
                                        @else
                                            <span style="color:#cbd5e1;font-size:1.05rem;">&#8212;</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td style="padding:0.75rem;text-align:center;">
                                    <x-filament::badge :color="$row['panel'] ? 'success' : 'gray'">
                                        {{ $row['panel'] ? 'Allowed' : 'Blocked' }}
                                    </x-filament::badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <x-filament::section heading="Notes">
            <ul style="list-style:disc;padding-left:1.25rem;color:#475569;font-size:0.875rem;line-height:1.9;">
                <li>Only Administrators and Developers can manage Users, Roles and System Settings.</li>
                <li>Accountants can manage payments, M-Pesa transactions, expenses and financial reports.</li>
                <li>Caretakers are limited to maintenance, move-out requests and property visibility.</li>
                <li>Tenants cannot sign in to the administration panel.</li>
            </ul>
        </x-filament::section>
    </div>
</x-filament-panels::page>
