<h2><?=$model->scenario==\backend\models\PermissionForm::SCENARIO_ADD?'添加':'修改';?>权限</h2>
<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name')->textInput(['readonly'=>$model->scenario!=\backend\models\PermissionForm::SCENARIO_ADD]);
echo $form->field($model,'description');
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();