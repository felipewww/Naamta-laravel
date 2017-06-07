<?php

namespace App\Console\Commands;

use App\Http\Controllers\Auth\ResetPasswordController;
use App\Mail\AuthEmails;
use App\Models\Application;
use App\Models\ContinuousCompliance;
use App\Models\SysContinuousCompliance;
use App\Models\User;
use Carbon\Carbon;
use Faker\Provider\DateTime;
use Illuminate\Console\Command;
use App\Models\tests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DailyActions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dailyActions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tasks that needs run everyday, once day';

    protected $name = 'dailyActions';

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
        $data = [
            'status' => true
        ];

        $sysContinuous = SysContinuousCompliance::all();
        $ms = 60*60*24; //ms in a day
        $today = strtotime( date('Y-m-d', time()) );

        $intro = 'Nothing to execute1';

        foreach ($sysContinuous as $continuous)
        {
            $intro = 'Nothing to execute2';
            $lastAction = ( $continuous->updated_at == null ) ? $continuous->created_at : $continuous->updated_at;

            //Remove Hours, minutes and seconds. Just DATE;
            $msLastAction = strtotime( date('Y-m-d', strtotime($lastAction->toDateTimeString())) );

            //get one day and multiply by interval (in days)
            $msAction = $ms * $continuous->interval;

            //Sum LastActionMS + MSinDaysToNextAction
            $nextAction = $msAction + $msLastAction; //ms

            //Comments to developer see what date is being generated
            // dd(
            //     'Today => '. date('Y-m-d', $today),
            //     'Generated (next action) => ' . date('Y-m-d', $nextAction)
            // );

            //If is today or before today, the action needs to be executed
            $validation = ($nextAction <= $today);

            if ($validation) {
                $intro = 'Has something to execute';
                \DB::beginTransaction();

                $newcc = new ContinuousCompliance();

                $newcc->form_template_id  = $continuous->form_template_id;
                $newcc->application_id    = $continuous->application_id;
                $newcc->save();

                echo $continuous->id;
                $moreTimes = ($continuous->times - 1);
                echo $moreTimes;

                if ($moreTimes > 0) {
                    echo "\n Has moretimes";
                    $continuous->times = $moreTimes;
                    $continuous->times_executed = ($continuous->times_executed + 1);
                    $continuous->save();
                }else{
                    echo "\n No moretimes";
                    $continuous->delete();
                }
                \DB::commit();
            }
        }

        echo $intro.". Daily actions executed \n";

        return $data;
    }
}
