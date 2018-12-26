<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 2018/12/20
 * Time:
 */

namespace app\models;

use yii\db\ActiveRecord;

class Role extends ActiveRecord{
    public static function tableName()
    {
        return 'role';
    }
}