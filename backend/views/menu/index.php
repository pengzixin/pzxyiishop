<?php
/* @var $this yii\web\View */
?>
<h1>菜单列表</h1>
<a href="<?=\yii\helpers\Url::to(['menu/add'])?>" class="btn btn-info">添加</a>
<table class="table table-responsive table-bordered">
    <tr>
        <th>菜单名称</th>
        <th>路由</th>
        <th>排序</th>
        <th>操作</th>
    </tr>
    <?php foreach($menus as $menu):?>
    <tr>
        <td><?=$menu->label?></td>
        <td><?=$menu->url?></td>
        <td><?=$menu->sort?></td>
        <td>
            <?=\yii\bootstrap\Html::a('删除',['menu/delete','id'=>$menu->id],['class'=>'btn btn-sm btn-danger'])?>
            <?=\yii\bootstrap\Html::a('修改',['menu/edit','id'=>$menu->id],['class'=>'btn btn-sm btn-success'])?>
        </td>
    </tr>
        <?php foreach(\backend\models\Menu::getChildren($menu->id) as $child):?>
        <tr>
            <td>——<?=$child->label?></td>
            <td><?=$child->url?></td>
            <td><?=$child->sort?></td>
            <td>
                <?=\yii\bootstrap\Html::a('删除',['menu/delete','id'=>$child->id],['class'=>'btn btn-sm btn-danger'])?>
                <?=\yii\bootstrap\Html::a('修改',['menu/edit','id'=>$child->id],['class'=>'btn btn-sm btn-success'])?>
            </td>
        </tr>
        <?php endforeach;?>
    <?php endforeach;?>
</table>

