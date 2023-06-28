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
        $title = $this->inputs['title'];
        $message = PHP_EOL . $this->inputs['content'];
        $img_path = $this->inputs['img_path'];
        $store_id = $this->inputs['store_id'];
        $history_id = $this->inputs['history_id'];

        try {

            $start_time = Carbon::now();

            $lines = DB::table('lines')
            ->select('id','token', 'user_name')
            ->where('is_valid', true)
            ->where('store_id', $store_id
            )->get();

            if ($lines->count() == 0)
            {
                DB::table('histories')
                ->where('id', $history_id )
                ->update(
                    [
                        'status'=> '対象0件',
                        'start_at'=> $start_time,
                        'end_at'=> $start_time,
                        'updated_at'=> $start_time
                    ]
                );
                return;
            }


            DB::table('histories')->where('id', $history_id )
            ->update([
                'status'=> '配信中',
                'start_at'=> $start_time,
                'updated_at'=> $start_time
            ]);

            $API = 'https://notify-api.line.me/api/notify';

            $result = "OK"; 
            $client = new Client();
            $err_list = [];

            ini_set("max_execution_time",0);


            for($i = 0; $i < 80; $i++){
                if ($img_path != '') {
                    foreach($lines as $line)
                    {    
                        $res = $client->request('POST', $API, [
                            'headers' => ['Authorization'=> 'Bearer '.$line->token, ],
                            'http_errors' => false,
                            'multipart' => [
                                [ 
                                    'name' => 'message',
                                    'contents' => $message.$i
                                ],
                                [ 
                                    'name'=> 'imageFile',
                                    'contents' => Psr7\Utils::tryFopen($img_path, 'r')
                                ]
                            ]
                        ]);
                        
                        $res_body = json_decode($res->getBody());  
                        if ($res_body->status != 200){                    
                            $result = 'NG';
                            array_push($err_list, '['.$line->user_name.']'.$res_body->status.'::'.$res_body->message);                 
                        }
                    }
                }
                else {
                    foreach($lines as $line)
                    {    
                        $res = $client->request('POST', $API, [
                            'headers' => ['Authorization'=> 'Bearer '.$line->token, ],
                            'http_errors' => false,
                            'multipart' => [
                                [ 
                                    'name' => 'message',
                                    'contents' => $message.$i
                                ]
                            ]
                        ]);
                        
                        $res_body = json_decode($res->getBody());  
                        if ($res_body->status != 200){                    
                            $result = 'NG';
                            array_push($err_list, '['.$line->user_name.']'.$res_body->status.'::'.$res_body->message);                    
                        }
                    }
                }
            }



            // if ($img_path != '') {
            //     foreach($lines as $line)
            //     {    
            //         $res = $client->request('POST', $API, [
            //             'headers' => ['Authorization'=> 'Bearer '.$line->token, ],
            //             'http_errors' => false,
            //             'multipart' => [
            //                 [ 
            //                     'name' => 'message',
            //                     'contents' => $message
            //                 ],
            //                 [ 
            //                     'name'=> 'imageFile',
            //                     'contents' => Psr7\Utils::tryFopen($img_path, 'r')
            //                 ]
            //             ]
            //         ]);
                    
            //         $res_body = json_decode($res->getBody());  
            //         if ($res_body->status != 200){                    
            //             $result = 'NG';
            //             array_push($err_list, '['.$line->user_name.']'.$res_body->status.'::'.$res_body->message);                 
            //         }
            //     }
            // }
            // else {
            //     foreach($lines as $line)
            //     {    
            //         $res = $client->request('POST', $API, [
            //             'headers' => ['Authorization'=> 'Bearer '.$line->token, ],
            //             'http_errors' => false,
            //             'multipart' => [
            //                 [ 
            //                     'name' => 'message',
            //                     'contents' => $message
            //                 ]
            //             ]
            //         ]);
                    
            //         $res_body = json_decode($res->getBody());  
            //         if ($res_body->status != 200){                    
            //             $result = 'NG';
            //             array_push($err_list, '['.$line->user_name.']'.$res_body->status.'::'.$res_body->message);                    
            //         }
            //     }
            // }

            

            $end_time = Carbon::now();
            DB::table('histories')->where('id',$history_id )
            ->update(
                [
                    'status'=> $result,
                    'end_at'=> $end_time,
                    'err_info' => empty($err_list) ? 'ー' : join('/', $err_list),
                    'updated_at'=> $end_time
                ]);
        } 
        catch (\Exception $e) {
            \Log::error('エラー機能:即時実行 【店舗ID:'.$store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
        }
    }
}
