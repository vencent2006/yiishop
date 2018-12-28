<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 2018/12/28
 * Time:
 */

namespace app\models;
use yii\db\ActiveRecord;

class Profile extends ActiveRecord{
    public static function tableName()
    {
        return 'shop_profile';
    }
}