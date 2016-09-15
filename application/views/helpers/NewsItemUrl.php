<?php
class Zend_View_Helper_NewsItemUrl extends Zend_View_Helper_Abstract
{
    public function newsItemUrl($newsItem) {
        
        return $this->view->url(array(
            'id' => $newsItem['id'],
            'newsitem_slug' => $newsItem['title'] 
            
        ), 'newsitem-route', true);
        
    }
}