<?php

namespace App;

use App\Http\Requests\Request;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function login()
    {
        $this->has_pas_name();

        $user=$this->where('username','=',request()->get('username'))->first();
        //校验账号密码
        if (!$user)
            return ['status'=>0,'msg'=>'username is not exist'];
        if (!Hash::check(request()->get('password'),$user->password))
            return ['status'=>0,'msg'=>'password is wrong'];
        //账号信息存入session\
        session()->put('username',$user->username);
        session()->put('user_id',$user->id);

        return ['status'=>1,'msg'=>'login success'];
    }

    public function register()
    {
        $this->has_pas_name();
        $this->username=request()->get('username');
        $this->password=bcrypt(request()->get('password'));
        if ($this->save()) {
            return ['status' => 1, 'msg' => 'register success.'];
        }else{
            return ['status'=>0,'msg'=>'db is down.'];
        }


    }


    public function logout()
    {
        session()->forget('username');
        session()->forget('user_id');
        return ['status'=>1,'msg'=>'logut success.'];
    }


    private function has_pas_name(){
        //判断账号密码等参数有没有问题
        if (!request()->get('username'))
            return ['status'=>0,'msg'=>'username required.'];
        if (!request()->get('password'))
            return ['status'=>0,'msg'=>'password required.'];

    }

    public function change_password()
    {
        if (!session('user_id'))
            return ['status'=>0,'msg'=>'login first.'];
        $old_pwd=request()->get('old_password');
        $new_pwd=request()->get('new_password');
        if (!$old_pwd||!$new_pwd)
            return ['status'=>0,'msg'=>'the old_password and new_password are required.'];
        if (!Hash::check($old_pwd,$this->find(session('user_id'))->password))
            return ['status'=>0,'msg'=>'the old_password is wrong.'];
        $a=$this->find(session('user_id'));
        $a->password=bcrypt($new_pwd);
        return $a->save()?['status'=>1]:['status'=>0,'msg'=>'db is down.'];
    }

    public function reset_password()
    {
        return 111;
    }

    public function valid_auth()
    {
        Mail::raw('test',function ($message){
            $message->from('504578726@qq.com','test');
            $message->to('504578726@qq.com');
        });
        echo 111;
    }

}
