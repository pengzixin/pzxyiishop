<a href="<?=\yii\helpers\Url::to(['user/login'])?>" class="btn btn-success">登录</a>
<a href="<?=\yii\helpers\Url::to(['user/logout'])?>" class="btn btn-warning">退出</a>
<a href="<?=\yii\helpers\Url::to(['user/add'])?>" class="btn btn-info">注册</a>
<table class="table table-bordered table-responsive table-condensed">
    <tr>
        <th>序号</th>
        <th>用户名</th>
        <th>邮箱</th>
        <th>角色</th>
        <th>最后登录时间</th>
        <th>最后登录ip</th>
        <th>操作</th>
    </tr>
    <?php foreach($users as $user):?>
    <tr>
        <td><?=$user->id?></td>
        <td><?=$user->username?></td>
        <td><?=$user->email?></td>
        <td>
            <select>
                <option>所有角色</option>
                <?php foreach(\Yii::$app->authManager->getRolesByUser($user->id) as $role):?>
                <option><?=$role->name?></option>
                <?php endforeach;?>
            </select>
        </td>
        <td><?=$user->lsat_login_time?date('Y-m-d H:i:s',$user->lsat_login_time):'从未登陆'?></td>
        <td><?=$user->lsat_login_ip?></td>
        <td>
            <?=\yii\bootstrap\Html::a('删除',['user/delete','id'=>$user->id],['class'=>'btn btn-sm btn-danger'])?>
            <?=\yii\bootstrap\Html::a('修改',['user/edit','id'=>$user->id],['class'=>'btn btn-sm btn-success'])?>
        </td>
    </tr>
    <?php endforeach;?>
</table>