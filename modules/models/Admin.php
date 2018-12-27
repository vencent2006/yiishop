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
    public $re_pass = '';
    public static function tableName(){
        return "shop_admin";
    }

    public function rules(){
        return [
            ['admin_user', 'required', 'message'=>'管理员的帐号不能为空', 'on'=>['login', 'seekpass','changepass']],
            ['admin_pass', 'required', 'message'=>'管理员的密码不能为空', 'on'=>['login','changepass']],
            ['remember_me', 'boolean', 'on'=>['login']],
            ['admin_pass', 'validatePass', 'on'=>['login']],
            ['admin_email', 'required', 'message'=>'电子邮箱不能为空', 'on'=>['seekpass']],
            ['admin_email', 'email', 'message'=>'电子邮箱格式不正确', 'on'=>['seekpass']],
            ['admin_email', 'validateEmail', 'on'=>['seekpass']],
            ['re_pass', 'required', 'message'=>'确认密码不能为空', 'on'=>['changepass']],
            ['re_pass', 'compare', 'compareAttribute'=>'admin_pass', 'message'=>'两次密码输入不一致', 'on'=>['changepass']],
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

    public function validateEmail(){
        if (!$this->hasErrors()){
            $data = self::find()->where('admin_user = :user and admin_email = :email', [':user'=>$this->admin_user, ':email'=>$this->admin_email])->one();
            if (is_null($data)){
                $this->addError('admin_email', '管理员邮箱不匹配');
            }
        }
    }



    public function login($data){
        $this->scenario = 'login';
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

    public function seekPass($data){
        $this->scenario = 'seekpass';
        if ($this->load($data) && $this->validate()){
            //view在basic/mail/下
            $time = time();
            $token = $this->createToken($data['Admin']['admin_user'], $time);
            $mailer = Yii::$app->mailer->compose('seekpass', ['admin_user'=>$data['Admin']['admin_user'], 'time'=>$time, 'token'=>$token]);
            $mailer->setFrom('lewis_sun@foxmail.com');
            $mailer->setTo($data['Admin']['admin_email']);
            $mailer->setSubject('慕课商城-找回密码');
            if ($mailer->send()){
                //send succ
                return true;
            }
        }
        return false;
    }

    public function createToken($admin_user, $time){
        return md5(md5($admin_user).base64_encode(Yii::$app->request->userIP).md5($time));
    }

    public function changePass($data){
        $this->scenario = 'changepass';
        if ($this->load($data) && $this->validate()){
            return (bool)$this->updateAll(
                ['admin_pass'=>md5($this->admin_pass)],
                'admin_user = :user',
                [':user'=>$this->admin_user]
            );
        }

        return false;
    }


}