<?php

namespace App\Filament\Resources\Properties\Pages;

use App\Filament\Resources\Properties\PropertyResource;
use App\Models\Payment;
use App\Models\Property;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Carbon;

class ViewProperty extends ViewRecord
{
    protected static string $resource = PropertyResource::class;

    protected string $view = 'filament.properties.show';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();
    }

    public function getViewData(): array
    {
        /** @var Property $property */
        $property = $this->record;
        $units = $property->units;

        $tenantIds = $units->pluck('tenant.id')->filter()->all();

        $recentPayments = Payment::query()
            ->whereHas('lease.unit', function ($query) use ($property) {
                $query->where('property_id', $property->id);
            })
            ->with(['tenant', 'lease.unit'])
            ->latest('payment_date')
            ->limit(10)
            ->get();

        $recentExpenses = $property->expenses
            ->sortByDesc('expense_date')
            ->take(10);

        $recentMaintenance = $property->maintenanceRequests
            ->sortByDesc('reported_at')
            ->take(10);

        $documents = $units
            ->filter(fn ($unit) => filled($unit->lease_agreement_path))
            ->map(fn ($unit) => [
                'unit' => $unit->unit_number,
                'name' => $unit->lease_agreement_filename ?? 'Lease agreement',
                'path' => 'private/lease_agreements/'.$unit->lease_agreement_path,
            ])->all();

        $occupancy = [
            'total' => $units->count(),
            'occupied' => $units->filter(fn ($u) => $u->status === 'occupied' || $u->activeLease?->isNotEmpty())->count(),
            'vacant' => $units->filter(fn ($u) => $u->status === 'vacant')->count(),
        ];

        $monthlyRent = $units->sum(fn ($u) => (float) ($u->monthly_rent ?? 0));

        return [
            'record' => $property,
            'property' => $property,
            'units' => $units,
            'tenants' => $units->pluck('tenant')->filter()->values()->all(),
            'recentPayments' => $recentPayments,
            'recentExpenses' => $recentExpenses,
            'recentMaintenance' => $recentMaintenance,
            'documents' => $documents,
            'occupancy' => $occupancy,
            'monthlyRent' => $monthlyRent,
            'tenantCount' => count($tenantIds),
            'now' => Carbon::now(),
        ];
    }

    public function getTitle(): string
    {
        return 'Property — '.$this->record?->name;
    }

    public function getHeading(): string
    {
        return $this->record?->name ?? 'Property';
    }

    public function getSubheading(): ?string
    {
        return 'Manage units, tenants, payments, expenses and maintenance for this property.';
    }
}
