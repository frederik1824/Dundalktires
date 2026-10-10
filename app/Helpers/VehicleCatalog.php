<?php

namespace App\Helpers;

class VehicleCatalog
{
    public static function get()
    {
        return [
            // Toyota
            ['make' => 'Toyota', 'model' => 'Corolla', 'year' => 2023, 'type' => 'sedan', 'tire_size' => '205/55R16'],
            ['make' => 'Toyota', 'model' => 'Corolla', 'year' => 2020, 'type' => 'sedan', 'tire_size' => '205/55R16'],
            ['make' => 'Toyota', 'model' => 'Corolla', 'year' => 2015, 'type' => 'sedan', 'tire_size' => '195/65R15'],
            ['make' => 'Toyota', 'model' => 'Camry', 'year' => 2022, 'type' => 'sedan', 'tire_size' => '215/55R17'],
            ['make' => 'Toyota', 'model' => 'Camry', 'year' => 2018, 'type' => 'sedan', 'tire_size' => '215/55R17'],
            ['make' => 'Toyota', 'model' => 'RAV4', 'year' => 2023, 'type' => 'suv', 'tire_size' => '225/65R17'],
            ['make' => 'Toyota', 'model' => 'RAV4', 'year' => 2019, 'type' => 'suv', 'tire_size' => '225/65R17'],
            ['make' => 'Toyota', 'model' => 'Highlander', 'year' => 2021, 'type' => 'suv', 'tire_size' => '235/65R18'],
            ['make' => 'Toyota', 'model' => 'Tacoma', 'year' => 2021, 'type' => 'pickup', 'tire_size' => '245/75R16'],
            ['make' => 'Toyota', 'model' => 'Tundra', 'year' => 2022, 'type' => 'pickup', 'tire_size' => '275/65R18'],
            
            // Honda
            ['make' => 'Honda', 'model' => 'Civic', 'year' => 2024, 'type' => 'sedan', 'tire_size' => '215/55R16'],
            ['make' => 'Honda', 'model' => 'Civic', 'year' => 2020, 'type' => 'sedan', 'tire_size' => '215/55R16'],
            ['make' => 'Honda', 'model' => 'Civic', 'year' => 2016, 'type' => 'sedan', 'tire_size' => '215/55R16'],
            ['make' => 'Honda', 'model' => 'Accord', 'year' => 2023, 'type' => 'sedan', 'tire_size' => '225/50R17'],
            ['make' => 'Honda', 'model' => 'Accord', 'year' => 2018, 'type' => 'sedan', 'tire_size' => '225/50R17'],
            ['make' => 'Honda', 'model' => 'CR-V', 'year' => 2023, 'type' => 'suv', 'tire_size' => '235/65R17'],
            ['make' => 'Honda', 'model' => 'CR-V', 'year' => 2017, 'type' => 'suv', 'tire_size' => '235/65R17'],
            ['make' => 'Honda', 'model' => 'Pilot', 'year' => 2022, 'type' => 'suv', 'tire_size' => '245/60R18'],
            ['make' => 'Honda', 'model' => 'Odyssey', 'year' => 2021, 'type' => 'van', 'tire_size' => '235/60R18'],

            // Ford
            ['make' => 'Ford', 'model' => 'F-150', 'year' => 2023, 'type' => 'pickup', 'tire_size' => '275/65R18'],
            ['make' => 'Ford', 'model' => 'F-150', 'year' => 2018, 'type' => 'pickup', 'tire_size' => '275/65R18'],
            ['make' => 'Ford', 'model' => 'F-150', 'year' => 2014, 'type' => 'pickup', 'tire_size' => '275/65R18'],
            ['make' => 'Ford', 'model' => 'Escape', 'year' => 2022, 'type' => 'suv', 'tire_size' => '225/65R17'],
            ['make' => 'Ford', 'model' => 'Explorer', 'year' => 2023, 'type' => 'suv', 'tire_size' => '255/50R20'],
            ['make' => 'Ford', 'model' => 'Explorer', 'year' => 2017, 'type' => 'suv', 'tire_size' => '245/60R18'],
            ['make' => 'Ford', 'model' => 'Mustang', 'year' => 2021, 'type' => 'sedan', 'tire_size' => '235/50R18'],
            ['make' => 'Ford', 'model' => 'Focus', 'year' => 2016, 'type' => 'sedan', 'tire_size' => '215/55R16'],

            // Chevrolet
            ['make' => 'Chevrolet', 'model' => 'Silverado 1500', 'year' => 2023, 'type' => 'pickup', 'tire_size' => '275/60R20'],
            ['make' => 'Chevrolet', 'model' => 'Silverado 1500', 'year' => 2019, 'type' => 'pickup', 'tire_size' => '265/70R17'],
            ['make' => 'Chevrolet', 'model' => 'Silverado 1500', 'year' => 2015, 'type' => 'pickup', 'tire_size' => '265/70R17'],
            ['make' => 'Chevrolet', 'model' => 'Equinox', 'year' => 2022, 'type' => 'suv', 'tire_size' => '225/65R17'],
            ['make' => 'Chevrolet', 'model' => 'Tahoe', 'year' => 2023, 'type' => 'suv', 'tire_size' => '265/65R18'],
            ['make' => 'Chevrolet', 'model' => 'Malibu', 'year' => 2020, 'type' => 'sedan', 'tire_size' => '225/55R17'],

            // Nissan
            ['make' => 'Nissan', 'model' => 'Altima', 'year' => 2023, 'type' => 'sedan', 'tire_size' => '215/60R16'],
            ['make' => 'Nissan', 'model' => 'Altima', 'year' => 2018, 'type' => 'sedan', 'tire_size' => '215/60R16'],
            ['make' => 'Nissan', 'model' => 'Sentra', 'year' => 2022, 'type' => 'sedan', 'tire_size' => '205/60R16'],
            ['make' => 'Nissan', 'model' => 'Sentra', 'year' => 2015, 'type' => 'sedan', 'tire_size' => '205/55R16'],
            ['make' => 'Nissan', 'model' => 'Rogue', 'year' => 2023, 'type' => 'suv', 'tire_size' => '225/65R17'],
            ['make' => 'Nissan', 'model' => 'Rogue', 'year' => 2017, 'type' => 'suv', 'tire_size' => '225/65R17'],
            ['make' => 'Nissan', 'model' => 'Frontier', 'year' => 2022, 'type' => 'pickup', 'tire_size' => '265/70R16'],

            // Hyundai
            ['make' => 'Hyundai', 'model' => 'Elantra', 'year' => 2023, 'type' => 'sedan', 'tire_size' => '205/55R16'],
            ['make' => 'Hyundai', 'model' => 'Elantra', 'year' => 2018, 'type' => 'sedan', 'tire_size' => '205/55R16'],
            ['make' => 'Hyundai', 'model' => 'Sonata', 'year' => 2022, 'type' => 'sedan', 'tire_size' => '215/55R17'],
            ['make' => 'Hyundai', 'model' => 'Tucson', 'year' => 2023, 'type' => 'suv', 'tire_size' => '225/60R17'],
            ['make' => 'Hyundai', 'model' => 'Santa Fe', 'year' => 2021, 'type' => 'suv', 'tire_size' => '235/65R17'],

            // Kia
            ['make' => 'Kia', 'model' => 'Forte', 'year' => 2022, 'type' => 'sedan', 'tire_size' => '205/55R16'],
            ['make' => 'Kia', 'model' => 'Optima', 'year' => 2019, 'type' => 'sedan', 'tire_size' => '215/55R17'],
            ['make' => 'Kia', 'model' => 'K5', 'year' => 2023, 'type' => 'sedan', 'tire_size' => '215/55R17'],
            ['make' => 'Kia', 'model' => 'Sportage', 'year' => 2023, 'type' => 'suv', 'tire_size' => '235/65R17'],
            ['make' => 'Kia', 'model' => 'Sorento', 'year' => 2022, 'type' => 'suv', 'tire_size' => '235/65R17'],

            // Jeep
            ['make' => 'Jeep', 'model' => 'Wrangler', 'year' => 2022, 'type' => 'suv', 'tire_size' => '245/75R17'],
            ['make' => 'Jeep', 'model' => 'Grand Cherokee', 'year' => 2023, 'type' => 'suv', 'tire_size' => '265/60R18'],
            ['make' => 'Jeep', 'model' => 'Grand Cherokee', 'year' => 2018, 'type' => 'suv', 'tire_size' => '245/70R17'],

            // GMC
            ['make' => 'GMC', 'model' => 'Sierra 1500', 'year' => 2023, 'type' => 'pickup', 'tire_size' => '275/60R20'],
            ['make' => 'GMC', 'model' => 'Terrain', 'year' => 2021, 'type' => 'suv', 'tire_size' => '225/65R17'],

            // Dodge / Ram
            ['make' => 'Ram', 'model' => '1500', 'year' => 2023, 'type' => 'pickup', 'tire_size' => '275/55R20'],
            ['make' => 'Ram', 'model' => '1500', 'year' => 2017, 'type' => 'pickup', 'tire_size' => '275/60R20'],
            ['make' => 'Dodge', 'model' => 'Charger', 'year' => 2021, 'type' => 'sedan', 'tire_size' => '235/55R18'],
            ['make' => 'Dodge', 'model' => 'Grand Caravan', 'year' => 2019, 'type' => 'van', 'tire_size' => '225/65R17'],
            
            // Volkswagen
            ['make' => 'Volkswagen', 'model' => 'Jetta', 'year' => 2023, 'type' => 'sedan', 'tire_size' => '205/55R17'],
            ['make' => 'Volkswagen', 'model' => 'Jetta', 'year' => 2018, 'type' => 'sedan', 'tire_size' => '205/55R16'],
            ['make' => 'Volkswagen', 'model' => 'Tiguan', 'year' => 2022, 'type' => 'suv', 'tire_size' => '215/65R17'],

            // Subaru
            ['make' => 'Subaru', 'model' => 'Outback', 'year' => 2023, 'type' => 'suv', 'tire_size' => '225/65R17'],
            ['make' => 'Subaru', 'model' => 'Forester', 'year' => 2022, 'type' => 'suv', 'tire_size' => '225/60R17'],
            ['make' => 'Subaru', 'model' => 'Crosstrek', 'year' => 2021, 'type' => 'suv', 'tire_size' => '225/60R17'],
        ];
    }
}
