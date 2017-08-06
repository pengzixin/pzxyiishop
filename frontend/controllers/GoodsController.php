<?php
namespace frontend\controllers;

use frontend\models\Goods;
use frontend\models\GoodsCategory;
use frontend\models\GoodsGallery;
use frontend\models\GoodsIntro;
use yii\data\Pagination;
use yii\web\Controller;

class GoodsController extends Controller{
    public $layout=false;
    //商品列表页面
    public function actionList($pid){
        //实例化模型);
        //判断是几级分类
        $cate = \backend\models\GoodsCategory::findOne(['id'=>$pid]);
        if($cate->depth == 2){
            $query = Goods::find()->where(['goods_category_id'=>$pid]);
        }else{
            $ids = $cate->leaves()->asArray()->column();
            //var_dump($ids);exit;
            $query = Goods::find()->where(['in','goods_category_id',$ids]);
        }
        //总条数
        $total=$query->count();
        $pageSize=3;
        //分页工具条
        $pager= new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>$pageSize
        ]);
        //根据条件查询数据
        $goods=$query->limit($pager->limit)->offset($pager->offset)->all();
        $categorys= GoodsCategory::find()->where(['parent_id'=>0])->all();
        //列表页面右侧导航分类列表
        $categoryNows=GoodsCategory::find()->where(['id'=>$pid])->one();
        //调用视图，展示页面
        return $this->render('list',['goods'=>$goods,'categorys'=>$categorys,'categoryNows'=>$categoryNows,'pager'=>$pager]);
    }

    //商品详情展示
    public function actionShow($id=1){
        $goods=Goods::findOne(['id'=>$id]);
        $gallerys=GoodsGallery::find()->where(['goods_id'=>$id])->all();
        $intro=GoodsIntro::findOne(['goods_id'=>$id]);
        return $this->render('show',['goods'=>$goods,'gallerys'=>$gallerys,'intro'=>$intro]);
    }
    //首页展示
    public function actionIndex(){
        $categorys= GoodsCategory::find()->where(['parent_id'=>0])->all();
        $contents=$this->render('index',['categorys'=>$categorys]);
        file_put_contents('index.html',$contents);
        return $contents;
    }
}