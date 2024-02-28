<?php

namespace Sendportal\Base\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Sendportal\Base\Http\Resources\Tag as TagResource;

class Subscriber extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'cs_source_id' => $this->cs_source_id,
            'cs_company_name' => $this->cs_company_name,
            'cs_phone_number' => $this->cs_phone_number,
            'cs_short_email' => $this->cs_short_email,
            'cs_short_sms' => $this->cs_short_sms,
            'cs_corporate_user' => $this->cs_corporate_user,
            'cs_corporate_code' => $this->cs_corporate_code,
            'cs_source_web' => $this->cs_source_web,
            'cs_user_name' => $this->cs_user_name,
            // 'cs_course_name' => $this->cs_course_name,
            // 'cs_quiz_taken' => $this->cs_quiz_taken,
            // 'cs_quiz_passed' => $this->cs_quiz_passed,
            // 'cs_quiz_paid' => $this->cs_quiz_paid,
            // 'cs_quiz_expiring' => $this->cs_quiz_expiring,
            // 'cs_quiz_date' => $this->cs_quiz_date,
            // 'cs_quiz_failed_attempts' => $this->cs_quiz_failed_attempts,
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'unsubscribed_at' => $this->unsubscribed_at ? $this->unsubscribed_at->toDateTimeString() : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString()
        ];
    }
}
