<?php
require_once _PS_MODULE_DIR_ . 'ec_ceneoanalitycs/classes/Models/CeneoConfiguration.php';

class AdminCeneoConfigurationController extends ModuleAdminController
{
public static $definition;
    public function __construct()
    {

        //$this->table      = 'ec_ceneo_configuration';
        //$this->className  = 'AdminCeneoConfiguration';
        $this->name= Context::getContext()->getTranslator()->trans ('CeneoConfiguration');
        $this->displayName=  Context::getContext()->getTranslator()->trans ('Ceneo Konfiguracja');
        $this->bootstrap  = true;
        parent::__construct();
    }

    public  function renderList(){

        $values = Tools::getAllValues();
        $CeneoConfiguration = new CeneoConfiguration();

        if(isset($values['submitAddconfiguration'])) {
            $CeneoConfiguration->delivery_cost=$values['delivery_cost'];
            $CeneoConfiguration->active=$values['active'];
            $CeneoConfiguration->in_stock=$values['in_stock'];
            $CeneoConfiguration->min_price_spread=$values['min_price_spread'];
            $CeneoConfiguration->include_delivery_cost=$values['include_delivery_cost'];
            $CeneoConfiguration->ceneo_always_get_price=$values['ceneo_always_get_price'];
            $CeneoConfiguration->enable_time_table=$values['enable_time_table'];
            $CeneoConfiguration->buy_now=$values['buy_now'];
            $CeneoConfiguration->shop_name=$values['shop_name'];
            $CeneoConfiguration->share_to_ceneo=$values['share_to_ceneo'];
            $CeneoConfiguration->always_enable_group_id=$values['always_enable_group_id'];
            $CeneoConfiguration->time_table= json_encode($values['TimeTable']);
        }

        $this->fields_value['delivery_cost'] = $CeneoConfiguration->delivery_cost;
        $this->fields_value['active'] = $CeneoConfiguration->active;
        $this->fields_value['in_stock'] = $CeneoConfiguration->in_stock;
        $this->fields_value['min_price_spread'] = $CeneoConfiguration->min_price_spread;
        $this->fields_value['include_delivery_cost'] = $CeneoConfiguration->include_delivery_cost;
        $this->fields_value['ceneo_always_get_price'] = $CeneoConfiguration->ceneo_always_get_price;
        $this->fields_value['buy_now'] = $CeneoConfiguration->buy_now;
        $this->fields_value['shop_name'] = $CeneoConfiguration->shop_name;
        $this->fields_value['share_to_ceneo'] = $CeneoConfiguration->share_to_ceneo;
        $this->fields_value['enable_time_table'] = $CeneoConfiguration->enable_time_table;

        $this->fields_value['always_enable_group_id'] = $CeneoConfiguration->always_enable_group_id;

        $TimeTableForm = new TimeTableForm();
        $TimeTableForm->values=json_decode($CeneoConfiguration->time_table,JSON_OBJECT_AS_ARRAY);


        $buy_now_options=CeneoConfiguration::FORMSELECTBUYNOW;
       array_splice($buy_now_options,0,1);
        $html_content=' <span> Pobieranie cen z CENEO do analizy:</span> <a href="'.Context::getContext()->shop->getBaseURL(true).'ceneo?getceneo&token='.substr(   md5(_COOKIE_KEY_),0,10).'">'.Context::getContext()->shop->getBaseURL(true).'ceneo?getceneo&token='.substr(   md5(_COOKIE_KEY_),0,10).'</a></br>';
        $html_content.=' <span> Autoregulacja cen w sklepie:</span> <a href="'.Context::getContext()->shop->getBaseURL(true).'ceneo?generate&token='.substr(   md5(_COOKIE_KEY_),0,10).'">'.Context::getContext()->shop->getBaseURL(true).'ceneo?generate&token='.substr(   md5(_COOKIE_KEY_),0,10).'</a></br>';
        $html_content.=' <span> Generowanie XML dla Ceneo:</span> <a href="'.Context::getContext()->shop->getBaseURL(true).'ceneo?xml&token='.substr(   md5(_COOKIE_KEY_),0,10).'">'.Context::getContext()->shop->getBaseURL(true).'ceneo?xml&token='.substr(   md5(_COOKIE_KEY_),0,10).'</a></br>';

        $this->fields_form =         array(
            'legend' => array(
                'title' => $this->l('Konfiguracja Ceneo'),
                'image' => '../img/admin/contact.gif'
            ),
            'input' => array(
                array('type' => 'text', 'label' => $this->l('Nazwa sklepu na Ceneo'), 'name' =>  'shop_name', 'size' => 30, 'required' => true),
                array('type' => 'text', 'label' => $this->l('Domyślny koszt dostawy'), 'name' =>  'delivery_cost', 'size' => 30, 'required' => true),
                array('type' => 'text', 'label' => $this->l('Domyślny narzut minimalny(%)'), 'name' =>  'min_price_spread', 'size' => 30, 'required' => true),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Domyślnie pobieraj ceny z Ceneo i reguluj je'),
                    'name' => 'active',
                    //  'desc' => $this->l('Enable grades on products.'),
                    'values' => array(
                        array(
                            'id' => 'active_1',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_0',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        ),

                    ),
                ),

                array(
                    'type' => 'switch',
                    'label' => $this->l('Domyślnie udostępniaj produkty na Ceneo'),
                    'name' => 'share_to_ceneo',
                    //  'desc' => $this->l('Enable grades on products.'),
                    'values' => array(
                        array(
                            'id' => 'share_to_ceneo_1',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'share_to_ceneo_0',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        ),

                    ),
                ),

                array(
                    'type' => 'switch',
                    'label' => $this->l('Porównuj ceny tylko z produktami dostępnymi:'),
                    'name' => 'in_stock',

                    'values' => array(
                        array(
                            'id' => 'in_stock_1',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'in_stock_0',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        ),

                    ),
                ),

                    array(
                    'type' => 'switch',
                    'label' => $this->l('Uwzględniaj koszt dostawy'),
                    'name' => 'include_delivery_cost',

                    'values' => array(
                        array(
                            'id' => 'include_delivery_cost_1',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'include_delivery_cost_0',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        ),

                    ),
                ),

                array(
                    'type' => 'switch',
                    'label' => $this->l('Zawsze pobieraj cenę z CENEO do Analizy'),
                    'name' => 'ceneo_always_get_price',

                    'values' => array(
                        array(
                            'id' => 'ceneo_always_get_price_1',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'ceneo_always_get_price_0',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        ),

                    ),
                ),
                array('type' => 'select', 'label' => $this->l('Grupa klientów'),'desc'=>'Grupa klientów dla której zawsze bedzie właczona autoregulacja cen', 'name' =>'always_enable_group_id', 'required' => true, 'default_value' => 0,
                    'options' =>array('query' => Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT id_group, `name` FROM `ps_group_lang` Union SELECT 0,\'Brak\'  '), 'id' => 'id_group', 'name' => 'name')),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Użyj Harmonogramu'),
                    'name' => 'enable_time_table',
                    'desc'=> 'Jeśli wyłączone produkty będą mialy właczoną autoregulację cen cały czas',
                    'values' => array(
                        array(
                            'id' => 'enable_time_table_1',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'enable_time_table_0',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        ),

                    ),
                ),
                array('type' => 'select', 'label' => $this->l('KUP TERAZ'),'desc'=>' ', 'name' =>'buy_now', 'required' => true, 'default_value' => 1,
                    'options' =>array('query' => $buy_now_options, 'id' => 'id_option', 'name' => 'name')),
                array('type' => 'html',  'html_content' =>     $TimeTableForm->generate(),'name'=>'Mon','label'=> $this->l('Harmonogram')),
                array('type' => 'html',  'html_content' =>     $html_content,'name'=>'aaa','label'=> $this->l('Linki')),

            ),
            'submit' => array('title' => $this->l('Save'))
        );
        ;

        return parent::renderForm();
}




}

