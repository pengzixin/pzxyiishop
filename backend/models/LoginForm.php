<?php
namespace backend\models;
use yii\base\Model;

class LoginForm extends Model{
    public $code;//验证码
    public $username;//用户名
    public $password;//密码
    public $rememberMe;
    public function rules()
    {
        return [
            [['username','password'],'required'],
            ['rememberMe','boolean'],
            //验证码验证规则
            ['code','captcha','captchaAction'=>'user/captcha'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'username'=>'用户名',
            'password'=>'密码',
            'code'=>'验证码',
            'rememberMe'=>'记住密码',
        ];
    }


    //登录功能
    public function login(){
        //通过用户名查找数据
        $user=User::findOne(['username'=>$this->username]);
        //判断是否存在用户
        if($user){
            //存在用户，将传过来的密码与数据库密码作对比
            if(\Yii::$app->security->validatePassword($this->password,$user->password_hash)){
                //密码正确，登录，保存到session，
                \Yii::$app->user->login($user,$this->rememberMe ? 3600: 0);
                $user->lsat_login_time=time();
                //获取当前登录ip
                $ip=\Yii::$app->request->getUserIP();
                $user->lsat_login_ip=$ip;
                $user->save(false);
                return true;
            }else{
                //密码错误
                $this->addError('password_hash','密码错误');
            }
        }else{//用户名不存在
            //用户名不存在
            $this->addError('username','用户不存在');
        }
        return false;
    }

}