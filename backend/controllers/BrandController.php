<?php
namespace backend\controllers;
use backend\models\Brand;
use yii\web\Controller;
use yii\web\Request;
use yii\web\UploadedFile;

class BrandController extends Controller{
    //展示页面
    public function actionIndex(){
        //实例化对象
        $brands=Brand::find()->where(['>','status',-1])->all();
        //调用视图，分配数据
        return $this->render('index',['brands'=>$brands]);
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
            //实例化文件上传对象
            $model->imgFile=UploadedFile::getInstance($model,'imgFile');
            //验证数据
            if($model->validate()){
                //验证成功
                //如果有文件上传就处理图片
                if($model->imgFile){
                    //var_dump($model->imgFile);exit;
                    //存放图片路径
                    $path=\yii::getAlias('@webroot').'/upload/'.date('Ymd');
                    //var_dump($path);exit;
                    //创建文件夹，如果有就不创建
                    if(!is_dir($path)){
                        mkdir($path,0777,true);
                    }
                    //拼凑图片路径
                    $filename='/upload/'.date('Ymd').'/'.uniqid().'.'.$model->imgFile->extension;
                    //移动图片
                    $model->imgFile->saveAs(\yii::getAlias('@webroot').$filename,false);
                    //将图片路径放到薯片属性中
                    $model->logo=$filename;
                }
                //保存数据到数据库
                $model->save();
                //跳转回首页
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
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            //实例化文件上传对象
            $model->imgFile=UploadedFile::getInstance($model,'imgFile');
            //验证数据
            if($model->validate()){
                //验证成功
                //如果有文件上传就处理图片
                if($model->imgFile){
                    //var_dump($model->imgFile);exit;
                    //存放图片路径
                    $path=\yii::getAlias('@webroot').'/upload/'.date('Ymd');
                    //var_dump($path);exit;
                    //创建文件夹，如果有就不创建
                    if(!is_dir($path)){
                        mkdir($path,0777,true);
                    }
                    //拼凑图片路径
                    $filename='/upload/'.date('Ymd').'/'.uniqid().'.'.$model->imgFile->extension;
                    //移动图片
                    $model->imgFile->saveAs(\yii::getAlias('@webroot').$filename,false);
                    //将图片路径放到薯片属性中
                    $model->logo=$filename;
                }
                //保存数据到数据库
                $model->save();
                //跳转回首页
                \yii::$app->session->setFlash('success','修改成功');
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
}