<?php




class CeneoShopPrice
{



    public function SaveSpecyficPrice( $cena, $id_product, $id_customer=0, $id_group = 0, $id_shop =1, $reduction_type_percentage = false, $reduction = 0)
    {


        $specificprice = new SpecificPriceCore();
        $specificprice->id_specific_price_rule = 0;
        $specificprice->id_cart = 0;
        $specificprice->id_product = $id_product;
        $specificprice->id_shop = $id_shop;
        $specificprice->id_shop_group = 0;
        $specificprice->id_currency = 0;
        $specificprice->id_group = $id_group;
        $specificprice->id_country = 0;
        $specificprice->id_customer = $id_customer;
        $specificprice->id_product_attribute = 0;
        $specificprice->price = $reduction_type_percentage ? -1 : round($cena, 2);
        $specificprice->from_quantity = 1;
        $specificprice->from = '0000-00-00 00:00:00';
        $specificprice->to = '0000-00-00 00:00:00';
        $specificprice->reduction = $reduction_type_percentage ? $reduction * 0.01 : 0;
        $specificprice->reduction_tax = $reduction_type_percentage ? '0' : '1';;
        $specificprice->reduction_type = $reduction_type_percentage ? 'percentage' : 'amount';


        $specificprice->id = $specificprice->exists(
            $specificprice->id_product,
            $specificprice->id_product_attribute,
            $specificprice->id_shop,
            $specificprice->id_group,
            $specificprice->id_country,
            $specificprice->id_currency,
            $specificprice->id_customer,
            $specificprice->from_quantity, $specificprice->from, $specificprice->to);

        try {
            
            $specificprice->save(1, 1);

        } catch (Exception $e) {
            echo $e->getMessage() . ' ' . $cena;
        }

    }

    public function DeleteSpecificPrice($id_product,$id_shop=0,$id_group=0){
        if($id_shop==0)
        $id_shop = (int)Shop::getContextShopID();

        $specificprice = new SpecificPriceCore();

        $specificprice->id = $specificprice->exists($id_product,
            0,
            $id_shop,
            $id_group,
         0,
            0,
            0,
            1,'0000-00-00 00:00:00', '0000-00-00 00:00:00');
        $specificprice->delete();

    }

    public function Rabat($cena, $rabat)
    {
        $tmp = round($cena - $cena * $rabat * 0.01, 2);
        //echo 'Cena przed rabatem:'.$cena.' po rabacie:'.$tmp.' rabat:'.$rabat.PHP_EOL;
        return $tmp;
    }

    public function SaveNormalPrice()
    {

    }

    public function PriceReductionForGroup($id_group, $id_shop, $id_product, $reduction, $reduction_percentage = true)
    {
        $this->ZapiszCeneSpecyficzna(0, $reduction, $id_product, $id_group, $id_shop, $reduction_percentage, $reduction);

    }


    public function ZapiszCene($cena, $id_product, $id_shop, $id_customer = 0)
    {

        $cena = round($cena, 6);
        if ($id_customer) {
            $this->ZapiszCeneSpecyficzna($id_customer, $cena, $id_product, 0, $id_shop);
            //echo "ZapiszCeneSpecyficzna($id_customer,$cena,$id_product,0,$id_shop)";
        } else {

            $sql = "UPDATE `" . _DB_PREFIX_ . "product_shop` SET  `price` = '$cena' WHERE `ps_product_shop`.`id_product` = $id_product AND `ps_product_shop`.`id_shop` = " . $id_shop . "";
            Db::getInstance()->execute($sql);
            // echo $sql."<br>";
        }

    }

}


