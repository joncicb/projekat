<?php
class Zend_View_Helper_ItemUrl extends Zend_View_Helper_Abstract
{
    public function itemUrl($item) {
        
        return $this->view->url(array(
            'id' => $item['id'],
            'item_slug' => $item['title'] 
            
        ), 'item-route', true);
        
    }
}