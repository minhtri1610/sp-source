<?php

declare(strict_types=1);

namespace Sendportal\Base\Repositories\Subscribers;

use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;
use Sendportal\Base\Interfaces\BaseTenantInterface;
use Sendportal\Base\Models\Subscriber;

interface SubscriberTenantRepositoryInterface extends BaseTenantInterface
{
    public function syncTags(Subscriber $subscriber, array $tags = []);

    public function countActive($workspaceId): int;

    public function getRecentSubscribers(int $workspaceId): Collection;

    public function getGrowthChartData(CarbonPeriod $period, int $workspaceId, array $params = []): array;

    public function insertOrIgnoreTags(int $workspaceId, string $key_name);

    public function syncTagsApi(Subscriber $subscriber, array $data);

    public function syncCourse(array $data);

    public function syncInfoCorporate(array $data);

    public function getSourceWeb(int $workspaceId):Collection;
    
}
