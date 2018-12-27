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

//
use yii\data\Pagination;


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




    public function actionManagers(){
        $this->layout = 'layout1';
//        $managers = Admin::find()->all();

        $model = Admin::find();
        $count = $model->count();
        $pageSize = Yii::$app->params['pageSize']['manage'];//pageSize配置在config/params.php
        $pager = new Pagination(['totalCount'=>$count, 'pageSize'=>$pageSize]);
        $managers = $model->offset($pager->offset)->limit($pager->limit)->all();
        return $this->render('managers', ['managers'=>$managers, 'pager'=>$pager]);
    }


    public function actionReg(){
        $this->layout = 'layout1';
        $model = new Admin;
        if (Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if ($model->reg($post)){
                Yii::$app->session->setFlash('info', '添加管理员成功');
            }else{
                Yii::$app->session->setFlash('info', '添加管理员失败');
            }
        }
        $model->admin_pass = '';
        $model->re_pass = '';
        return $this->render('reg', ['model'=>$model]);
    }


    public function actionDel(){
        $admin_id = (int)Yii::$app->request->get('admin_id');
        if (empty($admin_id)){
            $this->redirect(['manage/managers']);
        }

        $model = new Admin;
        if ($model->deleteAll('admin_id = :id', [':id'=>$admin_id])){
            Yii::$app->session->setFlash('info', '删除成功');
            $this->redirect(['manage/managers']);
        }

    }


    public function actionChangeemail(){
        $this->layout = 'layout1';
        $model = Admin::find()->where('admin_user = :user', [':user'=>Yii::$app->session['admin']['admin_user']])->one();
        if (Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if ($model->changeEmail($post)){
                Yii::$app->session->setFlash('info', '修改邮箱成功');
            }
        }

        $model->admin_pass = '';
        return $this->render('changeemail', ['model'=>$model]);
    }


    public function actionChangepass(){
        $this->layout = 'layout1';

        $model = Admin::find()->where('admin_user = :user', [':user'=>Yii::$app->session['admin']['admin_user']])->one();
        if (Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if ($model->changePass($post)){
                Yii::$app->session->setFlash('info', '修改密码成功');
            }
        }

        $model->admin_pass = '';
        $model->re_pass = '';

        return $this->render('changepass', ['model'=>$model]);
    }
}