<x-filament-panels::page>
    <div class="space-y-6">

        <!-- Quick actions -->
        <div class="flex flex-wrap gap-3">
            <a
                href="{{ \App\Filament\Resources\Tenants\Pages\EditTenant::getUrl(['record' => $record]) }}"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-amber-600/20 transition hover:bg-amber-700 hover:shadow-amber-600/30"
            >
                <x-filament::icon alias="heroicon-o-pencil" width="1.125rem" height="1.125rem" />
                Edit tenant
            </a>

            @if ($unit)
                <a
                    href="{{ \App\Filament\Resources\Units\UnitResource::getUrl(['record' => $unit]) }}"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                >
                    <x-filament::icon alias="heroicon-o-home" width="1.125rem" height="1.125rem" />
                    View unit
                </a>
            @endif

            <a
                href="{{ \App\Filament\Resources\Payments\PaymentResource::getUrl('create') }}?tenant_id={{ $record->id }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
            >
                <x-filament::icon alias="heroicon-o-currency-dollar" width="1.125rem" height="1.125rem" />
                Record payment
            </a>
        </div>

        <!-- Tab navigation -->
        <div x-data="{ tab: 'personal' }" class="border-b border-gray-200 bg-white">
            <div class="flex items-center justify-between">
                <div class="flex space-x-1 px-4 overflow-x-auto pb-2 no-scrollbar">
                    @foreach ([
                        'personal' => 'Personal Information',
                        'lease' => 'Lease',
                        'payments' => 'Payments',
                        'balance' => 'Outstanding Balance',
                        'maintenance' => 'Maintenance',
                        'support' => 'Support',
                    ] as $key => $label)
                        <button
                            x-on:click="tab = '{{ $key }}'"
                            :class="[
                                'px-4 py-3.5 text-sm font-medium transition-whitespace border-b-2',
                                tab === '{{ $key }}'
                                    ? 'border-amber-600 text-amber-600'
                                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:bg-gray-50',
                            ]"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <!-- Tab panels -->
            <div class="p-4">

                <!-- Personal Information -->
                <div x-show="tab === 'personal'" x-cloak class="space-y-6">
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <x-filament::section>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-widest">Full Name</div>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $record->name }}</p>
                        </x-filament::section>

                        <x-filament::section>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-widest">Phone</div>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $record->phone ?? '—' }}</p>
                        </x-filament::section>

                        <x-filament::section>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-widest">Email</div>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $record->email ?? '—' }}</p>
                        </x-filament::section>

                        <x-filament::section>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-widest">National ID</div>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $record->national_id ?? $record->id_number ?? '—' }}</p>
                        </x-filament::section>

                        <x-filament::section>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-widest">Status</div>
                            <span class="mt-1 inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold
                                {{ strtolower($record->status ?? 'active') === 'active' ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-700' }}">
                                {{ ucfirst($record->status ?? 'Active') }}
                            </span>
                        </x-filament::section>

                        <x-filament::section>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-widest">Current Unit</div>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $unit?->unit_number ?? '—' }}</p>
                            @if ($unit)
                                <p class="mt-1 text-xs text-gray-500">{{ $unit->property?->name ?? 'Property' }}</p>
                            @endif
                        </x-filament::section>
                    </div>

                    <x-filament::section heading="Contact &amp; Emergency Details">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Address</p>
                                <p class="mt-1 text-gray-700">{{ $record->address ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Occupation</p>
                                <p class="mt-1 text-gray-700">{{ $record->occupation ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Gender</p>
                                <p class="mt-1 text-gray-700">{{ ucfirst($record->gender ?? '—') }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Date of Birth</p>
                                <p class="mt-1 text-gray-700">{{ $record->date_of_birth?->format('d M Y') ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Emergency Contact</p>
                                <p class="mt-1 text-gray-700">{{ $record->emergency_contact_name ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Emergency Phone</p>
                                <p class="mt-1 text-gray-700">{{ $record->emergency_contact_phone ?? '—' }}</p>
                            </div>
                        </div>
                    </x-filament::section>
                </div>

                <!-- Lease -->
                <div x-show="tab === 'lease'" x-cloak class="space-y-4">
                    <x-filament::section heading="Current Lease">
                        @if (! $currentLease)
                            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center text-sm text-gray-500">
                                No active lease found for this tenant.
                            </div>
                        @else
                            <div class="grid gap-4 md:grid-cols-3">
                                <x-filament::section>
                                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-widest">Start Date</div>
                                    <p class="mt-1 text-gray-900">{{ $currentLease->start_date?->format('d M Y') ?? '—' }}</p>
                                </x-filament::section>
                                <x-filament::section>
                                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-widest">End Date</div>
                                    <p class="mt-1 text-gray-900">{{ $currentLease->end_date?->format('d M Y') ?? '—' }}</p>
                                </x-filament::section>
                                <x-filament::section>
                                    <div class="text-sm font-semibold text-gray-500 uppercase tracking-widest">Monthly Rent</div>
                                    <p class="mt-1 text-lg font-semibold text-gray-900">KSh {{ number_format($currentLease->monthly_rent ?? 0, 2) }}</p>
                                </x-filament::section>
                            </div>

                            @if (filled($currentLease->description))
                                <div class="mt-4">
                                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-widest">Lease Terms</p>
                                    <p class="mt-1 text-gray-700">{{ $currentLease->description }}</p>
                                </div>
                            @endif

                            <div class="mt-4 flex items-center gap-4 pt-4 border-t border-gray-200">
                                <a
                                    href="{{ \App\Filament\Resources\LeaseAgreements\LeaseAgreementResource::getUrl('create') }}?tenant={{ $record->id }}"
                                    class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
                                >
                                    <x-filament::icon alias="heroicon-o-document-text" width="1.125rem" height="1.125rem" />
                                    Manage agreement
                                </a>
                            </div>
                        @endif
                    </x-filament::section>
                </div>

                <!-- Payments -->
                <div x-show="tab === 'payments'" x-cloak class="space-y-4">
                    <x-filament::section heading="Payment History">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-semibold text-gray-500 uppercase tracking-widest">Total paid (shown)</span>
                            <span class="text-lg font-bold text-gray-900">KSh {{ number_format($totalPaid, 2) }}</span>
                        </div>
                        @if ($payments->isEmpty())
                            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center text-sm text-gray-500">
                                No payments recorded yet.
                            </div>
                        @else
                            <div class="overflow-hidden rounded-xl border border-gray-200">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Date</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Lease / Unit</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Amount</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Method</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Status</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200 text-right">Reference</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($payments as $payment)
                                            <tr class="group hover:bg-gray-50">
                                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $payment->payment_date?->format('d M Y') ?? '—' }}</td>
                                                <td class="px-4 py-3 text-gray-700">{{ $payment->lease?->unit?->unit_number ?? '—' }}</td>
                                                <td class="px-4 py-3 text-gray-900 font-medium">KSh {{ number_format($payment->amount, 2) }}</td>
                                                <td class="px-4 py-3 text-gray-700">{{ ucfirst($payment->payment_method ?? '—') }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold
                                                        {{ strtolower($payment->status ?? 'completed') === 'completed' ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : (strtolower($payment->status ?? '') === 'pending' ? 'border-amber-100 bg-amber-50 text-amber-700' : 'border-rose-100 bg-rose-50 text-rose-700') }}">
                                                        {{ ucfirst($payment->status ?? '—') }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-right text-gray-700">
                                                    {{ $payment->transaction_reference ?? '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </x-filament::section>
                </div>

                <!-- Outstanding Balance -->
                <div x-show="tab === 'balance'" x-cloak class="space-y-4">
                    <x-filament::section heading="Outstanding Rent Balance">
                        <div class="grid gap-4 md:grid-cols-3">
                            <x-filament::section>
                                <div class="text-sm font-semibold text-gray-500 uppercase tracking-widest">Total Outstanding</div>
                                <p class="mt-1 text-2xl font-bold
                                    {{ $totalOutstanding > 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                    KSh {{ number_format($totalOutstanding, 2) }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">{{ $totalOutstanding > 0 ? 'Balance due' : 'All caught up' }}</p>
                            </x-filament::section>
                            <x-filament::section>
                                <div class="text-sm font-semibold text-gray-500 uppercase tracking-widest">Invoices Due</div>
                                <p class="mt-1 text-2xl font-bold text-gray-900">{{ $outstandingInvoices->count() }}</p>
                                <p class="mt-1 text-xs text-gray-500">Unpaid rent invoices</p>
                            </x-filament::section>
                            <x-filament::section>
                                <div class="text-sm font-semibold text-gray-500 uppercase tracking-widest">Last Payment</div>
                                <p class="mt-1 text-gray-900 font-medium">
                                    {{ $payments->first()?->payment_date?->format('d M Y') ?? '—' }}
                                </p>
                                <p class="mt-1 text-xs text-gray-500">{{ $payments->first()?->amount ? 'KSh '.number_format($payments->first()->amount, 2) : '—' }}</p>
                            </x-filament::section>
                        </div>

                        @if ($outstandingInvoices->isEmpty())
                            <div class="rounded-xl border border-dashed border-emerald-200 bg-emerald-50 p-6 text-center text-sm text-emerald-700">
                                No outstanding rent balance. All invoices are paid.
                            </div>
                        @else
                            <div class="overflow-hidden rounded-xl border border-rose-200">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-rose-50">
                                        <tr>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-rose-200">Billing Month</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-rose-200">Unit</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-rose-200">Amount</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-rose-200 text-right">Paid</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-rose-200 text-right">Balance</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-rose-200 text-right">Due</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-rose-100">
                                        @foreach ($outstandingInvoices as $invoice)
                                            <tr class="group hover:bg-rose-50">
                                                <td class="px-4 py-3 text-gray-900 font-medium">
                                                    {{ $invoice->billing_month?->format('M Y') ?? '—' }}
                                                </td>
                                                <td class="px-4 py-3 text-gray-700">{{ $invoice->lease?->unit?->unit_number ?? '—' }}</td>
                                                <td class="px-4 py-3 text-gray-900 font-medium text-right">KSh {{ number_format($invoice->amount, 2) }}</td>
                                                <td class="px-4 py-3 text-gray-700 text-right">KSh {{ number_format($invoice->amount_paid, 2) }}</td>
                                                <td class="px-4 py-3 text-rose-700 font-bold text-right">KSh {{ number_format($invoice->balance, 2) }}</td>
                                                <td class="px-4 py-3 text-gray-700 text-right">{{ $invoice->due_date?->format('d M Y') ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </x-filament::section>
                </div>

                <!-- Maintenance -->
                <div x-show="tab === 'maintenance'" x-cloak class="space-y-4">
                    <x-filament::section heading="Maintenance Requests">
                        @if ($maintenanceRequests->isEmpty())
                            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center text-sm text-gray-500">
                                No maintenance requests yet.
                            </div>
                        @else
                            <div class="overflow-hidden rounded-xl border border-gray-200">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Issue</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Unit</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Priority</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Status</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200 text-right">Reported</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($maintenanceRequests as $request)
                                            <tr class="group hover:bg-gray-50">
                                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $request->title }}</td>
                                                <td class="px-4 py-3 text-gray-700">{{ $request->unit?->unit_number ?? '—' }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold
                                                        {{ in_array(strtolower($request->priority ?? ''), ['high','urgent']) ? 'border-rose-100 bg-rose-50 text-rose-700' : (strtolower($request->priority ?? '') === 'medium' ? 'border-amber-100 bg-amber-50 text-amber-700' : 'border-gray-200 text-gray-700') }}">
                                                        {{ ucfirst($request->priority ?? '—') }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold
                                                        {{ strtolower($request->status ?? 'pending') === 'pending' ? 'border-amber-100 bg-amber-50 text-amber-700' : (strtolower($request->status ?? '') === 'resolved' ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-700') }}">
                                                        {{ ucfirst($request->status ?? '—') }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-gray-900 font-medium text-right">{{ $request->reported_at?->format('d M Y') ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </x-filament::section>
                </div>

                <!-- Support -->
                <div x-show="tab === 'support'" x-cloak class="space-y-4">
                    <x-filament::section heading="Support Tickets">
                        @if (!$user || $supportTickets->isEmpty())
                            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center text-sm text-gray-500">
                                @if (!$user)
                                    No support account linked to this tenant's email.
                                @else
                                    No support tickets found.
                                @endif
                            </div>
                        @else
                            <div class="overflow-hidden rounded-xl border border-gray-200">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Ticket</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Subject</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Category</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Priority</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($supportTickets as $ticket)
                                            <tr class="group hover:bg-gray-50">
                                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $ticket->ticket_number ?? '—' }}</td>
                                                <td class="px-4 py-3 text-gray-700">{{ $ticket->subject }}</td>
                                                <td class="px-4 py-3 text-gray-700">{{ $ticket->category?->name ?? '—' }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold
                                                        {{ in_array(strtolower($ticket->priority ?? ''), ['high','urgent']) ? 'border-rose-100 bg-rose-50 text-rose-700' : (strtolower($ticket->priority ?? '') === 'medium' ? 'border-amber-100 bg-amber-50 text-amber-700' : 'border-gray-200 text-gray-700') }}">
                                                        {{ ucfirst($ticket->priority ?? '—') }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold
                                                        {{ strtolower($ticket->status ?? 'open') === 'open' ? 'border-amber-100 bg-amber-50 text-amber-700' : (strtolower($ticket->status ?? '') === 'resolved' ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-700') }}">
                                                        {{ ucfirst($ticket->status ?? '—') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </x-filament::section>
                </div>

            </div>
        </div>

    </div>
</x-filament-panels::page>
