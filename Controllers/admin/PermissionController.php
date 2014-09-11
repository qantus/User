<?php

class PermissionController extends CrudController
{
    public $model = 'UserPermission';

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view']);
        return $actions;
    }

    public function actionGenerator()
    {
        Yii::app()->authGenerator->run();
        $this->setFlash('success', UserModule::t('Permissions successfully generated'));
        $this->redirect(array('//user/admin/permission/admin'));
    }

    public function actionCustom($name,$id){

        if (isset($_POST['users']) || isset($_POST['groups'])){
            $users = (isset($_POST['users']) && is_array($_POST['users'])) ? $_POST['users']: array();
            $groups = (isset($_POST['groups']) && is_array($_POST['groups'])) ? $_POST['groups']: array();

            UserPermissionObject::model()->clearObjectPermissions($name,$id);

            foreach ($users as $user_id => $permissions){
                UserPermissionObject::model()->setObjectPermissions($name,$id,$permissions,$user_id,MPermissionManager::TYPE_USER);
            }

            foreach ($groups as $group_id => $permissions){
                UserPermissionObject::model()->setObjectPermissions($name,$id,$permissions,$group_id,MPermissionManager::TYPE_GROUP);
            }

            if (Yii::app()->request->isAjaxRequest) {
                $this->responseJson(array(
                    'status' => 'success',
                    'title' => CoreModule::t('Success')
                ));
            }

            Yii::app()->end();
        }

        $availible = UserPermission::model()->getAvailiblePermissions($name);

        $enabled = UserPermissionObject::model()->getObjectPermissions($name,$id);

        $users = User::model()->findAll();
        $groups = UserGroup::model()->findAll();

        $params = array(
            'availible' => $availible,
            'enabled' => $enabled,
            'users' => $users,
            'groups' => $groups
        );

        $this->renderJsonMaybe('form',$params);
    }
}