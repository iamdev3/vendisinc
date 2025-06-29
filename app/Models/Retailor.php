<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Retailor extends Model
{
    use HasTranslations;

    protected $table = 'retailors';
    protected $guarded = ['id'];

    public array $translatable = [
        'name',
        'address',
        'description',
        'additional_info',
    ];


}
