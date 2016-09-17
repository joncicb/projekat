<?php
class Zend_View_Helper_ItemUrl extends Zend_View_Helper_Abstract
{
    protected $urlSlugFilter;
	
	protected function getUrlSlugFilter() {
		
		/*** Lazy Loading ***/
		
		if (!$this->urlSlugFilter) {
			$this->urlSlugFilter = new Application_Model_Filter_UrlSlug();
		}
		
		return $this->urlSlugFilter;
	}
    public function itemUrl($item) {
        $urlSlugFilter = $this->getUrlSlugFilter();
        return $this->view->url(array(
            'id' => $item['id'],
            'item_slug' => $urlSlugFilter->filter ($item['title']) 
            
        ), 'item-route', true);
        
    }
}