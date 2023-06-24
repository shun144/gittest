<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Message;
use \GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Pool;
// use GuzzleHttp\Psr7\Request;

use \GuzzleHttp\Exception\ClientException;
// use App\Models\Template;
use App\Models\History;
use App\Models\Image;
use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use App\Jobs\PostMessageJob;
use Illuminate\Support\Facades\Artisan;

class ScheduleController extends Controller
{
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 即時配信
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function postMessage(Request $request)
    {
        try {
            $inputs = $request->only(['content']);
            $inputs['title'] = $request->has('title') ? $request->only(['title']):'ー';
            $inputs['store_id'] = Auth::user()->store_id;

            $img_path = '';
            if ($request->has('imagefile')){
                $img = $request->file('imagefile')[0];
                $save_name = Storage::disk('owner')->put('', $img);
                $img_path = Storage::disk('owner')->url($save_name);
            }
            
            $inputs['img_path'] = $img_path;

            $history_id = DB::table('histories')
            ->insertGetId(
                [
                    'store_id' => $inputs['store_id'],
                    'title'=> $inputs['title'],
                    'content'=> $inputs['content'],
                    'status'=> '配信待',
                    'img_url' => $img_path,
                    'created_at'=> Carbon::now()
                ]
            );
            $inputs['history_id'] = $history_id;
            PostMessageJob::dispatch($inputs);
        }
        catch (\Exception $e) {
            \Log::error('UserID:'. Auth::user()->id .' 即時投稿Job追加 開始');
            \Log::error($e->getMessage());
        }
    }


    // public function dummy()
    // {

    //     $sep_time = 10;
    //     $API = 'https://notify-api.line.me/api/notify';
    //     $now = Carbon::now();

    //     // 10分単位で切り捨て
    //     $date_down = $now->subMinutes($now->minute % $sep_time);
    //     $date_down = date('Y-m-d H:i', strtotime($date_down));

    //     // 配信対象メッセージ抽出
    //     $messages = DB::table('schedules')
    //     // ->where('plan_at', $date_down)
    //     ->join('messages','schedules.message_id','=','messages.id')
    //     ->leftjoin('images','messages.id','=','images.message_id')
    //     ->select(
    //         'messages.store_id as store_id',
    //         'messages.id as message_id',
    //         'messages.title as title',
    //         'messages.content as content',
    //         'images.save_name as save_name',
    //         )
    //     ->get();

        
    //     // 非同期リクエスト用パラメータリスト作成
    //     $requests_param = [];
    //     foreach($messages as $msg)
    //     {

    //         $history_id = DB::table('histories')
    //         ->insertGetId(
    //             [
    //                 'store_id' => $msg->store_id,
    //                 'title'=> $msg->title,
    //                 'content'=> $msg->content,
    //                 'status'=> '配信中',
    //                 'start_at'=> Carbon::now(),
    //                 'img_url' => $msg->save_name == Null ? Null: url(config('app.access_storage.image').'/'.$msg->save_name),
    //                 'created_at'=> Carbon::now()
    //             ]
    //         );
            
    //         $lines = DB::table('lines')
    //         ->select('id','token', 'user_name')
    //         ->where('is_valid', true)
    //         ->where('store_id', $msg->store_id
    //         )->get();

    //         foreach($lines as $line)
    //         {  
    //             if($msg->save_name == null)
    //             {
    //                 array_push($requests_param,
    //                 [
    //                     // 非同期リクエストの結果を特定するためのキー
    //                     'key' => $history_id. '_' . $msg->message_id . '_' . $line->id,
    //                     'history_id' => $history_id,
    //                     'user_name' => $line->user_name,
    //                     'params' =>  [
    //                         'headers' => ['Authorization'=> 'Bearer '.$line->token, ],
    //                         'http_errors' => false,
    //                         'multipart' => [
    //                             ['name' => 'message','contents' => $msg->content]
    //                         ]
    //                     ]
    //                 ]
    //             );
    //             } else {

