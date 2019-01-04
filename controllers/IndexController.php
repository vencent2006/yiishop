<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 2018/12/21
 * Time:
 */

namespace app\controllers;
use app\controllers\CommonController;

class IndexController extends CommonController{
    public $layout = false;

    public function actionIndex(){
        $this->layout = 'layout1';
        return $this->render('index');
    }

}