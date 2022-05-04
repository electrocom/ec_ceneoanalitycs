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

            $CeneoCategory = new CeneoCategory();
            $CeneoCategory->assignToCeneoCategory();

        }elseif( Tools::getIsset('xml')){
            $this->renderXML();
        }else{
            echo "Nieprawidłowy parametr";
        }

        }


       die();
    }

     public function generateCeneoOfferts(){

         $CeneoAnalyticsRepository = new CeneoAnalyticsRepository('CeneoOffersList');

         foreach ( $CeneoAnalyticsRepository as $key =>$tmp ) {
            $tmp->current_price=   $tmp->getCeneoPrice();





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
            }else if($CeneoConfiguration->last_always_enable_group_id>0){
                $CeneoShopPrice = new CeneoShopPrice();
                $CeneoShopPrice->DeleteSpecificPrice( $tmp->id_product,0,$CeneoConfiguration->last_always_enable_group_id);
                }


            //$tmp->TS_Mod=date('Y-m-d H:i:s');
          $tmp->update(1,1);

        }

    }

    public function renderXML()
    {
        $CeneoAnalyticsRepository = new CeneoAnalyticsRepository('OnlyBestPricesList');
        $CeneoRenderXML = new CeneoRenderXML($CeneoAnalyticsRepository);
        $CeneoRenderXML->generateXML();

    }

}
