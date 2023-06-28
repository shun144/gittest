<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use \GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use \GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Schedule;
use App\Models\Line;
use App\Models\Store;
use App\Models\Message;
use App\Models\Image;
use App\Models\History;
use App\Models\Template;


class OwnerController extends Controller
{
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 配信履歴一覧表示
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function viewPostHistory()
    {
        try {
            $store_id = Auth::user()->store_id;
            $posts = DB::table('histories')
            ->where('store_id', $store_id )
            ->select(
                'id',
                'start_at',
                // 'end_at',
                'title',
                'content',
                'img_url',
                'status',
                'err_info'
            )
            ->latest('created_at')
            ->get();

            return view('owner.postHistory', compact('posts'));
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:配信履歴表示 【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
            
            return view('owner.postHistory');
        }
    }


    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 配信履歴詳細表示
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function viewPostHistoryInfo(Request $request)
    {
        try {

            $history_id = $request->query('history_id');
            $post = DB::table('histories')
            ->where('id',$history_id)
            ->select(
                'start_at',
                // 'end_at',
                'title',
                'content',
                'img_url',
                'status',
                'err_info'
            )
            ->first();
            return view('owner.postHistory_info', compact('post'));
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:配信履歴詳細表示 【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());
            return view('owner.postHistory_info');
        }
    }


    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 配信スケジュール表示
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function viewSchedule()
    {
        try {
            $templates = DB::table('templates')
            ->whereNull('templates.deleted_at')
            ->join('messages','message_id','=','messages.id')
            ->where('messages.store_id', Auth::user()->store_id)
            ->select(
                'messages.id as id',
                'messages.title as title',
                'messages.title_color as title_color')
            ->latest('templates.updated_at')
            ->get();
            return view('owner.schedule', compact('templates'));
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:配信スケジュール表示 【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());

            $get_template_error_flushMsg = '定型メッセージ取得に失敗しました';
            return view('owner.schedule', compact('get_template_error_flushMsg'));
        }
    }


    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 連携LINEユーザ一覧表示
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function viewLineUsers()
    {
        try {

            $store_id = Auth::user()->store_id;
            $lines = DB::table('lines')
            ->select('id','user_name', 'is_valid','created_at')
            ->whereNull('deleted_at')
            ->where('store_id', $store_id)
            ->orderBy('created_at')
            ->get();
            $url_name = DB::table('stores')->find($store_id)->url_name;

            $valid_count = DB::table('lines')
            ->whereNull('deleted_at')
            ->where('store_id', $store_id)
            ->where('is_valid', true)
            ->count();

            $reg_url = url($url_name) . '/register';
            return view('owner.line_users', compact('lines', 'reg_url', 'valid_count'));
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:連携LINEユーザ一覧表示 【店舗ID:'.Auth::user()->store_id.'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());

            $get_lineuser_error_flushMsg = '連携LINEユーザ取得に失敗しました';
            return view('owner.line_users', compact('get_lineuser_error_flushMsg'));
        }
    }


    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    // 連携LINEユーザ更新
    // /_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_/_
    public function updateLineUser(Request $request)
    {
        $post = $request->only(['line_user_id','new_valid']);

        try {
            $now = Carbon::now();
            DB::table('lines')
            ->where('id',$post['line_user_id'])
            ->update([
                'is_valid' => $post['new_valid'],
                'updated_at' => $now
                ]
            );
            return redirect(route('owner.line_users'));
        }
        catch (\Exception $e) {
            \Log::error('エラー機能:連携LINEユーザ更新 【店舗ID:'.Auth::user()->store_id.'/LINEユーザID:'.$post['line_user_id'].'】');
            \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
            \Log::error('エラー内容:'.$e->getMessage());

            return redirect(route('owner.line_users'))->with('edit_lineuser_error_flushMsg','連携LINEユーザ更新に失敗しました');
        }
    }

}
