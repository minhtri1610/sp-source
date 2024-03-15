<?php

namespace Sendportal\Base\Http\Controllers\Api;

use Sendportal\Base\Facades\Sendportal;
use Sendportal\Base\Http\Controllers\Controller;
use Sendportal\Base\Http\Requests\Api\CampaignDispatchRequest;
use Sendportal\Base\Http\Resources\Campaign as CampaignResource;
use Sendportal\Base\Interfaces\QuotaServiceInterface;
use Sendportal\Base\Models\CampaignStatus;
use Sendportal\Base\Repositories\Campaigns\CampaignTenantRepositoryInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Sendportal\Base\Services\Messages\DispatchMessage;

class CampaignDispatchController extends Controller
{
    /**
     * @var CampaignTenantRepositoryInterface
     */
    protected $campaigns;

    /**
     * @var QuotaServiceInterface
     */
    protected $quotaService;

    public function __construct(
        CampaignTenantRepositoryInterface $campaigns,
        QuotaServiceInterface $quotaService
    ) {
        $this->campaigns = $campaigns;
        $this->quotaService = $quotaService;
    }

    /**
     * @throws \Exception
     */
    public function send(CampaignDispatchRequest $request, $campaignId)
    {
        $campaign = $request->getCampaign(['email_service', 'messages']);
        $workspaceId = Sendportal::currentWorkspaceId();

        if ($this->quotaService->exceedsQuota($campaign->email_service, $campaign->unsent_count)) {
            return response([
                'message' => __('The number of subscribers for this campaign exceeds your SES quota')
            ], 422);
        }

        $campaign = $this->campaigns->update($workspaceId, $campaignId, [
            'status_id' => CampaignStatus::STATUS_QUEUED,
        ]);

        return new CampaignResource($campaign);
    }


    protected function testData (){
        $data = array (
            'from_email'    =>  "epsminhtri@gmail.com", // required
            'to_email'      =>  "tri@javactechnology", // required
            'from_name'     =>  "dev", // required
            'subject'       =>  "test sendmail", // required
            'cc'            =>  array(),
            'bcc'           =>  array(),
            'tagging'       =>  "", // required
            'client_email_id'  =>  '',
            'files'         => [],
            'content'=>  html_entity_decode(mb_convert_encoding("test data", 'UTF-8'))
        );

        return array('data' => gzencode(base64_encode(json_encode($data))));
    }

    // public function getTag($header)
    // {
    //     $header = strtolower($header);
    //     if(preg_match('/password/', $header)) {
    //         return 'Reset Password';
    //     }
    //     if(preg_match('/certificate/', $header) || preg_match('/certs/', $header)) {
    //         return 'Certificate';
    //     }
    //     if(preg_match('/log in/', $header) || preg_match('/login/', $header) || preg_match('/account/', $header)) {
    //         return 'Account';
    //     }
    //     if(preg_match('/quiz/', $header)) {
    //         return 'Quiz';
    //     }
    //     if(preg_match('/receipt/', $header)) {
    //         return 'Receipts';
    //     }
    //     if(preg_match('/champ/', $header) || preg_match('/c.h.a.m.p/', $header)
    //         || preg_match('/invoice/', $header) || preg_match('/quote/', $header)) {
    //         return 'Invoice';
    //     }
    //     if(preg_match('/order/', $header)) {
    //         return 'Order';
    //     }
    //     if(preg_match('/refund/', $header)) {
    //         return 'Refund';
    //     }
    //     if(preg_match('/local/', $header) || preg_match('/hands-on/', $header)) {
    //         return 'Local CPR Assessment';
    //     }
    //     if(preg_match('/group authorization code/', $header) || preg_match('/codes extend/', $header)) {
    //         return 'Group Authorization Codes';
    //     }
    //     if(preg_match('/in person session/', $header)) {
    //         return 'Person Request';
    //     }
    //     if(preg_match('/support/', $header)) {
    //         return 'Support';
    //     }
    //     if(preg_match('/cancel/', $header) || preg_match('/void/', $header)) {
    //         return 'Voided/Canceled';
    //     }
    //     if(preg_match('/registering your group/', $header) || preg_match('/group discount/', $header)) {
    //         return 'New Group Registered';
    //     } elseif(preg_match('/for registering/', $header)) {
    //         return 'New User Registered';
    //     }
    //     return 'Others';
    // }


    protected function unzipData($raw_data) {
        $data = [];
        if(isset($raw_data['data'])){
            $base64DecodedData = gzdecode($raw_data['data']);
            $jsonString = base64_decode($base64DecodedData);
            $data = json_decode($jsonString, true);
        }
        return $data;
    }


    public function sendEmail(){
        try {
            $res = [
                "message" => '',
                "status" => false,
                "data" => []
            ];
            //get data test
            $test_data = $this->testData();

            //unzip data
            $params = $this->unzipData($test_data);

            //validate data
            $valid = $this->validateSendMail($params);

            if($valid){
                //send mail
                
                // save data to message table
            }
            $res['message'] = $valid;

        } catch (\Exception $ex) {
            $errors = $ex->getMessage();
            Log::channel('apilog')->error($errors);
            $res['message'] = $errors;
            return $res;
        }
        
    }

    private function validateSendMail($param){

        $validationRules = [
            'from_email' => ['required', 'email', 'max:255'],
            'to_email' => ['required', 'email', 'max:255'],
            'from_name' => ['required', 'max:255'],
            'subject' => ['required', 'max:255'],
            'tagging' => ['required']
        ];

        $validator = Validator::make(
            $param,
            $validationRules
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->getMessages() as $error) {
                return $error[0];
            }
        }

        return true;
    }
}
