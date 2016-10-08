<?php

class IndexController extends Zend_Controller_Action
 {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
        
        $request = $this->getRequest();
        $flashMessenger = $this->getHelper('FlashMessenger');
        $form = new Application_Form_Frontend_Contact();
        $systemMessages = 'init';
        if ($request->isPost() && $request->getPost('task') === 'contact') {
            try {
                //check form is valid
                if (!$form->isValid($request->getPost())) {
                    throw new Application_Model_Exception_InvalidInput('Invalid form data');
                }
                //get form data
                $formData = $form->getValues();
                // do actual task
                //save to database etc
                $mailHelper = new Application_Model_Library_MailHelper();
                $from_email = $formData['email'];
                $to_email = 'joncicb@gmail.com';
                $from_name = $formData['name'];
                
                $result = $mailHelper->sendmail($to_email, $from_email, $from_name);
                
                if(!$result){
                   $systemMessages = 'Error';
                }else{
                   $systemMessages = 'Success';  
                }
                
                
            } catch (Application_Model_Exception_InvalidInput $ex) {
                
            }
        }
        
        $cmsIndexSlidesDbTable = new Application_Model_DbTable_CmsIndexSlides();

        $indexSlides = $cmsIndexSlidesDbTable->search(array(
            'filters' => array(
                'status' => Application_Model_DbTable_CmsIndexSlides::STATUS_ENABLED
            ),
            'orders' => array(
                'order_number' => 'ASC'
            )
        ));
        $cmsSuppliersDbTable = new Application_Model_DbTable_CmsSuppliers();
        $suppliers = $cmsSuppliersDbTable->search(array(
            'filters' => array(
                'status' => Application_Model_DbTable_CmsSuppliers::STATUS_ENABLED
            ),
            'orders' => array(
                'order_number' => 'ASC'
            )
        ));
        
        $cmsProductsDbTable = new Application_Model_DbTable_CmsProducts();
        $products = $cmsProductsDbTable->search(array(
            'filters' => array(
                'status' => Application_Model_DbTable_CmsProducts::STATUS_ENABLED
            ),
            'orders' => array(
                'order_number' => 'ASC',
            ),
            'limit' => 12
            //'page' => 2
        ));
        $cmsSitemapPagesDbTable = new Application_Model_DbTable_CmsSitemapPages();
        $servicesSitemapPages = $cmsSitemapPagesDbTable->search(array(
            'filters' => array(
                'status' => Application_Model_DbTable_CmsSitemapPages::STATUS_ENABLED,
                'type' => 'ServicesPage'
            ),
            'limit' => 1
        ));
        $newsSitemapPages = $cmsSitemapPagesDbTable->search(array(
            'filters' => array(
                'status' => Application_Model_DbTable_CmsSitemapPages::STATUS_ENABLED,
                'type' => 'NewsPage',
            ),
            'limit' => 1
        ));
        $servicesSitemapPage = !empty($servicesSitemapPages) ? $servicesSitemapPages[0] : null;

        $cmsServicesDbTable = new Application_Model_DbTable_CmsServices();
        $newsSitemapPage = !empty($newsSitemapPages) ? $newsSitemapPages[0] : null;
        $photoGalleriesPages = $cmsSitemapPagesDbTable->search(array(
            'filters' => array(
                'status' => Application_Model_DbTable_CmsPhotoGalleries::STATUS_ENABLED,
                'type' => 'PhotoGalleriesPage'
            ),
            'limit' => 1
        ));

        $photoGalleriesPages = !empty($photoGalleriesPages) ? $photoGalleriesPages[0] : null;

        $cmsPhotoGalleriesTable = new Application_Model_DbTable_CmsPhotoGalleries ();
        $photoGalleries = $cmsPhotoGalleriesTable->search(array(
            'filters' => array(
                'status' => Application_Model_DbTable_CmsPhotoGalleries::STATUS_ENABLED
            ),
            'orders' => array(
                'order_number' => 'ASC',
            ),
            'limit' => 6
        ));
        $cmsNewsDbTable = new Application_Model_DbTable_CmsNews();
        $news = $cmsNewsDbTable->search(array(
            'filters' => array(
                'status' => Application_Model_DbTable_CmsNews::STATUS_ENABLED,
            ),
            'orders' => array(//sortiram tabelu po
                'order_number' => 'ASC'
            ),
            'limit' => 6,
                //'page' => 2
        ));

        $services = $cmsServicesDbTable->search(array(
            'filters' => array(
                'status' => Application_Model_DbTable_CmsServices::STATUS_ENABLED,
            ),
            'orders' => array(//sortiram tabelu po
                'order_number' => 'ASC'
            ),
            'limit' => 4,
                //'page' => 2
        ));
        
        $cmsProductsOnActionDbTable = new Application_Model_DbTable_CmsProducts();
        $productsOnAction = $cmsProductsDbTable->search(array(
           'filters' => array(
               'action' => Application_Model_DbTable_CmsProducts::ACTION_ENABLED,
               
           ),
           'orders' => array(
                'order_number' => 'ASC',
                
            ),
            'limit'=> 4
       ));
        
        $cmsDateAdedDbTable = new Application_Model_DbTable_CmsProducts();
        $productsByDateAded = $cmsDateAdedDbTable->search(array(
           'filters' => array(
               'stock_status' => Application_Model_DbTable_CmsProducts::STATUS_ENABLED
           ),
           'orders' => array(
                'date' => 'DESC',
                
               
            ),
            'limit'=> 4
       ));

        $this->view->suppliers = $suppliers;
        $this->view->news = $news;
        $this->view->newsSitemapPage = $newsSitemapPage;
        $this->view->indexSlides = $indexSlides;
        $this->view->services = $services;
        $this->view->servicesSitemapPage = $servicesSitemapPage;
        $this->view->photoGalleries = $photoGalleries;
        $this->view->photoGalleriesPages = $photoGalleriesPages;
        $this->view->products = $products;
        $this->view->productsOnAction = $productsOnAction;
        $this->view->productsByDateAded = $productsByDateAded;
        $this->view->systemMessages = $systemMessages;
        $this->view->form = $form;
        
    }

}
