<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Brand extends Model
{
    use HasTranslations;
    use HasFactory;

    protected $table = 'brands';
    protected $primaryKey = 'id';
    protected $guarded = ['id'];

    protected static ?string $recordTitleAttribute = 'name';
    public array $translatable = ['name', 'address', 'city', 'additional_info', 'description' ];

    public function getRouteKeyName(){
        return 'slug';
    }

    public function products(){
        return $this->hasMany(Product::class, 'brand_id', 'id');
    }


}
