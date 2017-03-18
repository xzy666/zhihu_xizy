<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    private function has_been_login(){
    if (!session('user_id'))
        return ['status'=>0,'msg'=>'you need login first.'];
}
    public function addc()
    {
        $this->has_been_login();
        $content=request()->get('content');
        $question_id=request()->get('question_id');
        $answer_id=request()->get('answer_id');
        $reply_to=request()->get('reply_to');
        /**
         * 1.评论回答
         * 2.评论问题
         * 3.回复评论
         */
        if (
            ($answer_id&&$reply_to&&!$question_id)||
            ($answer_id&&!$reply_to&&$question_id)||
            ($answer_id&&$reply_to&&$question_id)||
            (!$answer_id&&!$reply_to&&!$question_id)
        )
            return ['status'=>0,'msg'=>'answer_id or replay_id or question_id is required.'];
        if (!$content)
            return ['status'=>0,'msg'=>'content required.'];
        if (!$answer_id&&$reply_to&&$question_id){
            $comment=$this->find($reply_to);
            if (!$comment)
                return ['status' => 0, 'msg' => 'comment no exist.'];
            $this->reply_to=$reply_to;
            $this->question_id=$question_id;
        }
        if ($question_id) {
            $question = \App\Question::find($question_id);
            if (!$question)
                return ['status' => 0, 'msg' => 'question no exist.'];
            $this->question_id =$question_id;
        }
        if ($answer_id) {
            $answer = \App\Answer::find($answer_id);
            if (!$answer)
                return ['status' => 0, 'msg' => 'answer no exist.'];
            $this->answer_id = $answer_id;
        }

        $this->content=$content;
        $this->user_id=session('user_id');
        return $this->save()? ['status'=>1,'id'=>$this->id]:['status'=>0,'msg'=>'db is down.'];
    }
    public function readc()
    {
        /*
         * 指定用户的评论(这个只获得一级评论)
         * 指定问题的评论
         * 指定回答的评论
         * 以上三点都有对评论的评论
         * */
        $this->has_been_login();
        $user_id=session('user_id');
        $question=request()->get('question_id');
        $answer=request()->get('answer_id');
        if (!$question&&!$answer) {
            $comments = $this->
            where('user_id', '=', $user_id)
                ->where('reply_to', '=', null)
                ->get();
            foreach ($comments as $value){
                $value['questions']=$value->questions->toArray();
            }
            return !$comments->count() ? ['status' => 0, 'msg' => 'comment no exist.'] : ['status' => 1, 'data' => $comments->keyBy('id')];
        }
        if ($question&&!$answer) {
            $comments=$this->where('question_id','=',$question)->get()->toArray();
            $data=$this->unlimitedForComments($comments);
            return ['status'=>1,'data'=>$data];
        }
        if (!$question&&$answer) {
            $comments=$this->where('answer_id','=',$answer)->get()->toArray();
            $data=$this->unlimitedForComments($comments);
            return ['status'=>1,'data'=>$data];
        }
    }
    public function deletec()
    {
        /*
         * 删除评论和对评论的评论
         * **/
        $this->has_been_login();
        $id = request()->get('id');
        if (!$id)
            return ['status' => 0, 'msg' => 'id is required.'];
        $comment=$this->find($id);
        return $comment->delete() ? ['status' => 1] : ['status' => 0, 'msg' => 'db is down.'];
    }

    public function questions(){
        return $this->belongsTo(\App\Question::class,'question_id','id');
    }

    /**
     * 无限级分类 - 组合多维数组
     *
     * @param $quesion_arr
     * @param null $id
     * @return array
     */
    public function unlimitedForComments($quesion_arr,$id=null)
    {
        $arr=[];
        foreach ($quesion_arr as $value){
            if ($value['reply_to'] == $id){
                $value['comments']=self::unlimitedForComments($quesion_arr,$value['id']);
                $arr[]=$value;
            }
        }
        return $arr;
    }
}
