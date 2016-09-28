<?php
class CatalogController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
 {
        $flashMessenger = $this->getHelper('FlashMessenger');
        
        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' =>  $flashMessenger->getMessages('errors'),
        );
        
        
        $request = $this->getRequest();
        
        
        $sitemapPageId = (int) $request->getParam('sitemap_page_id');
        $cmsSitemapPageDbTable = new Application_Model_DbTable_CmsSitemapPages();
        $sitemapPage = $cmsSitemapPageDbTable->getSitemapPageById($sitemapPageId);
        
        if ($sitemapPageId <= 0) {
            throw new Zend_Controller_Router_Exception('Invalid sitemap  is found with id ' . $sitemapPageId, 404);
       }
       if (!$sitemapPage) {
           throw new Zend_Controller_Router_Exception('Invalid sitemap  is found with id ' . $sitemapPageId, 404);
        }
        if ( $sitemapPage['status'] == Application_Model_DbTable_CmsSitemapPages::STATUS_DISABLED
                                    && !Zend_Auth::getInstance()->hasIdentity()
        ) {
            throw new Zend_Controller_Router_Exception('No sitemap page is disabled ', 404);
       }
        
        $cmsSuppliersDbTable = new Application_Model_DbTable_CmsSuppliers();
        $suppliers = $cmsSuppliersDbTable->search(array(
            'filters' => array(
                'status' => Application_Model_DbTable_CmsSuppliers::STATUS_ENABLED
            ),
            'orders' => array(
                'order_number' => 'ASC',
            )
                    ));

        
        $form = new Application_Form_Frontend_FilterProducts();

         if ($request->isGet() && $request->getParam('task') === 'save') {
             
            try {
                if (!$form->isValid($request->getParams())) {
                     $this->getResponse()->setHttpResponseCode(404);
                }

                $formData = $form->getValues();
                
                $cmsProductsDbTable = new Application_Model_DbTable_CmsProducts();
                $filters = array(
                    'stock_status' => Application_Model_DbTable_CmsProducts::STATUS_ENABLED
                );
                
                if(count($formData['supplier_categories']) > 0){
                    $filters ['supplier_categories'] = $formData['supplier_categories'];
                }
                
                if(count($formData['model']) > 0){
                    $filters ['model'] = $formData['model'];
                }
                if(count($formData['type']) > 0){
                    $filters ['type'] = $formData['type'];
                }
                        
                $products = $cmsProductsDbTable->search(array(
                    'filters' => $filters,
                    'orders' => array(
                        'order_number' => 'ASC',
                    ),
                        //'limit' => 4,
                        //'page' => 2
                ));
              $this->view->products = $products;
                
            } catch (Application_Model_Exception_InvalidInput $ex) {
                $systemMessages['errors'][] = $ex->getMessage();
            }
        } else {
 
        $cmsProductsDbTable = new Application_Model_DbTable_CmsProducts();
        $products = $cmsProductsDbTable->search(array(
            'filters' => array(
                'status' => Application_Model_DbTable_CmsProducts::STATUS_ENABLED
            ),
            'orders' => array(
                'order_number' => 'ASC',
            ),
                //'limit' => 4,
                //'page' => 2
        ));
        $this->view->products =  $products;
       
        }
        $this->view->form =  $form;
        $sitemapPageBreadcrumbs = $cmsSitemapPageDbTable->getSitemapPageBreadcrumbs($sitemapPageId);

        $this->view->suppliers = $suppliers;
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
