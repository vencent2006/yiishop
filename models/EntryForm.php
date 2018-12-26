<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 2018/12/19
 * Time:
 */

namespace app\models;

use Yii;
use yii\base\Model;

class EntryForm extends Model{
    public $name;
    public $email;

    public function rules()
    {
        return [
            [['name','email'], 'required'],
            ['email','email'],
        ];
    }
}