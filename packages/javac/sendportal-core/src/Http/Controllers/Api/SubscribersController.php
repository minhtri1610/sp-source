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

    
    public function syncData(SubscriberStoreRequest $request){
        try {
            $res = [
                'msg' => '',
                'data' => []
            ];
            if(isset($request->data)){
                $data_syncs = json_decode($request->data);
                $workspaceId = Sendportal::currentWorkspaceId();
                foreach ($data_syncs as $key => $item) {
                    //check tags
                    $tag = $this->insertTags($workspaceId, $item['cs_course_name']);
                    //insert or ignore subscriber
                    $subscriber = $this->insertOrIgnoreSubscribers($workspaceId, $item);
                    
                    //// save subscriber with tag
                    // $data_tag_w_subscriber = [
                    //     'tag_id' => $tag->id,
                    //     'subscriber_id' => $subscriber->id
                    // ];
                    // $this->syncSubscriberTagsApi($data_tag_w_subscriber);

                    // save info course for subscriber
                    $data_couser = [];
                    $this->syncCouserInfo($workspaceId, $data_couser);

                }
            }
            return $res;

        } catch (\Exception $ex) {
            $errors = $ex->getMessage();
            return ['errors' => $errors];
        }
    }

    private function insertOrIgnoreSubscribers($workspaceId, $data){
        $existingSubscriber = $this->subscribers->findBy($workspaceId, 'email', $data['email']);

        if (!$existingSubscriber) {
            $subscriber = $this->subscribers->store($workspaceId, $data->toArray());
            return $subscriber;
        }
        return $existingSubscriber;
    }

    private function insertTags($workspaceId, $key){
        if(!empty($key)){
            return $this->subscribers->insertOrIgnoreTags($workspaceId, $key);
        }
    }
}
