<?php
class Application_Form_Admin_SupplierAdd extends Zend_Form
{
    
    // Overajdovan init metoda
    public function init() {
        $name = new Zend_Form_Element_Text('name');
        
        $name->addFilter('StringTrim')
                ->addValidator('StringLength', false, array('min' => 3, 'max' => 255))
                ->setRequired(true);
        
        $this->addElement($name);
        
        
        $description = new Zend_Form_Element_Textarea('description');
        $description->addFilter('StringTrim')
                ->setRequired(false);
        $this->addElement($description);
        
        $supplierPhoto = new Zend_Form_Element_File('supplier_photo');
        $supplierPhoto->addValidator('Count', true, 1) 
                ->addValidator('MimeType', true, array('image/gif', 'image/jpeg', 'image/png', 'messages' => 'File extension is not supported'))
                ->addValidator('ImageSize', false, array(
                    'minwidth' => 170,
                    'minheight' => 70,
                    'maxwidth' => 2000,
                    'maxheight' => 2000
                ))
                ->addValidator('Size', false, array(
                    'max' => '10MB'
                    ))
                ->setValueDisabled(true)
                ->setRequired(true);
        
        $this->addElement($supplierPhoto);
        
    }
    
}