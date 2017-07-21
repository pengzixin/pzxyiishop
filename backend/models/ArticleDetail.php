<?php
namespace backend\models;
use yii\db\ActiveRecord;

class ArticleDetail extends ActiveRecord{
//建立和文章详情表之间的关系
    public function getArticle(){
        return $this->hasOne(Article::className(),['id'=>'article_id']);
    }

    public function rules()
    {
        return [
            ['content','required'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'content'=>'内容',
        ];
    }

}