<?php
namespace backend\filters;
use yii\base\ActionFilter;
use yii\web\ForbiddenHttpException;

class RbacFilter extends ActionFilter{
    public function beforeAction($action)
    {
        if(\Yii::$app->user->isGuest){
            return $action->controller->redirect(['user/login']);
        }
        if(!\Yii::$app->user->can($action->uniqueId)){
            throw new ForbiddenHttpException('对不起，你没有该操作执行权限');
        }
        return parent::beforeAction($action);
    }
}