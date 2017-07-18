<a href="<?=\yii\helpers\Url::to(['brand/add'])?>" class="btn btn-info">添加</a>
<table class="table table-bordered table-condensed table-responsive">
    <tr>
        <th>序号</th>
        <th>名称</th>
        <th>简介</th>
        <th>LOGO</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach($brands as $brand):?>
    <tr>
        <td><?=$brand->id?></td>
        <td><?=$brand->name?></td>
        <td><?=$brand->intro?></td>
        <td><?=\yii\bootstrap\Html::img($brand->logo,['height'=>60])?></td>
        <td><?=$brand->sort?></td>
        <td><?=\backend\models\Brand::$logo_option[$brand->status]?></td>
        <td>
        <?=\yii\bootstrap\Html::a('删除',['brand/delete','id'=>$brand->id],['class'=>'btn btn-sm btn-danger'])?>
        <?=\yii\bootstrap\Html::a('修改',['brand/edit','id'=>$brand->id],['class'=>'btn btn-sm btn-success'])?>
        </td>
    </tr>
    <?php endforeach;?>
</table>
<!--分页工具条-->
<?php
echo \yii\widgets\LinkPager::widget(['pagination'=>$pager,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页','lastPageLabel'=>'末页']);