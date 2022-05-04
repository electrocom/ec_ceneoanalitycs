<?php


require_once 'CeneoCustomObject.php';
require_once _PS_MODULE_DIR_ . 'ec_ceneoanalitycs/classes/ceneo/CeneoPrices.php';
require_once _PS_MODULE_DIR_ . 'ec_ceneoanalitycs/classes/CeneoShopPrice.php';
require_once _PS_MODULE_DIR_ . 'ec_ceneoanalitycs/classes/CeneoRenderXML.php';
class CeneoAnalyticsModel extends CeneoCustomObject
{
private  $CeneoConfiguration;
private   $product;
    function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->CeneoConfiguration = new CeneoConfiguration();
        parent::__construct($id, $id_lang, $id_shop);
        $this->product=new Product($this->id_product);
    }

    public static $definition = [
        'table' => 'ec_ceneo_analitycs',
        'primary' => 'id_ceneo_analitycs',
        'multilang' => false,
        'fields' => [
            'id_ceneo_analitycs' => ['type' => self::TYPE_INT, 'db_type' => 'int', 'validate' => 'isInt'],
            'id_product' => ['type' => self::TYPE_INT, 'db_type' => 'int', 'validate' => 'isInt', 'required' => false],
            'id_ceneo' => ['type' => self::TYPE_INT, 'db_type' => 'int', 'validate' => 'isInt', 'required' => false],
            'quantity_offers' => ['type' => self::TYPE_INT, 'db_type' => 'int', 'validate' => 'isInt', 'required' => false],
            'shop_name' => ['type' => self::TYPE_STRING, 'db_type' => 'varchar(254)', 'required' => false],
            'ceneo_best_price' => ['type' => self::TYPE_FLOAT, 'db_type' => 'decimal(20,6)', 'required' => false],
            'ceneo_free_delivery' => ['type' => self::TYPE_BOOL, 'db_type' => 'int', 'required' => false],
            'ceneo_in_stock' => ['type' => self::TYPE_BOOL, 'db_type' => 'int', 'required' => false],
            'ceneo_price_with_delivery' => ['type' => self::TYPE_BOOL, 'db_type' => 'decimal(20,6)', 'required' => false],
            'ceneo_always_get_price' => ['type' => self::TYPE_BOOL, 'db_type' => 'int', 'required' => false],
            'ceneo_offers' => ['type' => self::TYPE_STRING, 'db_type' => 'JSON', 'required' => false],
            'current_price' => ['type' => self::TYPE_FLOAT, 'db_type' => 'decimal(20,6)', 'required' => false],
            'wholesale_price' => ['type' => self::TYPE_FLOAT, 'db_type' => 'decimal(20,6)', 'required' => false],
            'min_price_spread' => ['type' => self::TYPE_FLOAT, 'db_type' => 'decimal(20,6)', 'required' => false],
            'delivery_cost' => ['type' => self::TYPE_FLOAT, 'db_type' => 'int', 'required' => false],
            'active' => ['type' => self::TYPE_INT, 'db_type' => 'int', 'required' => false],
            'in_stock' => ['type' => self::TYPE_INT, 'db_type' => 'int', 'required' => false],
            'enable_time_table' => ['type' => self::TYPE_INT, 'db_type' => 'int', 'required' => false],
            'time_table' => ['type' => self::TYPE_STRING, 'db_type' => 'JSON', 'required' => false],
            'include_delivery_cost' => ['type' => self::TYPE_BOOL, 'db_type' => 'int', 'required' => false],
            'buy_now' => ['type' => self::TYPE_INT, 'db_type' => 'int', 'required' => false],
            'share_to_ceneo' => ['type' => self::TYPE_INT, 'db_type' => 'int', 'required' => false],

            'TS_Mod' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'db_type' => 'datetime',
            ],
            'TS_Zal' => [
                'type' => self::TYPE_DATE,
                'validate' => 'isDate',
                'validate' => 'isDate',
                'db_type' => 'datetime',
            ]
        ],
    ];



    public function getDeliveryCost(){
        if((int)$this->delivery_cost)
        return (float)$this->delivery_cost;
        else
        return (float)$this->CeneoConfiguration->delivery_cost;
    }


    public function getWholesalePrice($tax=false){

        if((int)$this->wholesale_price)
       return (float)$this->wholesale_price;
        else {
            if($tax)
            return (float)$this->product->wholesale_price+($this->product->wholesale_price*$this->product->getTaxesRate()/100 );
            else
            return    (float)$this->product->wholesale_price;

        }
    }

    public function getBuyNow(){

        if($this->buy_now)
            return $this->buy_now;
        else{
         return   $this->CeneoConfiguration->buy_now;
        }
    }

    public function getIsBuyNow(){
     switch ($this-> getBuyNow()){
         case 1:
             return true;
             break;
         case 2:
             if($this->hasBestPrice())
                 return true;
             else
                 return false;

         break;

         case 3:
             return false;
         break;

         default:
             return false;
     }

    }

    public function getIncludeDeliveryCost(){


        if($this->include_delivery_cost==0){
            return $this->CeneoConfiguration->include_delivery_cost;
        }
        elseif($this->include_delivery_cost==2){
            return false;
        }
        elseif($this->include_delivery_cost==1){
            return true;
        }
        else{
            return false;
        }
    }


    public function getEnableTimeTable(){
     if((int)$this->enable_time_table==0)
     {
         if((int)$this->CeneoConfiguration->enable_time_table)
             return 3;
         else
             return 2;
     }

        else{
        return (int)$this->enable_time_table;
        }


    }
    public function getTimeTable(){

        switch ($this->getEnableTimeTable()) {
            case 1:
                return json_decode($this->time_table,JSON_OBJECT_AS_ARRAY);
                break;
            case 3:
                return json_decode( $this->CeneoConfiguration->time_table,JSON_OBJECT_AS_ARRAY);
                break;
            case 2:
                return false;
                break;

            default:
                return false;
                break;
        }

    }

        public function getInStock(){


            if($this->in_stock==0){
                return $this->CeneoConfiguration->in_stock;
            }
            elseif($this->in_stock==2){
                return false;
            }
            elseif($this->in_stock==1){
                return true;
            }
            else{
                return false;
            }
    }



        public function getActive(){
            if($this->active==0){
                return $this->CeneoConfiguration->active;
            }
            elseif($this->active==2){
                return false;
            }
            elseif($this->active==1){
                return true;
            }
            else{
                return false;
            }
        }


    public function getShareToCeneo(){
        if($this->share_to_ceneo==0){
            return $this->CeneoConfiguration->share_to_ceneo;
        }
        elseif($this->share_to_ceneo==2){
            return false;
        }
        elseif($this->share_to_ceneo==1){
            return true;
        }
        else{
            return false;
        }


    }

        public function getMinPriceSpread(){
        /*Narzut minimalny */
            if((int)$this->min_price_spread)
                return $this->min_price_spread;
            else{
                return $this->CeneoConfiguration->min_price_spread;
            }
        }




    static public function ImportPriceFromCeneo(){

        $CeneoAnalyticsRepository = new CeneoAnalyticsRepository('CeneoOffersList');
        foreach ( $CeneoAnalyticsRepository as $key =>$val ) {

            $date= date_create()->format('Y-m-d H:i:s');
            $ceneo=new CeneoPrices($val->id_ceneo);
            $json_ceneo_offers = json_encode($ceneo->getPrices());
            $ceneo_best_price= $ceneo->getBestPrice(1,true);
            $quantity_offers=$ceneo->getQuantityOffers();
            if($ceneo_best_price){
            $sql = 'UPDATE `ps_ec_ceneo_analitycs` SET `ceneo_best_price` = \''.$ceneo_best_price['price'].'\', `TS_Mod` = \''.$date.'\', `shop_name` =\''.$ceneo_best_price['shop'].'\' , `ceneo_free_delivery` =\''.$ceneo_best_price['free_delivery'].'\' , `ceneo_in_stock` =\''.$ceneo_best_price['in_stock'].'\'  , `ceneo_price_with_delivery` =\''.$ceneo_best_price['price_with_delivery'].'\', `ceneo_offers`=\''.$json_ceneo_offers.'\' , `quantity_offers`=\''.$quantity_offers.'\' WHERE `ps_ec_ceneo_analitycs`.`id_ceneo_analitycs` = '.$val->id_ceneo_analitycs.'  ';
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
        }else{
                $sql = 'UPDATE `ps_ec_ceneo_analitycs` SET `ceneo_best_price` = \'\', `TS_Mod` = \''.$date.'\', `shop_name` =\'\' , `ceneo_free_delivery` =\'\' , `ceneo_in_stock` =\'\'  , `ceneo_price_with_delivery` =\'\', `ceneo_offers`=\'{}\'  , `quantity_offers`=\'\'  WHERE `ps_ec_ceneo_analitycs`.`id_ceneo_analitycs` = '.$val->id_ceneo_analitycs.'';
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
            }


        }

    }
   public function getCurrentPriceWithoutVat(){
     return  $this->current_price- ($this->current_price* $this->product->getTaxesRate()/(100+$this->product->getTaxesRate()));
   }

    static public function ImportFromSuppiler(){
$sql='SELECT * FROM `ps_product` WHERE `active` = 1 AND `id_product` NOT IN( SELECT `id_product` FROM `ps_ec_ceneo_analitycs` ) ';
$result= Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

foreach ( $result as $key =>$val ) {

   $date= date_create()->format('Y-m-d H:i:s');
   $id_ceneo=null;
    $sql = 'INSERT INTO `ps_ec_ceneo_analitycs` (`id_ceneo_analitycs`, `id_product`, `id_ceneo`, `share_to_ceneo`, `TS_Zal`)
VALUES (NULL,\'' . $val['id_product'] . '\', \'' . $id_ceneo. '\',\'0\', \''.$date.'\')';
    date_create()->format('Y-m-d H:i:s');
    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);


}
     return;
 }
    static public function getCeneoProductsPublic(){
        $CeneoConfiguration = new CeneoConfiguration();
        $share_to_ceneo=(int)$CeneoConfiguration->share_to_ceneo;
        $idShop = Context::getContext()->shop->id;
         $sql='SELECT  `ps_ec_ceneo_analitycs`.id_product FROM `ps_ec_ceneo_analitycs` inner join `ps_product_shop` on `ps_product_shop`.`id_product`=`ps_ec_ceneo_analitycs`.`id_product` WHERE ((`share_to_ceneo` = 0 and '.$share_to_ceneo.') or `share_to_ceneo` = 1) and `ps_product_shop`.`active`=1 and `ps_product_shop`.`id_shop`='.$idShop.' ';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    }


    public function ImportFromArray($arr){
        foreach ($arr as $key => $value) {
            if($key=='TimeTable')
                $this->time_table =  json_encode($value);
            else
                $this->{$key} = $value;
        }
    }
    public function hasBestPrice(){


         if($this->shop_name==$this->CeneoConfiguration->shop_name)
          return true;
        else
          return  false;
    }
    public function getMinPrice($incl_delivery=false){
        $wholesale_price=$this->getWholesalePrice();
        $spread_price=$this->getMinPriceSpread();
        $tax=$this->product->getTaxesRate();
        $withouttax= $wholesale_price+($wholesale_price*$spread_price/100);




    return (float)$withouttax+($withouttax*$tax/100)+$this->getDeliveryCost();


    }
    public function getCeneoOffer($tmp=true){
        return json_decode($this->ceneo_offers,JSON_OBJECT_AS_ARRAY);
    }
    public function getSecondOffer(){
       try{
        $tmp= $this->getCeneoOffer();
 //       echo '<pre>';
 //        print_r($tmp);
 //       echo '</pre>';
        if(isset($tmp[1]))
            return $tmp[1];
            else
            return null;
            }
            catch (Exception $e){
           return null;
            }
    }

    public function getCeneoPrice()
    {
        $cprice= $this->ceneo_price_with_delivery;
        $win=$this->hasBestPrice();
        $second_offer=$this->getSecondOffer();
        $min_price=$this->getMinPrice();
        $regular_price=$this->product->price+($this->product->price*$this->product->getTaxesRate()/100); //Cena brutto
        $max_price=$regular_price;
        $skok=1;
        $price=0;
        if(isset($second_offer['price_with_delivery']))
         $second_price=$second_offer['price_with_delivery'];
        else
         $second_price=0;

        if($this->quantity_offers==0||($this->quantity_offers==1&&$win))
        {//Przypadek gdy nie ma ofert na ceneo lub nasza oferta jest jedyna

            $price=  $regular_price;
        }elseif($win){//Przypadek gdy nasza cena jest najniższa, wtedy podwyższamy cenę
                if($second_price){
            $price=   $second_price-$skok;


                }
            else{
            $price= $cprice;

            }


                 }
        else{//Przypadek gdy przegrywamy, wtedy obniżamy cenę.
                   if($cprice-$skok>=$min_price)
                       $price= $cprice-$skok;
                   else
                       $price= $min_price;
                    }

               if($price<$min_price)
                     $price=$min_price;

               if($price>$max_price)
                     $price=$max_price;

               if($price==0){

                   return $regular_price;
                             }
               else{

                   return $price;
                   }


    }

    public function grtPrice(){

    }

    public function getIsActiveAutoControlPriceNow(){

        $time_table=$this->getTimeTable();
        if(is_array($time_table)){
        $dayofweek= date("D");
        $now=time();

      $start1=   strtotime(date("d-m-Y").' '.$time_table[$dayofweek]['start1']);
      $stop1=     strtotime(date("d-m-Y").' '.$time_table[$dayofweek]['stop1']);
      $start2=   strtotime(date("d-m-Y").' '.$time_table[$dayofweek]['start2']);
      $stop2=     strtotime(date("d-m-Y").' '.$time_table[$dayofweek]['stop2']);

        if(($now > $start1 && $now < $stop1)||($now > $start2 && $now < $stop2)) {
           return true;
        }else
           return false;
    }
        else{

            return true;
        }



    }


    public $id_ceneo_analitycs;
    public $id_product;
    public $id_ceneo;
    public $include_delivery_cost;
    public $delivery_cost;
    public $shop_name;
    public $ceneo_best_price;
    public $current_price;
    public $wholesale_price;
    public $min_price_spread;
    public $ceneo_free_delivery;
    public $ceneo_in_stock;
    public $ceneo_price_with_delivery;
    public $ceneo_always_get_price;
    public $buy_now;
    public $time_table;
    public $active;
    public $ceneo_offers;
    public $in_stock;
    public $enable_time_table;
    public $quantity_offers;
    public $share_to_ceneo;
    public $TS_Mod;
    public $TS_Zal;

//SELECT * FROM `ps_ec_ceneo_analitycs` WHERE (`share_to_ceneo` = 0 and 1)
}
