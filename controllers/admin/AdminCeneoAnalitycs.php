<?php
require_once _PS_MODULE_DIR_ . 'ec_ceneoanalitycs/classes/Models/CeneoAnalyticsModel.php';
require_once _PS_MODULE_DIR_ . 'ec_ceneoanalitycs/classes/TimeTableForm.php';
require_once _PS_MODULE_DIR_ . 'ec_ceneoanalitycs/classes/Models/CeneoConfiguration.php';
class AdminCeneoAnalitycsController extends ModuleAdminController
{
public static $definition;

    public function __construct()
    {


        $this->table      = 'ec_ceneo_analitycs';

        $this->identifier = 'id_ceneo_analitycs';
        $this->className  = 'AdminCeneoAnalitycsController';
        $this->name= Context::getContext()->getTranslator()->trans ('Ceneo Analitycs');
        $this->displayName=  Context::getContext()->getTranslator()->trans ('Ceny');

        $this->addRowAction('edit');
        $this->addRowAction('delete');


        $this->fields_list = array(
            'id_ceneo_analitycs' => array('title' =>Context::getContext()->getTranslator()->trans ('ID'), 'align' =>'center', 'width' => 25),
           'id_product' => array('title' => Context::getContext()->getTranslator()->trans  ('id_product'), 'width' =>  60,'filter_key' => 'a!id_product'),
            'id_ceneo' => array('title' => Context::getContext()->getTranslator()->trans  ('Ceneo ID'), 'width' =>  60,  'remove_onclick' => true,'callback' => 'giveMyCallBack'),
            'shop_name' => array('title' => Context::getContext()->getTranslator()->trans  ('Nazwa sklepu'), 'width' =>120),
            'name' => array('title' => Context::getContext()->getTranslator()->trans  ('Nazwa '), 'width' =>400),
            //'ceneo_best_price' => array('title' => Context::getContext()->getTranslator()->trans  ('Najniższa cena'), 'width' =>120),


           // 'ceneo_free_delivery' => array('title' => Context::getContext()->getTranslator()->trans  ('Darmowa<br> wysyłka'), 'width' =>40),
            //'ceneo_in_stock' => array('title' => Context::getContext()->getTranslator()->trans  ('Dostępny'), 'width' =>40),

            'ceneo_price_with_delivery' => array('title' => Context::getContext()->getTranslator()->trans  ('Cena z wysyłką'), 'width' =>120),
            'current_price' => array('title' => Context::getContext()->getTranslator()->trans  ('Nasza cena'), 'width' =>120),
            'price_difference' => array('title' => Context::getContext()->getTranslator()->trans  ('Różnica ceny'), 'width' =>120),
            'wholesale_price' => array('title' => Context::getContext()->getTranslator()->trans  ('Cena zakupu'), 'align' => 'right', 'width' => 80),


           // 'delivery_cost' => array('title' => Context::getContext()->getTranslator()->trans  ('Koszt dostawy'), 'align' => 'right', 'width' => 80),
            //'force_wholesale_price' => array('title' => Context::getContext()->getTranslator()->trans  ('Wymuszona cena zakupu'), 'align' => 'right', 'width' => 80),

            'TS_Mod' =>    array('title' =>Context::getContext()->getTranslator()->trans ('Data aktualizacjia'), 'type' =>'date'),
        );

        $this->_select .= 'a.id_product 
        ,a.id_ceneo
        ,a.shop_name 
        ,pl.name
        ,ROUND(a.min_price_spread,2) as min_price_spread 
        ,ROUND(a.ceneo_best_price,2) as ceneo_best_price 
        ,ROUND(a.current_price,2) as current_price
        ,ROUND(p.wholesale_price*1.23,2) as wholesale_price 
        ,ROUND(a.wholesale_price,2) as force_wholesale_price 
        ,ROUND(a.current_price - a.ceneo_price_with_delivery) as price_difference';


        $this->_join .= ' LEFT JOIN '._DB_PREFIX_.'product_shop p ON (a.id_product = p.id_product and  p.id_shop= '.Shop::getContextShopID().')
        inner JOIN  '._DB_PREFIX_.'product_lang pl ON (p.id_product=pl.id_product and p.id_shop=pl.id_shop)';
        $this->_use_found_rows = false;
        $this->bootstrap  = true;

        self::$definition=CeneoAnalyticsModel::$definition;
        parent::__construct();


    }

    public function update(){

        $values = Tools::getAllValues();

   //     die(  "<pre>".print_r($values,1));
        $ceneo_analytics = new CeneoAnalyticsModel($values['id_ceneo_analitycs']);
        $ceneo_analytics->ImportFromArray($values);

        $ceneo_analytics->TS_Mod=date_create()->format('Y-m-d H:i:s');
        $ceneo_analytics->save(true,false);
        return true;
    }

    public function add(){

       return true;
    }

public function giveMyCallBack($id_ceneo){
        return '<a target="_blank" href="https://ceneo.pl/'.$id_ceneo.'">'.$id_ceneo.'</a>';

}

    public function delete(){

        return true;
    }

    public  function renderList(){
        CeneoAnalyticsModel::ImportFromSuppiler();
       return  parent::renderList();
}

