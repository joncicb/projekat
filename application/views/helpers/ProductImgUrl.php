<?php

class Zend_View_Helper_ProductImgUrl extends Zend_View_Helper_Abstract {

    public function productImgUrl($product) {

        $productImgFileName = $product['id'] . '.jpg';

        $productImgFilePath = PUBLIC_PATH . '/uploads/products/' . $productImgFileName;
            //helper ima property view koji je Zend_view i preko kojeg pozivamo ostale view helpere na primer $this->view->baseUrl
        if (is_file($productImgFilePath)) {
            
            return $this->view->baseUrl('/uploads/products/' . $productImgFileName); 
        } else {
            
            return $this->view->baseUrl('/uploads/products/no-image.jpg');
        
            
        }
    }

}
