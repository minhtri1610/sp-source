<x-sendportal.text-field name="settings[postal_host]" :label="__('Postal Host')" :value="Arr::get($settings ?? [], 'postal_host')" />
<x-sendportal.text-field name="settings[key]" :label="__('API Key')" :value="Arr::get($settings ?? [], 'key')" autocomplete="off" />