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
    public function viewStore()
    {
        $stores = DB::table('stores')
        ->select(
            'stores.id as store_id',
            'stores.name',
            'stores.url_name',
            // DB::raw('DATE_FORMAT(DATE_ADD(schedules.plan_at, INTERVAL 1 DAY), "%Y-%m-%d 00:00:00") as url_name'),
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


    public function viewAddStore()
    {
        return view('admin.add_store');
    }

    public function insertStore(StoreRequest $request)
    {        
        $post = $request->only(['name', 'url_name', 'login_id', 'login_password', 'client_id', 'client_secret']);

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

    public function viewEditStore(Request $request)
    {
        $store_id = $request->query('store_id');

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


    public function updateStore(StoreRequest $request)
    {        
        $post = $request->only(['user_id','store_id', 'name', 'url_name', 'login_id', 
        'login_password','client_id','client_secret']);

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



    public function deleteStore(Request $request)
    {        
        $post = $request->only(['user_id','store_id']);

        Store::find($post['store_id'])->delete();
        User::find($post['user_id'])->delete();

        return redirect(route('admin.store'))->with('flash_message','店舗削除が完了しました');
    }
}   
