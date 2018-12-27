<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 2018/12/27
 * Time:
 */

namespace app\modules\controllers;
use yii\web\Controller;
use Yii;
use app\modules\models\Admin;

class ManageController extends Controller{
    public function actionMailchangepass(){
        $this->layout = false;

        $time = Yii::$app->request->get('timestamp');
        $admin_user = Yii::$app->request->get('admin_user');
        $token = Yii::$app->request->get('token');

        $model = new Admin;
        $myToken = $model->createToken($admin_user, $time);
        if ($token != $myToken){
            $this->redirect(['public/login']);
            Yii::$app->end();
        }

        //5min check
        if (time() - $time > 300){
            $this->redirect(['public/login']);
            Yii::$app->end();
        }

        if (Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if ($model->changePass($post)){
                Yii::$app->session->setFlash('info', '密码修改成功');
            }
        }

        $model->admin_user = $admin_user;
        return $this->render('mailchangepass', ['model'=>$model]);
    }
}