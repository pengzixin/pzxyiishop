<?php
namespace backend\models;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface{
    public $password;//密码
    public $repassword;//确认密码
    public $oldpassword;//旧密码
    public $newpssword;//新密码
    public $renewpssword;//确认新密码
    public $roles=[];//角色
    const SCENARIO_ADD='add';
    const SCENARIO_CHPWD='ChPwd';
    public static $status_option=[
        10=>'启用',0=>'禁用'
    ];
    //public $code;//验证码
    public function rules()
    {
        return [
            [['username','email'],'required'],
            [['oldpassword','newpssword','renewpssword'],'required','on'=>self::SCENARIO_CHPWD],
            [['password','repassword'],'string'],
            [['password','repassword'],'required','on'=>self::SCENARIO_ADD],
            ['repassword','compare','compareAttribute'=>'password','message'=>'两次密码必须一致'],
            ['renewpssword','compare','compareAttribute'=>'newpssword','message'=>'两次密码必须一致','on'=>self::SCENARIO_CHPWD],
            ['email','email'],
            [['email','username'],'unique'],
            ['roles','safe'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password'=>'密码',
            'repassword'=>'确认密码',
            'email'=>'电子邮箱',
            'renewpssword'=>'确认新密码',
            'newpssword'=>'新密码',
            'oldpassword'=>'旧密码',
            'roles'=>'角色',
        ];
    }
    //在save之前处理数据
    public function beforeSave($insert)
    {
        if($insert){
            $this->status=10;
            //添加时间
            $this->created_at=time();
            $this->auth_key = \Yii::$app->security->generateRandomString();
        }else{
            //添加时间
            $this->updated_at=time();
        }
        //验证通过
        if($this->password){//加密密码
            $this->password_hash=\Yii::$app->security->generatePasswordHash($this->password);
        }
        return parent::beforeSave($insert);
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        return self::findOne(['id'=>$id]);
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        // TODO: Implement findIdentityByAccessToken() method.
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
}