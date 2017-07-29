<?php

namespace backend\controllers;

use backend\filters\RbacFilter;
use backend\models\Menu;
use yii\web\NotFoundHttpException;

class MenuController extends \yii\web\Controller
{
    //添加菜单
    public function actionAdd(){
        //实例化模型
        $model = new Menu();
        //加载验证数据
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            $model->save();
            \Yii::$app->session->setFlash('success','添加成功');
            return $this->redirect(['menu/index']);
        }
        //调用视图，分配数据
        return $this->render('add',['model'=>$model]);
    }

    public function actionIndex()
    {
        $menus= Menu::find()->where(['parent_id'=>0])->all();
        return $this->render('index',['menus'=>$menus]);
    }
    //修改菜单
    public function actionEdit($id){
        $model = Menu::findOne(['id'=>$id]);
        //加载数据并验证
        if($model->load(\Yii::$app->request->post()) && $model->validate()){
            //判断不能修改到自己分类下面,以及预防出现三级分类
            if($model->parent_id && !empty(Menu::getChildren($id))){
                $model->addError('parent_id','只能为顶级分类');
                //调用视图
                return $this->render('add',['model'=>$model]);
            }
            $model->save();
            \Yii::$app->session->setFlash('success','修改成功');
            return $this->redirect(['menu/index']);
        }
        //调用视图
        return $this->render('add',['model'=>$model]);
    }

    //删除菜单
    public function actionDelete($id){
        $model = Menu::findOne(['id'=>$id]);
        if($model===null){
            throw new NotFoundHttpException('菜单不存在');
        }
        //查询菜单下面是否存在子菜单
        if(!empty(Menu::getChildren($id))){//查询子分类，如果是空数组，则没有子分类
            \Yii::$app->session->setFlash('danger','有子菜单不能删除');
            return $this->redirect(['index']);
        }else{
            $model->delete();
            return $this->redirect(['index']);
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
