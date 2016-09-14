<?php

class Zend_View_Helper_SupplierImgUrl extends Zend_View_Helper_Abstract {

    public function supplierImgUrl($supplier) {

        $supplierImgFileName = $supplier['id'] . '.jpg';

        $supplierImgFilePath = PUBLIC_PATH . '/uploads/suppliers/' . $supplierImgFileName;
        //helper ima property view koji je Zend_view i preko kojeg pozivamo ostale view helpere na primer $this->view->baseUrl
        if (is_file($supplierImgFilePath)) {

            return $this->view->baseUrl('/uploads/suppliers/' . $supplierImgFileName);
        } else {

            return $this->view->baseUrl('/uploads/suppliers/no-image.jpg');
        }
    }

}
