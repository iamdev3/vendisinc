<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{   
    use HasTranslations;

    protected $guarded = ['id'];
    protected $table = "categories";
    public array $translatable = ['name', 'description'];

    public function brand(){
        return $this->hasOne(Brand::class);
    }

    public function parent(){
       return $this->hasOne(Category::class, 'id', 'parent_id');
    }
}
