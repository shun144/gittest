<?php

namespace App\Libs;

use App\Jobs\PostMessageJob;

class SendMessage
{
    // public function send($para)
    // {
    //   return 'Send' . $para;
    // }
    public function send($content, $title, $img_path)
    {
      try {
        $now = \Carbon::now();
        $inputs = $request->only(['content']);
        $inputs['title'] = $request->has('title') ? $request->only(['title']):'ー';
        $inputs['store_id'] = \Auth::user()->store_id;
    
        $img_path = '';
        if ($request->has('imagefile')){
            $img = $request->file('imagefile')[0];
            $save_name = \Storage::disk('owner')->put('', $img);
            $img_path = \Storage::disk('owner')->url($save_name);
        }
        
        $inputs['img_path'] = $img_path;
    
        $history_id = \DB::table('histories')
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
        \Log::error('エラー機能:配信スケジュール表示 【店舗ID:'.Auth::user()->store_id.'】');
        \Log::error('エラー箇所:'.$e->getFile().'【'.$e->getLine().'行目】');
        \Log::error('エラー内容:'.$e->getMessage());

        $get_template_error_flushMsg = '定型メッセージ取得に失敗しました';
        return view('owner.schedule', compact('get_template_error_flushMsg'));
      }

    }
}