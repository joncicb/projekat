<?php

class Zend_View_Helper_ProductItemUrl extends Zend_View_Helper_Abstract
{
    public function productItemUrl($productItem) {
        
        return $this->view->url(array(
            'id' => $productItem['id'],
            'product_item_slug' => $productItem['type']
            
        ), 'product-item-route', true);
        
    }
}
