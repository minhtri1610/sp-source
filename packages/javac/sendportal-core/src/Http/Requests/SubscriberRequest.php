<?php

namespace Sendportal\Base\Http\Requests;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Sendportal\Base\Facades\Sendportal;

/**
 * @property-read string $subscriber
 */
class SubscriberRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('sendportal_subscribers', 'email')
                    ->ignore($this->subscriber, 'id')
                    ->where(static function (Builder $query) {
                        $query->where('workspace_id', Sendportal::currentWorkspaceId());
                    })
            ],
            'first_name' => [
                'max:255',
            ],
            'last_name' => [
                'max:255',
            ],
            'tags' => [
                'nullable',
                'array',
            ],
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
        ];
    }
           
}
