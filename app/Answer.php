<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\App;

class Answer extends Model
{
    private function has_been_login(){
        if (!session('user_id'))
            return ['status'=>0,'msg'=>'you need login first.'];
    }
    public function adda()
    {

        $this->has_been_login();
        $this->has_content_and_question_id();
        $a=$this->where('user_id','=',session('user_id'))
            ->where('question_id','=',request()->get('question_id'))
            ->count();
        if ($a>=1)
            return ['status'=>0,'msg'=>'everyone only can answer once in one question'];
        $this->question_id=request()->get('question_id');
        $this->content=request()->get('content');
        $this->user_id=session()->get('user_id');
        if ($this->save())
            return ['status'=>1];
        else
            return ['status'=>0,'msg'=>'db is down.'];
    }

    public function changea()
    {
        $this->has_been_login();
        if (!(request()->get('question_id')&&request()->get('content')))
            return ['status'=>0,'msg'=>'question_id and content are required.'];
        $id=request()->get('id');
        if (!$id)
        return ['status'=>0,'msg'=>'id is required.'];
        $answer=$this->find($id);
        if (!$answer)
            return ['status'=>0,'msg'=>'the answer is not exist.'];
        if (!request()->get('content'))
            return ['status'=>0,'msg'=>'content is required.'];
        $answer->content=request()->get('content');
        if ($answer->save())
            return ['status'=>1];
        else
            return ['status'=>0,'msg'=>'db is down.'];
    }

    public function reada()
    {
        if(!request()->get('id'))
            return ['status'=>0,'msg'=>'id is reqiured.'];
        $answer = $this->find(request()->get('id'));
        if (!$answer)
            return ['satus' =>0,'msg'=>'answer is not exist.'];
        return ['status'=>1,'data'=>$answer];
    }

    public function deletea()
    {
        $this->has_been_login();
        if(!request()->get('id'))
            return ['status'=>0,'msg'=>'id is reqiured.'];
        $id=request()->get('id');
        $answer=$this->find($id);
        if (!$answer)
            return ['status'=>0,'msg'=>'answer no exists.'];
        $comment= new \App\Comment();

        $comment->where('answer_id','=',$id)->delete();

        return $answer->delete()?['status'=>1]:['status'=>0,'db is down.'];
    }

    public function likeOrCancel()
    {
        $this->has_been_login();
        $answer_id=request()->get('answer_id');
        $like=request()->get('like');
        if (!$answer_id||!$like)
            return ['status'=>0,'msg'=>'answer and like are required.'];
        $answer = $this->find($answer_id);
        if ($like!=1&&$like!=2)
            return ['status'=>0,'msg'=>'like must be 1 or 2'];
        //判断有没有点赞过
        /*
         * 点赞后生成一条赞 1 answer_id 和user_id
         * 取消点赞 删除这条记录 重新生成 0
         * */
        $a=$answer
            ->users()
            ->newPivotStatement()
            ->where('answer_id','=',$answer_id)
            ->where('user_id','=',session('user_id'))
            ->delete();
        $answer->users()->attach(session('user_id'),['like'=>request()->get('like')]);
        return ['status'=>1];
    }
    private function has_content_and_question_id(){
        if (!request()->get('question_id'))
            return ['status' =>0,'msg'=>'question_id required.'];
        if (!request()->get('content'))
            return ['status' =>0,'msg'=>'content required.'];
    }

    public function questions()
    {
        return $this->belongsTo(\App\Question::class);
    }

    public function users()
    {
        return $this->
                belongsToMany(\App\User::class)
                ->withPivot('like')
                ->withTimestamps();
    }

}
