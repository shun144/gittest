<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\HalfAlfaNum;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // return false;
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        $route= $this->route()->getName();

        $rules =  [
            'name' => [
                'required',
                "max:".config('field.store.name.max')],
            'url_name' => [
                'required',
                new HalfAlfaNum(),
                "max:".config('field.store.url_name.max')],
            'login_id' => [
                'required',
                new HalfAlfaNum(),
                'between:'.config('field.user.login_id.min').','.config('field.user.login_id.max')],
            'login_password' => [
                'required',
                new HalfAlfaNum(),
                'between:'.config('field.user.login_password.min').','.config('field.user.login_password.max')],
            'client_id' => [
                'required',
                new HalfAlfaNum()],
            'client_secret' => [
                'required',
                new HalfAlfaNum()],
        ];
        
        switch($route){
            case 'store.add':
                array_push($rules['name'], 'unique:stores');
                array_push($rules['url_name'], 'unique:stores');
                array_push($rules['login_id'], 'unique:users');              
                array_push($rules['client_id'], 'unique:stores');
                array_push($rules['client_secret'], 'unique:stores');
                break;
            case 'store.edit':
                array_push($rules['name'],Rule::unique('stores')->ignore($this->store_id));
                array_push($rules['url_name'],Rule::unique('stores')->ignore($this->store_id));
                array_push($rules['login_id'],Rule::unique('users')->ignore($this->user_id));
                if (!$this->request->has('is_change_password'))
                {
                    unset($rules['login_password']);
                }
                array_push($rules['client_id'],Rule::unique('stores')->ignore($this->store_id));
                array_push($rules['client_secret'],Rule::unique('stores')->ignore($this->store_id));

                break;
        };
        return $rules;
   
    }

    public function messages()
    {
        return [
            'required' => '必須項目です',
            'unique' => '既に登録されているため別の値を入力してください',
            'name.max' => '入力可能文字数は最大'.config('field.store.name.max').'文字です',
            'url_name.max' => '入力可能文字数は最大'.config('field.store.url_name.max').'文字です',
            'login_id.between' => '入力可能文字数は'.config('field.user.login_id.min').'～'.config('field.user.login_id.max').'です',
            'login_password.between' => '入力可能文字数は'.config('field.user.login_password.min').'～'.config('field.user.login_password.max').'です'
        ];
    }
}
