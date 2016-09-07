<?php
class Application_Form_Admin_ProductAdd extends Zend_Form
{
    public function init() {
        
        $model = new Zend_Form_Element_Text('model');
        $model->addFilter('model')
                ->addValidator('StringLength', false, array('min'=>3, 'max'=>20))
                 ->setRequired(true);
        $this->addElement($model);
        
        $type = new Zend_Form_Element_Text('type');
        //$firstName->addFilter(new Zend_Filter_StringTrim());
        //$firstName->addValidator(new Zend_Validate_StringLength(array('min'=>3, 'max'=>255)));
        $type->addFilter('StringTrim')
                ->addValidator('StringLength', false, array('min'=>3, 'max'=>20))
                ->setRequired(true);
        $this->addElement($type);
        
        $description = new Zend_Form_Element_Textarea('description');
        $description->addFilter('StringTrim')
                ->addValidator('StringLength', false, array('min'=>3, 'max'=>255))
                ->setRequired(false);
        $this->addElement($description);
        
        $price = new Zend_Form_Element_Text('price');
        $price->addFilter('StringTrim')
                ->addValidator(new Zend_Validate_Float())
                ->setRequired(true);
        $this->addElement($price);
        
        $quantity = new Zend_Form_Element_Text('quantity');
        $quantity->addFilter('StringTrim')
                ->isValid($price);
                //->setRequired(false);
        $this->addElement($quantity);
        
        $stockStatus = new Zend_Form_Element_Button('stock_status');
        $stockStatus->addFilter('StringTrim')
                    ->addValidator(new Zend_Validate_InArray(array('stock_status' => 1,
                                             'stock_status' => 0)))
                    ->setRequired(false);
        $this->addElement($quantity);
        
        $partStatus = new Zend_Form_Element_Button('part_status');
        $partStatus->addFilter('StringTrim')
                
                //->addValidator('StringLength', false, array('min'=>3, 'max'=>10))
                ->setRequired(false);
        $this->addElement($partStatus);
  
        
    }

}

