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
 * @date 14/11/14.11.2014 14:10
 */

namespace Modules\User\Controllers;

use Mindy\Base\Mindy;
use Modules\Core\Controllers\CoreController;
use Modules\User\UserModule;

class ProfileController extends CoreController
{
    public function actionUpdate()
    {
        $user = Mindy::app()->user;

        /** @var \Modules\User\UserModule $module */
        $module = $this->getModule();

        $this->addTitle($user);
        $this->addTitle(UserModule::t("Update profile"));
        $this->addBreadcrumb($user, $user->getAbsoluteUrl());
        $this->addBreadcrumb(UserModule::t("Update profile"));

        $formClass = $module->profileFormClass;
        $form = new $formClass;
        if ($this->request->isPost && $form->populate($_POST)->isValid() && $form->save()) {
            $this->request->flash->success(UserModule::t('Profile successfully updated'));
            $this->redirect($user);
        }

        echo $this->render('user/profile_form.html', [
            'user' => $user,
            'form' => $form
        ]);
    }
}
