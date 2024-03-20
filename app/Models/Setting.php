<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    protected $table = 'setting_of_accounts';

    /** @var string[] */
    protected $fillable = [
        'user_id',
        'workspace_id',
        'meta_table_subscriber'
    ];
}
