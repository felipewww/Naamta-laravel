<?php

namespace App\Console\Commands;

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Mail\AuthEmails;
use App\Models\Application;
use App\Models\User;
use Carbon\Carbon;
use Faker\Provider\DateTime;
use Illuminate\Console\Command;
use App\Models\tests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FlowThreeYears extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '3 year flow to reset the application';

    protected $name = 'yearsflow';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $currYear   = date('Y');
        $minYear    = $currYear - 3;
        $currMonth  = date('m');
        $currDay    = date('d');

        $minDate = date('Y-m-d G:i:s', mktime(23, 59, 59, $currMonth, $currDay, $minYear));
        $apps = Application::where('created_at', '<=', $minDate)->get();

        if ($apps->isNotEmpty())
        {
            DB::beginTransaction();

            //----------------------------
            //insert it on LOG file
            $count = $apps->count();
            $msg = $count.' application(s) was reset';
            $this->info($msg);
            //-----------------------------

            foreach ($apps as $app)
            {

                $currDate = date('Y-m-d G:i:s', mktime(0,0,0,$currMonth, $currDay, $currYear));
                $app->status = 'wt_firstform';
                $app->created_at = date('Y-m-d G:i:s', mktime(0,0,0,$currMonth, $currDay, $currYear));
                $app->reset_at = $currDate;
                $app->save();

                foreach ($app->steps as $step)
                {
                    $step->status = '0';
                    $step->save();
                }

                $user = User::findOrFail($app->client->user)->first();
                /*
                 * If you're in development, set you e-mail in .ENV file to receive the confirmation email
                 */
                if (app('env') == 'local') {
                    $user->email = env('MAIL_LOCAL_RECEIVER');
                }

                $client = $app->client;
                Mail::to($user)->send(
                    new AuthEmails('resetApp', [
                        'client'    => $client,
                        'user'      => $client->user
                    ])
                );
            }

            DB::commit();
        }
    }
}