    //                 array_push($requests_param,
    //                 [
    //                     'key' => $history_id. '_' . $msg->message_id . '_' . $line->id,
    //                     'history_id' => $history_id,
    //                     'user_name' => $line->user_name,
    //                     'params' =>  [
    //                         'headers' => ['Authorization'=> 'Bearer '.$line->token, ],
    //                         'http_errors' => false,
    //                         'multipart' => [
    //                             ['name' => 'message','contents' => $msg->content],
    //                             ['name' => 'imageFile','contents' => Psr7\Utils::tryFopen(config('app.access_storage.image').'/'.$msg->save_name, 'r')]
    //                         ]
    //                     ]
    //                 ]);
    //             }
    //         }
    //     }

    //     ini_set("max_execution_time",0);
    //     $client = new Client();
    //     $requests = function ($requests_param) use ($client, $API) {
    //         foreach ($requests_param as $param) {
    //             yield function() use ($client, $API, $param) {
    //                 return $client->requestAsync('POST', $API, $param['params']);
    //             };
    //         }
    //     };

    //     $contents = [];
    //     $pool = new Pool($client, $requests($requests_param), [
    //         'concurrency' => 10,
    //         'fulfilled' => function ($response, $index) use ($requests_param, &$contents) {
    //             $contents[$requests_param[$index]['key']] = [
    //               'html'             => $response->getBody()->getContents(),
    //               'status_code'      => $response->getStatusCode(),
    //               'response_header'  => $response->getHeaders()
    //             ];

    //             $contents[$requests_param[$index]['key']]['history_id'] = $requests_param[$index]['history_id'];
    //             $contents[$requests_param[$index]['key']]['user_name'] = $requests_param[$index]['user_name'];
    //         },
    //         'rejected' => function ($reason, $index) use ($requests_param, &$contents) {
    //             $contents[$requests_param[$index]['key']] = [
    //               'html'   => '',
    //               'reason' => $reason
    //             ];
    //             $contents[$requests_param[$index]['key']]['history_id'] = $requests_param[$index]['history_id'];
    //             $contents[$requests_param[$index]['key']]['user_name'] = $requests_param[$index]['user_name'];
    //         },
    //     ]);
    //     $promise = $pool->promise();
    //     $promise->wait();



    //     function group_by(array $table, string $key): array
    //     {
    //         $groups = [];
    //         foreach ($table as $row) {
    //             $groups[$row[$key]][] = $row;
    //         }
    //         return $groups;
    //     }

    //     $history_group = group_by($contents, 'history_id');

    //     foreach ($history_group as $key => $value)
    //     {
    //         $result = 'OK';
    //         $err = 'ー';
            
    //         $res = array_map(function ($col) {
    //             $json = json_decode($col['html']);
    //             return '['.$col['user_name'].']'.$json->status.'::'.$json->message;
    //         }, array_filter($value, function ($col) {
    //             return $col['status_code'] != '200';
    //         }));

    //         if ($res)
    //         {
    //             $result = 'NG';
    //             $err = join('/', $res);
    //         }
            
    //         DB::table('histories')->where('id',$key)
    //         ->update(
    //             [
    //                 'status'=> $result,
    //                 'end_at'=> Carbon::now(),
    //                 'err_info' => $err,
    //                 'updated_at'=> Carbon::now()
    //             ]);
    //     }
    //     return redirect(route('owner.schedule'));
    // }



    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // スケジュール取得
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function getSchedule(Request $request)
    {   
        try {
            $start_date = date('Y-m-d 00:00:00', $request->input('start_date') / 1000);
            $end_date = date('Y-m-d 23:59:59', $request->input('end_date') / 1000);

            // $event_end_date = '2023-06-14 00:00:00';
            $data = Message::select(
                'messages.id as id',
                'messages.title as title',
                'schedules.plan_at as start',
                DB::raw('DATE_FORMAT(DATE_ADD(schedules.plan_at, INTERVAL 1 DAY), "%Y-%m-%d 00:00:00") as end'),
                'messages.title_color as backgroundColor',
                'messages.title_color as borderColor',
                DB::raw("'true' as allDay"),
                'messages.content as content',
                'schedules.plan_at as plan_at',
            )->where('messages.store_id', Auth::user()->store_id)
            ->join('schedules','schedules.message_id','=','messages.id')
            ->whereNull('schedules.deleted_at')
            ->where('plan_at', '>', $start_date)
            ->where('plan_at', '<', $end_date)
            ->with(['images' => function ($query) {
                $query->select('message_id','images.id as image_id', 'save_name', 'org_name');
            }])
            ->get();
            return $data;
        }
        catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }


