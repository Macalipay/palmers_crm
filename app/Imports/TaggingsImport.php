<?php

namespace App\Imports;

use App\Tagging;
use Maatwebsite\Excel\Concerns\ToModel;

class TaggingsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Tagging([
           'name'     => $row[0],
           'precinct'    => $row[5], 
           'region_id'    => 123, 
           'province_id'    => 123, 
           'city_id'    => 123, 
           'barangay_id'    => $row[1], 
           'address'    => $row[2], 
           'gender'    => $row[4], 
           'birthday'    => $row[3], 
           'degree'    => 1, 
           'status'    => 1, 
        ]);
    }
}
