<?php
class CatalogController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
 {
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
        $cmsProductsDbTable = new Application_Model_DbTable_CmsProducts();
        $products = $cmsProductsDbTable->search(array(
            'filters' => array(
            ),
            'orders' => array(
                'order_number' => 'ASC',
            ),
                //'limit' => 4,
                //'page' => 2
        ));
        
        $form = new Application_Form_Frontend_FilterProducts();
        
        $form->populate(array(
        ));

         if ($request->isPost() && $request->getPost('task') === 'save') {
            try {
                
                if (!$form->isValid($request->getPost())) {
                    throw new Application_Model_Exception_InvalidInput('Invalid data was sent for products');
                }
                $formData = $form->getValues();

                $cmsProductsDbTable = new Application_Model_DbTable_CmsProducts();
                
                        if(empty($formData['supplier_categories'])){
                           foreach ($products as $product) {
                               $formData['supplier_categories'][] = $product['supplier_categories'];
                           }
                        }
                        if(empty($formData['model'])){
                           foreach ($products as $product) {
                               $formData['model'][] = $product['model'];
                           }
                        }
                        
                        
                $productss = $cmsProductsDbTable->search(array(
                    'filters' => array(
                        'status' => Application_Model_DbTable_CmsProducts::STATUS_ENABLED,
                        'supplier_categories' => $formData['supplier_categories'],
                        'model' => $formData['model'],
                    ),
                    'orders' => array(
                        'order_number' => 'ASC',
                    ),
                        //'limit' => 4,
                        //'page' => 2
                ));
              $this->view->productss = $productss;
                
            } catch (Application_Model_Exception_InvalidInput $ex) {
                $systemMessages['errors'][] = $ex->getMessage();
            }
        } else {
 
        $cmsProductsDbTable = new Application_Model_DbTable_CmsProducts();
        $productss = $cmsProductsDbTable->search(array(
            'filters' => array(
                'status' => Application_Model_DbTable_CmsProducts::STATUS_ENABLED
            ),
            'orders' => array(
                'order_number' => 'ASC',
            ),
                //'limit' => 4,
                //'page' => 2
        ));
        $this->view->productss =  $productss;
       
        }
        
        
        
        $this->view->form =  $form;
        $sitemapPageBreadcrumbs = $cmsSitemapPageDbTable->getSitemapPageBreadcrumbs($sitemapPageId);

        $this->view->products =  $products;
        $this->view->sitemapPage = $sitemapPage;
        $this->view->breadcrumb = $sitemapPageBreadcrumbs;
    }

    public function productAction()
    {
        $request = $this->getRequest();
		$productItemId = (int) $request->getParam('id');
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
        $cmsProductsDbTable = new Application_Model_DbTable_CmsProducts();
		$productItem = $cmsProductsDbTable->search(array(
			'filters' => array(
				'id' => $productItemId,
				'status' => Application_Model_DbTable_CmsProducts::STATUS_ENABLED
			),
			'orders' => array(
				'order_number' => 'ASC',
			)
		));
		$productItem = $productItem[0];
        $cmsProductsDbTable = new Application_Model_DbTable_CmsProducts();
        $products = $cmsProductsDbTable->search(array(
            'filters' => array(
            ),
            'orders' => array(
                'order_number' => 'ASC',
            ),
                //'limit' => 4,
                //'page' => 2
        ));
        $sitemapPageBreadcrumbs = $cmsSitemapPageDbTable->getSitemapPageBreadcrumbs($sitemapPageId);
        
        $this->view->breadcrumb = $sitemapPageBreadcrumbs;
        $this->view->sitemapPage = $sitemapPage;
        $this->view->productItem = $productItem;
        $this->view->products =  $products;
    }
}
