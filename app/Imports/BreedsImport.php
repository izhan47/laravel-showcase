<?php

namespace App\Imports;

use App\Models\Breed;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
class BreedsImport implements ToModel, WithHeadingRow
{

    public function model(array $row)
    {   
        $breed_exist = Breed::where('name', $row['dog_breeds'])->first();
        if( ! $breed_exist ) {
            return new Breed([
                'name' => $row['dog_breeds'],
            ]);
        }
    }
}
