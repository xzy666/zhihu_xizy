<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //

    Route::get('/', function () {
        return 'this is zhihu api';
    });

    Route::any('/login',function (\App\User $user){
        return $user->login();
    });
    Route::any('/register',function (\App\User $user){
        return $user->register();
    });
    Route::any('/logout',function (\App\User $user){
        return $user->logout();
    });
//查看问题
    Route::any('/questions',function (\App\Question $question){
        return $question->questions();
    });
//添加问题
    Route::any('/addq',function (\App\Question $question){
        return $question->addq();
    });
//修改问题
    Route::any('/changeq',function (\App\Question $question){
        return $question->changeq();
    });
//删除问题
    Route::any('/deleteq',function (\App\Question $question){
        return $question->deleteq();
    });
    //回答相关路由
    Route::any('/adda',function (\App\Answer $answer){
        return $answer->adda();
    });
    Route::any('/changea',function (\App\Answer $answer){
        return $answer->changea();
    });
    Route::any('/reada',function (\App\Answer $answer){
        return $answer->reada();
    });
    Route::any('/deletea',function (\App\Answer $answer){
        return $answer->deletea();
    });
    //评论相关的路由
    Route::any('/addc',function (\App\Comment $comment){
        return $comment->addc();
    });
    Route::any('/readc',function (\App\Comment $comment){
        return $comment->readc();
    });
    Route::any('/deletec',function (\App\Comment $comment){
        return $comment->deletec();
    });
    //点赞 取消点赞
    Route::any('/likeOrCancel',function (\App\Answer $answer){
        return $answer->likeOrCancel();
    });
    //时间线  重置密码、验证 更改密码
    Route::any('/timeline','HomeController@timeline');
    Route::any('/change_password',function (\App\User $user){
        return $user->change_password();
    });
    Route::any('/reset_password',function (\App\User $user){
        return $user->reset_password();
    });
    Route::any('/valid_auth',function (\App\User $user){
        return $user->valid_auth();
    });


});
