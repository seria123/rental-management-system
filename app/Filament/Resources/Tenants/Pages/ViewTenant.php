<?php

namespace App\Filament\Resources\Tenants\Pages;

use App\Filament\Resources\Tenants\TenantResource;
use App\Models\Lease;
use App\Models\MaintenanceRequest;
use App\Models\Payment;
use App\Models\RentInvoice;
use App\Models\SupportTicket;
use App\Models\Tenant;
use App\Models\User;
use Filament\Resources\Pages\ViewRecord;

class ViewTenant extends ViewRecord
{
    protected static string $resource = TenantResource::class;

    protected string $view = 'filament.tenants.show';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();
    }

    public function getViewData(): array
    {
        /** @var Tenant $tenant */
        $tenant = $this->record;
        $currentLease = $tenant->currentLease;
        $unit = $currentLease?->unit ?? $tenant->unit;

        $payments = $tenant->payments()->latest('payment_date')->limit(10)->get();

        $outstandingInvoices = RentInvoice::where('tenant_id', $tenant->id)
            ->where('balance', '>', 0)
            ->latest('created_at')
            ->limit(10)
            ->get();

        $maintenanceRequests = MaintenanceRequest::query()
            ->where('tenant_id', $tenant->id)
            ->latest('reported_at')
            ->limit(10)
            ->get();

        $user = User::where('email', $tenant->email)->first();
        $supportTickets = $user
            ? SupportTicket::where('user_id', $user->id)->latest('created_at')->limit(10)->get()
            : collect();

        $totalOutstanding = (float) $outstandingInvoices->sum('balance');
        $totalPaid = (float) $payments->sum('amount');

        return [
            'record' => $tenant,
            'tenant' => $tenant,
            'currentLease' => $currentLease,
            'unit' => $unit,
            'payments' => $payments,
            'outstandingInvoices' => $outstandingInvoices,
            'totalOutstanding' => $totalOutstanding,
            'totalPaid' => $totalPaid,
            'maintenanceRequests' => $maintenanceRequests,
            'supportTickets' => $supportTickets,
        ];
    }

    public function getTitle(): string
    {
        return 'Tenant — '.$this->record?->name;
    }

    public function getHeading(): string
    {
        return $this->record?->name ?? 'Tenant';
    }

    public function getSubheading(): ?string
    {
        return 'Manage lease, payments, balances, maintenance and support for this tenant.';
    }
}
