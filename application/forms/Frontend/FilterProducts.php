<?php
class Application_Form_Frontend_FilterProducts extends Zend_Form
{   
    public function init() {
        
        $cmsSuppliersDbTable = new Application_Model_DbTable_CmsSuppliers();
            $suppliers = $cmsSuppliersDbTable->search(array(
                'filters' => array(
                ),
                'orders' => array(
                    'order_number' => 'ASC'
                )
            ));
            
            $data = array();
            foreach ($suppliers as $supplier) {
                $data[$supplier['id']] = $supplier['name']; 
            }
            $supplierCategories = new Zend_Form_Element_Select('supplier_categories');
            $supplierCategories->addMultiOptions($data)->setRequired(true);
            $this->addElement($supplierCategories);
            
        $cmsProductsDbTable = new Application_Model_DbTable_CmsProducts();
            $products = $cmsProductsDbTable->search(array(
                'filters' => array(
                ),
                'orders' => array(
                    'order_number' => 'ASC'
                )
            ));
            
            $data = array();
            foreach ($products as $product) {
                $data[$product['id']] = $product['model']; 
            }
            $productModels = new Zend_Form_Element_Select('model');
            $productModels->addMultiOptions($data)->setRequired(true);
            $this->addElement($productModels);    
                
               
                
               
    }
}
