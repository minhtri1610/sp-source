<x-sendportal.text-field name="email" :label="__('Email')" type="email" :value="$subscriber->email ?? null" />
<x-sendportal.text-field name="first_name" :label="__('First Name')" :value="$subscriber->first_name ?? null" />
<x-sendportal.text-field name="last_name" :label="__('Last Name')" :value="$subscriber->last_name ?? null" />
<x-sendportal.select-field name="tags[]" :label="__('Tags')" :options="$tags" :value="$selectedTags" multiple />
{{-- <x-sendportal.select-field name="tags[]" :label="__('Tags')" :options="$tags" :value="$selectedTags" /> --}}
 
{{-- fields new --}}
<x-sendportal.text-field name="cs_company_name" :label="__('Company Name')" :value="$subscriber->cs_company_name ?? null" />
<x-sendportal.text-field name="cs_phone_number" :label="__('Phone Number')" type="number" :value="$subscriber->cs_phone_number ?? null" />
<x-sendportal.text-field name="cs_short_email" :label="__('Magic Link (Short Email)')" :value="$subscriber->cs_short_email ?? null" /> 
<x-sendportal.text-field name="cs_short_sms" :label="__('Magic Link (Short SMS)')" :value="$subscriber->cs_short_sms ?? null" /> 
<x-sendportal.checkbox-field name="cs_corporate_user" :label="__('Customer Type')" :checked="!empty($subscriber->cs_corporate_user)" />
<x-sendportal.text-field name="cs_corporate_code" :label="__('Corporate Code')" :value="$subscriber->cs_corporate_code ?? null" />
<x-sendportal.text-field name="cs_source_web" :label="__('Source Web')" :value="$subscriber->cs_source_web ?? null" />   
<x-sendportal.text-field name="cs_user_name" :label="__('Username')" :value="$subscriber->cs_user_name ?? null" />  
<x-sendportal.text-field name="cs_course_name" :label="__('Coursename')" :value="$subscriber->cs_course_name ?? null" />  
<x-sendportal.checkbox-field name="cs_quiz_taken" :label="__('Quiz Taken')" :checked="!empty($subscriber->cs_quiz_taken)" />
<x-sendportal.checkbox-field name="cs_quiz_passed" :label="__('Quiz Passed')" :checked="!empty($subscriber->cs_quiz_passed)" />
<x-sendportal.checkbox-field name="cs_quiz_paid" :label="__('Quiz Paid')" :checked="!empty($subscriber->cs_quiz_paid)" />
<x-sendportal.text-field name="cs_quiz_expiring" type="number" :label="__('Quiz Expiring in Days')" :value="$subscriber->cs_quiz_expiring ?? null" />
<x-sendportal.text-field name="cs_quiz_date" type="date" :label="__('Quiz Date')" :value="$subscriber->cs_quiz_date ?? null" />
<x-sendportal.text-field name="cs_quiz_failed_attempts" type="number" :label="__('Quiz Failed Attempts')" :value="$subscriber->cs_quiz_failed_attempts ?? null" />
{{-- news fields new --}}
{{-- multiple --}}
<x-sendportal.checkbox-field name="subscribed" :label="__('Subscribed')" :checked="empty($subscriber->unsubscribed_at)" />

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.12/dist/css/bootstrap-select.min.css">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.12/dist/js/bootstrap-select.min.js"></script>
@endpush
