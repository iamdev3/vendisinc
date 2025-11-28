<?php

namespace App\Models;

use App\Observers\MailTemplateObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

#[ObservedBy(MailTemplateObserver::class)]
class MailTemplate extends Model
{
    use HasTranslations;

    protected $guarded = ['id'];

    public array $translatable = [
        'subject',
        'description',
        'content',
    ];

    protected $casts = [
       'is_active' => 'boolean',
       'cc'        => 'array',
       'bcc'       => 'array',
       'content'   => 'array',
    ];

    public function logs(){
       return $this->hasMany(MailLog::class);
    }

}
