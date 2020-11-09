<?php
if (!defined('_PS_VERSION_')) {
exit;
}

//require_once _PS_MODULE_DIR_ . 'ec_ceneoanalitycs/controllers/admin/AdminRecipients.php';
require_once _PS_MODULE_DIR_ . 'ec_ceneoanalitycs/controllers/admin/AdminCeneoAnalitycs.php';

class Ec_Ceneoanalitycs extends Module
{
    const PREFIX = 'ec_ceneoanalitycs';
    protected $models = ['CeneoAnalyticsModel'];
    protected $_hooks = array('displayProductPriceBlock');

    //tabs to be created in the backoffice menu
    protected $tabs = [
        [
            'name' => 'CeneoAnalitycs',
            'className' => 'AdminCeneoAnalitycs',
            'active' => 1,
            'parent_class_name' => 'AdminCatalog',

        ],
        [
            'name' => 'CeneoConfiguration',
            'className' => 'AdminCeneoConfiguration',
            'active' => 1,
            'parent_class_name' => 'AdminCatalog',

        ],
                         ];
    protected $templateFile;


    public function __construct()
    {
        $this->name = 'ec_ceneoanalitycs';
        $this->tab = 'front_features';
        $this->version = '1.7.9';
        $this->author = 'Krzysztof Mazur';
        $this->bootstrap = TRUE;
        $this->module_key = '';
        $this->displayName = $this->l('Ceneo Analytics');
        $this->description = $this->l('Ceneo Analytics');

        parent::__construct();

        $this->templateFile = 'module:'.$this->name.'/views/templates/hook/DisplayProductPrice.tpl';
    }


    public function install()
    {

        foreach ($this->models as $model)
        {
            // die( _PS_MODULE_DIR_ .'classes/Models/' . $model . '.php');
            require_once _PS_MODULE_DIR_ .'ec_ceneoanalitycs/classes/Models/' . $model . '.php';
            //instantiate the module
            $modelInstance = new $model();
            $modelInstance->createDatabase();
            $modelInstance->createMissingColumns();
        }


        $this->addTab($this->tabs, Tab::getIdFromClassName('AdminCatalog'));
        //$this->installTab('','AdminRecipients','Odbiorcy');
        if (parent::install()) {
            foreach ($this->_hooks as $hook) {
                if (!$this->registerHook($hook)) {
                    return FALSE;
                }
            }



            return TRUE;
        }

        return TRUE;
    }

    public function uninstall()
    {
        $this->removeTab($this->tabs);

        if (parent::uninstall()) {
            foreach ($this->_hooks as $hook) {
                if (!$this->unregisterHook($hook)) {
                    return FALSE;
                }
            }


            return TRUE;
        }

        return FALSE;
    }

    public function addTab($tabs, $id_parent = 0 )
    {
        foreach ($tabs as $tab)
        {
            $tabModel             = new Tab();
            $tabModel->module     = $this->name;
            $tabModel->active     = $tab['active'];
            $tabModel->class_name = $tab['className'];
            $tabModel->id_parent  = $id_parent;

            //tab text in each language
            foreach (Language::getLanguages(true) as $lang)
            {
                $tabModel->name[$lang['id_lang']] = $tab['name'];
            }

            $tabModel->add();

            //submenus of the tab
            if (isset($tab['childs']) && is_array($tab['childs']))
            {
                $this->addTab($tab['childs'], Tab::getIdFromClassName($tab['className']));
            }
        }
        return true;
    }

    public function removeTab($tabs)
    {
        foreach ($tabs as $tab)
        {
            $id_tab = (int) Tab::getIdFromClassName($tab["className"]);
            if ($id_tab)
            {
                $tabModel = new Tab($id_tab);
                $tabModel->delete();
            }

            if (isset($tab["childs"]) && is_array($tab["childs"]))
            {
                $this->removeTab($tab["childs"]);
            }
        }

        return true;
    }

    public function hookDisplayProductPriceBlock($param){

       if($param['type']=='price'){
        $CeneoConfiguration = new CeneoConfiguration();

        $id_product=$param['product']['id_product'];
        $product=new Product($id_product);
        $id_group=  (int)$CeneoConfiguration->always_enable_group_id;
        $id_shop = (int)Shop::getContextShopID();
        $regular_price =$product->getPublicPrice(false);


        $sql='SELECT `sp`.`price` FROM `'._DB_PREFIX_.'specific_price` sp INNER JOIN `'._DB_PREFIX_.'product_shop` ps ON sp.`id_product`=ps.`id_product`
        WHERE ps.`id_product`='.$id_product.'  and `id_group` = '.$id_group.'  and ps.id_shop='.$id_shop.' ';

        $clientprice=Db::getInstance()->getValue($sql);



    if($clientprice-$regular_price<0&&$clientprice!=null)
        $this->smarty->assign('isBetterPrice',1);
    else
        $this->smarty->assign('isBetterPrice',0);


    //   return $this->fetch($this->templateFile);
    }                                                       }
}
