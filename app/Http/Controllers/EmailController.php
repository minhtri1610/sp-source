<?php

namespace App\Http\Controllers;

use App\Jobs\TestEmailJob;
use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function sendEmailQueue(Request $request)
    {
        $email = $request->input('email', 'epsminhtri@gmail.com');
        $details['email'] = $email;
        $job = new TestEmailJob($details);
        dispatch($job);
        return response()->json(['message' => 'Mail Send Successfully!!']);
    }
}
