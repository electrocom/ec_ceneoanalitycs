<?php


class CeneoAnalyticsRepository implements Iterator,Countable
{
    private  $ceneoAnalytics;
    private  $idsCeneoAnalytics;
    private $offset;

    public function __construct($list=''){

        $this->offset=0;

        switch ($list){

            case 'CeneoOffersList':
                $sql='SELECT id_ceneo_analitycs,p.id_product FROM `ps_ec_ceneo_analitycs` ca  inner JOIN ps_product p on p.id_product= ca.id_product where ca.id_ceneo!=0  and p.active=1 ORDER BY `ca`.`id_product` ASC ';
                $this->idsCeneoAnalytics=  Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            break;
            case 'ImportPriceFromList':
                $sql='SELECT id_ceneo_analitycs,p.id_product FROM `ps_ec_ceneo_analitycs` ca  inner JOIN ps_product p on p.id_product= ca.id_product where ca.id_ceneo!=0  and p.active=1  ORDER BY `ca`.`id_product` ASC  ';
                $this->idsCeneoAnalytics=  Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            break;

            case 'OnlyBestPricesList':
                $CeneoConfiguration = new CeneoConfiguration();
                $sql='SELECT id_ceneo_analitycs,p.id_product FROM `ps_ec_ceneo_analitycs` ca inner JOIN ps_product p on p.id_product= ca.id_product  where ca.id_ceneo!=0  and p.active=1 and (ca.`current_price` <=ca. `ceneo_price_with_delivery` or ca.shop_name=\''.$CeneoConfiguration->shop_name.'\')';
                $this->idsCeneoAnalytics=  Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            break;



            default:
                $sql='SELECT id_ceneo_analitycs,p.id_product FROM ps_ec_ceneo_analitycs';
                $this->idsCeneoAnalytics= Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        }

    }

    public function getIdsProduct(){
     return   array_column($this->idsCeneoAnalytics, 'id_product');
    }

    public function current()
    {
       return $this->ceneoAnalytics= new CeneoAnalyticsModel($this->idsCeneoAnalytics[$this->offset]['id_ceneo_analitycs']);

    }

    public function next()
    {
        $this->offset++;
    }

    public function key()
    {
        return $this->idsCeneoAnalytics[$this->offset]['id_ceneo_analitycs'];
    }

    public function valid()
    {
        // TODO: Implement valid() method.
      return  array_key_exists($this->offset,  $this->idsCeneoAnalytics);
    }

    public function rewind()
    {
        $this->offset=0;
    }

    public function count()
    {
        return count($this->idsCeneoAnalytics);
    }
}