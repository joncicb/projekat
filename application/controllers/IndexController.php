<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
		$cmsIndexSlidesDbTable = new Application_Model_DbTable_CmsIndexSlides();
		
		$indexSlides = $cmsIndexSlidesDbTable->search(array(
			'filters' => array(
				'status' => Application_Model_DbTable_CmsIndexSlides::STATUS_ENABLED
			),
			'orders' => array(
				'order_number' => 'ASC'
			)
		));
                $cmsSitemapPagesDbTable = new Application_Model_DbTable_CmsSitemapPages();
		
                $newsSitemapPages = $cmsSitemapPagesDbTable->search(array(
                    'filters' => array(
                    'status' => Application_Model_DbTable_CmsSitemapPages::STATUS_ENABLED,
                    'type' => 'NewsPage'
                                ),
                    'limit' => 1
                        ));

                $newsSitemapPage = !empty($newsSitemapPages) ? $newsSitemapPages[0] : null;

                $cmsNewsDbTable = new Application_Model_DbTable_CmsNews();
                $news = $cmsNewsDbTable->search(array(
                    'filters' => array(
                        'status'=>  Application_Model_DbTable_CmsNews::STATUS_ENABLED,

                    ),
                    'orders' => array(//sortiram tabelu po
                        'order_number'=>'ASC'
                    ),
                    'limit' => 6,
                    //'page' => 2
                ));
		
                $this->view->news = $news;
                $this->view->newsSitemapPage = $newsSitemapPage;
		$this->view->indexSlides = $indexSlides;
    }
}

