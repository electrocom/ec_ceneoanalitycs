<?php


class Ec_CeneoanalitycsCeneoModuleFrontController extends ModuleFrontController
{

    public function __construct()
    {


        parent::__construct();
    }

    public function init()
    {
     parent::init();
    ;
        $token=Tools::getValue('token');
        if(substr(   md5(_COOKIE_KEY_),0,10)==$token){
        if( Tools::getIsset('getceneo')){
            CeneoAnalyticsModel::ImportPriceFromCeneo();
        }elseif ( Tools::getIsset('generate')){
            $this->generateCeneoOfferts();
        }elseif( Tools::getIsset('xml')){
            $this->renderXML();
        }else{
            echo "Nieprawidłowy parametr";
        }

        }


       die();
    }

     public function generateCeneoOfferts(){
        $sql='SELECT * FROM `ps_ec_ceneo_analitycs`  ORDER BY `ps_ec_ceneo_analitycs`.`TS_Mod` ASC ';
        $result= Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ( $result as $key =>$val ) {

            $tmp=new CeneoAnalyticsModel($val['id_ceneo_analitycs']);
            $tmp->current_price=$tmp->getCeneoPrice();



            if($tmp->getActive()&& $tmp->getIsActiveAutoControlPriceNow()){
                /*Ustawianie cen specjalnych, gdy autoregulacja jest  włączona */
                $CeneoShopPrice = new CeneoShopPrice();
                $CeneoShopPrice->SaveSpecyficPrice($tmp->getCurrentPriceWithoutVat(), $tmp->id_product);
            }else{
                /*Usuwanie cen specjalnych, gdy autoregulacja została wyłaczona */
                $CeneoShopPrice = new CeneoShopPrice();
                $CeneoShopPrice->DeleteSpecificPrice( $tmp->id_product);

            }

            $CeneoConfiguration = new CeneoConfiguration();
            if((int)$CeneoConfiguration->always_enable_group_id){
                $CeneoShopPrice = new CeneoShopPrice();
                $CeneoShopPrice->SaveSpecyficPrice($tmp->getCurrentPriceWithoutVat(), $tmp->id_product,0,(int)$CeneoConfiguration->always_enable_group_id);
            }else{
                $CeneoShopPrice = new CeneoShopPrice();
                $CeneoShopPrice->DeleteSpecificPrice( $tmp->id_product,0,$CeneoConfiguration->last_always_enable_group_id);
                }
            $tmp->update(1,1);
        }

    }

    public function renderXML()
    {
        $CeneoRenderXML = new CeneoRenderXML();
        $CeneoRenderXML->generateXML();

    }

}
