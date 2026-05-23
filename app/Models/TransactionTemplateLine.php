<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionTemplateLine extends Model
{
    protected $fillable = [
        'template_id',
        'account_code',
        'side',
        'amount_variable',
        'description_template',
        'line_order',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(TransactionTemplate::class, 'template_id');
    }
}
