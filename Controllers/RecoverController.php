<?php

namespace Modules\User\Controllers;

use Modules\Core\Controllers\CoreController;

class RecoverController extends CoreController
{
    public function actionIndex()
    {
        $form = new RecoverForm();

        echo $this->render('user/recover_form.html', [
            'form' => $form
        ]);
    }
}
