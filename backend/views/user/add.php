<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'username');
echo $form->field($model,'password')->passwordInput();
echo $form->field($model,'repassword')->passwordInput();
if(!$model->isNewRecord){
    echo $form->field($model,'status')->radioList(\backend\models\User::$status_option);
}
echo $form->field($model,'email')->textInput(['type'=>'email']);
echo \yii\bootstrap\Html::submitButton('注册',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();