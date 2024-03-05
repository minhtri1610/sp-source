<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SubscriberStoreRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'first_name' => ['nullable'],
            'last_name' => ['nullable'],
            'email' => ['required', 'email'],
            'tags' => ['array', 'nullable'],
            'cs_customer_type' => ['integer'],
            'unsubscribed_at' => ['nullable', 'date'],
            'cs_source_id' => ['nullable'],
            'cs_company_name' => ['nullable'],
            'cs_phone_number' => ['nullable'],
            'cs_short_email' => ['nullable'],
            'cs_short_sms'  => ['nullable'],
            'cs_corporate_user' => ['nullable' ,'boolean'],
            'cs_corporate_code' => ['nullable'],
            'cs_source_web' => ['nullable'],
            'cs_user_name' => ['nullable'],
            'user_created_at' => ['nullable', 'date']// data create_at from ahac
            // 'cs_course_name' => ['nullable'],
            // 'cs_quiz_taken' => ['nullable','boolean'],
            // 'cs_quiz_passed' => ['nullable','boolean'],
            // 'cs_quiz_paid' => ['nullable','boolean'],
            // 'cs_quiz_expiring' => ['nullable', 'integer'],
            // 'cs_quiz_date' => ['nullable', 'date'],
            // 'cs_quiz_failed_attempts' => ['integer']
        ];
        
    }

    public function validationData(): array
    {
        $data = $this->all();
        // Set default values for fields if they are null
        $individualKey = array_search(config('constants.customer_type')['1'], config('constants.customer_type'));
        $data['cs_customer_type'] = $data['cs_customer_type'] ?? $individualKey;
        $data['cs_corporate_user'] = $data['cs_corporate_user'] ?? false;
        $data['cs_quiz_taken'] = $data['cs_quiz_taken'] ?? false;
        $data['cs_quiz_passed'] = $data['cs_quiz_passed'] ?? false;
        $data['cs_quiz_paid'] = $data['cs_quiz_paid'] ?? false;
        $data['user_created_at'] = $data['user_created_at'] ?? date("Y-m-d H:i:s");

        return $data;
    }
}
