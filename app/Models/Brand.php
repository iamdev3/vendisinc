<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'brands';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    protected static ?string $recordTitleAttribute = 'name';

    public function getRouteKeyName(){
        return 'slug';
    }

    public function products(){
        return $this->hasMany(Product::class, 'brand_id', 'id');
    }


}
