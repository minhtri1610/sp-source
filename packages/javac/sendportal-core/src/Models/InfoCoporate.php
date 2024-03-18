<?php

declare(strict_types=1);

namespace Sendportal\Base\Models;

use Carbon\Carbon;

class InfoCoporate extends BaseModel
{

    /** @var string */
    protected $table = 'info_of_corporates';

    /** @var string[] */
    protected $fillable = [
        'subscriber_id',
        'co_codes_used_percent',
        'co_code_string',
        'co_admin_name',
        'co_admin_email',
        'co_admin_phone',
        'co_category',
        'co_paid_codes_expired',
        'co_paid_codes_not_expired',
        'co_group_invoice_status',   
        'co_invoice_created_not_paid_number',
        'co_invoice_created_not_paid_amount',
        'co_invoice_created_not_paid_date',       
        'group_codesexpire_datetime'
    ];


}
