<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Http\Requests\StoreRequest;
use App\Models\User;
use App\Models\Schedule;
use \GuzzleHttp\Client;
use \GuzzleHttp\Exception\ClientException;
use App\Models\Line;
use App\Models\Store;
use App\Models\Message;
use App\Models\History;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 店舗一覧表示
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function viewStore()
    {
        try {

            $stores = DB::table('stores')
            ->select(
                'stores.id as store_id',
                'stores.name',
                'stores.url_name',
                'users.id as user_id',
                'users.login_id',
                'stores.client_id',
                'stores.client_secret',
            )
            ->join('users','users.store_id','=','stores.id')
            ->where('users.role','owner')
            ->whereNull('stores.deleted_at')
            ->latest('stores.created_at')
            ->get();
            return view('admin.store', compact('stores'));
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:店舗一覧表示');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());

            $get_store_error_flushMsg = '店舗一覧取得に失敗しました';

            return view('admin.store', compact('get_store_error_flushMsg'));
        }
    }

    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 店舗追加画面表示
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function viewAddStore()
    {
        try {
            return view('admin.add_store');
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:店舗追加画面表示');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
            return view('admin.add_store');
        }
    }


    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 店舗追加
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function insertStore(StoreRequest $request)
    {      
        $post = $request->only(['name', 'url_name', 'login_id', 'login_password', 'client_id', 'client_secret']);
        try {
            $now = Carbon::now();
            DB::transaction(function () use($post, $now){
                $store_id = DB::table('stores')->insertGetId(
                    [
                        'name'=> $post['name'],
                        'url_name'=> $post['url_name'],
                        'client_id'=> $post['client_id'],
                        'client_secret'=> $post['client_secret'],
                        'created_at'=>$now,
                        'updated_at'=>$now,
                    ]);
                
                DB::table('users')->insert(
                    [
                        'name'=> $post['name'],
                        'login_id'=> $post['login_id'],
                        'password'=> Hash::make($post['login_password']),
                        'role' => 'owner',
                        'store_id' => $store_id,
                        'created_at'=>$now,
                        'updated_at'=>$now,
                ]);
            });
            return redirect(route('admin.store'))->with('flash_message','店舗登録が完了しました');
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:店舗追加');
            \Log::error('パラメータ:'.join('/', $post));
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
            return redirect(route('admin.store'))->with('error_flash_message','店舗登録に失敗しました');
        }
    }


    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 店舗編集画面表示
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function viewEditStore(Request $request)
    {
        $store_id = $request->query('store_id');
        try {

            $store = DB::table('stores')
            ->select(
                'stores.id as store_id',
                'stores.name',
                'stores.url_name',
                'users.id as user_id',
                'users.login_id',
                'stores.client_id',
                'stores.client_secret',
            )
            ->join('users','users.store_id','=','stores.id')
            ->where('stores.id',$store_id)
            ->first();
            return view('admin.edit_store', compact('store'));
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:店舗編集画面表示【対象店舗ID:'.$store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());


            $edit_store_error_flushMsg = '店舗情報取得に失敗しました';
            return view('admin.edit_store', compact('edit_store_error_flushMsg'));
        }
    }


    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 店舗編集
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function updateStore(StoreRequest $request)
    {        
        $post = $request->only(['user_id','store_id', 'name', 'url_name', 'login_id', 
        'login_password','client_id','client_secret']);

        try {
        

            $now = Carbon::now();
            DB::transaction(function () use($post, $now){
                DB::table('stores')
                ->where('id', $post['store_id'])
                ->update([
                    'name' => $post['name'],
                    'url_name' => $post['url_name'],
                    'client_id' => $post['client_id'],
                    'client_secret' => $post['client_secret'],
                    'updated_at'=>$now,
                ]);
    
                if ($post['login_password']) {
                    DB::table('users')
                    ->where('id', $post['user_id'])
                    ->update([
                        'login_id' => $post['login_id'],
                        'password' => Hash::make($post['login_password']),
                        'updated_at'=>$now,
                    ]);
                }else {
                    DB::table('users')
                    ->where('id', $post['user_id'])
                    ->update([
                        'login_id' => $post['login_id'],
                        'updated_at'=>$now,
                    ]);  
                }
            });
    
            return redirect(route('store.edit.view', ['store_id' => $post['store_id']]))
            ->with('flash_message','店舗情報更新が完了しました');
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:店舗編集');
            \Log::error('パラメータ:'.join('/', $post));
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());

            return redirect(route('store.edit.view'))
            ->with('error_flash_message','店舗情報更新に失敗しました');
        }
    }


    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 店舗削除
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function deleteStore(Request $request)
    {        
        $post = $request->only(['user_id','store_id']);
        try {
            $now = Carbon::now();

            DB::transaction(function () use($post, $now){

                DB::table('schedules')
                ->join('messages','schedules.message_id','=','messages.id')
                ->where('messages.store_id',$post['store_id'])
                ->update([
                    'schedules.updated_at' => $now,
                    'schedules.deleted_at' => $now
                ]);

                DB::table('stores')
                ->where('id', $post['store_id'])
                ->update([
                    'updated_at' => $now,
                    'deleted_at' => $now
                ]);

                DB::table('users')
                ->where('id', $post['user_id'])
                ->update([
                    'updated_at' => $now,
                    'deleted_at' => $now
                ]);
            });

            // DB::table('schedules')
            // ->join('messages','schedules.message_id','=','messages.id')
            // ->where('messages.store_id',$post['store_id'])
            // ->update([
            //     'schedules.updated_at' => $now,
            //     'schedules.deleted_at' => $now
            // ]);    
            // Store::find($post['store_id'])->delete();
            // User::find($post['user_id'])->delete();



            return redirect(route('admin.store'))->with('flash_message','店舗削除が完了しました');
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:店舗削除【対象店舗ID:'.$post['store_id'].'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());

            return redirect(route('admin.store'))->with('error_flash_message','店舗削除に失敗しました');
        }
    }
}   
