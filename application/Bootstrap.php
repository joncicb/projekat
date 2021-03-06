<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

	protected function _initRouter() {
		//ensure that database is configured
		$this->bootstrap('db');
		
		$sitemapPageTypes = array(
			
			'StaticPage' => array(
				'title' => 'Static Page',
				'subtypes' => array(
					// 0 means unlimited number
					'StaticPage' => 0
				)
			),
			
			'PhotoGalleriesPage' => array(
				'title' => 'Photo Galleries Page',
				'subtypes' => array(
					
				)
			),
                    
                        'AboutUsPage' => array(
                                'title' => 'About Us Page',
                                'subtypes' => array(
                                )
                        ),
                        'ContactPage'=>array(
                                'title'=>'Contact Page',
                                'subtypes'=>array(

                                )
                        ),
                        'CatalogPage'=>array(
                                'title'=>'Catalog Page',
                                'subtypes'=>array(
                                        
                                )
                        ),
                        'NewsPage'=>array(
                                'title'=>'News Page',
                                'subtypes'=>array(
                                         
                                )
                        ),
                        'ServicesPage'=>array(
                                'title'=>'Services Page',
                                'subtypes'=>array(
                                         
                                )
                        ),
        );
		
		$rootSitemapPageTypes = array(
			'StaticPage' => 0,
                        'CatalogPage' => 1,
                        'NewsPage' => 1,
			'PhotoGalleriesPage' => 1,
                        'AboutUsPage'=>1,
                        'ContactPage'=>1,
                        'ServicesPage'=>1,
		);
		
		Zend_Registry::set('sitemapPageTypes', $sitemapPageTypes);
		Zend_Registry::set('rootSitemapPageTypes', $rootSitemapPageTypes);
		
		$router = Zend_Controller_Front::getInstance()->getRouter();
		
		$router instanceof Zend_Controller_Router_Rewrite;
		
		$sitmapPagesMap = Application_Model_DbTable_CmsSitemapPages::getSitemapPagesMap(); 
                
		foreach ($sitmapPagesMap as $sitemapPageId => $sitemapPageMap) {
			
			if ($sitemapPageMap['type'] == 'StaticPage') {
				
				$router->addRoute('static-page-route-' . $sitemapPageId, new Zend_Controller_Router_Route_Static(
					$sitemapPageMap['url'],
					array(
						'controller' => 'staticpage',
						'action' => 'index',
						'sitemap_page_id' => $sitemapPageId
					)
				));
			}
			
			
			if ($sitemapPageMap['type'] == 'PhotoGalleriesPage') {
				
				$router->addRoute('static-page-route-' . $sitemapPageId, new Zend_Controller_Router_Route_Static(
					$sitemapPageMap['url'],
					array(
						'controller' => 'photogalleries',
						'action' => 'index',
						'sitemap_page_id' => $sitemapPageId
					)
				));
				
				$router->addRoute('photo-gallery-route', new Zend_Controller_Router_Route(
					$sitemapPageMap['url'] . '/:id/:photo_gallery_slug',
					array(
						'controller' => 'photogalleries',
						'action' => 'gallery',
						'sitemap_page_id' => $sitemapPageId
					)
				));
			}
                        if($sitemapPageMap['type']=='AboutUsPage'){
                                $router->addRoute('static-page-route-' . $sitemapPageId, new Zend_Controller_Router_Route_Static(//za rute koje nemaju parametre, moze i da se koristi za maskiranje putanje
                                        $sitemapPageMap['url'],
                                        array(
                                                'controller' => 'aboutus',
                                                'action' => 'index',
                                                'sitemap_page_id'=>$sitemapPageId

                                    )
                                ));
//                                $router->addRoute('member-route', new Zend_Controller_Router_Route(
//                                $sitemapPageMap['url'] . '/member/:id/:member_slug',
//                                array(
//                                    'controller' => 'aboutus',
//                                    'action' => 'member',
//                                    'member_slug' => '', // ovo je default za member_slug
//                                )
//                            ));
                                }
                        if($sitemapPageMap['type']=='ContactPage'){
                                $router->addRoute('static-page-route-' . $sitemapPageId, new Zend_Controller_Router_Route_Static(//za rute koje nemaju parametre, moze i da se koristi za maskiranje putanje
                                        $sitemapPageMap['url'],
                                        array(
                                                'controller' => 'contact',
                                                'action' => 'index',
                                                'sitemap_page_id'=>$sitemapPageId
                                    )
                                ));  
                        }
                        if($sitemapPageMap['type']=='ServicesPage'){
                                $router->addRoute('static-page-route-' . $sitemapPageId, new Zend_Controller_Router_Route_Static(//za rute koje nemaju parametre, moze i da se koristi za maskiranje putanje
                                        $sitemapPageMap['url'],
                                        array(
                                                'controller' => 'services',
                                                'action' => 'index',
                                                'sitemap_page_id'=>$sitemapPageId
                                    )
                                ));  
                        }
                        if($sitemapPageMap['type']=='CatalogPage'){
                                $router->addRoute('static-page-route-' . $sitemapPageId, new Zend_Controller_Router_Route_Static(//za rute koje nemaju parametre, moze i da se koristi za maskiranje putanje
                                        $sitemapPageMap['url'],
                                        array(
                                                'controller' => 'catalog',
                                                'action' => 'index',
                                                'sitemap_page_id'=>$sitemapPageId
                                    )
                                ));
                            $router->addRoute('product-item-route', new Zend_Controller_Router_Route(
                                        $sitemapPageMap['url'] . '/:id/:product_item_slug',
                                        array(
                                                'controller' => 'catalog',
                                                'action' => 'product',
                                                'sitemap_page_id'=>$sitemapPageId
                                            )
                                        ));
                        }
                        if($sitemapPageMap['type']=='NewsPage'){
                                $router->addRoute('static-page-route-' . $sitemapPageId, new Zend_Controller_Router_Route_Static(//za rute koje nemaju parametre, moze i da se koristi za maskiranje putanje
                                        $sitemapPageMap['url'],
                                        array(
                                                'controller' => 'news',
                                                'action' => 'index',
                                                'sitemap_page_id'=>$sitemapPageId
                                    )
                                ));
                                $router->addRoute('item-route', new Zend_Controller_Router_Route(
                                        $sitemapPageMap['url'] . '/:id/:item_slug',
                                        array(
                                                'controller' => 'news',
                                                'action' => 'item',
                                                'sitemap_page_id'=>$sitemapPageId
                                            )
                                        ));
                        }
		}
	}
}

