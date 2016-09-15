<?php
class Admin_ServicesController extends Zend_Controller_Action {

    public function indexAction() {
        $flashMessenger = $this->getHelper('FlashMessenger');
        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );

        $cmsServicesDbTable = new Application_Model_DbTable_CmsServices();
        $services = $cmsServicesDbTable->search(array(
            //'filters' => array(
            //'description_search'=> 'ideja'
            
            //),
            'orders' => array(//sortiram tabelu po
                'order_number'=>'ASC'
            ),
            //'limit' => 4,
            //'page' => 2
        ));
        $services = $cmsServicesDbTable->search();
        $this->view->services = $services;
        $this->view->systemMessages = $systemMessages;
    }

    public function addAction() {

        $request = $this->getRequest();
        $flashMessenger = $this->getHelper('FlashMessenger');
        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );
        $form = new Application_Form_Admin_ServiceAdd();
        $form->populate(array(
        ));
        if ($request->isPost() && $request->getPost('task') === 'save') {
            try {

                if (!$form->isValid($request->getPost())) {
                    throw new Application_Model_Exception_InvalidInput('Invalid data was sent for new service');
                }

                $formData = $form->getValues();
                unset($formData['service_photo']);

                $cmsServicesTable = new Application_Model_DbTable_CmsServices();

                $serviceId = $cmsServicesTable->insertService($formData);

                if ($form->getElement('service_photo')->isUploaded()) {

                    $fileInfos = $form->getElement('service_photo')->getFileInfo('service_photo');
                    $fileInfo = $fileInfos['service_photo'];

                    try {

                        $servicePhoto = Intervention\Image\ImageManagerStatic::make($fileInfo['tmp_name']);
                        $servicePhoto->fit(150, 150);
                        $servicePhoto->save(PUBLIC_PATH . '/uploads/services/' . $serviceId . '.jpg');
                    } catch (Exception $ex) {
                        $flashMessenger->addMessage('Service has been saved, but error occured during image processing', 'errors');

                        $redirector = $this->getHelper('Redirector');
                        $redirector->setExit(true)
                                ->gotoRoute(array(
                                    'controller' => 'admin_services',
                                    'action' => 'edit',
                                    'id' => $serviceId
                                        ), 'default', true);
                    }
                }
                $flashMessenger->addMessage('Service has been saved', 'success');

                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_services',
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
        if ($id <= 0) {

            throw new Zend_Controller_Router_Exception('Invalid service id: ' . $id, 404);
        }
        $cmsServicesTable = new Application_Model_DbTable_CmsServices();
        $service = $cmsServicesTable->getServiceById($id);
        if (empty($service)) {
            throw new Zend_Controller_Router_Exception('No service is found with id ' . $id, 404);
        }
        $flashMessenger = $this->getHelper('FlashMessenger');
        $systemMessages = array(
            'success' => $flashMessenger->getMessages('success'),
            'errors' => $flashMessenger->getMessages('errors'),
        );
        $form = new Application_Form_Admin_ServiceAdd();
        $form->populate($service);
        if ($request->isPost() && $request->getPost('task') === 'update') {
            try {
                if (!$form->isValid($request->getPost())) {
                    throw new Application_Model_Exception_InvalidInput('Invalid data was sent for  service');
                }
                $formData = $form->getValues();
                unset($formData['service_photo']);
                if ($form->getElement('service_photo')->isUploaded()) {
                    $fileInfos = $form->getElement('service_photo')->getFileInfo('service_photo');
                    $fileInfo = $fileInfos['service_photo'];
                    try {
                        $servicePhoto = Intervention\Image\ImageManagerStatic::make($fileInfo['tmp_name']);
                        $servicePhoto->fit(250, 350);
                        $servicePhoto->save(PUBLIC_PATH . '/uploads/services/' . $service['id'] . '.jpg');
                    } catch (Exception $ex) {
                        throw new Application_Model_Exception_InvalidInput('Error occured during image processing');
                    }
                }
                $cmsServicesTable->updateService($service['id'], $formData);
                $flashMessenger->addMessage('Service has been updated', 'success');
                $redirector = $this->getHelper('Redirector');
                $redirector->setExit(true)
                        ->gotoRoute(array(
                            'controller' => 'admin_services',
                            'action' => 'index'
                                ), 'default', true);
            } catch (Application_Model_Exception_InvalidInput $ex) {
                $systemMessages['errors'][] = $ex->getMessage();
            }
        }
        $this->view->systemMessages = $systemMessages;
        $this->view->form = $form;
        $this->view->service = $service;
    }

    public function deleteAction() {

        $request = $this->getRequest();

        if (!$request->isPost() || $request->getPost('task') != 'delete') {


            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_services',
                        'action' => 'index'
                            ), 'default', true);
        }

        $flashMessenger = $this->getHelper('FlashMessenger');

        try {
            $id = (int) $request->getPost('id');
            if ($id <= 0) {
                throw new Application_Model_Exception_InvalidInput('Invalid service id: ' . $id, 'errors');
            }
            $cmsServicesTable = new Application_Model_DbTable_CmsServices();
            $service = $cmsServicesTable->getServiceById($id);
            if (empty($service)) {
                throw new Application_Model_Exception_InvalidInput('No service is found with id: ' . $id, 'errors');
            }

            $cmsServicesTable->deleteService($id);


            $flashMessenger->addMessage('Service: ' . $service['title'] . 'has been deleted', 'success');
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_services',
                        'action' => 'index'
                            ), 'default', true);
        } catch (Application_Model_Exception_InvalidInput $ex) {

            $flashMessenger->addMessage($ex->getMessage(), 'errors');
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_services',
                        'action' => 'index'
                            ), 'default', true);
        }
    }

    public function disableAction() {

        $request = $this->getRequest();

        if (!$request->isPost() || $request->getPost('task') != 'disable') {

            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_services',
                        'action' => 'index'
                            ), 'default', true);
        }

        $flashMessenger = $this->getHelper('FlashMessenger');

        try {

            $id = (int) $request->getPost('id');
            if ($id <= 0) {
                throw new Application_Model_Exception_InvalidInput('Invalid service id: ' . $id, 'errors');
            }
            $cmsServicesTable = new Application_Model_DbTable_CmsServices();
            $service = $cmsServicesTable->getServiceById($id);
            if (empty($service)) {
                throw new Application_Model_Exception_InvalidInput('No service is found with id: ' . $id, 'errors');
            }

            $cmsServicesTable->disableService($id);


            $flashMessenger->addMessage('Service: ' . $service['title'] . 'has been disabled', 'success');
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_services',
                        'action' => 'index'
                            ), 'default', true);
        } catch (Application_Model_Exception_InvalidInput $ex) {

            $flashMessenger->addMessage($ex->getMessage(), 'errors');
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_services',
                        'action' => 'index'
                            ), 'default', true);
        }
    }

    public function enableAction() {

        $request = $this->getRequest();

        if (!$request->isPost() || $request->getPost('task') != 'enable') {

            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_services',
                        'action' => 'index'
                            ), 'default', true);
        }

        $flashMessenger = $this->getHelper('FlashMessenger');

        try {
            $id = (int) $request->getPost('id');
            if ($id <= 0) {
                throw new Application_Model_Exception_InvalidInput('Invalid service id: ' . $id, 'errors');
            }

            $cmsServiceTable = new Application_Model_DbTable_CmsServices();

            $service = $cmsServiceTable->getServiceById($id);
            if (empty($service)) {
                throw new Application_Model_Exception_InvalidInput('No service is found with id: ' . $id, 'errors');
            }
            $cmsServiceTable->enableService($id);
            $flashMessenger->addMessage('Service: ' . $service['title'] . 'has been enabled', 'success');
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_services',
                        'action' => 'index'
                            ), 'default', true);
        } catch (Application_Model_Exception_InvalidInput $ex) {

            $flashMessenger->addMessage($ex->getMessage(), 'errors');
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_services',
                        'action' => 'index'
                            ), 'default', true);
        }
    }

    public function updateorderAction() {

        $request = $this->getRequest();

        if (!$request->isPost() || $request->getPost('task') != 'saveOrder') {

            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_services',
                        'action' => 'index'
                            ), 'default', true);
        }

        $flashMessenger = $this->getHelper('FlashMessenger');

        try {

            $sortedIds = $request->getPost('sorted_ids');

            if (empty($sortedIds)) {

                throw new Application_Model_Exception_InvalidInput('Sorted ids are not sent');
            }

            $sortedIds = trim($sortedIds, ' ,');

            if (!preg_match('/^[0-9]+(,[0-9]+)*$/', $sortedIds)) {
                throw new Application_Model_Exception_InvalidInput('Invalid  sorted ids ' . $sortedIds);
            }

            $sortedIds = explode(',', $sortedIds);

            $cmsServicesTable = new Application_Model_DbTable_CmsServices();

            $cmsServicesTable->updateOrderOfServices($sortedIds);

            $flashMessenger->addMessage('Order is successfully saved', 'success');
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_services',
                        'action' => 'index'
                            ), 'default', true);
        } catch (Application_Model_Exception_InvalidInput $ex) {

            $flashMessenger->addMessage($ex->getMessage(), 'errors');
            $redirector = $this->getHelper('Redirector');
            $redirector->setExit(true)
                    ->gotoRoute(array(
                        'controller' => 'admin_services',
                        'action' => 'index'
                            ), 'default', true);
        }
    }

    public function dashboardAction() {

        $cmsServicesDbTable = new Application_Model_DbTable_CmsServices();

        $totalNumberOfServices = $cmsServicesDbTable->count();
        $activeServices = $cmsServicesDbTable->count(array(
            'status' => Application_Model_DbTable_CmsServices::STATUS_ENABLED
        ));

        $this->view->activeServices = $activeServices;
        $this->view->totalNumberOfServices = $totalNumberOfServices;
    }

}
