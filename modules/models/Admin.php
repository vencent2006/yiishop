<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 2018/12/24
 * Time:
 */

namespace app\modules\models;
use yii\db\ActiveRecord;
use Yii;

class Admin extends ActiveRecord{
    public $remember_me = true;
    public static function tableName(){
        return "shop_admin";
    }

    public function rules(){
        return [
            ['admin_user', 'required', 'message'=>'管理员的帐号不能为空'],
            ['admin_pass', 'required', 'message'=>'管理员的密码不能为空'],
            ['remember_me', 'boolean'],
            ['admin_pass', 'validatePass'],
        ];
    }

    public function validatePass(){

        if (!$this->hasErrors()){
            $data = self::find()->
                where('admin_user=:user and admin_pass=:pass',[
                    ':user'=>$this->admin_user,
                    ':pass'=>md5($this->admin_pass)
                ])->one();
            if (is_null($data)){
                $this->addError('admin_pass', '用户名或者密码错误');
            }
        }


    }

    public function login($data){
        if ($this->load($data) && $this->validate()){
            $session = Yii::$app->session;
            $life_time = $this->remember_me? 24*3600 : 0;

            session_set_cookie_params($life_time);

            $session['admin'] =[
                'admin_user' => $this->admin_user,
                'isLogin' => 1,
            ];


            //update
            $this->updateAll(['login_time'=>time(), 'login_ip'=>ip2long(Yii::$app->request->userIP)], 'admin_user = :user', [':user'=>$this->admin_user]);
            return (bool)$session['admin']['isLogin'];
        }

        return false;
    }
}