<?php

namespace Modules\User\Controllers;

use Mindy\Base\Mindy;
use Modules\Core\Controllers\FrontendController;
use Modules\User\Models\User;

class UserController extends FrontendController
{
    public function allowedActions()
    {
        return ['index', 'view'];
    }

    public function init()
    {
        parent::init();

        if (Mindy::app()->auth->isGuest) {
            $this->redirect(Mindy::app()->getModule('user')->getLoginUrl());
        }
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

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $models = User::objects()->active()->paginate(isset($_GET['page']) ? $_GET['page'] : 1)->all();
        echo $this->render('user/list.twig', ['models' => $models]);
    }

    public function actionProfile()
    {
        $id = Mindy::app()->user->pk;
        $model = User::objects()->filter(['pk' => $id])->get();
        if ($model === null) {
            $this->error(404);
        }
        echo $this->render('user/profile.twig', [
            'model' => $model,
            'profile' => $model->profile,
        ]);
    }
}
