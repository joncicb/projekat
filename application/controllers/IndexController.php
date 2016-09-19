<?php

class IndexController extends Zend_Controller_Action
 {

    public function init() {
        /* Initialize action controller here */
    }

    public function indexAction() {
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
        $cmsSuppliersNameDbTable = new Application_Model_DbTable_CmsSuppliers();
        $suppliersName = $cmsSuppliersNameDbTable->search(array(
            'filters' => array(
                'name_search' => ''
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
        
        $this->view->suppliersName=$suppliersName;
        $this->view->suppliers = $suppliers;
        $this->view->news = $news;
        $this->view->newsSitemapPage = $newsSitemapPage;
        $this->view->indexSlides = $indexSlides;
        $this->view->services = $services;
        $this->view->servicesSitemapPage = $servicesSitemapPage;
        $this->view->photoGalleries = $photoGalleries;
        $this->view->photoGalleriesPages = $photoGalleriesPages;
        $this->view->products = $products;
        
    }

}
