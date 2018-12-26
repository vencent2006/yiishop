<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 2018/12/21
 * Time:
 */

namespace app\controllers;
use yii\web\Controller;

class CartController extends Controller{
//    public $layout = false;
    public function actionIndex(){
        $this->layout = "layout1";
        return $this->render('index');
    }

}