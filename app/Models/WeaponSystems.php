<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeaponSystems extends Model
{
    protected $table = 'global_weapons_systems'; // Pastikan nama tabel sesuai dengan database
    protected $primaryKey = 'ID'; // Pastikan primary key sesuai dengan database
    protected $keyType = 'integer'; // Tipe data primary key

    
    protected $fillable = [
        'Name',
        'Category',
        'Country_of_Origin',
        'Year_Introduced',
        'Unit_Cost_USD',
        'Combat_Proven'
    ];
}
