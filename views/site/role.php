<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 2018/12/20
 * Time:
 */


use yii\helpers\Html;
use yii\widgets\LinkPager;
?>
    <h1>Roles</h1>
    <ul>
        <?php foreach ($roles as $role): ?>
            <li>
                <?= Html::encode("{$role->name} ({$role->id})") ?>:
                <?= $role->world_id ?>
            </li>
        <?php endforeach; ?>
    </ul>

<?= LinkPager::widget(['pagination' => $pagination]) ?>