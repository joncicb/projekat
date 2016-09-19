<?php
class Application_Form_Admin_ProductAdd extends Zend_Form
{
    public function init() {
        $model = new Zend_Form_Element_Text('model');
        //$model->addFilter(new Zend_Filter_StringTrim());
        //$model->addValidator(new Zend_Validate_StringLength(array('min'=>3, 'max'=>255)));
        $model->addFilter('StringTrim')
                ->addValidator('StringLength', false, array('min'=>3, 'max'=>20))
                ->setRequired(true);
        $this->addElement($model);
        
        $type = new Zend_Form_Element_Text('type');
        $type->addFilter('StringTrim')
                ->addValidator('StringLength', false, array('min'=>3, 'max'=>10))
                ->setRequired(true);
        $this->addElement($type);
        
        $price = new Zend_Form_Element_Text('price');
        $price->addFilter('StringTrim')
                ->addValidator(new Zend_Validate_Float())
                ->setRequired(true);
        $this->addElement($price);
        
        $discount = new Zend_Form_Element_Text('discount');
        $discount->addFilter('StringTrim')
                ->addValidator('StringLength', false, array('min'=>2, 'max'=>4))
                ->setRequired(false);
        $this->addElement($discount);
        
        $quantity = new Zend_Form_Element_Text('quantity');
        $quantity->addFilter('StringTrim')
                ->addValidator($validator = new Zend_Validate_Int())
                ->setRequired(true);
        $this->addElement($quantity);
        
        $description = new Zend_Form_Element_Textarea('description');
        $description->addFilter('StringTrim')
               ->setRequired(true);
        $this->addElement($description);
        
        $productPhoto = new Zend_Form_Element_File('product_photo');
        $productPhoto->addValidator('Count', true, 1)//true se stavlja da ako ne prodje validator prekida se izvrsavanje validacije na nivou elementa forme a ne cele forme
                ->addValidator('MimeType', true, array('image/jpeg', 'image/gif', 'image/png'))
                ->addValidator('ImageSize', false, array(
                    'minwidth' => 150,
                    'minheight' => 150,
                    'maxwidth' => 2000,
                    'maxheight' => 2000
                ))
                ->addValidator('Size', false, array(
                    'max' => '10MB'
                ))
                //->setDestination('')stavlja u destination folder koji preciziramo
                ->setValueDisabled(true)//sa ovim uvek stavja file u default direktorijum disable move file to destination when calling method getValues()
                ->setRequired(false);
                
                $this->addElement($productPhoto);
                
        
        
    }

}

