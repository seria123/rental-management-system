<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Filament\Resources\Payments\PaymentResource;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    public function getHeaderActions(): array
    {
        return [
            Action::make('downloadReceipt')
                ->label('Download Receipt')
                ->icon(Heroicon::OutlinedDocumentArrowDown)
                ->url(fn (): string => route('tenant.payment-receipt.download', [
                    'payment' => $this->getRecord()->getKey(),
                ]))
                ->openUrlInNewTab()
                ->color('gray'),
            Action::make('edit')
                ->label('Edit')
                ->icon(Heroicon::OutlinedPencil)
                ->url(PaymentResource::getUrl('edit', ['record' => $this->getRecord()]))
                ->color('primary'),
        ];
    }
}
