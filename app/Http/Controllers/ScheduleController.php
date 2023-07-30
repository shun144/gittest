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
            $now = Carbon::now();
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

            // \Log::info('★★確認用'.$img_path);

            $history_id = DB::table('histories')
            ->insertGetId(
                [
                    'store_id' => $inputs['store_id'],
                    'title'=> $inputs['title'],
                    'content'=> $inputs['content'],
                    'status'=> '配信待',
                    'img_url' => $img_path,
                    'created_at'=> $now,
                    'updated_at'=> $now
                ]
            );
            $inputs['history_id'] = $history_id;
            PostMessageJob::dispatch($inputs);
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:即時配信job追加【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
        }
    }


    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // スケジュール取得
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function getSchedule(Request $request)
    {   
        try {
            $start_date = date('Y-m-d 00:00:00', $request->input('start_date') / 1000);
            $end_date = date('Y-m-d 23:59:59', $request->input('end_date') / 1000);

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
            \Log::error('エラー機能:スケジュール取得 【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
        }
    }


    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 定型メッセージ詳細取得
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
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
            \Log::error('エラー機能:定型メッセージ詳細取得 【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
            return response()->json([
                'message' => '定型メッセージ詳細取得エラー'
            ], 500);
        }
    }

    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 定型メッセージ追加
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function insertTemplate(Request $request)
    {
        $post = $request->only(['title','content','title_color']);
        $images = $request->file('imagefile');
        
        try {
            $now = Carbon::now();

            $para = array_merge($post,array('images'=>$images));
            $user = Auth::user();
            DB::transaction(function() use($para, $user, $now)
            {
                $msg_id = Message::insertGetId(
                    [
                        'user_id' => $user->id,
                        'store_id' => $user->store_id,
                        'title'=> $para['title'],
                        'title_color' => strtoupper($para['title_color']),
                        'content'=> $para['content'],
                        'created_at'=> $now,
                        'updated_at'=> $now
                    ]
                );
                DB::table('templates')->insert(
                    [
                        'message_id' => $msg_id,
                        'created_at'=> $now,
                        'updated_at'=> $now
                    ]);
                
                if ($para['images'])
                {
                    $img = $para['images'][0];
                    $save_name = Storage::disk('owner')->put('', $img);
                    $org_name = $img->getClientOriginalName();                                   
                    Image::insert(
                        [
                            'message_id' => $msg_id,
                            'save_name' => $save_name,
                            'org_name' => $org_name,
                            'created_at'=> $now
                        ]);
                }
            });
            return redirect(route('owner.schedule'))->with('add_template_success_flushMsg','定型メッセージの追加が完了しました');
        }
        catch (\Exception $e) {
            $img_info = $images != null ? $images[0]->getClientOriginalName() : 'なし';
            \Log::error('エラー機能:定型メッセージ追加 【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('パラメータ:'.join('/', $post).'/画像:'.$img_info);
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
            return redirect(route('owner.schedule'))->with('add_template_error_flushMsg','定型メッセージ追加に失敗しました');
        }
    }

    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 定型メッセージ更新
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function updateTemplate(Request $request)
    {  
        $post = $request->only(['message_id', 'title', 'content','title_color','has_file']);           
        $images = $request->file('imagefile');
        try {    
            $now = Carbon::now();
            DB::transaction(function() use($post, $images, $now){
                DB::table('messages')
                ->where('id', $post['message_id'])
                ->update([
                    'title' => $post['title'],
                    'content' => $post['content'],
                    'title_color' => $post['title_color'],
                    'updated_at' => $now
                ]);

                DB::table('templates')
                ->where('message_id', $post['message_id'])
                ->update([
                    'updated_at' => $now
                ]);

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
                                'org_name' => $org_name,
                                'created_at' => $now
                            ]);
                        }
                    }
                // ファイル保持フラグなし
                } else {    
                    // 既に登録されている画像レコードを削除
                    if ($dt_images->count())
                    {
                        $dt_images->delete();
                    }
                }
            });
            return redirect(route('owner.schedule'))->with('edit_template_success_flushMsg','定型メッセージの更新が完了しました');
        }
        catch (\Exception $e) {
            $img_info = $images != null ? $images[0]->getClientOriginalName() : 'なし';
            \Log::error('エラー機能:定型メッセージ更新 【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('パラメータ:'.join('/', $post).'/画像:'.$img_info);
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
            return redirect(route('owner.schedule'))->with('edit_template_error_flushMsg','定型メッセージ更新に失敗しました');
        }
    }


    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 定型メッセージ削除
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function deleteTemplate(Request $request)
    {
        $post = $request->only(['message_id']);
        try {
            $now = Carbon::now();
            DB::transaction(function () use($post, $now){
                DB::table('messages')
                ->where('id', $post['message_id'])
                ->update(
                    [
                        'updated_at' => $now,
                        'deleted_at' => $now
                    ]
                );
    
                DB::table('templates')
                ->where('message_id', $post['message_id'])
                ->update(
                    [
                        'updated_at' => $now,
                        'deleted_at' => $now
                    ]
                );
            });
            
            return redirect(route('owner.schedule'))->with('del_template_success_flushMsg','定型メッセージの削除が完了しました');
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:定型メッセージ削除 【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('パラメータ:'.join('/', $post));
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
            return redirect(route('owner.schedule'))->with('del_template_error_flushMsg','定型メッセージ削除に失敗しました');
        }
    }


    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // スケジュール追加
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function insertSchedule(Request $request)
    {
        $post = $request->only(['title','content','title_color','has_file','image_id', 'date', 'hh', 'mm']);
        $images = $request->file('imagefile');
        $datatime = sprintf('%s %s:%s:00', $post['date'], $post['hh'], $post['mm']);

        try {

            $now = Carbon::now();
            $user = Auth::user();
            $message_id = DB::transaction(function() use($post, $images, $user, $datatime, $now)
            {
                $msg_id = Message::insertGetId(
                    [
                        'user_id' => $user->id,
                        'store_id' => $user->store_id,
                        'title'=> $post['title'],
                        'title_color' => strtoupper($post['title_color']),
                        'content'=> $post['content'],
                        'created_at'=> $now,
                        'updated_at'=> $now
                    ]
                );
                DB::table('schedules')->insert(
                    [
                        'message_id' => $msg_id,
                        'plan_at' => $datatime,
                        'created_at'=> $now,
                        'updated_at'=> $now
                    ]
                );

                if($images)
                {
                    $img = $images[0];
                    $save_name = Storage::disk('owner')->put('', $img);
                    $org_name = $img->getClientOriginalName();                         
                    Image::insert([
                        'message_id' => $msg_id, 
                        'save_name' => $save_name, 
                        'org_name' => $org_name,
                        'created_at'=> $now,
                    ]);
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
                                        'org_name' => $get_img->org_name,
                                        'created_at'=> $now
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
        }
        catch (\Exception $e) {
            $img_info = $images != null ? $images[0]->getClientOriginalName() : 'なし';
            \Log::error('エラー機能:スケジュール追加 【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('パラメータ:'.join('/', $post).'/画像:'.$img_info);
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
            return response()->json([
                'message' => 'スケジュール追加エラー'
            ], 500);
        }
    }



    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // スケジュール更新
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function updateSchedule(Request $request)
    {          
        $post = $request->only(['message_id', 'title', 'content','title_color','has_file', 'date', 'hh', 'mm']);
        $datatime = sprintf('%s %s:%s:00', $post['date'], $post['hh'], $post['mm']);           
        $images = $request->file('imagefile');
        
        try {


            $now = Carbon::now();
            DB::transaction(function() use($post, $datatime, $images, $now){
                DB::table('messages')
                ->where('id',$post['message_id'])
                ->update([
                    'title' => $post['title'],
                    'content' => $post['content'],
                    'title_color' => $post['title_color'],
                    'updated_at' => $now
                ]);
    
                DB::table('schedules')
                ->where('message_id',$post['message_id'])
                ->update([
                    'plan_at' => $datatime,
                    'updated_at' => $now
                ]);
        
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
                                'org_name' => $org_name,
                                'created_at' => $now
                            ]);
                        }
                    }
                // ファイル保持フラグなし
                } else {    
                    // 既に登録されている画像レコードを削除
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
        }
        catch (\Exception $e) {
            $img_info = $images != null ? $images[0]->getClientOriginalName() : 'なし';
            \Log::error('エラー機能:スケジュール更新【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('パラメータ:'.join('/', $post).'/画像:'.$img_info);
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
            return response()->json([
                'message' => 'スケジュール更新エラー'
            ], 500);
        }
    }

    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // スケジュール削除
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function deleteSchedule(Request $request)
    {        
        $post = $request->only(['message_id']);
        try {
            $now = Carbon::now();

            DB::transaction(function () use($post, $now){
                DB::table('messages')
                ->where('id', $post['message_id'])
                ->update(
                    [
                        'updated_at' => $now,
                        'deleted_at' => $now
                    ]
                );
                DB::table('schedules')
                ->where('message_id', $post['message_id'])
                ->update(
                    [
                        'updated_at' => $now,
                        'deleted_at' => $now
                    ]
                );
            });
            return $post['message_id'];

        }
        catch (\Exception $e) {
            \Log::error('エラー機能:スケジュール削除【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
            return response()->json([
                'message' => 'スケジュール削除エラー'
            ], 500);
        }
    }
}
