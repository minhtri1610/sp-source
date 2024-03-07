<?php

declare(strict_types=1);

namespace Sendportal\Base\Http\Controllers\Api;

use Exception;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\Api\SubscriberStoreRequest;
use Sendportal\Base\Http\Requests\Api\SubscriberUpdateRequest;
use Sendportal\Base\Http\Resources\Subscriber as SubscriberResource;
use Sendportal\Base\Repositories\Subscribers\SubscriberTenantRepositoryInterface;
use Sendportal\Base\Services\Subscribers\ApiSubscriberService;
use Illuminate\Support\Facades\Log;

class SubscribersController extends Controller
{
    /** @var SubscriberTenantRepositoryInterface */
    protected $subscribers;

    /** @var ApiSubscriberService */
    protected $apiService;

    public function __construct(
        SubscriberTenantRepositoryInterface $subscribers,
        ApiSubscriberService $apiService
    ) {
        $this->subscribers = $subscribers;
        $this->apiService = $apiService;
    }

    /**
     * @throws Exception
     */
    public function index(): AnonymousResourceCollection
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $subscribers = $this->subscribers->paginate($workspaceId, 'last_name');

        return SubscriberResource::collection($subscribers);
    }

    /**
     * @throws Exception
     */
    public function store(SubscriberStoreRequest $request): SubscriberResource
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $subscriber = $this->apiService->storeOrUpdate($workspaceId, collect($request->validated()));

        $subscriber->load('tags');

        return new SubscriberResource($subscriber);
    }

    /**
     * @throws Exception
     */
    public function show(int $id): SubscriberResource
    {
        $workspaceId = Sendportal::currentWorkspaceId();

        return new SubscriberResource($this->subscribers->find($workspaceId, $id, ['tags']));
    }

    /**
     * @throws Exception
     */
    public function update(SubscriberUpdateRequest $request, int $id): SubscriberResource
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $subscriber = $this->subscribers->update($workspaceId, $id, $request->validated());

        return new SubscriberResource($subscriber);
    }

    /**
     * @throws Exception
     */
    public function destroy(int $id): Response
    {
        $workspaceId = Sendportal::currentWorkspaceId();
        $this->apiService->delete($workspaceId, $this->subscribers->find($workspaceId, $id));

        return response(null, 204);
    }

    
    public function syncData(){
        try {
            $res = [
                'msg' => '',
                'data' => [
                    'sync_success' => [],
                    'sync_failed' => []
                ]
            ];
            if(isset(request()->data) && !empty(request()->data)){
                $data_syncs = json_decode(request()->data);
                $workspaceId = request()->workspace_id;
                $sync_success = [];
                $sync_failed = [];
                foreach ($data_syncs as $key => $item) {
                    
                    try {
                        //check tags
                        // $tag = $this->insertTags($workspaceId, $item['cs_course_name']);
                        
                        //insert or ignore subscriber
                        $item = (array)$item;

                        $item['cs_corporate_user'] = $item['cs_corporate_user'] ?? false;
                        if(!empty($item['user_created_at'])){
                            $item['created_at'] = date('Y-m-d H:i:s', strtotime($item['user_created_at']));
                        }
                        $item['sync_date'] = date('Y-m-d H:i:s');
                        $subscriber = $this->insertOrIgnoreSubscribers($workspaceId, $item);
                        //// save subscriber with tag
                        // $data_tag_w_subscriber = [
                        //     'tag_id' => $tag->id,
                        //     'subscriber_id' => $subscriber->id
                        // ];
                        // $this->syncSubscriberTagsApi($data_tag_w_subscriber);

                        // save info course for subscriber
                        if(!$item['cheap_email_sent']){//check sent on cheapmail
                            $data_couser = [
                                'subscriber_id' => $subscriber->id,
                                'cs_course_name' => $item['cs_course_name'] ?? 'no-name',
                                'cs_quiz_taken' => $item['cs_quiz_taken'] ?? false,
                                'cs_quiz_passed' => $item['cs_quiz_passed'] ?? false,
                                'cs_quiz_paid' => $item['cs_quiz_paid'] ?? false,
                                'cs_quiz_expiring' => $item['cs_quiz_expiring'],
                                'cs_quiz_date' => $item['cs_quiz_date'],
                                'cs_quiz_failed_attempts' => $item['cs_quiz_failed_attempts']
                            ];
                            $this->syncCouserInfo($data_couser);
                        }

                        $sync_success[] = $item['cs_source_id'];
                        Log::channel('apilog')->info("Sync Success Item: ".$item['cs_source_id']);

                    } catch (\Exception $ex) {
                        // dd($ex->getMessage());
                        Log::channel('apilog')->error($ex->getMessage());
                        $sync_failed[] = $item['cs_source_id'];
                        continue;
                    }

                }
                $res['msg'] = 'Completed Sync Data';
                $res['data']['sync_success'] = $sync_success;
                $res['data']['sync_failed'] = $sync_failed;
            }
            return $res;

        } catch (\Exception $ex) {
            $errors = $ex->getMessage();
            Log::channel('apilog')->error($errors);
            $res['msg'] = $errors;
            return $res;
        }
    }

    private function insertOrIgnoreSubscribers($workspaceId, $data){
        
        $existingSubscriber = $this->subscribers->findBy($workspaceId, 'email', $data['email']);

        if (!$existingSubscriber) {
            $subscriber = $this->subscribers->store($workspaceId, $data);
            return $subscriber;
        }
        return $existingSubscriber;
    }

    private function syncCouserInfo($data){
        return $this->subscribers->syncCourse($data);
    }

    private function insertTags($workspaceId, $key){
        if(!empty($key)){
            return $this->subscribers->insertOrIgnoreTags($workspaceId, $key);
        }
    }
}
