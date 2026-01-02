<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'medicines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'sku',
        'unit',
        'stock',
        'price', 
        'price', 
    ];

    /**
     * Relasi ke PrescriptionDetail.
     * Satu obat bisa ada di banyak detail resep.
     */
    public function prescriptionDetails()
    {
        return $this->hasMany(PrescriptionDetail::class);
    }
}
