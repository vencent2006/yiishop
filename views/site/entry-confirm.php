<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 2018/12/19
 * Time:
 */
use yii\helpers\Html;
?>

<p>You have entered the following information:</p>

<ul>
    <li><label>Name</label>:<?= Html::encode($model->name) ?></li>
    <li><label>Email</label>:<?= Html::encode($model->email) ?></li>
</ul>
