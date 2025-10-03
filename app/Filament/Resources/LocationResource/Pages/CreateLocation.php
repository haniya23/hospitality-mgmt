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
        // Handle JSON import if provided
        if (isset($this->data['locations_json']) && $this->data['locations_json']) {
            $this->importFromJson($this->data['locations_json']);
            return;
        }

        // Handle individual country creation
        if (isset($this->data['countries']) && is_array($this->data['countries'])) {
            foreach ($this->data['countries'] as $countryData) {
                if (isset($countryData['name']) && isset($countryData['code'])) {
                    $country = Country::firstOrCreate(
                        ['code' => $countryData['code']],
                        ['name' => $countryData['name'], 'code' => $countryData['code']]
                    );
                    
                    // Handle states for this country if provided
                    if (isset($countryData['states']) && is_array($countryData['states'])) {
                        foreach ($countryData['states'] as $stateData) {
                            if (isset($stateData['name'])) {
                                $state = State::firstOrCreate(
                                    ['name' => $stateData['name'], 'country_id' => $country->id],
                                    [
                                        'name' => $stateData['name'],
                                        'code' => $stateData['code'] ?? null,
                                        'country_id' => $country->id
                                    ]
                                );
                                
                                // Handle districts for this state if provided
                                if (isset($stateData['districts']) && is_array($stateData['districts'])) {
                                    foreach ($stateData['districts'] as $districtData) {
                                        if (isset($districtData['name'])) {
                                            $district = District::firstOrCreate(
                                                ['name' => $districtData['name'], 'state_id' => $state->id],
                                                ['name' => $districtData['name'], 'state_id' => $state->id]
                                            );
                                            
                                            // Handle cities for this district if provided
                                            if (isset($districtData['cities']) && is_array($districtData['cities'])) {
                                                foreach ($districtData['cities'] as $cityData) {
                                                    if (isset($cityData['name'])) {
                                                        $city = City::firstOrCreate(
                                                            ['name' => $cityData['name'], 'district_id' => $district->id],
                                                            ['name' => $cityData['name'], 'district_id' => $district->id]
                                                        );
                                                        
                                                        // Handle pincodes for this city if provided
                                                        if (isset($cityData['pincodes']) && is_array($cityData['pincodes'])) {
                                                            foreach ($cityData['pincodes'] as $pincodeData) {
                                                                if (isset($pincodeData['code'])) {
                                                                    Pincode::firstOrCreate(
                                                                        ['code' => $pincodeData['code'], 'city_id' => $city->id],
                                                                        ['code' => $pincodeData['code'], 'city_id' => $city->id]
                                                                    );
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // Handle individual states creation (legacy)
        if (isset($this->data['states']) && is_array($this->data['states'])) {
            foreach ($this->data['states'] as $stateData) {
                if (isset($stateData['country_id']) && $stateData['country_id']) {
                    State::create($stateData);
                }
            }
        }

        // Handle individual districts creation (legacy)
        if (isset($this->data['districts']) && is_array($this->data['districts'])) {
            foreach ($this->data['districts'] as $districtData) {
                if (isset($districtData['state_id']) && $districtData['state_id']) {
                    District::create($districtData);
                }
            }
        }

        // Handle individual cities creation (legacy)
        if (isset($this->data['cities']) && is_array($this->data['cities'])) {
            foreach ($this->data['cities'] as $cityData) {
                if (isset($cityData['district_id']) && $cityData['district_id']) {
                    City::create($cityData);
                }
            }
        }

        // Handle individual pincodes creation (legacy)
        if (isset($this->data['pincodes']) && is_array($this->data['pincodes'])) {
            foreach ($this->data['pincodes'] as $pincodeData) {
                if (isset($pincodeData['city_id']) && $pincodeData['city_id']) {
                    Pincode::create($pincodeData);
                }
            }
        }
    }

    private function importFromJson($filePath): void
    {
        $jsonContent = file_get_contents(storage_path("app/public/{$filePath}"));
        $data = json_decode($jsonContent, true);

        if (!$data || !isset($data['countries'])) {
            return;
        }

        foreach ($data['countries'] as $countryData) {
            $country = Country::firstOrCreate(
                ['code' => $countryData['code']],
                ['name' => $countryData['name'], 'code' => $countryData['code']]
            );
            
            if (isset($countryData['states'])) {
                foreach ($countryData['states'] as $stateData) {
                    $state = State::firstOrCreate(
                        ['name' => $stateData['name'], 'country_id' => $country->id],
                        [
                            'name' => $stateData['name'],
                            'code' => $stateData['code'] ?? null,
                            'country_id' => $country->id
                        ]
                    );
                    
                    if (isset($stateData['districts'])) {
                        foreach ($stateData['districts'] as $districtData) {
                            $district = District::firstOrCreate(
                                ['name' => $districtData['name'], 'state_id' => $state->id],
                                ['name' => $districtData['name'], 'state_id' => $state->id]
                            );
                            
                            if (isset($districtData['cities'])) {
                                foreach ($districtData['cities'] as $cityData) {
                                    $city = City::firstOrCreate(
                                        ['name' => $cityData['name'], 'district_id' => $district->id],
                                        ['name' => $cityData['name'], 'district_id' => $district->id]
                                    );
                                    
                                    if (isset($cityData['pincodes'])) {
                                        foreach ($cityData['pincodes'] as $pincodeData) {
                                            Pincode::firstOrCreate(
                                                ['code' => $pincodeData['code'], 'city_id' => $city->id],
                                                ['code' => $pincodeData['code'], 'city_id' => $city->id]
                                            );
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
