<?php

class UserController extends CrudController
{
    public $model = 'User';

    /**
     * @var string Имя формы
     */
    public $formName = 'user-form';


    public function actions()
    {
        $actions = parent::actions();
        unset($actions['update'], $actions['create']);
        return $actions;
    }

    /*
     * Права доступа по умолчанию
     */
    public function accessRules()
    {
        return array(
            array('allow', // allow admin user to perform 'admin' and 'delete' actions
                'actions' => array('admin', 'delete', 'create', 'update', 'view'),
                //'users'=>UserModule::getAdmins(),
            ),
            array('deny', // deny all users
                'users' => array('*'),
            ),
        );
    }

    public function actionCreate()
    {
        $this->actionUpdate(null, true);
    }

    /**
     * Редактирование пользователя и его профиля (кастомных полей)
     *
     * Если id пользователя гость, рендрим пояснения по работе с пользователем гость
     * Редактирование данного пользователя не возможно. Только выставление прав доступа.
     *
     * @param null $id
     * @param bool $new
     */
    public function actionUpdate($id = null, $new = false)
    {
        if ($new && $id === null) {
            // Если создаем юзера то создаем новые экземпляры класса юзера и профиля
            $model = new $this->model();
            $profile = new UserProfile();
        } else {
            // Загружаем модель пользователи и если профиль отсутствует создаем его
            $model = $this->getModel($this->model, $id);

            if($model->isGuest)
                $model->setScenario('guest');

            if (($profile = $model->profile) === null)
                $profile = new UserProfile();
        }

        /*
         * ajax валидация формы. Если пользователь гость то профиль не валидируем.
         * У пользователя гость не может быть профиля
         */
        $this->ajaxValidation($model->isGuest ? array($model) : array($model, $profile), $this->formName);

        if (isset($_POST[$this->model])) {
            $model->attributes = $_POST[$this->model];

            /*
             * Если профиль не отправлен значит и не присваиваем
             * Нужно в случае если есть не обязательные пользовательские поля
             */
            if (isset($_POST['UserProfile']) && $model->isGuest === false)
                $profile->attributes = $_POST['UserProfile'];

            // У пользователя гость не может быть профиля
            if($model->isGuest)
                $valid = $model->validate();
            else
                $valid = $model->validate() && $profile->validate();

            if ($valid) {

                // Применение полученных прав доступа
                if(isset($_POST[$this->model]['permissions']))
                    $model->setPermissionsRaw($_POST[$this->model]['permissions']);

                /*
                 * @TODO: wtf? переписать, отрефакторить, вынести в event модели. Это логика отправки письма активированному пользователю.
                 * @TODO: так же поставить запрет на отправку письма повторно активированному пользователю основываясь на last_login времени
                $old_model = User::model()->notsafe()->findByPk($model->id);
                if (($old_model->status == User::STATUS_MODERATION) && ($model->status != User::STATUS_MODERATION)) {
                    $message = $this->renderPartial('/mail/activated', array(), true);
                    Y::sendEmail($model->email, UserModule::t("User activation"), $message);
                }
                */

                // Сохраняем
                if ($model->save()) {

                    // Сохраняем профиль пользователя
                    if ($model->isGuest == false && $profile->validate()) {
                        $profile->user_id = $model->id;
                        $profile->save();
                    }

                    if(Yii::app()->request->isAjaxRequest)
                        $this->responseJson(array(
                            'status' => 'success',
                            'title' => CoreModule::t('Success')
                        ));
                    else
                        $this->redirect(array('admin'));
                }
            }
        }

        if($model->isGuest)
            $view = 'guest';
        else
            $view = ($new && $id === null) ? 'create' : 'update';

        $params = array(
            'model' => $model,
            'profile' => $profile,
            'fields' => $profile->getFields()
        );

        if(Yii::app()->request->isAjaxRequest)
            $this->renderJson($view, $params);
        else
            $this->render($view, $params);
    }

    /**
     * @TODO: Просмотр сайта от другого пользователя
     * @param $userId
     */
    public function actionAsuser($userId)
    {
        // Yii::app()->user->changeIdentity();
    }
}