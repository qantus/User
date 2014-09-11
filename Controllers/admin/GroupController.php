<?php

class GroupController extends CrudController
{
    public $model = 'UserGroup';
    public $redirectRoute = 'admin';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view']);
        return array_merge($actions, array(
            'toggle' => array(
                'class' => 'mindy.zii.widgets.grid.actions.ToggleAction',
                'model' => $this->model
            )
        ));
    }

    public function actionUpdate($id)
    {
        if ($id === null) {
            $model = new $this->model();
        } else {
            $model = $this->getModel($this->model, $id);
        }

        $this->ajaxValidation($model, $this->formName);

        if (isset($_POST[$this->model])) {
            $model->attributes = $_POST[$this->model];

            /*
             * Применение полученных прав доступа
             */
            if(isset($_POST[$this->model]['permissions']))
                $model->setPermissionsRaw($_POST[$this->model]['permissions']);

            if ($model->validate() && $model->save()) {
                if (Yii::app()->request->isAjaxRequest) {
                    echo CJSON::encode(array(
                        'status' => 'success',
                        'title' => CoreModule::t('Success')
                    ));
                    Yii::app()->end();
                } else {
                    if($this->redirectRoute === null) {
                        $pk = $model->{$model->tableSchema->primaryKey};
                        $this->redirect(array('view', 'id' => $pk));
                    } else
                        $this->redirect(array($this->redirectRoute));
                }
            }
        }

        $view = ($id === null) ? 'create' : 'update';

        $params = array(
            'model' => $model,
        );

        if (Yii::app()->request->isAjaxRequest) {
            echo CJSON::encode(array(
                'content' => $this->renderPartial($view, $params, true, true)
            ));
            Yii::app()->end();
        } else
            $this->render($view, $params);
    }
}