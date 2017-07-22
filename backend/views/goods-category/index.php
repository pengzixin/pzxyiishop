<a href="<?=\yii\helpers\Url::to(['goods-category/add'])?>" class="btn btn-info">添加</a>
<table class="table table-responsive table-bordered table-condensed">
    <tr>
        <th>序号</th>
        <th>名称</th>
        <th>父级分类</th>
        <th>简介</th>
        <th>操作</th>
    </tr>
    <?php foreach($categorys as $category):?>
    <tr>
        <td><?=$category->id?></td>
        <td><?=$category->name?></td>
        <td><?=\backend\models\GoodsCategory::getParent($category->parent_id)->name;?></td>
        <td><?=$category->intro?></td>
        <td>
            <?=\yii\bootstrap\Html::a('删除',['goods-category/delete','id'=>$category->id],['class'=>'btn btn-sm btn-danger'])?>
            <?=\yii\bootstrap\Html::a('修改',['goods-category/edit','id'=>$category->id],['class'=>'btn btn-sm btn-success'])?>
        </td>
    </tr>
    <?php endforeach;?>
</table>