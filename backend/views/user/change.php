<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'oldpassword')->passwordInput();
echo $form->field($model,'newpssword')->passwordInput();
echo $form->field($model,'renewpssword')->passwordInput();
echo \yii\bootstrap\Html::submitButton('修改',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();