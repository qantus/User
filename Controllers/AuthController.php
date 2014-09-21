<?php

namespace Modules\User\Controllers;

use Mindy\Base\Mindy;
use Modules\Core\Controllers\CoreController;
use Modules\User\Forms\LoginForm;
use Modules\User\UserModule;

class AuthController extends CoreController
{
    public function allowedActions()
    {
        return ['login', 'logout'];
    }

    public function actionLogin()
    {
        $app = Mindy::app();
        if (!$app->user->isGuest) {
            $this->r->redirect('user.profile');
        }

        $form = new LoginForm();
        if ($this->r->isPost && $form->setAttributes($_POST)->isValid() && $form->login()) {
            if ($this->r->isAjax) {
                echo $this->json([
                    'status' => 'success',
                    'title' => UserModule::t('You have successfully logged in to the site')
                ]);
            } else {
                $this->r->redirect('user.profile');
            }
        }

        $data = [
            'form' => $form
        ];

        if ($this->r->isAjax) {
            echo $this->json([
                'content' => $this->render('user/_login.html', $data)
            ]);
        } else {
            echo $this->render('user/login.html', $data);
        }
    }

    /**
     * Logout the current user and redirect to returnLogoutUrl.
     */
    public function actionLogout()
    {
        $auth = Mindy::app()->auth;
        if($auth->isGuest) {
            $this->redirect(Mindy::app()->homeUrl);
        }

        $auth->logout();
        $this->redirect(Mindy::app()->homeUrl);
    }
}
