<?php
/**
 * @var $this \yii\web\View
 */
$form = \yii\bootstrap\ActiveForm::begin();
echo $form->field($model,'name');
echo $form->field($model,'parent_id')->hiddenInput();
echo '<div>
    <ul id="treeDemo" class="ztree"></ul>
</div>';
echo $form->field($model,'intro')->textarea();
echo \yii\bootstrap\Html::submitButton('提交',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();

//调用视图的方法加载静态资源
$this->registerCssFile('@web/zTree/css/zTreeStyle/zTreeStyle.css');
$this->registerJsFile('@web/zTree/js/jquery.ztree.core.js',['depends'=>\yii\web\JqueryAsset::className()]);
//加载js代码
$categorys[]=['id'=>0,'parent_id'=>0,'name'=>'顶级分类','open'=>1];
//将数组转换成json格式字符串
$nodes=\yii\helpers\Json::encode($categorys);
$nodeId=$model->parent_id;
$this->registerJs(new \yii\web\JsExpression(
<<<JS
var zTreeObj;
        // zTree 的参数配置，深入使用请参考 API 文档（setting 配置详解）
        var setting = {
            data: {
                simpleData: {
                    enable: true,
                    idKey: "id",
                    pIdKey: "parent_id",
                    rootPId: 0
                }
            },
            callback: {
		        onClick: function(event, treeId, treeNode){
		            //console.log(treeNode.id);
		            //将当期选中的分类的id，赋值给parent_id隐藏域
		            $("#goodscategory-parent_id").val(treeNode.id);
		        }
	        }
        };
        // zTree 的数据属性，深入使用请参考 API 文档（zTreeNode 节点数据详解）
        var zNodes = {$nodes};

        zTreeObj = $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        zTreeObj.expandAll(true);//展开全部节点

        //获取节点
        var node = zTreeObj.getNodeByParam("id", "{$nodeId}", null);
        //选中节点
        zTreeObj.selectNode(node);
JS
));