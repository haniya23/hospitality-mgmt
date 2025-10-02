<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\LocationResource;
use App\Models\Country;
use App\Models\State;
use App\Models\District;
use App\Models\City;
use App\Models\Pincode;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateLocation extends CreateRecord
{
    protected static string $resource = LocationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterCreate(): void
    {
        // Handle states creation
        if (isset($this->data['states']) && is_array($this->data['states'])) {
            foreach ($this->data['states'] as $stateData) {
                if (isset($stateData['country_id']) && $stateData['country_id']) {
                    State::create($stateData);
                }
            }
        }

        // Handle districts creation
        if (isset($this->data['districts']) && is_array($this->data['districts'])) {
            foreach ($this->data['districts'] as $districtData) {
                if (isset($districtData['state_id']) && $districtData['state_id']) {
                    District::create($districtData);
                }
            }
        }

        // Handle cities creation
        if (isset($this->data['cities']) && is_array($this->data['cities'])) {
            foreach ($this->data['cities'] as $cityData) {
                if (isset($cityData['district_id']) && $cityData['district_id']) {
                    City::create($cityData);
                }
            }
        }

        // Handle pincodes creation
        if (isset($this->data['pincodes']) && is_array($this->data['pincodes'])) {
            foreach ($this->data['pincodes'] as $pincodeData) {
                if (isset($pincodeData['city_id']) && $pincodeData['city_id']) {
                    Pincode::create($pincodeData);
                }
            }
        }
    }
}
