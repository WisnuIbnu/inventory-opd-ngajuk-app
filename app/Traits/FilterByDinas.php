<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FilterByDinas
{
    protected static function booted()
    {
        static::addGlobalScope('dinas_scope', function (Builder $builder) {
            if (auth()->check()) {
                // Jika user adalah OPD, kunci ke dinas mereka
                if (auth()->user()->role === 'OPD') {
                    $builder->where('dinas_id', auth()->user()->dinas_id);
                } 
                // Jika user adalah Admin dan ada session filter dinas
                elseif (auth()->user()->role === 'Admin' && session()->has('admin_dinas_id') && session('admin_dinas_id') != null) {
                    $builder->where('dinas_id', session('admin_dinas_id'));
                }
            }
        });
    }
}