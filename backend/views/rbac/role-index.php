<h3>角色列表</h3>
<a href="<?=\yii\helpers\Url::to(['rbac/add-role'])?>" class="btn btn-info ">添加权限</a>
<table class="table table-bordered table-condensed">
    <tr>
        <th>角色</th>
        <th>描述</th>
        <th>权限</th>
        <th>操作</th>
    </tr>
    <?php foreach($roles as $role):?>
    <tr>
        <td><?=$role->name?></td>
        <td><?=$role->description?></td>
        <td>
            <select>
                <option>所有权限</option>
                <?php foreach(\Yii::$app->authManager->getChildren($role->name)as $permission):?>
                <option><?=$permission->description?></option>
                <?php endforeach;?>
            </select>
        </td>
        <td>
            <?=\yii\bootstrap\Html::a('删除',['rbac/del-role','name'=>$role->name],['class'=>'btn btn-sm btn-danger'])?>
            <?=\yii\bootstrap\Html::a('修改',['rbac/edit-role','name'=>$role->name],['class'=>'btn btn-sm btn-success'])?>
        </td>
    </tr>
    <?php endforeach;?>
</table>