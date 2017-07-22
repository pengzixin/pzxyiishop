<?php
namespace backend\controllers;
use yii\db\Exception;
use yii\web\Controller;
use backend\models\GoodsCategory;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\Request;

class GoodsCategoryController extends Controller{
    //商品分类展示
    public function actionIndex(){
        //实例化对象
        $categorys=GoodsCategory::find()->orderBy('tree,lft')->all();
        $request =new Request();
        //调用视图，分配数据
        return $this->render('index',['categorys'=>$categorys]);
    }
    //添加
    public function actionAdd(){
        //实力化对象
        $model = new GoodsCategory(['parent_id'=>0]);
        $request= new Request();
        //判断提交方式
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            //验证数据
            if($model->validate()){
                //验证成功
                //判断是否是一级分类
                if($model->parent_id){
                    //非一级分类，获取父id
                    $category=GoodsCategory::findOne(['id'=>$model->parent_id]);
                    if($category){
                        $model->prependTo($category);
                    }else{
                        throw new HttpException(404,'上级分类不存在');
                    }
                }else{// 是一级分类
                    $model->makeRoot();
                }
                \Yii::$app->session->setFlash('success','分类添加成功');
                return $this->redirect(['index']);
            }else{
                //验证失败
                var_dump($model->getErrors());exit;
            }
        }
        //获取所有分类数据
        $categorys=GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        //调用视图，分配数据
        return $this->render('add',['model'=>$model,'categorys'=>$categorys]);
    }

    //修改
    public function actionEdit($id){
        //实力化对象
        $model = GoodsCategory::findOne(['id'=>$id]);
        $request= new Request();
        //判断提交方式
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            //验证数据
            if($model->validate()){
                //验证成功
                try{
                    //判断是否是一级分类
                    if($model->parent_id){
                        //非一级分类，获取父id
                        $category=GoodsCategory::findOne(['id'=>$model->parent_id]);
                        if($category){
                            $model->prependTo($category);
                        }else{
                            throw new HttpException(404,'上级分类不存在');
                        }
                    }else{// 是一级分类
                        if($model->oldAttributes['parent_id']==0){//根节点修改到根节点
                            $model->save();
                        }else{
                            $model->makeRoot();
                        }
                    }
                    \Yii::$app->session->setFlash('success','分类修改成功');
                    return $this->redirect(['index']);
                }catch(Exception $e){
                    $model->addError('parent_id',GoodsCategory::exceptionInfo($e->getMessage()));
                }
            }else{
                //验证失败
                var_dump($model->getErrors());exit;
            }
        }
        //获取所有分类数据
        $categorys=GoodsCategory::find()->select(['id','parent_id','name'])->asArray()->all();
        //调用视图，分配数据
        return $this->render('add',['model'=>$model,'categorys'=>$categorys]);
    }

    //删除
    public function actionDelete($id){
        $model=GoodsCategory::findOne(['id'=>$id]);
        //判断下面是否有子节点
//        $pat=GoodsCategory::find()->where(['parent_id'=>$id]);
//        $count=$pat->count();
//        if($count>0){
//            \Yii::$app->session->setFlash('danger','有子分类的分类不能删除');
//            return $this->redirect(['index']);
//        }else{
//            $model=$model->delete();
//            return $this->redirect(['index']);
//        }
        if($model==null){
            throw new NotFoundHttpException('商品分类不存在');
        }
        //判断下面是否有子节点
        if(!$model->isLeaf()){
            \Yii::$app->session->setFlash('danger','有子分类的分类不能删除');
          return $this->redirect(['index']);
        }
        $model->deleteWithChildren();
        \Yii::$app->session->setFlash('success','删除成功');
        return $this->redirect(['index']);
    }
}