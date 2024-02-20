<?php

namespace App\Jobs;

use App\Helpers\Muwiza;
use App\Models\User;
use App\Models\WeeklySallary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenerateSallaries implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $periodStart;

    /**
     * Create a new job instance.
     */
    public function __construct($period_start = null)
    {
        $this->periodStart = $period_start ?? date('Y-m-d', strtotime(Muwiza::firstMonday()));
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        WeeklySallary::where('period_start', $this->periodStart)->delete();

        $users = User::where('access_id', '>=', 5)->where('active', true)->get();
        foreach ($users as $user) {
            WeeklySallary::currentWeekFrom($user, $this->periodStart);
        }
    }
}
