<?php


require_once 'CeneoCustomObject.php';
require_once _PS_MODULE_DIR_ . 'ec_ceneoanalitycs/classes/ceneo/CeneoPrices.php';
class CeneoConfiguration
{
const PREFIX = 'CeneoAnalitycs_';
    const  INITCONF=array(
'delivery_cost'=>15,
'active'=>false,
'in_stock'=>true,
'with_delivery'=>false,
'min_price_spread'=>5,
'buy_now'=>2,
'include_delivery_cost'=>1,
'ceneo_always_get_price'=>1,
'buy_now'=>1,
'enable_time_table'=>2,
'share_to_ceneo'=>false,
 'always_enable_group_id'=>0
);

    const FORMSELECT=array(array('id_option' => 0, 'name' => 'Użyj ustawień globalnych'), array('id_option' => 1, 'name' => 'TAK'), array('id_option' => 2, 'name' => 'NIE'));
    const FORMSELECTTIMETABLE=array(array('id_option' => 0,'name' => 'Użyj ustawień globalnych'), array('id_option' => 1, 'name' => 'Używaj Harmonogramu produktu'), array('id_option' => 2,'name' => 'Wyłącz harmonogram' ));
    const FORMSELECTBUYNOW=array(
        array(
            'id_option' => 0,
            'name' => 'Użyj ustawień globalnych',
        ),
        array(
            'id_option' => 1,
            'name' => 'Włączne zawsze',
        ),

        array(
            'id_option' => 2,
            'name' => 'Włączone gdy najlepsza cena',
        ),

        array(
            'id_option' => 3,
            'name' => 'Wyłączone zawsze',
        )
    );


    public function __get($name)
    {
        if(Configuration::hasKey(CeneoConfiguration::PREFIX.$name))
            return Configuration::get( CeneoConfiguration::PREFIX.$name);
        else{
            if(isset(CeneoConfiguration::INITCONF[$name]))
                    return CeneoConfiguration::INITCONF[$name];
                else return null;
        }
        return $this->get($name);
    }


    public function __set($name,$value){
        if($name=='always_enable_group_id')
        {
            Configuration::updateValue( CeneoConfiguration::PREFIX.'last_always_enable_group_id',  Configuration::get( CeneoConfiguration::PREFIX.$name));
            Configuration::updateValue( CeneoConfiguration::PREFIX.$name,  $value);

        }

        Configuration::updateValue( CeneoConfiguration::PREFIX.$name,  $value);
    }


}