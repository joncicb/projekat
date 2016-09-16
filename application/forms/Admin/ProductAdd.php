<?php
class Application_Form_Admin_ProductAdd extends Zend_Form
{
    public function init() {
        
        $model = new Zend_Form_Element_Text('model');
        $model->addFilter('StringTrim')
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
        
//        $productPhoto = new Zend_Form_Element_File('product_photo');
//        $productPhoto->addValidator('Count', true, 1) 
//                    ->addValidator('MimeType', true, array('image/jpeg', 'image/gif', 'image/png'))
//                    ->addValidator('ImageSize', false, array(
//                        'minwidth' => 150,
//                        'minheight' => 150,
//                        'maxwidth' => 2000,
//                        'maxheight' => 2000
//                    ))
//                    ->addValidator('Size', false, array(
//                        'max' => '10MB'
//                    ))
//                    ->setValueDisabled(true)
//                    ->setRequired(false);
//        
//            $this->addElement($productPhoto);
  
        
    }

}

