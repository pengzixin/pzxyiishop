<?php
namespace backend\controllers;
use backend\filters\RbacFilter;
use backend\models\Goods;
use backend\models\GoodsGallery;
use flyok666\qiniu\Qiniu;
use flyok666\uploadifive\UploadAction;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class GoodsGalleryController extends Controller{
    //展示
    public function actionIndex(){

    }
//    //添加相册图片
//    public function actionAdd($id){
//        $model=new GoodsGallery();
//        $request = new Request();
//        //调用视图，分配数据
//        return $this->render('add',['model'=>$model]);
//    }

    //图片上传
    public function actions()
    {
        return [
//            //ueditor编辑器
//            'upload' => [
//                'class' => 'kucha\ueditor\UEditorAction',
//            ],
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
                    $goods_id=\yii::$app->request->post('goods_id');
                    if($goods_id){
                        $model=new GoodsGallery();
                        $model->goods_id=$goods_id;
                        $model->path=$url;
                        $model->save();
                        $action->output['fileUrl'] = $model->path;
                        $action->output['id'] = $model->id;
                    }else{
                        $action->output['fileUrl']  = $url;//输出文件的相对路径
                    }
                },
            ],
        ];
    }

    /*
     * 商品相册
     */
    public function actionAdd($id)
    {
        $goods = Goods::findOne(['id'=>$id]);
        if($goods == null){
            throw new NotFoundHttpException('商品不存在');
        }
        return $this->render('add',['goods'=>$goods]);
    }

    /*
     * AJAX删除图片
     */
    public function actionDelGallery(){
        $id = \Yii::$app->request->post('id');
        $model = GoodsGallery::findOne(['id'=>$id]);
        if($model && $model->delete()){
            return 'success';
        }else{
            return 'fail';
        }

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