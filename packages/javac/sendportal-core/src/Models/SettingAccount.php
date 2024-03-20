<?php

declare(strict_types=1);

namespace Sendportal\Base\Models;

use Carbon\Carbon;

class SettingAccount extends BaseModel
{
    // use HasFactory;

    // // NOTE(david): we require this because of namespace issues when resolving factories from models
    // // not in the default `App\Models` namespace.
    // protected static function newFactory()
    // {
    //     return SubscriberFactory::new();
    // }

    /** @var string */
    protected $table = 'setting_of_accounts';

    /** @var string[] */
    protected $fillable = [
        'user_id',
        'workspace_id',
        'meta_table_subscriber'
    ];


}
