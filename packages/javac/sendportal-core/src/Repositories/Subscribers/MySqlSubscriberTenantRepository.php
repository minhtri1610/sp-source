<?php

namespace Sendportal\Base\Repositories\Subscribers;

use Carbon\CarbonPeriod;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class MySqlSubscriberTenantRepository extends BaseSubscriberTenantRepository
{
    /**
     * @inheritDoc
     */
    public function getGrowthChartData(CarbonPeriod $period, int $workspaceId, array $params = []): array
    {
        $startingValue = DB::table('sendportal_subscribers')
            ->where('workspace_id', $workspaceId)
            ->where(function (Builder $q) use ($period) {
                $q->where('unsubscribed_at', '>=', $period->getStartDate())
                    ->orWhereNull('unsubscribed_at');
            })
            ->where('created_at', '<', $period->getStartDate());
        if(isset($params['cus_type']) && $params['cus_type'] != ''){
            $startingValue->where('cs_customer_type', $params['cus_type']);
        }
        if(isset($params['source']) && $params['source'] != ''){
            $startingValue->where('cs_source_web', $params['source']);
        }
        $startingValue = $startingValue->count();

        $runningTotal = DB::table('sendportal_subscribers')
            ->selectRaw("date_format(created_at, '%d-%m-%Y') AS date, count(*) as total")
            ->where('workspace_id', $workspaceId)
            ->where('created_at', '>=', $period->getStartDate())
            ->where('created_at', '<=', $period->getEndDate());
        if(isset($params['cus_type']) && $params['cus_type'] != ''){
            $runningTotal->where('cs_customer_type', $params['cus_type']);
        }
        if(isset($params['source']) && $params['source'] != ''){
            $runningTotal->where('cs_source_web', $params['source']);
        }
        $runningTotal = $runningTotal->groupBy('date')->get();

        $unsubscribers = DB::table('sendportal_subscribers')
            ->selectRaw("date_format(unsubscribed_at, '%d-%m-%Y') AS date, count(*) as total")
            ->where('workspace_id', $workspaceId)
            ->where('unsubscribed_at', '>=', $period->getStartDate())
            ->where('unsubscribed_at', '<=', $period->getEndDate());
            if(isset($params['cus_type']) && $params['cus_type'] != ''){
                $unsubscribers->where('cs_customer_type', $params['cus_type']);
            }
            if(isset($params['source']) && $params['source'] != ''){
                $unsubscribers->where('cs_source_web', $params['source']);
            }
            $unsubscribers = $unsubscribers->groupBy('date')->get();

        return [
            'startingValue' => $startingValue,
            'runningTotal' => $runningTotal->flatten()->keyBy('date'),
            'unsubscribers' => $unsubscribers->flatten()->keyBy('date'),
        ];
    }
}
