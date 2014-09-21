<?php

namespace Modules\User\Forms;

use Mindy\Form\Fields\CharField;
use Mindy\Form\Form;

class RecoverForm extends Form
{
    public function getFields()
    {
        return [
            'login_or_email' => [
                'class' => CharField::className()
            ]
        ];
    }

    public function cleanLogin_or_email()
    {

    }
}
