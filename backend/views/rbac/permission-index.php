<!--<link rel="stylesheet" type="text/css" href="http://cdn.datatables.net/1.10.15/css/jquery.dataTables.css">-->
<!--<script type="text/javascript" charset="utf8" src="http://cdn.datatables.net/1.10.15/js/jquery.dataTables.js"></script>-->
<h3>权限列表</h3>
<a href="<?=\yii\helpers\Url::to(['rbac/add-permission'])?>" class="btn btn-info ">添加权限</a>
<table id="table_id_example" class="table table-bordered table-responsive display">
    <thead>
    <tr>
        <th>权限</th>
        <th>描述</th>
        <th>操作</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach($permissions as $permission):?>
    <tr>
        <td><?=$permission->name?></td>
        <td><?=$permission->description?></td>
        <td>
            <?=\yii\bootstrap\Html::a('删除',['rbac/del-permission','name'=>$permission->name],['class'=>'btn btn-sm btn-danger'])?>
            <?=\yii\bootstrap\Html::a('修改',['rbac/edit-permission','name'=>$permission->name],['class'=>'btn btn-sm btn-success'])?>
        </td>
    </tr>
    <?php endforeach;?>
    </tbody>
</table>
<!--<script>-->
<!--    $(document).ready( function () {-->
<!--        $('#table_id_example').DataTable();-->
<!--    } );-->
<!--</script>-->
<?php
/**
* @var $this \yii\web\View
*/
$this->registerCssFile('//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css');
$this->registerCssFile('//cdn.datatables.net/1.10.15/css/dataTables.bootstrap.css');
$this->registerJsFile('//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js',['depends'=>\yii\web\JqueryAsset::className()]);
$this->registerJsFile('//cdn.datatables.net/1.10.15/js/dataTables.bootstrap.js',['depends'=>\yii\web\JqueryAsset::className()]);
$this->registerJs('$(".table").DataTable({
language: {
    url: "//cdn.datatables.net/plug-ins/1.10.15/i18n/Chinese.json"
}
});');