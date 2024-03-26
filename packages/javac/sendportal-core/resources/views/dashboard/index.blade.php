@extends('sendportal::layouts.app')

@section('title', __('Dashboard'))

@section('heading')
    {{ __('Dashboard') }}
@endsection

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-header-accent">
                    <div class="card-header-inner">
                        {{ __('Total Subscribers') }}
                        <div class="card-filter">
                            <div class="item-filter filter-cus-type">
                                <select name="cus_type" id="">
                                    <option value="">All customer type</option>
                                    @foreach (  $data_filler['customer_type'] as $key=>$value_c )
                                        <option value="{{ $key }}" @if(@request('cus_type')==$key) selected @endif>{{ $value_c }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="item-filter filter-source-web">
                                <select name="source" id="">
                                    <option value="">All source web</option>
                                    @foreach (  $data_filler['source_web'] as $value_s )
                                        <option value="{{ $value_s->cs_source_web }}"  @if(@request('source') == $value_s->cs_source_web) selected @endif>{{ $value_s->cs_source_web }}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                        <div class="btn-calendar" id="datetimerange-input">
                            <i class="far fa-calendar-alt"></i>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div style="width: 99%;">
                        <canvas id="growthChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-header-accent">
                    <div class="card-header-inner">
                        {{ __('Completed Campaigns') }}
                    </div>
                </div>
                <div class="card-table table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Sent') }}</th>
                            <th>{{ __('Opened') }}</th>
                            <th>{{ __('Clicked') }}</th>
                            <th>{{ __('Created') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($completedCampaigns as $campaign)
                            <tr>
                                <td>
                                    @if ($campaign->draft)
                                        <a href="{{ route('sendportal.campaigns.edit', $campaign->id) }}">{{ $campaign->name }}</a>
                                    @elseif($campaign->sent)
                                        <a href="{{ route('sendportal.campaigns.reports.index', $campaign->id) }}">{{ $campaign->name }}</a>
                                    @else
                                        <a href="{{ route('sendportal.campaigns.status', $campaign->id) }}">{{ $campaign->name }}</a>
                                    @endif
                                </td>
                                <td>{{ $campaignStats[$campaign->id]['counts']['sent'] }}</td>
                                <td>{{ number_format($campaignStats[$campaign->id]['ratios']['open'] * 100, 1) . '%' }}</td>
                                <td>{{ number_format($campaignStats[$campaign->id]['ratios']['click'] * 100, 1) . '%' }}</td>
                                <td><span title="{{ $campaign->created_at }}">{{ $campaign->created_at->diffForHumans() }}</span></td>
                                <td>
                                    @include('sendportal::campaigns.partials.status')
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button class="btn btn-light btn-sm btn-wide" type="button"
                                                id="dropdownMenuButton"
                                                data-toggle="dropdown" data-boundary="viewport" aria-haspopup="true"
                                                aria-expanded="false">
                                            <i class="fas fa-ellipsis-h"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            @if ($campaign->draft)
                                                <a href="{{ route('sendportal.campaigns.edit', $campaign->id) }}"
                                                   class="dropdown-item">
                                                    {{ __('Edit') }}
                                                </a>
                                            @else
                                                <a href="{{ route('sendportal.campaigns.reports.index', $campaign->id) }}"
                                                   class="dropdown-item">
                                                    {{ __('View Report') }}
                                                </a>
                                            @endif

                                            <a href="{{ route('sendportal.campaigns.duplicate', $campaign->id) }}"
                                               class="dropdown-item">
                                                {{ __('Duplicate') }}
                                            </a>

                                            @if ($campaign->draft)
                                                <div class="dropdown-divider"></div>
                                                <a href="{{ route('sendportal.campaigns.destroy.confirm', $campaign->id) }}"
                                                   class="dropdown-item">
                                                    {{ __('Delete') }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="empty-table-text">{{ __('You have not completed any campaigns.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header card-header-accent">
                    <div class="card-header-inner">
                        {{ __('Recent Subscribers') }}
                    </div>
                </div>
                <div class="card-table table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Created') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($recentSubscribers as $subscriber)
                            <tr>
                                <td>
                                    <a href="{{ route('sendportal.subscribers.show', $subscriber->id) }}">
                                        {{ $subscriber->email }}
                                    </a>
                                </td>
                                <td>{{ $subscriber->full_name }}</td>
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
                                <td><a href="{{ route('sendportal.subscribers.edit', $subscriber->id) }}"
                                       class="btn btn-sm btn-light">{{ __('Edit') }}</a></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%">
                                    <p class="empty-table-text">{{ __('No recent subscribers.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('css')
    <link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/gh/alumuko/vanilla-datetimerange-picker@latest/dist/vanilla-datetimerange-picker.css">
@endpush

@push('js')
    <script src="{{ asset('vendor/sendportal/js/Chart.bundle.min.js') }}"></script>

    <script>
        $(function () {
            var ctx = document.getElementById("growthChart");
            ctx.height = 300;
            var subscriberGrowthChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! $subscriberGrowthChartLabels !!},
                    datasets: [{
                        data: {!! $subscriberGrowthChartData !!},
                        label: "{{ __("Subscriber Count") }}",
                        borderColor: 'rgba(93,99,255)',
                        backgroundColor: 'rgba(93,99,255,0.34)',
                    }]
                },
                options: {
                    maintainAspectRatio: false,
                    legend: {
                        display: false
                    },
                    scales: {
                        xAxes: [{
                            gridLines: {
                                display: false
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                precision: 0,
                                suggestedMax: 10
                            }
                        }]
                    },
                    tooltips: {
                        intersect: false
                    }
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/gh/alumuko/vanilla-datetimerange-picker@latest/dist/vanilla-datetimerange-picker.js"></script>
    <script>
        var dashboard_url = "{{ route('sendportal.dashboard') }}";
        var drp;
        var start_date = "{{ @request('start') }}";
        var end_date = "{{ @request('end') }}";
        var rangeChange = function(){
            drp.updateRanges({
                'Last 3 Days': [moment().subtract(2, 'days').startOf('day'), moment().endOf('day')],
                'This Year': [moment().startOf('year').startOf('day'), moment().endOf('year').endOf('day')],
            });
        };
        // window.addEventListener("load", function (event) {
            drp = new DateRangePicker('datetimerange-input',
                {
                    //startDate: '2000-01-01',
                    //endDate: '2000-01-03',
                    //minDate: '2021-07-15 15:00',
                    //maxDate: '2021-08-16 15:00',
                    //maxSpan: { "days": 9 },
                    showDropdowns: true,
                    // minYear: 2020,
                    maxYear: moment().year(),
                    showWeekNumbers: true,
                    //showISOWeekNumbers: true,
                    // timePicker: true,
                    //timePickerIncrement: 10,
                    //timePicker24Hour: true,
                    //timePickerSeconds: true,
                    showCustomRangeLabel: true,
                    alwaysShowCalendars: true,
                    opens: 'center',
                    //drops: 'up',
                    singleDatePicker: false,
                    //autoApply: true,
                    //linkedCalendars: false,
                    //isInvalidDate: function(m){
                    //    return m.weekday() == 3;
                    //},
                    //isCustomDate: function(m){
                    //    return "weekday-" + m.weekday();
                    //},
                    //autoUpdateInput: false,
                    ranges: {
                        'Today': [moment().startOf('day'), moment().endOf('day')],
                        'Yesterday': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
                        'This Week': [moment().startOf('week'), moment().endOf('week')],
                        'Last Week': [moment().subtract(6, 'days').startOf('day'), moment().endOf('day')],
                        'This Month': [moment().startOf('month').startOf('day'), moment().endOf('month').endOf('day')],
                        'Last Month' : [moment().subtract(1, 'months').startOf('month'), moment().subtract(1, 'months').endOf('month')],
                        'This Year': [moment().startOf('year'), moment().endOf('year')],
                        'Last Year': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
                    },
                    locale: {
                        format: "YYYY-MM-DD",
                    }
                },
                function (start, end) {
                    // alert(start.format() + " - " + end.format());
                })
            //drp.setStartDate('2014/03/01');
            //drp.setEndDate('2014/03/03');
            window.addEventListener('apply.daterangepicker', function (ev) {
                let params = [];
                params['start'] = ev.detail.startDate.format('YYYY-MM-DD');
                start_date = params['start'];
                params['end'] = ev.detail.endDate.format('YYYY-MM-DD');
                end_date = params['end'];
                params['cus_type'] = $('select[name="cus_type"]').val();
                params['source'] = $('select[name="source"]').val();
                request_dashboard(params)
            });
        // });

        if(start_date != '' && end_date != ''){
            drp.setStartDate(start_date)
            drp.setEndDate(end_date)
        } else{
            drp.setStartDate(moment().startOf('month').startOf('day'))
            drp.setEndDate(moment().endOf('month').endOf('day'))
        }


        $('select[name="cus_type"], select[name="source"]').change(function () {
            let cusTypeValue = $('select[name="cus_type"]').val();
            let sourceValue = $('select[name="source"]').val();
            let delimiter = (dashboard_url.indexOf('?') !== -1) ? '&' : '?';
            if(drp){
                start_date = drp.startDate.format('YYYY-MM-DD');
                end_date = drp.endDate.format('YYYY-MM-DD');
            }
            let queryParams = 'cus_type=' + cusTypeValue + '&source=' + sourceValue + '&start=' + start_date + '&end=' + end_date;

            window.location.href = dashboard_url + delimiter + queryParams;
        });

        function request_dashboard(params) {
            let delimiter = (dashboard_url.indexOf('?') !== -1) ? '&' : '?';
            let queryParams = 'cus_type=' + params['cus_type'] + '&source=' + params['source'] + '&start=' + params['start'] + '&end=' + params['end'];

            window.location.href = dashboard_url + delimiter + queryParams;

        }
    </script>
@endpush
