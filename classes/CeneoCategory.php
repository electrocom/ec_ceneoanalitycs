<?php




class CeneoCategory
{
public function __construct()
{
}

public function assignToCeneoCategory(){

   $CeneoAnalyticsRepository = new CeneoAnalyticsRepository('OnlyBestPricesList');
   $id_ceneo_category=75;

   $ids= $CeneoAnalyticsRepository->getIdsProduct();


    $sql='DELETE FROM `ps_category_product` WHERE `ps_category_product`.`id_category` = '.$id_ceneo_category.'   ';
    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
   foreach ($ids as $val){

       $sql='INSERT INTO `ps_category_product` (`id_category`, `id_product`, `position`) VALUES (\''.$id_ceneo_category.'\', \''.$val.'\', \'0\')';
       Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
echo $val.',';

   }
}




}