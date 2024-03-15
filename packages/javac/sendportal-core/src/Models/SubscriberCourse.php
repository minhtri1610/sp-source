<?php

declare(strict_types=1);

namespace Sendportal\Base\Models;

use Carbon\Carbon;

class SubscriberCourse extends BaseModel
{
    // use HasFactory;

    // // NOTE(david): we require this because of namespace issues when resolving factories from models
    // // not in the default `App\Models` namespace.
    // protected static function newFactory()
    // {
    //     return SubscriberFactory::new();
    // }

    /** @var string */
    protected $table = 'course_of_subscribers';

    /** @var string[] */
    protected $fillable = [
        'subscriber_id',
        'code_course',
        'sent_cheap_mail',
        'cs_course_name',
        'cs_quiz_taken',
        'cs_quiz_passed',
        'cs_quiz_paid',
        'cs_quiz_expiring',
        'cs_quiz_date',
        'cs_quiz_failed_attempts'          
    ];


}
