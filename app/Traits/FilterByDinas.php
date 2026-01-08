<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FilterByDinas
{
    protected static function booted()
    {
        static::addGlobalScope('dinas_scope', function (Builder $builder) {
            if (auth()->check()) {
                if (auth()->user()->role === 'OPD') {
                    $builder->where('dinas_id', auth()->user()->dinas_id);
                } 
                elseif (auth()->user()->role === 'Admin' && session()->has('admin_dinas_id') && session('admin_dinas_id') != null) {
                    $builder->where('dinas_id', session('admin_dinas_id'));
                }
            }
        });
    }
}