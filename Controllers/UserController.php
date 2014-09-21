<?php

namespace Modules\User\Controllers;

use Mindy\Base\Mindy;
use Mindy\Pagination\Pagination;
use Modules\Core\Controllers\CoreController;
use Modules\User\Models\User;

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

    public function actionView($id)
    {
        $model = User::objects()->filter(['pk' => $id])->get();
        if ($model === null) {
            $this->error(404);
        }
        echo $this->render('user/view.twig', [
            'model' => $model,
            'profile' => $model->profile,
        ]);
    }

    public function actionIndex()
    {
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
        echo $this->render('user/profile.html', [
            'model' => $model,
            'profile' => $model->profile,
        ]);
    }
}
