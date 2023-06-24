<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SchedulePostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $inputs;

    public function __construct(array $inputs)
    {
        $this->inputs = $inputs;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $API = 'https://notify-api.line.me/api/notify';
  
        $schedules = DB::table('schedules')
        ->join('messages','schedules.message_id','=','messages.id')
        ->select(
            'schedules.id as schedule_id',
            'message_id')
        ->get();

        // \Log::info('UserID:'. Auth::user()->id .' スケジュール投稿 開始');
        // \Log::info('スケジュール投稿 開始');
        // \Log::info($this->inputs[0]['test']);
        // sleep(60);
        // \Log::info('スケジュール投稿 終了');
        // \Log::info('UserID:'. Auth::user()->id .' スケジュール投稿 終了');
    }
}
