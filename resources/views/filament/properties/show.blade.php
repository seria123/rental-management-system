<x-filament-panels::page>
    <div class="space-y-6">

        <!-- Quick actions -->
        <div class="flex flex-wrap gap-3">
            <a
                href="{{ \App\Filament\Resources\Properties\Pages\EditProperty::getUrl(['record' => $record]) }}"
                class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-lg shadow-amber-600/20 transition hover:bg-amber-700 hover:shadow-amber-600/30"
            >
                <x-filament::icon alias="heroicon-o-pencil" width="1.125rem" height="1.125rem" />
                Edit property
            </a>

            <a
                href="{{ \App\Filament\Resources\Units\UnitResource::getUrl('create') }}?property={{ $record->id }}"
                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
            >
                <x-filament::icon alias="heroicon-o-plus" width="1.125rem" height="1.125rem" />
                Add unit
            </a>

            <a
                href="{{ \App\Filament\Resources\Payments\PaymentResource::getUrl('create') }}?tenant_id="
                class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50"
            >
                <x-filament::icon alias="heroicon-o-currency-dollar" width="1.125rem" height="1.125rem" />
                Record payment
            </a>
        </div>

        <!-- Tab navigation -->
        <div x-data="{ tab: 'overview' }" class="border-b border-gray-200 bg-white">
            <div class="flex items-center justify-between">
                <div class="flex space-x-1 px-4 overflow-x-auto pb-2 no-scrollbar">
                    @foreach ([
                        'overview' => 'Overview',
                        'units' => 'Units',
                        'tenants' => 'Tenants',
                        'payments' => 'Payments',
                        'expenses' => 'Expenses',
                        'maintenance' => 'Maintenance',
                        'documents' => 'Documents',
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

                <!-- Overview -->
                <div x-show="tab === 'overview'" x-cloak class="space-y-6">
                    <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                        <x-filament::section>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Property Owner</div>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $record->owner->name ?? 'Not assigned' }}</p>
                        </x-filament::section>

                        <x-filament::section>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Type &amp; Status</div>
                            <div class="mt-1 flex flex-wrap gap-2">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold border-gray-200 text-gray-700">
                                    {{ $record->property_type }}
                                </span>
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold
                                    {{ $record->status === 'active' ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : 'border-amber-100 bg-amber-50 text-amber-700' }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </div>
                        </x-filament::section>

                        <x-filament::section>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Monthly Rent Potential</div>
                            <p class="mt-1 text-lg font-semibold text-gray-900">KSh {{ number_format($monthlyRent, 2) }}</p>
                            <p class="mt-1 text-xs text-gray-500">Across {{ $units->count() }} unit{{ $units->count() !== 1 ? 's' : '' }}</p>
                        </x-filament::section>

                        <x-filament::section>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Tenants</div>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $tenantCount }}</p>
                            <p class="mt-1 text-xs text-gray-500">Registered tenants in this property</p>
                        </x-filament::section>

                        <x-filament::section>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Occupancy</div>
                            <div class="mt-1 flex items-center gap-2">
                                <div class="w-24 bg-gray-200 rounded-full h-2.5">
                                    <div class="bg-amber-600 h-2.5 rounded-full" style="width: {{ $occupancy['total'] > 0 ? round(($occupancy['occupied'] / $occupancy['total']) * 100) : 0 }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ $occupancy['total'] > 0 ? round(($occupancy['occupied'] / $occupancy['total']) * 100) : 0 }}%
                                </span>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ $occupancy['occupied'] }} occupied · {{ $occupancy['vacant'] }} vacant
                            </p>
                        </x-filament::section>

                        <x-filament::section>
                            <div class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Address</div>
                            <p class="mt-1 text-gray-900">{{ $record->address }}, {{ $record->city }}</p>
                        </x-filament::section>
                    </div>

                    @if (filled($record->description))
                        <x-filament::section heading="Description">
                            <p class="text-gray-700">{{ $record->description }}</p>
                        </x-filament::section>
                    @endif
                </div>

                <!-- Units -->
                <div x-show="tab === 'units'" x-cloak class="space-y-4">
                    <x-filament::section heading="Units">
                        @if ($units->isEmpty())
                            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center text-sm text-gray-500">
                                No units have been added to this property yet.
                            </div>
                        @else
                            <div class="overflow-hidden rounded-xl border border-gray-200">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Unit</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Type</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Rent</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Status</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Tenant</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200 text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($units as $unit)
                                            <tr class="group hover:bg-gray-50">
                                                <td class="px-4 py-3 text-gray-900 font-medium">
                                                    {{ $unit->unit_number }}
                                                    @if ($unit->floor)
                                                        <span class="ml-2 text-xs font-normal text-gray-500">Floor {{ $unit->floor }}</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-gray-700">{{ $unit->unit_type ?? '—' }}</td>
                                                <td class="px-4 py-3 text-gray-900 font-medium">KSh {{ number_format($unit->monthly_rent ?? 0, 2) }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold
                                                        {{ $unit->status === 'occupied' ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : 'border-amber-100 bg-amber-50 text-amber-700' }}">
                                                        {{ ucfirst($unit->status) }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3">
                                                    @if ($unit->tenant)
                                                        <div class="text-gray-900 font-medium">{{ $unit->tenant->name }}</div>
                                                        <div class="text-xs text-gray-500">{{ $unit->tenant->phone ?? '—' }}</div>
                                                    @else
                                                        <span class="text-gray-400 italic">—</span>
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <a
                                                        href="{{ \App\Filament\Resources\Units\UnitResource::getUrl(['record' => $unit]) }}"
                                                        class="text-xs font-semibold text-amber-600 hover:text-amber-700 underline"
                                                    >
                                                        View unit
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </x-filament::section>
                </div>

                <!-- Tenants -->
                <div x-show="tab === 'tenants'" x-cloak class="space-y-4">
                    <x-filament::section heading="Tenants">
                        @if ($tenants->isEmpty())
                            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center text-sm text-gray-500">
                                No tenants are assigned to this property.
                            </div>
                        @else
                            <div class="overflow-hidden rounded-xl border border-gray-200">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Name</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Phone</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Email</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Unit</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($tenants as $tenant)
                                            <tr class="group hover:bg-gray-50">
                                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $tenant->name }}</td>
                                                <td class="px-4 py-3 text-gray-700">{{ $tenant->phone ?? '—' }}</td>
                                                <td class="px-4 py-3 text-gray-700">{{ $tenant->email ?? '—' }}</td>
                                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $tenant->unit?->unit_number ?? '—' }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold
                                                        {{ strtolower($tenant->status ?? 'active') === 'active' ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-700' }}">
                                                        {{ ucfirst($tenant->status ?? 'Active') }}
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

                <!-- Payments -->
                <div x-show="tab === 'payments'" x-cloak class="space-y-4">
                    <x-filament::section heading="Recent Payments">
                        @if ($recentPayments->isEmpty())
                            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center text-sm text-gray-500">
                                No payment records yet.
                            </div>
                        @else
                            <div class="overflow-hidden rounded-xl border border-gray-200">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Tenant</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Unit</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Amount</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Method</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Status</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200 text-right">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($recentPayments as $payment)
                                            <tr class="group hover:bg-gray-50">
                                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $payment->tenant?->name ?? '—' }}</td>
                                                <td class="px-4 py-3 text-gray-700">{{ $payment->lease?->unit?->unit_number ?? '—' }}</td>
                                                <td class="px-4 py-3 text-gray-900 font-medium">KSh {{ number_format($payment->amount, 2) }}</td>
                                                <td class="px-4 py-3 text-gray-700">{{ ucfirst($payment->payment_method ?? '—') }}</td>
                                                <td class="px-4 py-3">
                                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold
                                                        {{ strtolower($payment->status ?? 'completed') === 'completed' ? 'border-emerald-100 bg-emerald-50 text-emerald-700' : (strtolower($payment->status ?? '') === 'pending' ? 'border-amber-100 bg-amber-50 text-amber-700' : 'border-rose-100 bg-rose-50 text-rose-700') }}">
                                                        {{ ucfirst($payment->status ?? '—') }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-gray-900 font-medium text-right">{{ $payment->payment_date?->format('d M Y') ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </x-filament::section>
                </div>

                <!-- Expenses -->
                <div x-show="tab === 'expenses'" x-cloak class="space-y-4">
                    <x-filament::section heading="Recent Expenses">
                        @if ($recentExpenses->isEmpty())
                            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center text-sm text-gray-500">
                                No expenses recorded yet.
                            </div>
                        @else
                            <div class="overflow-hidden rounded-xl border border-gray-200">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Title</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Category</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Amount</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200 text-right">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($recentExpenses as $expense)
                                            <tr class="group hover:bg-gray-50">
                                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $expense->title }}</td>
                                                <td class="px-4 py-3 text-gray-700">{{ ucfirst($expense->category ?? '—') }}</td>
                                                <td class="px-4 py-3 text-gray-900 font-medium text-right">KSh {{ number_format($expense->amount, 2) }}</td>
                                                <td class="px-4 py-3 text-gray-900 font-medium text-right">{{ $expense->expense_date?->format('d M Y') ?? '—' }}</td>
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
                        @if ($recentMaintenance->isEmpty())
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
                                        @foreach ($recentMaintenance as $request)
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

                <!-- Documents -->
                <div x-show="tab === 'documents'" x-cloak class="space-y-4">
                    <x-filament::section heading="Lease Agreements &amp; Documents">
                        @if ($documents->isEmpty())
                            <div class="rounded-xl border border-dashed border-gray-200 bg-gray-50 p-8 text-center text-sm text-gray-500">
                                No lease agreements or documents have been uploaded for this property's units.
                            </div>
                        @else
                            <div class="overflow-hidden rounded-xl border border-gray-200">
                                <table class="w-full text-sm text-left">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Unit</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200">Document</th>
                                            <th class="px-4 py-3 font-semibold text-gray-500 uppercase tracking-widest border-b border-gray-200 text-right">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200">
                                        @foreach ($documents as $document)
                                            <tr class="group hover:bg-gray-50">
                                                <td class="px-4 py-3 text-gray-900 font-medium">{{ $document['unit'] }}</td>
                                                <td class="px-4 py-3 text-gray-700">{{ $document['name'] }}</td>
                                                <td class="px-4 py-3 text-right">
                                                    <a
                                                        href="{{ \Illuminate\Support\Facades\Storage::url($document['path']) }}"
                                                        target="_blank"
                                                        rel="noopener"
                                                        class="inline-flex items-center gap-1.5 rounded-lg border border-amber-200 bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700 hover:bg-amber-100 transition"
                                                    >
                                                        <x-filament::icon alias="heroicon-o-download" width="1rem" height="1rem" />
                                                        Download
                                                    </a>
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
