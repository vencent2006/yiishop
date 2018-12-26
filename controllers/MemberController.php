<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 2018/12/21
 * Time:
 */

namespace app\controllers;

use yii\web\Controller;

class MemberController extends Controller {
    public $layout = false;

    public function actionAuth(){
        $this->layout = 'layout2';
        return $this->render('auth');
    }
}