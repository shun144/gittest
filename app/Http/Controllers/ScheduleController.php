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
                $image = $request->file('imagefile')[0];
                $save_path = Storage::putFile(config('app.save_storage.image'), $image);
                $save_name = basename($save_path);        
                $img_path = url(config('app.access_storage.image').'/'.$save_name);
            }
            
            $inputs['img_path'] = $img_path;

            $history_id = DB::table('histories')
            ->insertGetId(
                [
                    'store_id' => $inputs['store_id'],
                    'title'=> $inputs['title'],
                    'content'=> $inputs['content'],
                    'status'=> '配信待ち',
                    'img_url' => $img_path,
                    'created_at'=> Carbon::now()
                ]
            );
            $inputs['history_id'] = $history_id;
            PostMessageJob::dispatch($inputs);
        }
        catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // スケジュール取得
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function getSchedule()
    {   
        try {
            $data = Message::select(
                'messages.id as id',
                'messages.title as title',
                'schedules.plan_at as start',
                'messages.title_color as backgroundColor',
                'messages.title_color as borderColor',
                DB::raw("'true' as allDay"),
                'messages.content as content',
                'schedules.plan_at as plan_at',
            )->where('messages.store_id', Auth::user()->store_id)
            ->join('schedules','schedules.message_id','=','messages.id')
            ->whereNull('schedules.deleted_at')
            ->whereNotNull('schedules.plan_at')
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
                    $save_path = Storage::putFile(config('app.save_storage.image'), $img);
                    $save_name = basename($save_path);
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




    // public function testPost()
    // {
    //     $message = 'テストメッセージ';
    //     $image_path = 'C:\WebApp\work\20230430\work\login.jpg';
        
    //     $API = 'https://notify-api.line.me/api/notify';
    //     $store_id = Auth::user()->store_id;
    //     $lines = DB::table('lines')->select('id','token')->where('store_id', $store_id)->get();
    //     $line = $lines[0];
    //     $img_path = 'https://img07.shop-pro.jp/PA01423/383/product/135946838.jpg';
    //     $cfile = new \CURLFile($img_path);
    //     $data = array(
    //         'message' => ' ',
    //         'imageFile' => $cfile
    //     );

    //     $chOne = curl_init();
    //     curl_setopt($chOne, CURLOPT_URL, $API); 
    //     // curl_setopt($chOne, CURLOPT_SSL_VERIFYHOST, 0);
    //     // curl_setopt($chOne, CURLOPT_SSL_VERIFYPEER, 0);
    //     curl_setopt($chOne, CURLOPT_POST, 1);
    //     curl_setopt($chOne, CURLOPT_POSTFIELDS, $data);
    //     $headers = array('Content-Type: multipart/form-data','Authorization: Bearer ' . $line->token);
    //     curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($chOne, CURLOPT_RETURNTRANSFER, 1);
    //     $result = curl_exec($chOne);
    //     // dd($result);

    //     return redirect(route('owner.schedule'));
    // }


    // public function testPost()
    // {
    //     $message = 'テストメッセージ';
    //     $image_path = 'C:\WebApp\work\20230430\work\login.jpg';
        
    //     $API = 'https://notify-api.line.me/api/notify';
    //     $store_id = Auth::user()->store_id;
    //     $lines = DB::table('lines')->select('id','token')->where('store_id', $store_id)->get();
    //     $line = $lines[0];
    //     $img_path = 'https://img07.shop-pro.jp/PA01423/383/product/135946838.jpg';
    //     $cfile = new \CURLFile($img_path);
    //     $data = array(
    //         'message' => 'テスト送信',
    //         'imageFile' => $cfile
    //     );

    //     $chOne = curl_init();
    //     curl_setopt($chOne, CURLOPT_URL, $API); 
    //     // curl_setopt($chOne, CURLOPT_SSL_VERIFYHOST, 0);
    //     // curl_setopt($chOne, CURLOPT_SSL_VERIFYPEER, 0);
    //     curl_setopt($chOne, CURLOPT_POST, 1);
    //     curl_setopt($chOne, CURLOPT_POSTFIELDS, $data);
    //     $headers = array('Content-Type: multipart/form-data','Authorization: Bearer ' . $line->token);
    //     curl_setopt($chOne, CURLOPT_HTTPHEADER, $headers);
    //     curl_setopt($chOne, CURLOPT_RETURNTRANSFER, 1);
    //     $result = curl_exec($chOne);

    //     dd($result);
        
    //     return redirect(route('owner.schedule'));

    // }










    public function insertSchedule(Request $request)
    {
        $post = $request->only(['title','content','title_color','has_file','image_id', 'date', 'hh', 'mm']);
        $images = $request->file('imagefile');

        $datatime = sprintf('%s %s:%s:00', $post['date'], $post['hh'], $post['mm']);
        $user = Auth::user();
    
        DB::transaction(function() use($post, $images, $user, $datatime)
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

            if($images)
            {
                foreach ($images as $img)
                {
                    $save_path = Storage::putFile(config('app.save_storage.image'), $img);
                    $save_name = basename($save_path);
                    $org_name = $img->getClientOriginalName();                    
                    Image::insert(['message_id' => $msg_id, 'save_name' => $save_name, 'org_name' => $org_name]);
                }
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
        });  
        return redirect(route('owner.schedule'));
    }




    public function updateSchedule(Request $request)
    {          
        $post = $request->only(['message_id', 'title', 'content','title_color','has_file', 'date', 'hh', 'mm']);
        $datatime = sprintf('%s %s:%s:00', $post['date'], $post['hh'], $post['mm']);           
        $images = $request->file('imagefile');
        
        // dd($post);
        // \Log::info('Something happends');

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
                        $save_path = Storage::putFile(config('app.save_storage.image'), $img);
                        $save_name = basename($save_path);
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
        return redirect(route('owner.schedule'));
    }









    public function updateTemplate(Request $request)
    {  
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
                        $save_path = Storage::putFile(config('app.save_storage.image'), $img);
                        $save_name = basename($save_path);
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
        return redirect(route('owner.schedule'))->with('edit_template_complate_flushMsg','定型テンプレートの更新が完了しました');
    }

    public function deleteTemplate(Request $request)
    {
        dd('test');
        try {
            $post = $request->only(['template_id']);

            Template::find($post['template_id'])->delete();
    
            return redirect(route('owner.schedule'))->with('delete_template_complate_flushMsg','定型テンプレートの削除が完了しました');
        }
        catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }






    // public function getEventInfo(Request $request)
    // {
    //     $messages = Message::select('id', 'title', 'content', 'title_color')
    //     ->where('id', $request->id)
    //     ->whereNull('deleted_at')
    //     ->with(['images' => function ($query) {
    //         $query->select('message_id', 'id as image_id', 'save_name', 'org_name');
    //     }])->first();
    //     return $messages;
    // }



    public function _getSchedule()
    {
        return [
            [
                'title' => 'All Day Event',
                'start' => '2023-05-31 10:00:00',
                // 'end'   => '2023-05-20 10:10:00',
                'backgroundColor'=> '#f56954', //red
                'borderColor'    => '#f56954', //red
                // 'allDay'         => true
                'content'=>'finished'
            ],
            [
                'title' => 'second Day Event',
                'start' => '2023-05-31 10:00:00',
                // 'end'   => '2023-05-20 10:10:00',
                'backgroundColor'=> '#f56954', //red
                'borderColor'    => '#f56954', //red
                // 'allDay'         => true
                'content'=>'finished'
            ],
            // [
            //     'title'          => 'Long Event',
            //     'start'          => '2023-05-25 10:00:00',
            //     'end'            => '2023-05-25 10:00:00',
            //     'backgroundColor'=> '#f39c12', //yellow
            //     'borderColor'    => '#f39c12', //yellow
            //     'status'=>'finished'
            // ],  
            // [
            //     'title' => 'シルバーウィーク旅行',
            //     'description' => '人気の旅館の予約が取れた',
            //     'start' => '2021-09-20 10:00:00',
            //     'end'   => '2021-09-22 18:00:00',
            //     'url'   => 'https://admin.juno-blog.site',
            //     'status'=>'finished'
            // ],
            // [
            //     'title' => '給料日',
            //     'start' => '2021-09-30',
            //     'color' => '#ff44cc',
            //     'status'=>'finished'
            // ],
        ];

        // $date = [
        //     [
        //         title          => 'All Day Event',
        //         start          => new Date(y, m, 2),
        //         backgroundColor=> '#f56954', //red
        //         borderColor    => '#f56954', //red
        //         allDay         => true
        //     ],
        //     [
        //         title          => 'Long Event',
        //         start          => new Date(y, m, d - 5),
        //         end            => new Date(y, m, d - 2),
        //         backgroundColor=> '#f39c12', //yellow
        //         borderColor    => '#f39c12' //yellow
        //       ],
        //       [
        //         title          => 'shun',
        //         start          => new Date(y, m, d, 10, 30),
        //         allDay         => false,
        //         backgroundColor=> '#0073b7', //Blue
        //         borderColor    => '#0073b7' //Blue
        //       ],
        //       [
        //         title          => 'Lunch',
        //         start          => new Date(y, m, d, 12, 00),
        //         end            => new Date(y, m, d, 14, 0),
        //         allDay         => false,
        //         backgroundColor=> '#00c0ef', //Info (aqua)
        //         borderColor    => '#00c0ef' //Info (aqua)
        //       ],
        //       [
        //         title          => 'Birthday Party',
        //         start          => new Date(y, m, d + 1, 19, 0),
        //         end            => new Date(y, m, d + 1, 22, 30),
        //         allDay         => false,
        //         backgroundColor=> '#00a65a', //Success (green)
        //         borderColor    => '#00a65a' //Success (green)
        //       ],
        //       [
        //         title          => 'Click for Google',
        //         start          => new Date(y, m, 28),
        //         end            => new Date(y, m, 29),
        //         url            => 'https=>//www.google.com/',
        //         backgroundColor=> '#3c8dbc', //Primary (light-blue)
        //         borderColor    => '#3c8dbc' //Primary (light-blue)
        //       ]
        // ];
        // echo json_encode($data);


    //   $data = [];
    //   $authUser = Auth::user()->id;
    //   $events = Event::where("user_id", "=", $authUser)->get();
    //   $data = $events;  
    //   echo json_encode($data);
    }
}
