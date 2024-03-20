@extends('sendportal::layouts.app')

@section('title', __('Subscribers'))

@section('heading')
    {{ __('Subscribers') }}
@endsection

@section('content')

    @component('sendportal::layouts.partials.actions')

        @slot('left')
            <form action="{{ route('sendportal.subscribers.index') }}" method="GET" class="form-inline mb-3 mb-md-0">
                <input class="form-control form-control-sm" name="name" type="text" value="{{ request('name') }}"
                       placeholder="{{ __('Search...') }}">

                <div class="mr-2">
                    <select name="status" class="form-control form-control-sm">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ __('All') }}</option>
                        <option
                            value="subscribed" {{ request('status') == 'subscribed' ? 'selected' : '' }}>{{ __('Subscribed') }}</option>
                        <option
                            value="unsubscribed" {{ request('status') == 'unsubscribed' ? 'selected' : '' }}>{{ __('Unsubscribed') }}</option>
                    </select>
                </div>

                @if(count($tags))
                    <div id="tagFilterSelector" class="mr-2">
                        <select multiple="" class="selectpicker form-control form-control-sm" name="tags[]" data-width="auto">
                            @foreach($tags as $tagId => $tagName)
                                <option value="{{ $tagId }}" @if(in_array($tagId, request()->get('tags') ?? [])) selected @endif>{{ $tagName }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <button type="submit" class="btn btn-light btn-md">{{ __('Search') }}</button>

                @if(request()->anyFilled(['name', 'status']))
                    <a href="{{ route('sendportal.subscribers.index') }}"
                       class="btn btn-md btn-light">{{ __('Clear') }}</a>
                @endif
            </form>
        @endslot

        @slot('right')
            <div class="btn-group mr-2">
                <button class="btn btn-md btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="fa fa-bars color-gray-400"></i>
                </button>
                <div class="dropdown-menu">
                    <a href="{{ route('sendportal.subscribers.import') }}" class="dropdown-item">
                        <i class="fa fa-upload color-gray-400 mr-2"></i> {{ __('Import Subscribers') }}
                    </a>
                    <a href="{{ route('sendportal.subscribers.export') }}" class="dropdown-item">
                        <i class="fa fa-download color-gray-400 mr-2"></i> {{ __('Export Subscribers') }}
                    </a>

                </div>
            </div>
            <a class="btn btn-light btn-md mr-2" href="{{ route('sendportal.tags.index') }}">
                <i class="fa fa-tag color-gray-400 mr-1"></i> {{ __('Tags') }}
            </a>
            <a class="btn btn-primary btn-md btn-flat" href="{{ route('sendportal.subscribers.create') }}">
                <i class="fa fa-plus mr-1"></i> {{ __('New Subscriber') }}
            </a>
        @endslot
    @endcomponent

    <div class="card">
        <div class="card-table table-responsive">

            {{-- button fill-columns --}}
            <div class="mr-2 btn-fill-columns">
                <button class="btn btn-md btn-default" type="button" id="dropdownMenuClickableInside">
                    <i class="fa fa-bars color-gray-400"></i>
                </button>
                <div class="dropdown-menu cus-drop-menu" aria-labelledby="dropdownMenuClickableInside">
                    <form action="{{ route('profile.setting') }}" method="post">
                        @csrf
                        <ul class="ul-wapper-menu">
                            <li>
                                <label for="check-all">
                                    @php
                                        $check_all = count($columns) == count($columns_selected) ? 'checked' : '';
                                    @endphp
                                    <input id="check-all" type="checkbox" {{ $check_all }} name="check-all" value="all"> <span>All Fields</span>
                                </label>
                            </li>
                            {{-- @dd($columns_selected) --}}
                            @foreach ( $columns as $key => $value) 
                                <li>
                                    <label for="{{ $key }}">
                                        <input @if(in_array($key, $columns_selected)) checked @endif id="{{ $key }}" type="checkbox" name="columns[]" value="{{ $key }}"> <span>{{ $value }}</span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                        <div class="wapper-btn-menu">
                            <button class="btn-menu btn-apply btn-md">Apply</button>
                            <button type="button" class="btn-menu btn-close btn-md">Close</button>
                        </div>
                    </form>
                </div>
            </div>
            {{-- end button fill-columns --}}

            <table class="table sub-table">
                <thead>
                    @if(empty($columns_selected))
                        <tr>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Name') }}</th>
                            {{-- fields new --}}
                            <th>{{ __('Company Name') }}</th>
                            <th>{{ __('Phone Number') }}</th>
                            <th>{{ __('Customer Type') }}</th>
                            {{-- <th>{{ __('Magic Link') }}</th> --}}
                            {{-- end fields new --}}
                            <th>{{ __('Tags') }}</th>
                            <th>{{ __('Created') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    @else
                        <tr>
                            @foreach ($columns_selected as $val)
                                <th>{{ $columns[$val] }}</th>
                            @endforeach
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    @endif
                </thead>

                <tbody>

                    @forelse($subscribers as $subscriber)
                        @if(empty($columns_selected))
                            <tr>
                                <td>
                                    <a href="{{ route('sendportal.subscribers.show', $subscriber->id) }}">
                                        {{ $subscriber->email }}
                                    </a>
                                </td>
                                <td>{{ $subscriber->full_name }}</td>
                                <td>{{ $subscriber->cs_company_name }}</td>
                                <td>{{ $subscriber->cs_phone_number }}</td>
                                <td>{{ $subscriber->cs_customer_type ? config('constants.customer_type')[$subscriber->cs_customer_type] : '-' }}</td>
                                {{-- <td>
                                    {{ $subscriber->cs_short_email ? 's-mail: '.$subscriber->cs_short_email.'-' : '' }} <br>
                                    {{ $subscriber->cs_short_sms ? 's-sms:'.$subscriber->cs_short_sms : ''}}
                                </td> --}}
                                <td>
                                    @forelse($subscriber->tags as $tag)
                                        <span class="badge badge-light">{{ $tag->name }}</span>
                                    @empty
                                        -
                                    @endforelse
                                <td><span
                                        title="{{ $subscriber->created_at }}">{{ $subscriber->created_at->diffForHumans() }}</span>
                                </td>
                                <td>
                                    @if($subscriber->unsubscribed_at)
                                        <span class="badge badge-danger">{{ __('Unsubscribed') }}</span>
                                    @else
                                        <span class="badge badge-success">{{ __('Subscribed') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <form action="{{ route('sendportal.subscribers.destroy', $subscriber->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <a href="{{ route('sendportal.subscribers.edit', $subscriber->id) }}"
                                        class="btn btn-xs btn-light">{{ __('Edit') }}</a>
                                        <button type="submit"
                                                class="btn btn-xs btn-light delete-subscriber">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>

                        @else
                            <tr>
                                @foreach ($columns_selected as $val)
                                    @switch($val)
                                        @case('email')
                                            <td>
                                                <a href="{{ route('sendportal.subscribers.show', $subscriber->id) }}">
                                                    {{ $subscriber->email }}
                                                </a>
                                            </td>
                                        @break

                                        @case('first_name')
                                            <td>
                                                {{ $subscriber->first_name }}
                                            </td>
                                        @break

                                        @case('last_name')
                                            <td>
                                                {{ $subscriber->last_name }}
                                            </td>
                                        @break
                                        
                                        @case('company_name')
                                            <td>
                                                {{ $subscriber->company_name }}
                                            </td>
                                        @break

                                        @case('phone_number')
                                            <td>
                                                {{ $subscriber->phone_number }}
                                            </td>
                                        @break

                                        @case('customer_type')
                                            <td>
                                                {{ $subscriber->cs_customer_type ? config('constants.customer_type')[$subscriber->cs_customer_type] : '-' }}
                                            </td>
                                        @break

                                        @case('tags')
                                            <td>
                                                @forelse($subscriber->tags as $tag)
                                                    <span class="badge badge-light">{{ $tag->name }}</span>
                                                @empty
                                                    -
                                                @endforelse
                                            </td>
                                        @break

                                        @case('created')
                                            <td>
                                                <span title="{{ $subscriber->created_at }}">{{ $subscriber->created_at->diffForHumans() }}
                                                </span>
                                            </td>
                                        @break

                                        @case('status')
                                            <td>
                                                @if($subscriber->unsubscribed_at)
                                                    <span class="badge badge-danger">{{ __('Unsubscribed') }}</span>
                                                @else
                                                    <span class="badge badge-success">{{ __('Subscribed') }}</span>
                                                @endif
                                            </td>
                                        @break
                                    @endswitch
                                @endforeach
                                
                                <td>
                                    <form action="{{ route('sendportal.subscribers.destroy', $subscriber->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <a href="{{ route('sendportal.subscribers.edit', $subscriber->id) }}"
                                        class="btn btn-xs btn-light">{{ __('Edit') }}</a>
                                        <button type="submit"
                                                class="btn btn-xs btn-light delete-subscriber">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="100%">
                                <p class="empty-table-text">{{ __('No Subscribers Found') }}</p>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

    @include('sendportal::layouts.partials.pagination', ['records' => $subscribers])

    <script>
        let subscribers = document.getElementsByClassName('delete-subscriber');

        Array.from(subscribers).forEach((element) => {
            element.addEventListener('click', (event) => {
                event.preventDefault();

                let confirmDelete = confirm('Are you sure you want to permanently delete this subscriber and all associated data?');

                if (confirmDelete) {
                    element.closest('form').submit();
                }
            });
        });

    </script>

@endsection

@push('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.12/dist/css/bootstrap-select.min.css">
@endpush

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.13.12/dist/js/bootstrap-select.min.js"></script>
    <script>
        $('#dropdownMenuClickableInside').click(function () {
            $('div[aria-labelledby="dropdownMenuClickableInside"]').toggle();
        })
        $('.btn-close').click(function () {
            $('div[aria-labelledby="dropdownMenuClickableInside"]').hide();
        })

        $('#check-all').click(function () {
            if($(this).is(":checked")){
                $('input[type="checkbox"]').not(this).prop('checked', true);
            } else{
                $('input[type="checkbox"]').not(this).prop('checked', false);
            }
        })

        $('.ul-wapper-menu input[type="checkbox"]').not('#check-all').click(function () {
            let checkAll = checkedAllInput()
            if(checkAll){
                $('#check-all').prop('checked', true);
            } else{
                $('#check-all').prop('checked', false);
            }
        })

        function checkedAllInput() {
            let res = true;
            $('.ul-wapper-menu input[type="checkbox"]').not('#check-all').each(function () {
                if(!$(this).is(':checked')){ 
                    res = false;
                    return;
                }
            })
            return res;
        }
    </script>
@endpush