    public function getTemplateDetail()
    {
        try {
            $data = Message::select('messages.id', 'title', 'title_color', 'content')
            ->join('templates','templates.message_id','=','messages.id')
            ->where('store_id', Auth::user()->store_id)
            ->whereNull('templates.deleted_at')
            ->with(['images' => function ($query) {
                $query->select('message_id','images.id as image_id', 'save_name', 'org_name');
            }])
            ->get();
            return $data;
        }
        catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }


    public function insertTemplate(Request $request)
    {
        $post = $request->only(['title','content','title_color']);
        $images = $request->file('imagefile');
        $para = array_merge($post,array('images'=>$images));
        $user = Auth::user();
        DB::transaction(function() use($para, $user)
        {
            $msg_id = Message::insertGetId(
                [
                    'user_id' => $user->id,
                    'store_id' => $user->store_id,
                    'title'=> $para['title'],
                    'title_color' => strtoupper($para['title_color']),
                    'content'=> $para['content']
                ]
            );
            DB::table('templates')->insert(['message_id' => $msg_id]);
            
            if ($para['images'])
            {
                foreach ($para['images'] as $img)
                {
                    $save_name = Storage::disk('owner')->put('', $img);
                    $org_name = $img->getClientOriginalName();                                   
                    Image::insert(['message_id' => $msg_id, 'save_name' => $save_name, 'org_name' => $org_name]);

                }
            }
        });
        return redirect(route('owner.schedule'));
    }



    // public function insertTemplate(Request $request)
    // {
    //     $post = $request->only(['title','content','title_color']);
    //     $images = $request->file('imagefile');
    //     $para = array_merge($post,array('images'=>$images));
    //     $user = Auth::user();
    //     DB::transaction(function() use($para, $user)
    //     {
    //         $msg_id = Message::insertGetId(
    //             [
    //                 'user_id' => $user->id,
    //                 'store_id' => $user->store_id,
    //                 'title'=> $para['title'],
    //                 'title_color' => strtoupper($para['title_color']),
    //                 'content'=> $para['content']
    //             ]
    //         );
    //         DB::table('templates')->insert(['message_id' => $msg_id]);
            
    //         if ($para['images'])
    //         {
    //             foreach ($para['images'] as $img)
    //             {
    //                 $save_path = Storage::putFile(config('app.save_storage.image'), $img);
    //                 $save_name = basename($save_path);
    //                 $org_name = $img->getClientOriginalName();                    
    //                 Image::insert(['message_id' => $msg_id, 'save_name' => $save_name, 'org_name' => $org_name]);
    //             }
    //         }
    //     });
    //     return redirect(route('owner.schedule'));
    // }



