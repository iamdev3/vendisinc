<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MailLog extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'metadata'  => 'array',
        'sent_at'   => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function template(){
        return $this->belongsTo(MailTemplate::class, 'template_id');
    }
}
