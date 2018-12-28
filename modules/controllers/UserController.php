<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 2018/12/28
 * Time:
 */

namespace app\modules\controllers;
use yii\web\Controller;
use app\models\User;
use app\models\Profile;
use yii\data\Pagination;
use Yii;

class UserController extends Controller{

    public function actionUsers(){
        $this->layout = 'layout1';

        $model = User::find()->joinWith('profile');
        $count = $model->count();
        $pageSize = Yii::$app->params['pageSize']['user'];//pageSize配置在config/params.php
        $pager = new Pagination(['totalCount'=>$count, 'pageSize'=>$pageSize]);
        $users = $model->offset($pager->offset)->limit($pager->limit)->all();
        return $this->render('users', ['users'=>$users, 'pager'=>$pager]);
    }


    public function actionReg(){
        $this->layout = 'layout1';
        $model = new User;
        if (Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if ($model->reg($post)){
                Yii::$app->session->setFlash('info', '添加会员成功');
            }else{
                Yii::$app->session->setFlash('info', '添加会员失败');
            }
        }
        $model->user_pass = '';
        $model->re_pass = '';
        return $this->render('reg', ['model'=>$model]);
    }


    public function actionDel(){
        try{
            $user_id = (int)Yii::$app->request->get('user_id');
            if (empty($user_id)){
                throw new \Exception();
            }

            $trans = Yii::$app->db->beginTransaction();
            if ($obj = Profile::find()->where('user_id = :id', [':id'=>$user_id])->one()){
                $res = Profile::deleteAll('user_id = :id', [':id'=>$user_id]);
                if (empty($res)){
                    throw new \Exception();
                }
            }

            if (!User::deleteAll('user_id = :id', [':id'=>$user_id])){
                throw new \Exception();
            }

            $trans->commit();
        }catch (\Exception $e){
            if (Yii::$app->db->getTransaction()){
                $trans->rollBack();
            }
        }

        $this->redirect(['user/users']);

    }

}