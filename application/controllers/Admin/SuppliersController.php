<?php

class Admin_SuppliersController extends Zend_Controller_Action {

    public function indexAction() {
        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors')
        );
        
        $cmsSuppliersDbTable = new Application_Model_DbTable_CmsSuppliers();

        $suppliers = $cmsSuppliersDbTable->search(array(
            //'filters' => array(//filtriram tabelu po
            //'status'=>Application_Model_DbTable_CmsSuppliers::STATUS_ENABLED,
            //'description_search' => 'farm'
            
            //),
            'orders' => array(//sortiram tabelu po
                'order_number'=>'ASC'
            ),
            //'limit' => 4,
            //'page' => 2
        ));
        //$select = $cmsSuppliersDbTable->select();

        //$select->order('order_number');
        //debug za db select - vraca se sql upit
        //die($select->assemble());

        //$suppliers = $cmsSuppliersDbTable->fetchAll($select);

        $this->view->suppliers = $suppliers; //prosledjivanje rezultata
        $this->view->systemMessages = $systemMessages;
    }

    public function addAction() {

        $request = $this->getRequest();
        $flashMessenger = $this->getHelper('FlashMessenger');

        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors')
        );

        $form = new Application_Form_Admin_SupplierAdd();

        //default form data
        $form->populate(array(
        ));

        if ($request->isPost() && $request->getPost('task') === 'save') {
            try {

                //check form is valid
                if (!$form->isValid($request->getPost())) {
                    throw new Application_Model_Exception_InvalidInput('Invalid form data was sent for new supplier');
                }

                //get form data
                $formData = $form->getValues(); 
                
                //remove key supplier_photo form because there is no column supplier_photo in cms_supplier
                unset($formData['supplier_photo']);
                
                $cmsSuppliersTable = new Application_Model_DbTable_CmsSuppliers();
                
                $supplierId =  $cmsSuppliersTable->insertSuppliers($formData);
                
                if($form->getElement('supplier_photo')->isUploaded()) {
                //photo is uploaded
                    $fileInfos = $form->getElement('supplier_photo')->getFileInfo('supplier_photo');
                    $fileInfo=$fileInfos['supplier_photo'];
                    
                    try{
                      //open uploaded photo in temporary directory
                     $supplierPhoto = Intervention\Image\ImageManagerStatic::make($fileInfo['tmp_name']);
                     //dimenzionise sliku
                     $supplierPhoto->fit(270, 190);
                     
                     $supplierPhoto->save(PUBLIC_PATH . '/uploads/suppliers/' . $supplierId . '.jpg');
                     
                    } catch (Exception $ex) {
                        $flashMessenger->addMessage('Supplier has been saved, but error occured during image processing', 'errors');
                //redirect to same or another page
                        $redirector = $this->getHelper('Redirector');
                        $redirector->setExit(true)
                            ->gotoRoute(array(
                                'controller' => 'admin_suppliers',
                                'action' => 'edit',
                                'id' => $supplierId
                                    ), 'default', true);  
                    }
                }
                // do actual task
                //save to database etc
                //set system message
                $flashMessenger->addMessage('Supplier has been saved', 'success');
                //redirect to same or another page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_suppliers',
                            'action' => 'index'
                                ), 'default', true);
            } catch (Application_Model_Exception_InvalidInput $ex) {
                $systemMessages['errors'][] = $ex->getMessage();
            }
        }

        $this->view->systemMessages = $systemMessages;
        $this->view->form = $form;
    }

    public function editAction() {
 $request = $this->getRequest();
        
        $id = (int) $request->getParam('id');
        
        if($id <= 0){
           
            throw  new Zend_Controller_Router_Exception('Invalid supplier id: ' . $id, 404);
        }
        
        $cmsSuppliersTable = new Application_Model_DbTable_CmsSuppliers();
        
        $supplier = $cmsSuppliersTable->getSuppliersById($id);
        
        if(empty($supplier)){
            throw new Zend_Controller_Router_Exception('No supplier is found with id: ' . $id, 404);
        }
        
        $flashMessenger = $this->getHelper('FlashMessenger');
        
        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors')
        );
        $form = new Application_Form_Admin_SupplierEdit();
        //default form data
        $form->populate($supplier);
      
        if ($request->isPost() && $request->getPost('task') === 'update') {
            try {
                //check form is valid
                if (!$form->isValid($request->getPost())) {
                    throw new Application_Model_Exception_InvalidInput('Invalid data was sent for supplier.');
                }
                //get form data
                $formData = $form->getValues();
                
                // do actual task
                //save to database etc
                // Update postojeceg zapisa u tabeli
                
                unset($formData['supplier_photo']);
                
                if($form->getElement('supplier_photo')->isUploaded()){
                
                    // photo is uploaded
                    
                    $fileInfos = $form->getElement('supplier_photo')->getFileInfo('supplier_photo');
                    $fileInfo = $fileInfos['supplier_photo'];
                    
                    try{
                        // Open uploaded photo in temporary directory
                        $supplierPhoto = Intervention\Image\ImageManagerStatic::make($fileInfo['tmp_name']);
                        
                        $supplierPhoto->fit(270, 190);
                        
                        $supplierPhoto->save(PUBLIC_PATH . '/uploads/suppliers/' . $supplier['id'] . '.jpg');
                       
                    } catch (Exception $ex) {
                        
                        throw new Application_Model_Exception_InvalidInput('Error ocured during image processing');
                        
                    }
                }
                
                $cmsSuppliersTable->updateSuppliers($supplier['id'], $formData);

                //set system message
                $flashMessenger->addMessage('Supplier has been updated', 'success');
                //redirect to same or another page
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_suppliers',
                            'action' => 'index'
                                ), 'default', true);
            } catch (Application_Model_Exception_InvalidInput $ex) {
                $systemMessages['errors'][] = $ex->getMessage();
            }
        }
        $this->view->systemMessages = $systemMessages;
        $this->view->form = $form;
        
        $this->view->supplier = $supplier;
    }
    public function deleteAction(){
        $request = $this->getRequest(); //dohvatamo request objekat
        
        if(!$request->isPost() || $request->getPost('task') != 'delete'){
            //request is not post
            //or task is not delete
            //redirect to index page
            
            $redirector = $this->getHelper('Redirector'); 
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_suppliers',
                        'action' => 'index'
                            ), 'default', true);
        }
        $flashMessenger = $this->getHelper('FlashMessenger');
        
        try  {
            
            $id = (int) $request->getPost('id'); 

            if ($id <= 0) {
                throw new Application_Model_Exception_InvalidInput('Invalid supplier id: ' . $id);
                
            }

            $cmsSuppliersTable = new Application_Model_DbTable_CmsSuppliers();

            $supplier = $cmsSuppliersTable->getSuppliersById($id);

            if (empty($supplier)) {
                throw new Application_Model_Exception_InvalidInput('No supplier is found with id: ' . $id);
            }

            $cmsSuppliersTable->deleteSuppliers($id);

            $flashMessenger->addMessage('Supplier : ' . $supplier['name'] . ' has been deleted', 'success');
            $redirector = $this->getHelper('Redirector'); 
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_suppliers',
                        'action' => 'index'
                            ), 'default', true);
        } catch (Application_Model_Exception_InvalidInput $ex) {
            $flashMessenger->addMessage($ex->getMessage(), 'errors');
            
            $redirector = $this->getHelper('Redirector'); 
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_suppliers',
                        'action' => 'index'
                            ), 'default', true);
        } 
    }
    public function disableAction(){
        $request = $this->getRequest(); 
        
        if(!$request->isPost() || $request->getPost('task') != 'disable'){
            //request is not post
            //or task is not delete
            //redirect to index page
            
            $redirector = $this->getHelper('Redirector'); 
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_suppliers',
                        'action' => 'index'
                            ), 'default', true);
        }
        $flashMessenger = $this->getHelper('FlashMessenger');
        
        try  {
            
            $id = (int) $request->getPost('id');

            if ($id <= 0) {
                throw new Application_Model_Exception_InvalidInput('Invalid supplier id: ' . $id);
                
            }

            $cmsSuppliersTable = new Application_Model_DbTable_CmsSuppliers();

            $supplier = $cmsSuppliersTable->getSuppliersById($id);

            if (empty($supplier)) {
                throw new Application_Model_Exception_InvalidInput('No supplier is found with id: ' . $id);
            }

            $cmsSuppliersTable->disableSuppliers($id);

            $flashMessenger->addMessage('Supplier : ' . $supplier['name'] .  ' has been disabled', 'success');
            $redirector = $this->getHelper('Redirector'); 
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_suppliers',
                        'action' => 'index'
                            ), 'default', true);
        } catch (Application_Model_Exception_InvalidInput $ex) {
            $flashMessenger->addMessage($ex->getMessage(), 'errors');
            
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_suppliers',
                        'action' => 'index'
                            ), 'default', true);
        } 
    }
   
    public function enableAction(){
        $request = $this->getRequest();
        
        if(!$request->isPost() || $request->getPost('task') != 'enable'){
            //request is not post
            //or task is not delete
            //redirect to index page
            
            $redirector = $this->getHelper('Redirector'); 
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_suppliers',
                        'action' => 'index'
                            ), 'default', true);
        }
        $flashMessenger = $this->getHelper('FlashMessenger');
        
        try  {
            // read $_POST['id']
            $id = (int) $request->getPost('id');

            if ($id <= 0) {
                throw new Application_Model_Exception_InvalidInput('Invalid supplier id: ' . $id);
                
            }

            $cmsSuppliersTable = new Application_Model_DbTable_CmsSuppliers();

            $supplier = $cmsSuppliersTable->getSuppliersById($id);

            if (empty($supplier)) {
                throw new Application_Model_Exception_InvalidInput('No supplier is found with id: ' . $id);
            }

            $cmsSuppliersTable->enableSuppliers($id);

            $flashMessenger->addMessage('Supplier : ' . $supplier['name'] .  ' has been enabled', 'success');
            $redirector = $this->getHelper('Redirector'); 
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_suppliers',
                        'action' => 'index'
                            ), 'default', true);
        } catch (Application_Model_Exception_InvalidInput $ex) {
            $flashMessenger->addMessage($ex->getMessage(), 'errors');
            
            $redirector = $this->getHelper('Redirector'); 
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_suppliers',
                        'action' => 'index'
                            ), 'default', true);
        } 
    }
    public function updateorderAction(){
       $request = $this->getRequest(); 
        
        if(!$request->isPost() || $request->getPost('task') != 'saveOrder'){
            //request is not post
            //or task is not saveOrder
            //redirect to index page
            
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_suppliers',
                        'action' => 'index'
                            ), 'default', true);
        }
        $flashMessenger = $this->getHelper('FlashMessenger'); 
        
        try{
           $sortedIds =  $request->getPost('sorted_ids'); 

            if(empty($sortedIds)){
                
                throw new Application_Model_Exception_InvalidInput('Sorted ids are not sent');
                
            }
            $sortedIds = trim($sortedIds, ' ,');
            
            
            
            if(!preg_match('/^[0-9]+(,[0-9]+)*$/', $sortedIds)){
                throw new Application_Model_Exception_InvalidInput('Invalid sorted ids: ' . $sortedIds);
            }
            
            $sortedIds = explode(',', $sortedIds);
            
            $cmsSuppliersTable = new Application_Model_DbTable_CmsSuppliers();
            
            $cmsSuppliersTable->updateOrderOfSuppliers($sortedIds);
            
            $flashMessenger->addMessage('Order is successfully saved', 'success');
            
            $redirector = $this->getHelper('Redirector'); 
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_suppliers',
                        'action' => 'index'
                            ), 'default', true);
            
            
        } catch (Application_Model_Exception_InvalidInput $ex) {
            $flashMessenger->addMessage($ex->getMessage(), 'errors');
            
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_suppliers',
                        'action' => 'index'
                            ), 'default', true);
        }
    }

    public function dashboardAction() {
        
        $cmsSuppliersDbTable = new Application_Model_DbTable_CmsSuppliers();

        $enabled = $cmsSuppliersDbTable->count(array(
        'status'=>Application_Model_DbTable_CmsSuppliers::STATUS_ENABLED,
        //'name'=>'dosen'        
                ));
        
        $allSuppliers =$cmsSuppliersDbTable->count();
        
        $this->view->enabledSuppliers = $enabled;
        $this->view->allSuppliers = $allSuppliers;
    }
    
}


