<a href="<?=\yii\helpers\Url::to(['goods/add'])?>" class="btn btn-info">添加</a>
<?php
$form = \yii\bootstrap\ActiveForm::begin([
//get方式
'method' => 'get',
//get方式提交,需要显式指定action
'action'=>\yii\helpers\Url::to(['goods/index']),
'layout'=>'inline'
]);

echo $form->field($model,'name')->textInput(['placeholder'=>'商品名'])->label(false);
echo $form->field($model,'sn')->textInput(['placeholder'=>'货号'])->label(false);
echo $form->field($model,'lft')->textInput(['placeholder'=>'￥'])->label(false);
echo $form->field($model,'rht')->textInput(['placeholder'=>'￥'])->label(false);
echo \yii\bootstrap\Html::submitButton('搜索',['class'=>'btn btn-info']);
\yii\bootstrap\ActiveForm::end();
?>
<table class="table table-bordered table-condensed table-responsive">
    <tr>
        <th>排序</th>
        <th>名称</th>
        <th>货号</th>
        <th>商品分类</th>
        <th>品牌分类</th>
        <th>售价</th>
        <th>市场价</th>
        <th>库存</th>
        <th>是否在售</th>
        <th>添加时间</th>
        <th>logo</th>
        <th>操作</th>
    </tr>
    <?php foreach($goods as $good):?>
    <tr>
        <td><?=$good->sort?></td>
        <td><?=$good->name?></td>
        <td><?=$good->sn?></td>
        <td><?=$good->categoryName->name?></td>
        <td><?=$good->brandName->name?></td>
        <td><?=$good->shop_price?></td>
        <td><?=$good->market_price?></td>
        <td><?=$good->stock?></td>
        <td><?=\backend\models\Goods::$sale_option[$good->is_on_sale]?></td>
        <td><?=date('Y-m-d H:i:s',$good->create_time)?></td>
        <td><?=\yii\bootstrap\Html::img($good->logo,['height'=>60])?></td>
        <td>
            <?=\yii\bootstrap\Html::a('<span class="glyphicon glyphicon-picture"></span>相册',['goods-gallery/add','id'=>$good->id],['class'=>'btn btn-sm btn-default'])?>
            <?=\yii\bootstrap\Html::a('<span class="glyphicon glyphicon-film"></span>查看',['goods/show','id'=>$good->id],['class'=>'btn btn-sm btn-warning'])?>
            <?=\yii\bootstrap\Html::a('<span class="glyphicon glyphicon-trash"></span>删除',['goods/delete','id'=>$good->id],['class'=>'btn btn-sm btn-danger'])?>
            <?=\yii\bootstrap\Html::a('<span class="glyphicon glyphicon-edit"></span>修改',['goods/edit','id'=>$good->id],['class'=>'btn btn-sm btn-success'])?>
        </td>

    </tr>
    <?php endforeach;?>
</table>
<!--分页工具条-->
<?php
echo \yii\widgets\LinkPager::widget(['pagination'=>$pager,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页','lastPageLabel'=>'末页']);
