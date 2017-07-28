<h3><?=$model->scenario==\backend\models\RoleForm::SCENARIO_ADD?'添加':'修改';?>角色</h3>
<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput(['readonly'=>$model->scenario!=\backend\models\RoleForm::SCENARIO_ADD]);
echo $form->field($model,'description');
echo $form->field($model,'permissions',['inline'=>'none'])->checkboxList(
    \yii\helpers\ArrayHelper::map(\Yii::$app->authManager->getPermissions(),'name','description')
);
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();