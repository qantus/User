<?php

namespace Modules\User\Controllers;

use Mindy\Base\Mindy;
use Modules\Core\Controllers\CoreController;
use Modules\User\Forms\UserLoginForm;
use Modules\User\UserModule;

class AuthController extends CoreController
{
    public $defaultAction = 'login';

    public $defaultRedirectUrl = '/';

    public function allowedActions()
    {
        return ['login', 'logout'];
    }

    public function init()
    {
        parent::init();

        if (isset($_GET['redirectUrl'])) {
            Mindy::app()->auth->setReturnUrl($_GET['redirectUrl']);
        }
    }

    public function redirectUser()
    {
        $returnUrl = Mindy::app()->getModule('user')->getReturnUrl();
        parent::redirect($returnUrl ? $returnUrl : $this->defaultRedirectUrl);
    }

    public function actionLogin()
    {
        if (!Mindy::app()->auth->getIsGuest()) {
            $this->redirectUser();
        }

        $form = new UserLoginForm();

        if (!empty($_POST)) {
            $form->setAttributes($_POST);

            if ($form->isValid() && $form->login()) {
                if (Mindy::app()->request->isAjaxRequest) {
                    $this->json(array(
                        'status' => 'success',
                        'title' => UserModule::t('You have successfully logged in to the site')
                    ));
                } else {
                    $this->redirectUser();
                }
            }
        }

        $data = [
            'form' => $form
        ];

        if (Mindy::app()->request->isAjaxRequest) {
            $this->json([
                'content' => $this->render('user/_login.twig', $data)
            ]);
        } else {
            echo $this->render('user/login.twig', $data);
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
        $this->redirect(Mindy::app()->getModule('user')->returnUrl);
    }
}
