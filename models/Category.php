<?php
/**
 * Created by PhpStorm.
 * User: vincent
 * Date: 2019/1/3
 * Time:
 */

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use Yii;

class Category extends ActiveRecord{
    public static function tableName(){
        return 'shop_category';
    }

    public function attributeLabels(){
        return [
        'parentid'=>'上级分类',
        'title'=>'分类名称',
        ];
    }

    public function rules(){
        return [
            ['parentid','required','message'=>'上级分类不能为空'],
            ['title','required','message'=>'标题名称不能为空'],
            ['createtime','safe'],
        ];
    }


    public function add($data){
        $data['Category']['createtime'] = time();
        if ($this->load($data) && $this->save()){
            return true;
        }

        return false;
    }

    public function getData(){
        $cates = self::find()->all();
        $cates = ArrayHelper::toArray($cates);
        return $cates;
    }

    public function getTree($cates, $pid = 0){
        $tree = [];
        foreach ($cates as $cate){
            if ($cate['parentid'] == $pid){
                $tree[] = $cate;
                $tree = array_merge($tree, $this->getTree($cates, $cate['cateid']));
            }
        }

        return $tree;
    }


    public function setPrefix($data, $p = '|-----'){
        $tree = [];
        $num = 1;
        $prefix = [0=>1];
        while($val = current($data)){
            $key = key($data);
            if ($key > 0){
                if ($data[$key-1]['parentid'] != $val['parentid']){
                    $num++;
                }
            }

            if (array_key_exists($val['parentid'], $prefix)){
                $num = $prefix[$val['parentid']];
            }

            $val['title'] = str_repeat($p, $num).$val['title'];
            $prefix[$val['parentid']] = $num;
            $tree[] = $val;
            next($data);
        }

        return $tree;
    }

    public function getOptions(){
        $data = $this->getData();
        $tree = $this->getTree($data);
        $tree = $this->setPrefix($tree);
        $options = ['添加顶级分类'];
        foreach ($tree as $cate){
            $options[$cate['cateid']] = $cate['title'];
        }

        return $options;
    }


    public function getTreeList(){
        $data = $this->getData();
        $tree = $this->getTree($data);
        $tree = $this->setPrefix($tree);
        return $tree;
    }
}