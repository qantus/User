<?php

namespace Modules\User\Controllers;

use Mindy\Base\Mindy;
use Mindy\Pagination\Pagination;
use Modules\Core\Controllers\CoreController;
use Modules\User\Models\User;
use Modules\User\UserModule;

class UserController extends CoreController
{
    public function allowedActions()
    {
        return ['index', 'view'];
    }

    public function beforeAction($action)
    {
        if (Mindy::app()->user->isGuest) {
            $this->r->redirect(Mindy::app()->getModule('user')->getLoginUrl());
        }

        return true;
    }

    public function actionView($username)
    {
        $model = User::objects()->filter(['username' => $username])->get();
        if ($model === null) {
            $this->error(404);
        }

        $this->addBreadcrumb(UserModule::t("Users"), Mindy::app()->urlManager->reverse('user.list'));
        $this->addBreadcrumb($model);

        echo $this->render('user/view.html', [
            'model' => $model,
            'profile' => $model->profile,
        ]);
    }

    public function actionIndex()
    {
        $this->addBreadcrumb(UserModule::t("Users"), Mindy::app()->urlManager->reverse('user.list'));

        $qs = User::objects()->active();
        $pager = new Pagination($qs);
        echo $this->render('user/list.html', [
            'pager' => $pager,
            'models' => $pager->paginate()
        ]);
    }

    public function actionProfile()
    {
        $model = Mindy::app()->user;

        $this->addBreadcrumb(UserModule::t("Users"), Mindy::app()->urlManager->reverse('user.list'));
        $this->addBreadcrumb($model);

        echo $this->render('user/profile.html', [
            'model' => $model,
            'profile' => $model->profile,
        ]);
    }
}
