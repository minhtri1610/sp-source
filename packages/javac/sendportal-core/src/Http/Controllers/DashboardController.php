<?php

namespace Sendportal\Base\Http\Controllers;

use Carbon\CarbonPeriod;
use Exception;
use Illuminate\View\View;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;
use Sendportal\Base\Repositories\Messages\MessageTenantRepositoryInterface;
use Sendportal\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;
use Sendportal\Base\Services\Campaigns\CampaignStatisticsService;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * @var SubscriberTenantRepositoryInterface
     */
    protected $subscribers;

    /**
     * @var CampaignTenantRepositoryInterface
     */
    protected $campaigns;

    /**
     * @var MessageTenantRepositoryInterface
     */
    protected $messages;

    /**
     * @var CampaignStatisticsService
     */
    protected $campaignStatisticsService;

    public function __construct(SubscriberTenantRepositoryInterface $subscribers, CampaignTenantRepositoryInterface $campaigns, MessageTenantRepositoryInterface $messages, CampaignStatisticsService $campaignStatisticsService)
    {
        $this->subscribers = $subscribers;
        $this->campaigns = $campaigns;
        $this->messages = $messages;
        $this->campaignStatisticsService = $campaignStatisticsService;
    }

    /**
     * @throws Exception
     */
    public function index(): View
    {
        $params = request()->all();
        // dd($params);
        $workspaceId = Sendportal::currentWorkspaceId();
        $completedCampaigns = $this->campaigns->completedCampaigns($workspaceId, ['status']);
        $subscriberGrowthChart = $this->getSubscriberGrowthChart($workspaceId, $params);
        $data_filler = [
            'customer_type' => config('constants.customer_type'),
            'source_web' => $this->getAllSourceWeb($workspaceId)
        ];

        return view('sendportal::dashboard.index', [
            'recentSubscribers' => $this->subscribers->getRecentSubscribers($workspaceId),
            'completedCampaigns' => $completedCampaigns,
            'campaignStats' => $this->campaignStatisticsService->getForCollection($completedCampaigns, $workspaceId),
            'subscriberGrowthChartLabels' => json_encode($subscriberGrowthChart['labels']),
            'subscriberGrowthChartData' => json_encode($subscriberGrowthChart['data']),
            'data_filler' => $data_filler
        ]);
    }
    

    protected function getAllSourceWeb($workspaceId)
    {
        $data_sources =  $this->subscribers->getSourceWeb($workspaceId);
        return $data_sources;
    }

    protected function getSubscriberGrowthChart($workspaceId, $params): array
    {
        if(isset($params['start']) && isset($params['end'])){
            $start_date = Carbon::createFromFormat('Y-m-d', $params['start']);
            $end_date = Carbon::createFromFormat('Y-m-d', $params['end']);
            $period = CarbonPeriod::create($start_date, $end_date);
        } else{
            $period = CarbonPeriod::create(now()->subDays(30)->startOfDay(), now()->endOfDay());
        }
        // dd($period->getEndDate());

        $growthChartData = $this->subscribers->getGrowthChartData($period, $workspaceId, $params);

        $growthChart = [
            'labels' => [],
            'data' => [],
        ];

        $currentTotal = $growthChartData['startingValue'];

        foreach ($period as $date) {
            $formattedDate = $date->format('d-m-Y');

            $periodValue = $growthChartData['runningTotal'][$formattedDate]->total ?? 0;
            $periodUnsubscribe = $growthChartData['unsubscribers'][$formattedDate]->total ?? 0;
            $currentTotal += $periodValue - $periodUnsubscribe;

            $growthChart['labels'][] = $formattedDate;
            $growthChart['data'][] = $currentTotal;
        }

        return $growthChart;
    }
}
