<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Message;
use \GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use \GuzzleHttp\Exception\ClientException;
// use App\Models\Template;
use App\Models\History;
use App\Models\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;


class PostMessageJob implements ShouldQueue
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
        try {

            // \Log::info('UserID:'. Auth::user()->id .' 即時投稿 開始');


            $title = $this->inputs['title'];
            $message = PHP_EOL . $this->inputs['content'];
            $img_path = $this->inputs['img_path'];
            $store_id = $this->inputs['store_id'];
            $history_id = $this->inputs['history_id'];

            DB::table('histories') ->where('id', $history_id )
            ->update([
                'status'=> '配信中',
                'start_at'=> Carbon::now(),
                'updated_at'=> Carbon::now()
            ]);

            $API = 'https://notify-api.line.me/api/notify';
            $store_id = $store_id;
            $lines = DB::table('lines')
            ->select('id','token', 'user_name')
            ->where('is_valid', true)
            ->where('store_id', $store_id
            )->get();

            $result = "OK"; 
            $client = new Client();
            $err_list = [];

            ini_set("max_execution_time",0);

            $multipart = [[ 'name' => 'message','contents' => $message]];
            if ($img_path != '') {
                array_push($multipart,[ 'name'=> 'imageFile','contents' => Psr7\Utils::tryFopen($img_path, 'r')]);
            }

            foreach($lines as $line)
            {
                $res = $client->request('POST', $API, [
                    'headers' => ['Authorization'=> 'Bearer '.$line->token, ],
                    'http_errors' => false,
                    'multipart' => $multipart
                ]);
                
                $res_body = json_decode($res->getBody());  
                if ($res_body->status != 200){                    
                    $result = 'NG';
                    array_push($err_list, '['.$line->user_name.']'.$res_body->status.'::'.$res_body->message);
                    \Log::error('['.$line->user_name.']'.$res_body->status.'::'.$res_body->message);                      
                }
            }

            DB::table('histories')->where('id',$history_id )
            ->update(
                [
                    'status'=> $result,
                    'end_at'=> Carbon::now(),
                    'err_info' => empty($err_list) ? 'ー' : join('/', $err_list),
                    'updated_at'=> Carbon::now()
                ]);
            // \Log::info('UserID:'. Auth::user()->id .' 即時投稿 終了');
        } 
        catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }
}
