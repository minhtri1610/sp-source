<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class ProfileController extends Controller
{
    public function show(): View
    {
        return view('profile.show');
    }

    public function edit(): View
    {
        return view('profile.edit');
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->route('profile.show')->with('success', __('Your profile was updated successfully!'));
    }
    
    public function setting(Request $request){
        try {

            $json_data = null;
            if(!empty($request->all())){
                $data = $request->all();
                if(isset($data['check-all']) && $data['check-all'] == 'all'){
                    $list_columns = config('constants.subscriber_cols');
                    $params = array_keys($list_columns);
                    $fill_value = array_fill_keys($params, 1);
                } else{
                    $fill_value = array_fill_keys($data['columns'], 1);
                }
                $json_data = json_encode($fill_value);
            }

            $data_setting = [
                'user_id' => Auth::user()->id,
                'workspace_id' => Auth::user()->current_workspace_id,
                'meta_table_subscriber' => $json_data
            ];
            Setting::updateOrCreate(['user_id'=>$data_setting['user_id'], 'workspace_id' => $data_setting['workspace_id']], $data_setting);
            return redirect()->route('sendportal.subscribers.index');
        
        } catch (\Exception $ex) {
            $errors = $ex->getMessage();
            return $errors;
        }
    }

}