    public function insertSchedule(Request $request)
    {
        $post = $request->only(['title','content','title_color','has_file','image_id', 'date', 'hh', 'mm']);
        $images = $request->file('imagefile');

        $datatime = sprintf('%s %s:%s:00', $post['date'], $post['hh'], $post['mm']);
        $user = Auth::user();

    
        $message_id = DB::transaction(function() use($post, $images, $user, $datatime)
        {
            $msg_id = Message::insertGetId(
                [
                    'user_id' => $user->id,
                    'store_id' => $user->store_id,
                    'title'=> $post['title'],
                    'title_color' => strtoupper($post['title_color']),
                    'content'=> $post['content']
                ]
            );
            DB::table('schedules')->insert(
                [
                    'message_id' => $msg_id,
                    'plan_at' => $datatime
                ]
            );

            \Log::info($images);

            if($images)
            {
                $img = $images[0];
                $save_name = Storage::disk('owner')->put('', $img);
                $org_name = $img->getClientOriginalName();                         
                Image::insert(['message_id' => $msg_id, 'save_name' => $save_name, 'org_name' => $org_name]);
            }
            else {
                if ($post['has_file'] == '1'){
                    $imgIdList = explode(",",$post['image_id']);
                    if ($imgIdList)
                    {
                        foreach ($imgIdList as $imgId)
                        {
                            $get_img = DB::table('images')
                            ->select('save_name','org_name')
                            ->where('id',$imgId)
                            ->first();              
                            Image::insert(
                                [
                                    'message_id' => $msg_id,
                                    'save_name' => $get_img->save_name,
                                    'org_name' => $get_img->org_name
                                ]);
                        }
                    }
                }
            }
            return $msg_id;
        });

        $data = Message::select(
            'messages.id as id',
            'messages.title as title',
            'schedules.plan_at as start',
            DB::raw('DATE_FORMAT(DATE_ADD(schedules.plan_at, INTERVAL 1 DAY), "%Y-%m-%d 00:00:00") as end'),
            'messages.title_color as backgroundColor',
            'messages.title_color as borderColor',
            DB::raw("'true' as allDay"),
            'messages.content as content',
            'schedules.plan_at as plan_at',
        )->where('messages.id', $message_id)
        ->join('schedules','schedules.message_id','=','messages.id')
        ->with(['images' => function ($query) {
            $query->select('message_id','images.id as image_id', 'save_name', 'org_name');
        }])
        ->first();
        return $data;
        // return redirect(route('owner.schedule'));
    }




    // public function insertSchedule(Request $request)
    // {
    //     $post = $request->only(['title','content','title_color','has_file','image_id', 'date', 'hh', 'mm']);
    //     $images = $request->file('imagefile');

    //     $datatime = sprintf('%s %s:%s:00', $post['date'], $post['hh'], $post['mm']);
    //     $user = Auth::user();
    
    //     DB::transaction(function() use($post, $images, $user, $datatime)
    //     {
    //         $msg_id = Message::insertGetId(
    //             [
    //                 'user_id' => $user->id,
    //                 'store_id' => $user->store_id,
    //                 'title'=> $post['title'],
    //                 'title_color' => strtoupper($post['title_color']),
    //                 'content'=> $post['content']
    //             ]
    //         );
    //         DB::table('schedules')->insert(
    //             [
    //                 'message_id' => $msg_id,
    //                 'plan_at' => $datatime
    //             ]
    //         );

    //         if($images)
    //         {
    //             foreach ($images as $img)
    //             {
    //                 // $save_path = Storage::putFile(config('app.save_storage.image'), $img);
    //                 // $save_name = basename($save_path);
    //                 // $org_name = $img->getClientOriginalName();        
    //                 $save_name = Storage::disk('owner')->put('', $img);
    //                 $org_name = $img->getClientOriginalName();                         
    //                 Image::insert(['message_id' => $msg_id, 'save_name' => $save_name, 'org_name' => $org_name]);
    //             }
    //         }
    //         else {
    //             if ($post['has_file'] == '1'){
    //                 $imgIdList = explode(",",$post['image_id']);
    //                 if ($imgIdList)
    //                 {
    //                     foreach ($imgIdList as $imgId)
    //                     {
    //                         $get_img = DB::table('images')
    //                         ->select('save_name','org_name')
    //                         ->where('id',$imgId)
    //                         ->first();              
    //                         Image::insert(
    //                             [
    //                                 'message_id' => $msg_id,
    //                                 'save_name' => $get_img->save_name,
    //                                 'org_name' => $get_img->org_name
    //                             ]);
    //                     }
    //                 }
    //             }
    //         }
    //     });  
    //     return redirect(route('owner.schedule'));
    // }


