<?php
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'label')->textInput();
echo $form->field($model,'parent_id')->dropDownList(\backend\models\Menu::getLableOption());
echo $form->field($model,'url')->dropDownList(\yii\helpers\ArrayHelper::map(\backend\models\Menu::getUrl(),'name','name'),['prompt'=>'==请选择路由==']);
echo $form->field($model,'sort')->textInput();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();