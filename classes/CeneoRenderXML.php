<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

include dirname(__FILE__).'/SimpleXMLExtended.php';


class CeneoRenderXML
{
    public $promo;
    
        public function __construct()
    {
            
$this->promo="";
  
       
    }  
    
    function generateXML($tofile=false){
      /*  
     
        if (isset(Context::getContext()->controller)) {
    $controller = Context::getContext()->controller;
} else {
    
    
   
}*/     
       if( Context::getContext()->customer-> isLogged())
       {
           Context::getContext()->customer-> logout();
           die ('Błąd użytkownik zalogowany');
       
           
       }
   

       
         $controller = new FrontController();

          $controller->init();

 $link = Context::getContext()->link;   


     $xmlstr   ='<?xml version="1.0" encoding="UTF-8" '
  . 'standalone="yes"?><offers xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" version="1"></offers>'; 
$xml = new SimpleXMLExtended($xmlstr);



$data = CeneoAnalyticsModel::getCeneoProductsPublic();
  
foreach( $data as $val){

    $product = new Product((int)  $val['id_product']);
    $qty=Product::getQuantity($val['id_product']);
    $avail=empty($qty)?'14':'1';
   $cat= $this->getCategoryPath($product->id_category_default);
   $img_main=$product->getCoverWs(); 
   $allimg=$product->getWsImages();
   
$manufacturer = new Manufacturer((int)  $product->id_manufacturer);
$xml_product= $xml->addChild('o');
$xml_product->addAttribute('id', $val['id_product']);
$xml_product->addAttribute('url', $product->getLink());
$xml_product->addAttribute('price',$product->getPublicPrice());
$xml_product->addAttribute('avail',$avail);
$xml_product->addAttribute('stock',$qty);
$xml_product->addChildWithCDATA('cat', htmlspecialchars($cat));
$xml_product->addChildWithCDATA('desc', htmlspecialchars(strip_tags( $product->description['1'] ) ));
$xml_product->addChildWithCDATA('name', htmlspecialchars(strip_tags( $product->name['1'].' '.$this->promo ) ));

$attrs=$xml_product->addChild('attrs');
$attrs->addChild('a', htmlspecialchars(($manufacturer->name)))->addAttribute('name' , 'Producent');
$attrs->addChild('a',htmlspecialchars(($product->reference)))->addAttribute('name' , 'Kod_Producenta');
$attrs->addChild('a',htmlspecialchars(($product->ean13)))->addAttribute('name' , 'EAN');
 
$imgs=$xml_product->addChild('imgs');
$imgs->addChild('main')->addAttribute('url' ,  $link->getImageLink( $product->link_rewrite[1], $img_main, 'large_default'));

foreach($allimg as $img)
$imgs->addChild('i')->addAttribute('url' ,  $link->getImageLink($product->link_rewrite[1], $img['id'], 'large_default'));
}


  header("Content-Type: application/xml; charset=utf-8");    
  echo $xml->saveXML();           

    }
    
  
    
   function getCategoryPath($id_category){
       
             $context = Context::getContext();
   $home=false;
   $path='';
   $url_base='';
            $category = Db::getInstance()->getRow('
		SELECT id_category, level_depth, nleft, nright
		FROM '._DB_PREFIX_.'category
		WHERE id_category = '.(int)$id_category);
            if (isset($category['id_category'])) {
                $sql = 'SELECT c.id_category, cl.name, cl.link_rewrite,c.level_depth
					FROM '._DB_PREFIX_.'category c
					LEFT JOIN '._DB_PREFIX_.'category_lang cl ON (cl.id_category = c.id_category'.Shop::addSqlRestrictionOnLang('cl').')
					WHERE c.nleft <= '.(int)$category['nleft'].'
						AND c.nright >= '.(int)$category['nright'].'
						AND cl.id_lang = '.(int)$context->language->id.
                       ($home ? ' AND c.id_category='.(int)$id_category : '').'
						AND c.id_category != '.(int)Category::getTopCategory()->id.'
					GROUP BY c.id_category
					ORDER BY c.level_depth ASC
					LIMIT '.(!$home ? (int)$category['level_depth'] + 1 : 1);
            
                $categories = Db::getInstance()->executeS($sql);
                $full_path = '';
                $n = 1;
                $n_categories = (int)count($categories);
                foreach ($categories as $category) {
                  if($category['level_depth']<3)
                      continue;
                    
                      $full_path .= '/'.htmlentities($category['name']);
                }
                return $full_path.$path;
            }
        
   }
 public function addCDATA($string){
           $string = trim($string);
        $string = str_replace(array("\n", "\t", "\r"), ' ', $string);
        return '<![CDATA['.$string.']]>';
    }
   
}