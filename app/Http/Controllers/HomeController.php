<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Question;
use Illuminate\Http\Request;

use App\Http\Requests;

class HomeController extends Controller
{

    public function timeline(Question $question,Answer $answer)
    {
        /*
         * 时间线 展示问题和对应答案
         * 指定每页多少条记录  传入页码就跳进指定页
         * 问题 根据记录生成  组合成数组
         * */
        $limit = request()->get('limit')?:15;
        $page = request()->get('page')?:1;
        $question_info=$question
                        ->limit($limit)
                        ->skip(($page-1)*$limit)
                        ->get();
        //遍历整个记录组
        $arr=[];
        foreach ($question_info->toArray() as  $v){
            $v['answers']=$answer->where('question_id','=',$v['id'])->get()->toArray();
            $arr[]=$v;
        }
        return ['status'=>1];
    }
}