    public function updateSchedule(Request $request)
    {          
        $post = $request->only(['message_id', 'title', 'content','title_color','has_file', 'date', 'hh', 'mm']);
        $datatime = sprintf('%s %s:%s:00', $post['date'], $post['hh'], $post['mm']);           
        $images = $request->file('imagefile');
        
        DB::transaction(function() use($post, $datatime, $images){
            DB::table('messages')
            ->where('id',$post['message_id'])
            ->update([
                'title' => $post['title'],
                'content' => $post['content'],
                'title_color' => $post['title_color'],
            ]);

            DB::table('schedules')
            ->where('message_id',$post['message_id'])
            ->update(['plan_at' => $datatime, ]);
    
            $dt_images = DB::table('images')->where('message_id', $post['message_id']);
        
            // ファイル保持フラグあり
            if ($post['has_file'] == '1'){
                
                if ($images) {
                    $dt_images->delete();
                    foreach ($images as $img){
                        $save_name = Storage::disk('owner')->put('', $img);
                        $org_name = $img->getClientOriginalName();             
                        DB::table('images')->insert([
                            'message_id' => $post['message_id'],
                            'save_name' => $save_name,
                            'org_name' => $org_name
                        ]);
                    }
                }
            // ファイル保持フラグなし
            } else {    
                // 既に登録されている画像を削除
                if ($dt_images->count())
                {
                    $dt_images->delete();
                }
            }
        });

        $data = Message::select(
            'messages.id as id',
            'messages.title as title',
            'schedules.plan_at as start',
            DB::raw('DATE_FORMAT(DATE_ADD(schedules.plan_at, INTERVAL 1 DAY), "%Y-%m-%d 00:00:00") as end'),
            'messages.title_color as backgroundColor',
            'messages.title_color as borderColor',
            DB::raw("'true' as allDay"),
            'messages.content as content',
            'schedules.plan_at as plan_at',
        )->where('messages.id', $post['message_id'])
        ->join('schedules','schedules.message_id','=','messages.id')
        ->with(['images' => function ($query) {
            $query->select('message_id','images.id as image_id', 'save_name', 'org_name');
        }])
        ->first();
        return $data;

        // return redirect(route('owner.schedule'));
    }



    // public function updateSchedule(Request $request)
    // {          
    //     $post = $request->only(['message_id', 'title', 'content','title_color','has_file', 'date', 'hh', 'mm']);
    //     $datatime = sprintf('%s %s:%s:00', $post['date'], $post['hh'], $post['mm']);           
    //     $images = $request->file('imagefile');
        
    //     // dd($post);
    //     // \Log::info('Something happends');

    //     DB::transaction(function() use($post, $datatime, $images){
    //         DB::table('messages')
    //         ->where('id',$post['message_id'])
    //         ->update([
    //             'title' => $post['title'],
    //             'content' => $post['content'],
    //             'title_color' => $post['title_color'],
    //         ]);

    //         DB::table('schedules')
    //         ->where('message_id',$post['message_id'])
    //         ->update(['plan_at' => $datatime, ]);
    
    //         $dt_images = DB::table('images')->where('message_id', $post['message_id']);
        
    //         // ファイル保持フラグあり
    //         if ($post['has_file'] == '1'){
                
    //             if ($images) {
    //                 $dt_images->delete();
    //                 foreach ($images as $img){
    //                     // $save_path = Storage::putFile(config('app.save_storage.image'), $img);
    //                     // $save_name = basename($save_path);
    //                     // $org_name = $img->getClientOriginalName();
    //                     $save_name = Storage::disk('owner')->put('', $img);
    //                     $org_name = $img->getClientOriginalName();             
    //                     DB::table('images')->insert([
    //                         'message_id' => $post['message_id'],
    //                         'save_name' => $save_name,
    //                         'org_name' => $org_name
    //                     ]);
    //                 }
    //             }
    //         // ファイル保持フラグなし
    //         } else {    
    //             // 既に登録されている画像を削除
    //             if ($dt_images->count())
    //             {
    //                 $dt_images->delete();
    //             }
    //         }
    //     });
    //     return redirect(route('owner.schedule'));
    // }

