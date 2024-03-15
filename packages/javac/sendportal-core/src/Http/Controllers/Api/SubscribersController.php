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
                $data_syncs = request()->data;//json_decode(request()->data);
                $workspaceId = request()->workspace_id;
                $sync_success = [];
                $sync_failed = [];
                foreach ($data_syncs as $key => $item) {
                    
                    try {
                        //check tags
                        // $tag = $this->insertTags($workspaceId, $item['cs_course_name']);
                        
                        //insert or ignore subscriber
                        // $item = (array)$item;
                        dd($item);
                        $item['cs_corporate_user'] = $item['cs_corporate_user'] ?? false;
                        if(!empty($item['user_created_at'])){
                            $item['created_at'] = date('Y-m-d H:i:s', strtotime($item['user_created_at']));
                        }
                        $item['sync_date'] = date('Y-m-d H:i:s');
                        
                        //// save subscriber with tag
                        // $data_tag_w_subscriber = [
                        //     'tag_id' => $tag->id,
                        //     'subscriber_id' => $subscriber->id
                        // ];
                        // $this->syncSubscriberTagsApi($data_tag_w_subscriber);
                        $data_courses = [
                            'courses' => $item['courses'],
                            'is_sent_cheap_mail' => $item['cheap_email_sent'],
                        ];

                        unset($item['courses']);
                        unset($item['cheap_email_sent']);
                        $subscriber = $this->insertOrIgnoreSubscribers($workspaceId, $item);
                        $data_courses['subscriber_id'] = $subscriber->id;

                        $this->syncCouserInfo($data_courses);
                        // save info course for subscriber

                        $sync_success[] = $item['cs_source_id'];
                        Log::channel('apilog')->info("Sync Success Item: ".$item['cs_source_id']);

                    } catch (\Exception $ex) {
                        dd($ex->getMessage());
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

    private function handelCourseInfo($data_courses){
        $res = [];
        if(isset($data_courses["courses"]) && !empty($data_courses['courses'])){
            foreach ($data_courses["courses"] as $key => $value) {
                $item = [
                    'sent_cheap_mail' => $data_courses['is_sent_cheap_mail'] ?? false,
                    'subscriber_id' => $data_courses['subscriber_id'],
                    'code_course' => @$value->code_course,
                    'cs_course_name' => @$value->cs_course_name,
                    'cs_quiz_taken' => @$value->cs_quiz_taken ?? 0,
                    'cs_quiz_passed' => @$value->cs_quiz_passed ?? 0,
                    'cs_quiz_paid' => @$value->cs_quiz_paid ?? 0,
                    'cs_quiz_expiring' => @$value->cs_quiz_expiring ?? 0,
                    'cs_quiz_date' => @$value->cs_quiz_date,
                    'cs_quiz_failed_attempts' => @$value->cs_quiz_failed_attempts
                ];
                $res[] = $item;
            }
        }
        return $res;
    }

    private function syncCouserInfo($raw_data){
        $data = $this->handelCourseInfo($raw_data);
        foreach ($data as $key => $value) {
            $this->subscribers->syncCourse($value);
        }
        return true;
    }

    private function insertTags($workspaceId, $key){
        if(!empty($key)){
            return $this->subscribers->insertOrIgnoreTags($workspaceId, $key);
        }
    }
}
