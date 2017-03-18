<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{

    protected $fillable=[
        'title','user_id'
    ];


    public function questions()
    {
        if ($id=request()->get('id'))
            return ['status'=>1,'data'=>$this->find($id)];
        $limit=request()->get('data_limit')?:4;
        $page=request()->get('page')?:1;
        return ['status'=>1,'data'=>
                    $this->limit($limit)->where('id','>',$limit*($page-1))->get()
                ];
    }

    public function addq()
    {
        $this->has_been_login();
        $this->title=request()->get('title');
        $this->user_id=session('user_id');
        if(!$this->save())
            return ['status'=>0,'msg'=>'db is down.'];
        return ['status'=>1,'msg'=>'add question success.'];
    }

    public function changeq()
    {
        $this->has_been_login();
        $id= request()->get('id');
        $questions= $this->find($id);
        if (!$questions)
            return ['status'=>0,'msg'=>'check the id you gave.'];
        if ($title= request()->get('title'))
            $questions->title=$title;
        if ($descript= request()->get('descript'))
            $questions->descript=$descript;
        if ($questions->save())
            return ['status'=>1,'msg'=>'change success.'];
        return ['status'=>0,'msg'=>'db is down.'];
    }

    public function deleteq()
    {
        $this->has_been_login();
        $id= request()->get('id');
        $questions= $this->find($id);
        if ($questions->delete())
            return ['status'=>1,'msg'=>'delete success.'];
        return ['status'=>0,'msg'=>'db is down.'];
        //or Question::destroy($id); 不用获取模型可以直接删除
    }
    private function has_been_login(){
        if (!session('user_id'))
            return ['status'=>0,'msg'=>'you need login first.'];
    }

    public function answers()
    {
        return $this->hasMany(\App\Answer::class);
    }
}
