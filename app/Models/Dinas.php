<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dinas extends Model
{
    use HasFactory;
    protected $table = 'dinas';
    
    protected $fillable = ['nama_opd'];

    public function users(): HasMany 
    { 
        return $this->hasMany(User::class); 
    }

    public function gudangs(): HasMany 
    { 
        return $this->hasMany(Gudang::class); 
    }

    public function jenisBarangs(): HasMany 
    { 
        return $this->hasMany(JenisBarang::class); 
    }

    public function penanggungJawabs(): HasMany 
    { 
        return $this->hasMany(PenanggungJawab::class); 
    }

    public function barangs(): HasMany 
    { 
        return $this->hasMany(Barang::class); 
    }
}