    public function deleteSchedule(Request $request)
    {
        try {

            $post = $request->only(['message_id']);

            DB::transaction(function () use($post){
                DB::table('messages')
                ->where('id', $post['message_id'])
                ->update(
                    [
                        'deleted_at' => Carbon::now()
                    ]
                );
                DB::table('schedules')
                ->where('message_id', $post['message_id'])
                ->update(
                    [
                        'deleted_at' => Carbon::now()
                    ]
                );
            });

            return $post['message_id'];

            
            // return redirect(route('owner.schedule'))->with('delete_schedule_complate_flushMsg','スケジュールの削除が完了しました');
        }
        catch (\Exception $e) {
            \Log::error('UserID:'. Auth::user()->id .' スケジュール削除');
            \Log::error($e->getMessage());
        }
    }

    // public function deleteSchedule(Request $request)
    // {
    //     try {
    //         \Log::info('UserID:'. Auth::user()->id .' スケジュール削除 開始');

    //         $post = $request->only(['message_id']);

    //         DB::transaction(function () use($post){
    //             DB::table('messages')
    //             ->where('id', $post['message_id'])
    //             ->update(
    //                 [
    //                     'deleted_at' => Carbon::now()
    //                 ]
    //             );
    //             DB::table('schedules')
    //             ->where('message_id', $post['message_id'])
    //             ->update(
    //                 [
    //                     'deleted_at' => Carbon::now()
    //                 ]
    //             );
    //         });

    //         \Log::info('UserID:'. Auth::user()->id .' スケジュール削除 終了');
            
    //         return redirect(route('owner.schedule'))->with('delete_schedule_complate_flushMsg','スケジュールの削除が完了しました');
    //     }
    //     catch (\Exception $e) {
    //         \Log::error($e->getMessage());
    //     }
    // }

    public function updateTemplate(Request $request)
    {  
        try {

            \Log::info('UserID:'. Auth::user()->id .' 定型メッセージ編集 開始');

            $post = $request->only(['message_id', 'title', 'content','title_color','has_file']);           
            $images = $request->file('imagefile');
    
            DB::transaction(function() use($post, $images){
                DB::table('messages')
                ->where('id', $post['message_id'])
                ->update([
                    'title' => $post['title'],
                    'content' => $post['content'],
                    'title_color' => $post['title_color'],
                ]);
        
                $dt_images = DB::table('images')->where('message_id', $post['message_id']);
            
                // ファイル保持フラグあり
                if ($post['has_file'] == '1'){
                    
                    if ($images) {
                        $dt_images->delete();
                        foreach ($images as $img){
                            // $save_path = Storage::putFile(config('app.save_storage.image'), $img);
                            // $save_name = basename($save_path);
                            // $org_name = $img->getClientOriginalName();

                            $save_name = Storage::disk('owner')->put('', $img);
                            $org_name = $img->getClientOriginalName();             

                            DB::table('images')->insert([
                                'message_id' => $post['message_id'],
                                'save_name' => $save_name,
                                'org_name' => $org_name
                            ]);
                        }
                    }
                // ファイル保持フラグなし
                } else {    
                    // 既に登録されている画像を削除
                    if ($dt_images->count())
                    {
                        $dt_images->delete();
                    }
                }
            });

            \Log::info('UserID:'. Auth::user()->id .' 定型メッセージ編集 終了');

            return redirect(route('owner.schedule'))->with('edit_template_complate_flushMsg','定型メッセージの更新が完了しました');
        }
        catch (\Exception $e) {
            \Log::error($e->getMessage());
        }

    }

    public function deleteTemplate(Request $request)
    {
        try {
            \Log::info('UserID:'. Auth::user()->id .' 定型メッセージ削除 開始');

            $post = $request->only(['message_id']);

            DB::transaction(function () use($post){
                DB::table('messages')
                ->where('id', $post['message_id'])
                ->update(
                    [
                        'deleted_at' => Carbon::now()
                    ]
                );
    
                DB::table('templates')
                ->where('message_id', $post['message_id'])
                ->update(
                    [
                        'deleted_at' => Carbon::now()
                    ]
                );
            });

            \Log::info('UserID:'. Auth::user()->id .' 定型メッセージ削除 終了');
            
            return redirect(route('owner.schedule'))->with('delete_template_complate_flushMsg','定型テンプレートの削除が完了しました');
        }
        catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }
}
