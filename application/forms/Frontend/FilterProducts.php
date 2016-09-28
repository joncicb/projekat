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
            //$data[''] = [''];
            foreach ($suppliers as $supplier) {
                $data[$supplier['id']] = $supplier['name']; 
            }
            $supplierCategories = new Zend_Form_Element_MultiCheckbox('supplier_categories');
            $supplierCategories->addMultiOptions($data)->setRequired(false);
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
                $data[$product['model']] = $product['model']; 
            }
            $productModels = new Zend_Form_Element_MultiCheckbox('model');
            $productModels->addMultiOptions($data)->setRequired(false);
            $this->addElement($productModels);    
                
             
                
               
    }
}
