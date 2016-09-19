<?php

class Zend_View_Helper_ProductItemUrl extends Zend_View_Helper_Abstract
{
    protected $urlSlugFilter;
	
	protected function getUrlSlugFilter() {
		
		/*** Lazy Loading ***/
		
		if (!$this->urlSlugFilter) {
			$this->urlSlugFilter = new Application_Model_Filter_UrlSlug();
		}
		
		return $this->urlSlugFilter;
	}
    public function productItemUrl($productItem) {
        $urlSlugFilter = $this->getUrlSlugFilter();
        return $this->view->url(array(
            'id' => $productItem['id'],
            'product_item_slug' => $urlSlugFilter->filter ($productItem['type'])
            
        ), 'product-item-route', true);
        
    }
}
