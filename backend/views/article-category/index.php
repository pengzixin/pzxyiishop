<a href="<?=\yii\helpers\Url::to(['article-category/add'])?>" class="btn btn-info">添加</a>
<table class="table table-responsive table-condensed table-bordered">
    <tr>
        <th>序号</th>
        <th>名称</th>
        <th>简介</th>
        <th>排序</th>
        <th>状态</th>
        <th>操作</th>
    </tr>
    <?php foreach($categorys as $category):?>
    <tr>
        <td><?=$category->id?></td>
        <td><?=$category->name?></td>
        <td><?=$category->intro?></td>
        <td><?=$category->sort?></td>
        <td><?=\backend\models\ArticleCategory::$logo_option[$category->status]?></td>
        <td>
            <?=\yii\bootstrap\Html::a('删除',['article-category/delete','id'=>$category->id],['class'=>'btn btn-sm btn-danger'])?>
            <?=\yii\bootstrap\Html::a('修改',['article-category/edit','id'=>$category->id],['class'=>'btn btn-sm btn-success'])?>
        </td>
    </tr>
    <?php endforeach;?>
</table>
<!--分页工具条-->
<?php
echo \yii\widgets\LinkPager::widget(['pagination'=>$pager,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页','lastPageLabel'=>'末页']);