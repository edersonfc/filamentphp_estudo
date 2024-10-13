<?php

namespace App\Filament\Resources\VendaResource\Pages;

use App\Filament\Resources\VendaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateVenda extends CreateRecord
{
    protected static string $resource = VendaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['total'] = collect($data['produtos'])->sum('subtotal');
        return $data;
    }
}