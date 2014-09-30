<?php

namespace Modules\User\Controllers;

use Modules\Core\Controllers\CoreController;
use Modules\User\Forms\ChangePasswordForm;
use Modules\User\Forms\RecoverForm;
use Modules\User\Models\User;
use Modules\User\UserModule;

class RecoverController extends CoreController
{
    public function actionIndex()
    {
        $form = new RecoverForm();
        if ($this->r->isPost && $form->setAttributes($_POST)->isValid() && $form->send()) {
            echo $this->render('user/recover_form_success.html');
        } else {
            echo $this->render('user/recover_form.html', [
                'form' => $form
            ]);
        }
    }

    public function actionActivate($key)
    {
        $model = User::objects()->filter(['activation_key' => $key])->get();
        if ($model === null) {
            $this->error(404);
        }

        if ($model->activation_key === $key) {
            $form = new ChangePasswordForm([
                'instance' => $model
            ]);
            if ($this->r->isPost && $form->setAttributes($_POST)->isValid() && $form->save()) {
                $this->r->flash->success(UserModule::t('Password changed'));
                $this->r->redirect('user.login');
            } else {
                echo $this->render('user/change_password.html', [
                    'form' => $form,
                    'model' => $model,
                    'key' => $key
                ]);
            }
        } else {
            echo $this->render('user/change_password_incorrect.html');
        }
    }
}