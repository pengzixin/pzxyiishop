<?php
namespace backend\controllers;
use backend\models\ArticleCategory;
use yii\web\Controller;
use yii\web\Request;

class ArticleCategoryController extends Controller{
    //展示列表功能
    public function actionIndex(){
        //实例化对象
        $categorys= ArticleCategory::find()->where(['>','status',-1])->all();
        //调用视图，分配数据
        return $this->render('index',['categorys'=>$categorys]);
    }

    //添加功能
    public function actionAdd(){
        //实例化对象
        $model = new ArticleCategory();
        $request = new Request();
        //判断提交方式
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            //验证数据
            if($model->validate()){
                //验证通过,保存到数据库
                $model->save();
                //跳转页面
                return $this->redirect(['article-category/index']);
            }else{
                //验证失败
                var_dump($model->getErrors());exit;
            }
        }
        //调用视图，展示页面，分配数据
        return $this->render('add',['model'=>$model]);
    }

    //修改
    public function actionEdit($id){
        //实例化对象
        $model = ArticleCategory::findOne($id);
        $request = new Request();
        //判断提交方式
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            //验证数据
            if($model->validate()){
                //验证通过,保存到数据库
                $model->save();
                //跳转页面
                \yii::$app->session->setFlash('success','修改成功');
                return $this->redirect(['article-category/index']);
            }else{
                //验证失败
                var_dump($model->getErrors());exit;
            }
        }
        //调用视图，展示页面，分配数据
        return $this->render('add',['model'=>$model]);
    }

    //删除
    public function actionDelete($id){
        $model= ArticleCategory::findOne($id);
        //判断状态
        if($model->status>-1){
            $model->status=-1;
        }
        $model->save();
        \yii::$app->session->setFlash('success','删除成功');
        //跳转回首页
        return $this->redirect(['article-category/index']);
    }
}