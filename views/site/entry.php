<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 2018/12/19
 * Time:
 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name') ?>
    <?= $form->field($model, 'email') ?>

    <div class="form-group">
        <?= Html::submitButton('Submit', ['class'=> 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>

