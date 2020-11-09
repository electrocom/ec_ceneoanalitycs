<?php
define("CENEO_URL",'https://www.ceneo.pl/');
require_once ("simplehtmldom/simple_html_dom.php");
class CeneoPrices
{
private $prices;
public function __construct($id_ceneo)
{
    $this->prices = null;
    try {
    $url = CENEO_URL;
    $agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
        $html='';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, $agent);
    curl_setopt($ch, CURLOPT_URL, 'https://www.ceneo.pl/' . $id_ceneo);
    $result = curl_exec($ch);

        if (!curl_errno($ch)) {
            switch ($http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
                case 200:    $html = str_get_html($result);
                    break;
                case 301:
                    $info = curl_getinfo($ch);
                    curl_setopt($ch, CURLOPT_URL, $info['redirect_url']);

                    $result = curl_exec($ch);
                    $html = str_get_html($result);
                    break;
                default:
                    echo 'Unexpected HTTP code: ', $http_code, "\n";
            }
        }




    $html->find('tr.product-offer');
    foreach ($html->find('tr.product-offer') as $a) {
        if($a->find('span.free-delivery-txt'))
            $is_free_delivery=1;
        else
            $is_free_delivery=0;

        if($a->find('span.instock'))
            $is_in_stock=1;
        else
            $is_in_stock=0;

        foreach($a->find('div.product-delivery-info') as $value){

          // die( $value->plaintext);
            $value->plaintext=str_replace('&#243;','',$value->plaintext);
            $product_delivery_info= (float)str_replace(',','.',preg_replace('/[^0-9,]/','',  $value->plaintext));
        }
        if(!$product_delivery_info)
            $product_delivery_info=$a->{'data-price'};
        $this->prices[] = array('price' => $a->{'data-price'}, 'shop' => $a->{'data-shopurl'},'free_delivery'=>$is_free_delivery,'in_stock'=>$is_in_stock
        ,'price_with_delivery'=>$is_free_delivery?$a->{'data-price'}:$product_delivery_info );


    }


     }
     catch(Exception $e){
echo "Błąd przetwarzania:".$id_ceneo.PHP_EOL;
     }
}
    function array_msort($array, $cols)
    {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) { $colarr[$col]['_'.$k] = strtolower($row[$col]); }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\''.$col.'\'],'.$order.',';
        }
        $eval = substr($eval,0,-1).');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k,1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;

    }

public function  getPrices($withdelivery=true)
{
    if(is_array($this->prices))
   return    array_values($this->array_msort( $this->prices, array( $withdelivery?'price_with_delivery':'price'=>SORT_ASC,'in_stock'=>SORT_DESC)));
    else
        return array();
}

public function getQuantityOffers()
{
  return count ($this->getPrices());
}
public function getBestPrice($in_stock=null, $with_delivery=true,$free_delivery=null)
{try{
    $tmp=$this->getPrices();
 return   reset($tmp);
    }
    catch(Exception $e){
    return null;
    }

}

}
