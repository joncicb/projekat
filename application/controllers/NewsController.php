<?php

class NewsController extends Zend_Controller_Action {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors')
        );
        $request = $this->getRequest();

        $sitemapPageId = (int) $request->getParam('sitemap_page_id');

        if ($sitemapPageId <= 0) {
            throw new Zend_Controller_Router_Exception('Invalid sitemap page id: ' . $sitemapPageId, 404);
        }

        $cmsSitemapPageDbTable = new Application_Model_DbTable_CmsSitemapPages();

        $sitemapPage = $cmsSitemapPageDbTable->getSitemapPageById($sitemapPageId);

        if (!$sitemapPage) {
            throw new Zend_Controller_Router_Exception('No sitemap page is found for id: ' . $sitemapPageId, 404);
        }

        if (
                $sitemapPage['status'] == Application_Model_DbTable_CmsSitemapPages::STATUS_DISABLED
                //check if user is not logged in
                //then preview is not available
                //for disabled pages
                && !Zend_Auth::getInstance()->hasIdentity()
        ) {
            throw new Zend_Controller_Router_Exception('Sitemap page is disabled', 404);
        }

        $cmsNewsDbTable = new Application_Model_DbTable_CmsNews();
        $news = $cmsNewsDbTable->search(array(
            'filters' => array(
                'status' => Application_Model_DbTable_CmsNews::STATUS_ENABLED
            ),
            'orders' => array(
                'order_number' => 'ASC'
            ),
            'limit' => 6
        ));
        $sitemapPageBreadcrumbs = $cmsSitemapPageDbTable->getSitemapPageBreadcrumbs($sitemapPageId);

        $this->view->breadcrumb = $sitemapPageBreadcrumbs;
        $this->view->sitemapPage = $sitemapPage;
        $this->view->news = $news;
        $this->view->systemMessages = $systemMessages;

    }
    public function itemAction() {
		$request = $this->getRequest();
		$itemId = (int) $request->getParam('id');
		$sitemapPageId = (int) $request->getParam('sitemap_page_id');
		if ($sitemapPageId <= 0) {
			throw new Zend_Controller_Router_Exception('Invalid sitemap page id: ' . $sitemapPageId, 404);
		}
		$cmsSitemapPageDbTable = new Application_Model_DbTable_CmsSitemapPages();
		$sitemapPage = $cmsSitemapPageDbTable->getSitemapPageById($sitemapPageId);
		if (!$sitemapPage) {
			throw new Zend_Controller_Router_Exception('No sitemap page is found for id: ' . $sitemapPageId, 404);
		}
		if (
				$sitemapPage['status'] == Application_Model_DbTable_CmsSitemapPages::STATUS_DISABLED &&
				!Zend_Auth::getInstance()->hasIdentity()
		) {
			throw new Zend_Controller_Router_Exception('Sitemap page is disabled', 404);
		}
		$cmsNewsDbTable = new Application_Model_DbTable_CmsNews();
		$item = $cmsNewsDbTable->search(array(
			'filters' => array(
				'id' => $itemId,
				'status' => Application_Model_DbTable_CmsNews::STATUS_ENABLED
			),
			'orders' => array(
				'order_number' => 'ASC',
			)
		));
		$item = $item[0];
		
		
                
		$sitemapPageBreadcrumbs = $cmsSitemapPageDbTable->getSitemapPageBreadcrumbs($sitemapPageId);

                $this->view->breadcrumb = $sitemapPageBreadcrumbs;
		$this->view->sitemapPage = $sitemapPage;
		$this->view->item = $item;
		
        
	}

}


