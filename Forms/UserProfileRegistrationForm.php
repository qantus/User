<?php

class UserProfileRegistrationForm extends MForm
{
    public function getFields()
    {
        $model = $this->getModel();
        $profileFields = $model->getFields();

        $fields = array();

        if ($profileFields) {
            foreach($profileFields as $field) {
                if ($field->range){
                    $fields[$field->varname] = array(
                        'class' => 'Select2Field',
                        'data' => UserProfile::range($field->range)
                    );
                }elseif($field->field_type=="TEXT"){
                    $fields[$field->varname] = array(
                        'class' => 'TextareaField'
                    );
                }else{
                    $fields[$field->varname] = array(
                        'class' => 'TextField',
                        'options' => array(
                            'htmlOptions' => array(
                                'maxlength' => ($field->field_size) ? $field->field_size : 255
                            )
                        )
                    );
                }
            }
        }

	    return $fields;
    }

}