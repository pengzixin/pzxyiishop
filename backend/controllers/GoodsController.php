<?php
namespace backend\controllers;
use backend\filters\RbacFilter;
use backend\models\Brand;
use backend\models\Goods;
use backend\models\GoodsCategory;
use backend\models\GoodsDayCount;
use backend\models\GoodsIntro;
use backend\models\SearchForm;
use flyok666\qiniu\Qiniu;
use flyok666\uploadifive\UploadAction;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;

class GoodsController extends Controller{
    //首页展示列表
    public function actionIndex(){
        $model=new SearchForm();
        $sr=Goods::find()->where(['=','status',1]);
        //调用方法合成搜索条件
        $model->search($sr);
        //查询总条数
        $total=$sr->count();
        //每页显示条数
        $pageSize=3;
        //分页工具条
        $pager= new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>$pageSize
        ]);
        //根据条件查询数据
        $goods=$sr->limit($pager->limit)->offset($pager->offset)->orderBy(['sort'=>SORT_ASC])->all();
        //调用视图，分配数据
        return $this->render('index',['goods'=>$goods,'pager'=>$pager,'model'=>$model]);
    }
    //添加功能
    public function actionAdd(){
        //实例化
        $model= new Goods();
        $model2=new GoodsIntro();
        $request = new Request();
        //判断提交方式
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            $model2->load($request->post());
            //验证数据
            if($model->validate() && $model2->validate()){
                //验证成功
                //生成货号
                $category=GoodsCategory::findOne(['id'=>$model->goods_category_id]);
                if(!$category->isLeaf()){
                    $model->addError('goods_category_id','只能添加到三级分类下面');
                }else{
                    $model->sn=GoodsDayCount::getSn();
                    $model->status=1;
                    $model->create_time=time();
                    $model->save();
                    $model2->goods_id=$model->id;
                    $model2->save();
                    \Yii::$app->session->setFlash('success','商品添加成功');
                    return $this->redirect(["goods-gallery/add?id=$model->id"]);
                }
            }else{//验证失败
                var_dump($model->getErrors());exit;
            }
        }
        //调用视图，分配数据
        return $this->render('add',['model'=>$model,'model2'=>$model2]);
    }

    //修改功能
    public function actionEdit($id){
        //实例化
        $model= Goods::findOne(['id'=>$id]);
        $model2=GoodsIntro::findOne(['goods_id'=>$id]);
        $request = new Request();
        //判断提交方式
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            $model2->load($request->post());
            //验证数据
            if($model->validate() && $model2->validate()){
                //验证成功
                $model->save();
                $model2->goods_id=$model->id;
                $model2->save();
                \Yii::$app->session->setFlash('success','商品修改成功');
                return $this->redirect(['goods/index']);
            }else{//验证失败
                var_dump($model->getErrors());exit;
            }
        }
        //调用视图，分配数据
        return $this->render('add',['model'=>$model,'model2'=>$model2]);
    }

    //删除功能
    public function actionDelete($id){
        $model=Goods::findOne(['id'=>$id]);
       //判断状态
        if($model->status==1){
            $model->status=0;
        }
        $model->save();
        \yii::$app->session->setFlash('success','删除成功');
        //跳转回首页
        return $this->redirect(['goods/index']);
    }
    public function actionShow($id){
        $good=GoodsIntro::findOne(['goods_id'=>$id]);
        //var_dump($good);exit;
        //调用视图分配数据
        return $this->render('show',['good'=>$good]);
    }

    //图片上传
    public function actions()
    {
        return [
            //ueditor编辑器
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
            ],
            //图片上传
            's-upload' => [
                'class' => UploadAction::className(),
                'basePath' => '@webroot/upload',
                'baseUrl' => '@web/upload',
                'enableCsrf' => true, // default
                'postFieldName' => 'Filedata', // default
                'overwriteIfExist' => true,//如果文件已存在，是否覆盖
                'format' => function (UploadAction $action) {
                    $fileext = $action->uploadfile->getExtension();
                    $filehash = sha1(uniqid() . time());
                    $p1 = substr($filehash, 0, 2);
                    $p2 = substr($filehash, 2, 2);
                    return "{$p1}/{$p2}/{$filehash}.{$fileext}";
                },//文件的保存方式
                'validateOptions' => [
                    'extensions' => ['jpg', 'png'],
                    'maxSize' => 1 * 1024 * 1024, //file size
                ],
                'beforeValidate' => function (UploadAction $action) {
                    //throw new Exception('test error');
                },
                'afterValidate' => function (UploadAction $action) {},
                'beforeSave' => function (UploadAction $action) {},
                'afterSave' => function (UploadAction $action) {
                    //将图片上传到七牛云
                    $qiniu = new Qiniu(\Yii::$app->params['qiniu']);
                    $qiniu->uploadFile(
                        $action->getSavePath(), $action->getWebUrl()
                    );
                    $url = $qiniu->getLink($action->getWebUrl());
                    $action->output['fileUrl']  = $url;
                },
            ],
        ];
    }

    public function behaviors()
    {
        return [
            'rbac'=>[
                'class'=>RbacFilter::className(),
                'only'=>['add','edit','delete','index']
            ]
        ];
    }

}