<?php

namespace YasserElgammal\LogistiTawseel\Models;

use Illuminate\Database\Eloquent\Model;

class LogistiRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'error_codes' => 'array',
        'successful' => 'boolean',
    ];

    public function model()
    {
        return $this->morphTo();
    }
}
