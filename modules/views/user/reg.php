<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
?>
<link rel="stylesheet" href="assets/admin/css/compiled/new-user.css" type="text/css" media="screen" />
<!-- main container -->
<div class="content">

    <div class="container-fluid">
        <div id="pad-wrapper" class="new-user">
            <div class="row-fluid header">
                <h3>添加新会员</h3>
            </div>

            <div class="row-fluid form-wrapper">
                <!-- left column -->
                <div class="span9 with-sidebar">
                    <div class="container">
                        <?php
                        if (Yii::$app->session->hasFlash('info')) {
                            echo Yii::$app->session->getFlash('info');
                        }
                        $form = ActiveForm::begin([
                            'options' => ['class' => 'new_user_form inline-input'],
                            'fieldConfig' => [
                                'template' => '<div class="span12 field-box">{label}{input}</div>{error}'
                            ],
                        ]);
                        ?>
                        <?php echo $form->field($model, 'user_name')->textInput(['class' => 'span9']); ?>
                        <?php echo $form->field($model, 'user_email')->textInput(['class' => 'span9']); ?>
                        <?php echo $form->field($model, 'user_pass')->passwordInput(['class' => 'span9']); ?>
                        <?php echo $form->field($model, 're_pass')->passwordInput(['class' => 'span9']); ?>
                        <div class="span11 field-box actions">
                            <?php echo Html::submitButton('创建', ['class' => 'btn-glow primary']); ?>
                            <span>或者</span>
                            <?php echo Html::resetButton('取消', ['class' => 'reset']); ?>
                        </div>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>

                <!-- side right column -->
                <div class="span3 form-sidebar pull-right">

                    <div class="alert alert-info hidden-tablet">
                        <i class="icon-lightbulb pull-left"></i>
                        请在左侧填写会员相关信息，包括会员账号，电子邮箱，以及密码
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- end main container -->