    public function renderForm()
    {
        $id_ceneo_analitycs = (int)Tools::getValue( 'id_ceneo_analitycs');
        $ceneo_analytics = new  CeneoAnalyticsModel($id_ceneo_analitycs);

        $this->fields_value=(array)$ceneo_analytics;
        $this->fields_value['tt']='';



        $TimeTableForm = new TimeTableForm();
       // die(print_r(json_decode($ceneo_analytics->time_table,JSON_OBJECT_AS_ARRAY),1));
        $TimeTableForm->values=json_decode($ceneo_analytics->time_table,JSON_OBJECT_AS_ARRAY);

        $this->fields_form =         array(
            'legend' => array(
                'title' => $this->l('Add / Edit Ceneo Item'),
                'image' => '../img/admin/contact.gif'
            ),
            'input' => array(
                array('type' => 'hidden', 'label' => $this->l('ID'), 'name' => 'id_ceneo_analitycs', 'size' => 30, 'required' => true),
                array('type' => 'text', 'label' => $this->l('id_ceneo'), 'name' =>       'id_ceneo', 'size' => 30, 'required' => true),
                array('type' => 'text', 'label' => $this->l('Najniższa cena na Ceneo (brutto) '), 'name' =>  'ceneo_best_price', 'size' => 30, 'required' => false,'disabled'=>true),

                array('type' => 'text', 'label' => $this->l('Rzeczywista cena zakupu (brutto)'),'desc'=>'Uzupełnić tylko, gdy cena zakup w systemie ERP(OPTIMA) jest błędna', 'name' =>  'wholesale_price', 'size' => 30, 'required' => true),
                array('type' => 'text', 'label' => $this->l('Minimalny narzut (%)'),'desc'=>'cena_zakupu+(cena_zakupu*Minimalny narzut/100)=Minimalna cena sprzedaży (Jeśli  wartość jest równa 0, pobierany jest narzut z ustawień globalnych)', 'name' =>  'min_price_spread', 'size' => 30, 'required' => true),
                array('type' => 'text', 'label' => $this->l('Nasz koszt dostawy'), 'desc'=>'Jeśli wartość jest równa 0, koszt dostawy pobierany jest z ustawień globalnych','name' =>  'delivery_cost', 'size' => 30, 'required' => true),

                array('type' => 'select', 'label' => $this->l('Udostępniaj produkt do Ceneo'),'desc'=>'Produkt będzie publikowany na Ceneo  ', 'name' =>'share_to_ceneo', 'required' => true, 'default_value' => 0,
                    'options' =>array('query' => CeneoConfiguration::FORMSELECT, 'id' => 'id_option', 'name' => 'name')),
                array('type' => 'select', 'label' => $this->l('Automatycznie reguluj cene'),'desc'=>'Cena w sklepie będzie ustalana automatycznie względem CENEO', 'name' =>'active', 'required' => true, 'default_value' => 0,
                    'options' =>array('query' => CeneoConfiguration::FORMSELECT, 'id' => 'id_option', 'name' => 'name')),
                array('type' => 'select', 'label' => $this->l('Zawsze pobieraj cenę z CENEO do Analizy'),'desc'=>'Cena zawsze będzie pobierana z CENEO, pomimo wyłączonej automatycznej regulacji cen', 'name' =>'ceneo_always_get_price', 'required' => true, 'default_value' => 0,
                    'options' =>array('query' => CeneoConfiguration::FORMSELECT, 'id' => 'id_option', 'name' => 'name')),
                array('type' => 'select', 'label' => $this->l('Porównuj z dostępnym'),'desc'=>'Porównuj cenę tylko z produktami dostepnymi "od ręki" na Ceneo', 'name' =>'in_stock', 'required' => true, 'default_value' => 0,
                    'options' =>array('query' => CeneoConfiguration::FORMSELECT, 'id' => 'id_option', 'name' => 'name')),
                array('type' => 'select', 'label' => $this->l('Uwzględniaj koszt dostawy'),'desc'=>'Do Minimalnej ceny sprzedaży dodawany będzie nasz koszt dostawy, porównanie będzie wykonywane z Ceneo z uwglądnieniem koszów dostawy ', 'name' =>'include_delivery_cost', 'required' => true, 'default_value' => 0,
                    'options' =>array('query' => CeneoConfiguration::FORMSELECT, 'id' => 'id_option', 'name' => 'name')),
                array('type' => 'select', 'label' => $this->l('KUP TERAZ'),'desc'=>' ', 'name' =>'buy_now', 'required' => true, 'default_value' => 0,
                    'options' =>array('query' => CeneoConfiguration::FORMSELECTBUYNOW, 'id' => 'id_option', 'name' => 'name')),
                array('type' => 'select', 'label' => $this->l('Użyj Harmonogramu'), 'name' =>'enable_time_table', 'required' => true, 'default_value' => 0,
                    'options' =>array('query' => CeneoConfiguration::FORMSELECTTIMETABLE, 'id' => 'id_option', 'name' => 'name')),
                array('type' => 'html',  'html_content' =>     $TimeTableForm->generate(),'name'=>'Mon','label'=> $this->l('Harmonogram')),

            ),


            'submit' => array('title' => $this->l('Save'))
        );


       return parent::renderForm();

    }



}

