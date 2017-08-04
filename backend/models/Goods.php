<?php
namespace backend\models;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class Goods extends ActiveRecord{
    //public $imgFile;
    public static $sale_option=[0=>'下架',1=>'在售'];
    //查询出所有商品分类
    public static function getCategory(){
        $categorys=GoodsCategory::find()->select(['name','id','depth'])->orderBy('tree,lft')->asArray()->all();
        //var_dump($categorys);exit;
        foreach($categorys as &$category){
            $category['name']=str_repeat('—',$category['depth']).$category['name'];
        }
       $categorys=ArrayHelper::map($categorys,'id','name');
        return $categorys;
    }
    //查询出所有品牌分类
    public static function getBrands(){
        $brands=Brand::find()->select(['name','id'])->where(['>','status','-1'])->asArray()->all();
        $brands=ArrayHelper::map($brands,'id','name');
        return $brands;
    }
    //属性
    public function attributeLabels()
    {
        return [
            'name'=>'名称',
            'sn'=>'货号',
            'logo'=>'LOGO',
            'goods_category_id'=>'商品分类',
            'brand_id'=>'品牌分类',
            'market_price'=>'市场价',
            'shop_price'=>'商品售价',
            'stock'=>'库存',
            'is_on_sale'=>'是否在售',
            'sort'=>'排序'
        ];
    }
    public function rules()
    {
        return [
            [['name','goods_category_id','brand_id','stock','sort','market_price','shop_price','is_on_sale'],'required'],
            [['sort','stock'],'integer'],
            [['market_price','shop_price'],'match','pattern'=>'/^\d{1,}\.\d{2}$/','message'=>'价格需要精确到小数点后两位'],
            [['name'], 'string', 'max' => 50],
            [['logo'], 'string', 'max' => 255],
        ];
    }

    //建立商品与品牌的关系
    public function getBrandName(){
        return $this->hasOne(Brand::className(),['id'=>'brand_id']);
    }
    //建立商品与商品分类之间的关系
    public function getCategoryName(){
        return $this->hasOne(GoodsCategory::className(),['id'=>'goods_category_id']);
    }

    //建立与商品相册之间的关系
    public function getGalleries(){
        return $this->hasMany(GoodsGallery::className(),['goods_id'=>'id']);
    }
}