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

    public function  attributeLabels(){
        return [
            'admin_user' => '管理员帐号',
            'admin_email' => '管理员邮箱',
            'admin_pass' => '管理员密码',
            're_pass' => '确认密码',
        ];
    }

    public function rules(){
        return [
            ['admin_user', 'required', 'message'=>'管理员的帐号不能为空', 'on'=>['login', 'seekpass','changepass','admin_add','change_email']],
            ['admin_user', 'unique', 'message'=>'管理员的帐号已被注册', 'on'=>['admin_add']],
            ['admin_pass', 'required', 'message'=>'管理员的密码不能为空', 'on'=>['login','changepass','admin_add','change_email']],
            ['remember_me', 'boolean', 'on'=>['login']],
            ['admin_pass', 'validatePass', 'on'=>['login','change_email']],
            ['admin_email', 'required', 'message'=>'电子邮箱不能为空', 'on'=>['seekpass','admin_add','change_email']],
            ['admin_email', 'email', 'message'=>'电子邮箱格式不正确', 'on'=>['seekpass','admin_add','change_email']],
            ['admin_email', 'unique', 'message'=>'电子邮箱已被注册', 'on'=>['admin_add','change_email']],
            ['admin_email', 'validateEmail', 'on'=>['seekpass']],
            ['re_pass', 'required', 'message'=>'确认密码不能为空', 'on'=>['changepass','admin_add']],
            ['re_pass', 'compare', 'compareAttribute'=>'admin_pass', 'message'=>'两次密码输入不一致', 'on'=>['changepass','admin_add']],
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

    public function reg($data){
        $this->scenario = 'admin_add';

//        $data['Admin']['admin_pass'] = md5($data['Admin']['admin_pass']);
//        $data['Admin']['re_pass'] = md5($data['Admin']['re_pass']);

        if ($this->load($data) && $this->validate()){
            $this->admin_pass = md5($this->admin_pass);
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

            return (bool)$this->updateAll(['admin_email'=> $this->admin_email], 'admin_user = :user', [':user'=>$this->admin_user]);
        }

        return false;
    }


}