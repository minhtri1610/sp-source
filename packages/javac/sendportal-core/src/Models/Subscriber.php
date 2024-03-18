<?php

declare(strict_types=1);

namespace Sendportal\Base\Models;

use Carbon\Carbon;
use Database\Factories\SubscriberFactory;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Ramsey\Uuid\Uuid;
use Sendportal\Base\Models\SubscriberCourse;
use Sendportal\Base\Models\InfoCoporate;

/**
 * @property int $id
 * @property int $workspace_id
 * @property string $hash
 * @property string $email
 * @property string|null $first_name
 * @property string|null $last_name
 * @property array|null $meta
 * @property Carbon|null $unsubscribed_at
 * @property int|null $unsubscribed_event_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @property EloquentCollection $tags
 * @property EloquentCollection $messages
 *
 * @property-read string $full_name
 *
 * @method static SubscriberFactory factory
 */
class Subscriber extends BaseModel
{
    use HasFactory;

    // NOTE(david): we require this because of namespace issues when resolving factories from models
    // not in the default `App\Models` namespace.
    protected static function newFactory()
    {
        return SubscriberFactory::new();
    }

    /** @var string */
    protected $table = 'sendportal_subscribers';

    /** @var string[] */
    protected $fillable = [
        'hash',
        'email',
        'first_name',
        'last_name',
        'meta',
        'unsubscribed_at',
        'unsubscribe_event_id',
        'cs_source_id',//code string from ahac, ahcc
        'cs_company_name',
        'cs_phone_number',
        'cs_short_email',
        'cs_short_sms',
        'cs_corporate_user',
        'cs_corporate_code',
        'cs_source_web',
        'cs_user_name',
        'cs_customer_type',//'1' => 'Indiviual','2' => 'Corporate'
        'sync_date',
        'created_at'
        // 'cs_course_name',
        // 'cs_quiz_taken',
        // 'cs_quiz_passed',
        // 'cs_quiz_paid',
        // 'cs_quiz_expiring',
        // 'cs_quiz_date',
        // 'cs_quiz_failed_attempts'
    ];

    /** @var string[] */
    protected $casts = [
        'unsubscribed_at' => 'datetime',
    ];

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'sendportal_tag_subscriber')->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)
            ->orderBy('id', 'desc');
    }

    public function courses(): HasMany
    {
        return $this->hasMany(SubscriberCourse::class)
            ->orderBy('id', 'desc');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(
            function ($model) {
                $model->hash = Uuid::uuid4()->toString();
            }
        );
        static::deleting(
            function (self $subscriber) {
                $subscriber->tags()->detach();
                $subscriber->messages()->each(static function (Message $message) {
                    $message->failures()->delete();
                });
                $subscriber->messages()->delete();
            }
        );
    }
}
