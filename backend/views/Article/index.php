<form action="" method="get">
    <input type="text" name="keywords"/>
    <input type="submit" value="搜索" class="btn btn-success btn-sm">
</form>
<a href="<?=\yii\helpers\Url::to(['article/add'])?>" class="btn btn-info">添加</a>
<table class="table table-bordered table-condensed table-responsive">
    <tr>
        <th>序号</th>
        <th>名称</th>
        <th>简介</th>
        <th>文章分类</th>
        <th>排序</th>
        <th>状态</th>
        <th>创建时间</th>
        <th>操作</th>
    </tr>
    <?php foreach($articles as $article):?>
    <tr>
        <td><?=$article->id?></td>
        <td><?=$article->name?></td>
        <td><?=$article->intro?></td>
        <td><?=$article->category->name?></td>
        <td><?=$article->sort?></td>
        <td><?=\backend\models\Article::$status_option[$article->status]?></td>
        <td><?=date('Y-m-d',$article->create_time)?></td>
        <td>
            <?=\yii\bootstrap\Html::a('查看',['article/show','id'=>$article->id],['class'=>'btn btn-sm btn-warning'])?>
            <?=\yii\bootstrap\Html::a('删除',['article/delete','id'=>$article->id],['class'=>'btn btn-sm btn-danger'])?>
            <?=\yii\bootstrap\Html::a('修改',['article/edit','id'=>$article->id],['class'=>'btn btn-sm btn-success'])?>
        </td>
    </tr>
    <?php endforeach;?>
</table>
<!--分页工具条-->
<?php
echo \yii\widgets\LinkPager::widget(['pagination'=>$pager,'nextPageLabel'=>'下一页','prevPageLabel'=>'上一页','firstPageLabel'=>'首页','lastPageLabel'=>'末页']);