<?php
/**
 * 
 *
 * All rights reserved.
 * 
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 14/11/14.11.2014 14:13
 */

namespace Modules\User\Forms;

use Mindy\Form\ModelForm;
use Modules\User\Models\Profile;

class ProfileForm extends ModelForm
{
    public function getModel()
    {
        return new Profile;
    }
}
