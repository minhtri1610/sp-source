<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Exception\ProcessFailedException;

class DeployController extends Controller
{
    /**
     * @return View|RedirectResponse
     */
    public function index(Request $request)
    {
        try {

            $deploymentDir = env('ROOT_DIR', '/var/www/html/send-portal');
            $pre_set = new Process(["sudo git config --global --add safe.directory "], $deploymentDir);
            $pre_set->run();

            $process = Process::fromShellCommandline("sudo git pull origin stagging");
            $process->run();
            // Run additional deployment tasks as needed
            Artisan::call('migrate');
            Artisan::call('vendor:publish --provider=Sendportal\\Base\\SendportalBaseServiceProvider --force');
            
            if (!$process->isSuccessful()) {
                throw new ProcessFailedException($process);
            }
            Log::channel('deploy')->info('Deployment output success');
            return response()->json(['message' => 'Deployment successful'], 200);

        } catch (ProcessFailedException $ex) {
            $errors = $ex->getMessage();
            Log::channel('deploy')->error($errors);
            return $errors;
        }
    }
}
