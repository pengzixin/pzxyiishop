<?php
namespace backend\models;
use yii\base\Model;
use yii\db\ActiveQuery;

class SearchForm extends Model{
    public $name;
    public $sn;
    public $lft;
    public $rht;
    public function rules(){
        return [
            [['lft','rht'],'integer'],
            ['name','string','max'=>50],
            ['sn','string'],
        ];
    }
    public function search(ActiveQuery $rs){
        //加载搜索表单提交过来的关键字
        $this->load(\Yii::$app->request->get());
        if($this->name){
            //andWhere 并行条件
            $rs->andWhere(['like','name',$this->name]);
        }
        if($this->sn){
            $rs->andWhere(['like','sn',$this->sn]);
        }
        if($this->lft){
            $rs->andWhere(['>','shop_price',$this->lft]);
        }
        if($this->rht){
            $rs->andWhere(['<','shop_price',$this->rht]);
        }
    }
}