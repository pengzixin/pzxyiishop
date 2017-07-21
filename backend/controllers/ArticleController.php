<?php
namespace backend\controllers;
use backend\models\Article;
use backend\models\ArticleCategory;
use backend\models\ArticleDetail;
use yii\data\Pagination;
use yii\web\Controller;
use yii\web\Request;

class ArticleController extends Controller{
    //展示页面
    public function actionIndex($keywords=''){
        //实例化对象
        $query= Article::find()->where(['and','status>-1',"name like '%{$keywords}%'"]);
        //查询出总条数
        $total=$query->count();
        //每页显示条数
        $pageSize=3;
        //分页工具条
        $pager= new Pagination([
            'totalCount'=>$total,
            'defaultPageSize'=>$pageSize
        ]);
        //根据条件查询数据
        $articles=$query->limit($pager->limit)->offset($pager->offset)->orderBy(['sort'=>SORT_ASC])->all();
        //调用视图，分配数据
        return $this->render('index',['articles'=>$articles,'pager'=>$pager]);
    }

    //添加
    public function actionAdd(){
        //实例化对象
        $model = new Article();
        $model2= new ArticleDetail();
        $request = new Request();
        //判断提交方式
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            $model2->load($request->post());

            //验证数据
            //var_dump($model);
            //var_dump($model2);exit;
            if($model->validate() && $model2->validate()){
                //验证通过，保存到数据库
                $model->create_time=time();
                $model->save();
                //var_dump($model->id);exit;
                $model2->article_id=$model->id;
                $model2->save();
                //调转页面
                return $this->redirect(['article/index']);
            }
        }
        //将分类名称查询出来，分配到页面
        $rows= ArticleCategory::find()->where(['>','status',-1])->all();
        //调用视图，分配数据
        return $this->render('add',['model'=>$model,'rows'=>$rows,'model2'=>$model2]);
    }

    //修改
    public function actionEdit($id){
        //实例化对象
        $model = Article::findOne($id);
        $model2= ArticleDetail::findOne(['article_id'=>$id]);
        $request = new Request();
        //判断提交方式
        if($request->isPost){
            //加载数据
            $model->load($request->post());
            $model2->load($request->post());
            //验证数据
            //var_dump($model);
            //var_dump($model2);exit;
            if($model->validate() && $model2->validate()){
                //验证通过，保存到数据库
                $model->save();
                //var_dump($model->id);exit;
                $model2->article_id=$model->id;
                //var_dump($model2->article_id);exit;
                $model2->save();
                //调转页面
                return $this->redirect(['article/index']);
            }
        }
        //将分类名称查询出来，分配到页面
        $rows= ArticleCategory::find()->where(['>','status',-1])->all();
        //调用视图，分配数据
        return $this->render('add',['model'=>$model,'rows'=>$rows,'model2'=>$model2]);
    }

    //删除
    public function actionDelete($id){
        $model= Article::findOne($id);
        //判断状态
        if($model->status>-1){
            $model->status=-1;
        }
        $model->save();
        \yii::$app->session->setFlash('success','删除成功');
        //跳转回首页
        return $this->redirect(['article/index']);
    }

    //展示文章详情
    public function actionShow($id){
        $model=Article::find()->select(['name','create_time'])->where(['id'=>$id])->one();//getAttributes(['name','create_time']);
        //var_dump($model);exit;
        $model2=ArticleDetail::findOne(['article_id'=>$id]);
        //调用视图，分配数据
        return $this->render('show',['model'=>$model,'model2'=>$model2]);
    }
    //ueditor编辑器
    public function actions()
    {
        return [
            'upload' => [
                'class' => 'kucha\ueditor\UEditorAction',
            ]
        ];
    }
}