<?php
namespace backend\controllers;
use backend\models\Brand;
use flyok666\uploadifive\UploadAction;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;
use flyok666\qiniu\Qiniu;

class BrandController extends Controller{
    //展示页面
    public function actionIndex(){
        //实例化对象
        $query=Brand::find()->where(['and','status',-1]);
        //$brands=Brand::find()->where(['>','status',-1])->all();
        //查询出总条数
        $total=$query->count();
        //每页显示条数
        $pageSize=3;
        //实例化分页工具类
        $pager= new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>$pageSize,
        ]);
        $brands=$query->limit($pager->limit)->offset($pager->offset)->all();
        //调用视图，分配数据
        return $this->render('index',['brands'=>$brands,'pager'=>$pager]);
    }
    //添加
    public function actionAdd(){
        //实例化对象
        $model= new Brand();
        $request = new Request();
        //判断提交方式
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            //验证数据
            if($model->validate()){
                //验证成功保存数据到数据库
                $model->save();
                //跳转回首页
                \Yii::$app->session->setFlash('success','品牌添加成功');
                return $this->redirect(['brand/index']);
            }else{
                //验证失败
                var_dump($model->getErrors());exit;
            }
        }
        //调用视图，分配数据
        return $this->render('add',['model'=>$model]);
    }

    //修改
    public function actionEdit($id){
        //实例化对象
        $model= Brand::findOne($id);
        $request = new Request();
        //判断提交方式
        $request = new Request();
        //判断提交方式
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            //验证数据
            if($model->validate()){
                //验证成功保存数据到数据库
                $model->save();
                //跳转回首页
                \Yii::$app->session->setFlash('success','品牌修改成功');
                return $this->redirect(['brand/index']);
            }else{
                //验证失败
                var_dump($model->getErrors());exit;
            }
        }
        //调用视图，分配数据
        return $this->render('add',['model'=>$model]);
    }

    //删除,不是删除数据库信息，而是将状态改成删除（-1）
    public function actionDelete($id){
        $model= Brand::findOne($id);
        //var_dump($model);exit;
        if($model->status>-1){
            $model->status=-1;
        }
        $model->save();
        //跳转回首页
        \yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['brand/index']);
    }

    //图片上传
    public function actions()
    {
        return [
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
}