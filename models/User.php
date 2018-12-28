<?php

namespace app\models;
use yii\db\ActiveRecord;
use app\models\Profile;


class User extends ActiveRecord{
    public $remember_me = true;
    public $re_pass = '';
    public static function tableName(){
        return "shop_user";
    }

    public function  attributeLabels(){
        return [
            'user_name' => '会员名称',
            'user_email' => '会员邮箱',
            'user_pass' => '会员密码',
            're_pass' => '确认密码',
        ];
    }

    public function rules(){
        return [
            ['user_name', 'required', 'message'=>'管理员的帐号不能为空', 'on'=>['login', 'seekpass','changepass','user_add','change_email']],
            ['user_name', 'unique', 'message'=>'管理员的帐号已被注册', 'on'=>['user_add']],
            ['user_pass', 'required', 'message'=>'管理员的密码不能为空', 'on'=>['login','changepass','user_add','change_email']],
            ['user_pass', 'validatePass', 'on'=>['login','change_email']],
            ['remember_me', 'boolean', 'on'=>['login']],
            ['user_email', 'required', 'message'=>'电子邮箱不能为空', 'on'=>['seekpass','user_add','change_email']],
            ['user_email', 'email', 'message'=>'电子邮箱格式不正确', 'on'=>['seekpass','user_add','change_email']],
            ['user_email', 'unique', 'message'=>'电子邮箱已被注册', 'on'=>['user_add','change_email']],
            ['user_email', 'validateEmail', 'on'=>['seekpass']],
            ['re_pass', 'required', 'message'=>'确认密码不能为空', 'on'=>['changepass','user_add']],
            ['re_pass', 'compare', 'compareAttribute'=>'user_pass', 'message'=>'两次密码输入不一致', 'on'=>['changepass','user_add']],
        ];
    }

    public function validatePass(){

        if (!$this->hasErrors()){
            $data = self::find()->
            where('user_name=:user and user_pass=:pass',[
                ':user'=>$this->user_name,
                ':pass'=>md5($this->user_pass)
            ])->one();
            if (is_null($data)){
                $this->addError('user_pass', '用户名或者密码错误');
            }
        }


    }

    public function validateEmail(){
        if (!$this->hasErrors()){
            $data = self::find()->where(
                'user_name = :user and user_email = :email',
                [':user'=>$this->user_name, ':email'=>$this->user_email])->one();
            if (is_null($data)){
                $this->addError('user_email', '邮箱不匹配');
            }
        }
    }

    public function getProfile(){
        return $this->hasOne(Profile::className(), ['user_id'=>'user_id']);
    }



    public function login($data){
        $this->scenario = 'login';
        if ($this->load($data) && $this->validate()){
            $session = Yii::$app->session;
            $life_time = $this->remember_me? 24*3600 : 0;

            session_set_cookie_params($life_time);

            $session['user'] =[
                'user_name' => $this->user_name,
                'isLogin' => 1,
            ];


            //update
            $this->updateAll(
                ['login_time'=>time(), 'login_ip'=>ip2long(Yii::$app->request->userIP)],
                'user_name = :user',
                [':user'=>$this->user_name]);
            return (bool)$session['user']['isLogin'];
        }

        return false;
    }

    public function seekPass($data){
        $this->scenario = 'seekpass';
        if ($this->load($data) && $this->validate()){
            //view在basic/mail/下
            $time = time();
            $token = $this->createToken($data['User']['user_name'], $time);
            $mailer = Yii::$app->mailer->compose('seekpass', ['user_name'=>$data['User']['user_name'], 'time'=>$time, 'token'=>$token]);
            $mailer->setFrom('lewis_sun@foxmail.com');
            $mailer->setTo($data['User']['user_name']);
            $mailer->setSubject('慕课商城-找回密码');
            if ($mailer->send()){
                //send succ
                return true;
            }
        }
        return false;
    }

    public function createToken($user_name, $time){
        return md5(md5($user_name).base64_encode(Yii::$app->request->userIP).md5($time));
    }

    public function changePass($data){
        $this->scenario = 'changepass';
        if ($this->load($data) && $this->validate()){
            return (bool)$this->updateAll(
                ['user_pass'=>md5($this->user_pass)],
                'user_name = :user',
                [':user'=>$this->user_name]
            );
        }

        return false;
    }

    public function reg($data){
        $this->scenario = 'user_add';


        if ($this->load($data) && $this->validate()){
            $this->user_pass = md5($this->user_pass);
            if ($this->save(false)){//false表示save就单独进行验证(validate)了
                return true;
            }
            return false;
        }

        return false;
    }


    public function changeEmail($data){
        $this->scenario = 'change_email';

        if ($this->load($data) && $this->validate()){

            return (bool)$this->updateAll(['user_email'=> $this->user_email],
                'user_name = :user',
                [':user'=>$this->user_name]);
        }

        return false;
    }


}