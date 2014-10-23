<?php

/**
 * TODO refactoring
 * @DEPRECATED
 * Class MPermissionCheckBehavior
 */
class MPermissionCheckBehavior extends CActiveRecordBehavior
{
    public $can_view = 'can_view';
    public $can_update = 'can_update';
    public $can_delete = 'can_delete';
    public $can_create = 'can_create';
    public $can_nested = 'can_nested';

    public $parent_property = 'parent_id';
    protected $_old_parent_id;

    public $relation = false;
    protected $_owner_id;
    protected $_owner_class;
    protected $_owner_property;

    protected function getIsNested()
    {
        return array_key_exists('nestedSet', $this->getOwner()->behaviors());
    }

    protected function getIsBelongs()
    {
        return (bool) $this->relation;
    }

    protected function belongsInit()
    {
        if ($this->relation) {
            if (array_key_exists($this->relation, $this->getOwner()->relations())) {
                $relations = $this->getOwner()->relations();
                $relation = $relations[$this->relation];

                $this->_owner_class = $relation[1];
                $this->_owner_property = $relation[2];
                $this->_owner_id = $this->getOwnerProperty($relation[2]);

                return true;
            }
        }

        return false;
    }

    protected function getOwnerProperty($property)
    {
        if (property_exists($this->getOwner(), $property) || array_key_exists($property, $this->getOwner()->attributes)) {
            return $this->getOwner()->{$property};
        } else {
            return null;
        }
    }

    public function error($message = null)
    {
        throw new CHttpException(403, $message);
    }

    public function route($action, $class = false)
    {
        if (!$class) {
            $class = get_class($this->getOwner());
        }

        return $this->getOwner()->module . '.' . strtolower($class) . '.' . $action;
    }

    public function can($action, $id = null, $class = false)
    {
        $can_nested = false;

        if ($this->isNested) {
            $can_nested = $this->can_nested($action, $id, $class);
        }

        if ($id === null)
            $id = $this->getOwner()->primaryKey;

        return $can_nested || Yii::app()->user->canObject($this->route($action, $class), $id);
    }

    public function can_nested($action, $id = null, $class = false)
    {
        $parent_id = null;

        if ($id === null) {
            $parent_id = $this->getOwnerProperty($this->parent_property);
        } else {
            if ($class) {
                $model = new $class();
                $model = $model->findByPk($id);
            } else {
                $model = $this->getOwner()->findByPk($id);
            }
            if ($model && (property_exists($model, $this->parent_property) || array_key_exists($this->parent_property, $model->attributes)))
                $parent_id = $model->{$this->parent_property};
        }

        if ($parent_id) {
            return Yii::app()->user->canObject($this->route($this->can_nested, $class), $parent_id) &&
            Yii::app()->user->canObject($this->route($action, $class), $parent_id);
        } else
            return false;
    }

    public function can_view($id = null, $class = false)
    {
        return $this->can($this->can_view, $id, $class);
    }

    public function can_update($id = null, $class = false)
    {
        return $this->can($this->can_update, $id, $class);
    }

    public function can_create($id = null, $class = false)
    {
        return $this->can($this->can_update, $id, $class);
    }

    public function can_delete($id = null, $class = false)
    {
        return $this->can($this->can_delete, $id, $class);
    }

    public function beforeFind($event)
    {
        $owner = $this->getOwner();
        $ownerCriteria = $owner->getDbCriteria();
        $ownerCriteria->mergeWith($this->getPermissionCriteria($this->can_view));
        $owner->setDbCriteria($ownerCriteria);
        return $owner;
    }

    public function afterFind($event)
    {

        if ($this->getIsNested()) {
            $this->_old_parent_id = $this->getOwnerProperty('parent_id');
        }
    }

    public function getPermissionCriteria($action)
    {
        $criteria = new CDbCriteria();
        $fake_criteria = new CDbCriteria();

        if ($this->getIsNested())
            return $this->getCriteriaNested($action);

        $user = Yii::app()->user;

        if ($user->getIsSuperuser() || Yii::app()->getAuthManager()->canGlobal($this->route($action)))
            return $criteria;
        else {
            $tablePermission = Yii::app()->getAuthManager()->tablePermission; //tablePerObjectPermission
            $tablePerObject = Yii::app()->getAuthManager()->tablePerObjectPermission;
            $tableAlias = $this->getOwner()->tableAlias;

            $criteria->join = "LEFT JOIN {$tablePerObject} objectpermission ON objectpermission.model_id = {$tableAlias}.id ";
            $criteria->join .= "LEFT JOIN {$tablePermission} permissions ON objectpermission.permission_id = permissions.id";

            $criteria->addCondition('objectpermission.type =:type_user AND objectpermission.owner_id = :owner_id');

            $fake_criteria->addInCondition('objectpermission.owner_id', array_keys($user->groups));
            $fake_criteria->addCondition('objectpermission.type =:type_group');

            $criteria->addCondition($fake_criteria->condition, 'OR');

            $criteria->addCondition('permissions.code = :code');

            $criteria->params = array_merge($fake_criteria->params, array(
                'code' => $this->route($action),
                'owner_id' => $user->id,
                'type_user' => MPermissionManager::TYPE_USER,
                'type_group' => MPermissionManager::TYPE_GROUP,
            ));

            return $criteria;
        }
    }

    protected function getCriteriaNested($action)
    {
        $criteria = new CDbCriteria();
        $fake_criteria = new CDbCriteria();

        $user = Yii::app()->getUser();
        if ($user->getIsSuperuser() || Yii::app()->getAuthManager()->canGlobal($this->route($action)))
            return $criteria;
        else {
            $tablePermission = Yii::app()->getAuthManager()->tablePermission; //tablePerObjectPermission
            $tablePerObject = Yii::app()->getAuthManager()->tablePerObjectPermission;
            $tableAlias = $this->getOwner()->tableAlias;

            $criteria->join = "LEFT JOIN {$tablePerObject} objectpermission ON objectpermission.model_id = {$tableAlias}.id ";
            $criteria->join .= "LEFT JOIN {$tablePermission} permissions ON objectpermission.permission_id = permissions.id";

            $criteria->addCondition('objectpermission.type =:type_user AND objectpermission.owner_id = :owner_id');

            $fake_criteria->addInCondition('objectpermission.owner_id', array_keys($user->groups));
            $fake_criteria->addCondition('objectpermission.type =:type_group');

            $criteria->addCondition($fake_criteria->condition, 'OR');

            $criteria->addCondition('permissions.code = :code');

            $criteria->params = array_merge($fake_criteria->params, array(
                'code' => $this->route($action),
                'owner_id' => $user->id,
                'type_user' => MPermissionManager::TYPE_USER,
                'type_group' => MPermissionManager::TYPE_GROUP,
            ));

            return $criteria;
        }
    }

    public function nestedUpdateCheck()
    {
        if ($this->getIsNested()) {
            if (Yii::app()->user->isSuperuser)
                return true;
            if ($this->_old_parent_id != $this->getOwnerProperty($this->parent_property)) {
                return false;
            }
            return true;
        } else
            return true;
    }

    public function belongsCheck()
    {
        if ($this->getIsBelongs()) {
            if (Yii::app()->user->isSuperuser)
                return true;

            $this->belongsInit();

            if (!$this->can('can_update', $this->_owner_id, $this->_owner_class)) {
                return false;
            }
            return true;
        } else
            return true;
    }


    public function beforeDelete($event)
    {
        if ($this->getIsBelongs()) {
            if (!$this->belongsCheck()) {
                $this->getOwner()->addError($this->_owner_property, UserModule::t("You can't update objects in this category"));
                return false;
            }
            return true;
        } else {
            if ($this->can($this->can_delete) == false) {
                $this->error();
            }
            return true;
        }
    }

    public function beforeSave($event)
    {
        if ($this->getIsBelongs()) {
            if (!$this->belongsCheck()) {
                $this->getOwner()->addError($this->_owner_property, UserModule::t("You can't update objects in this category"));
                $event->isValid = false;
                return false;
            }
            return true;
        } else {

            if ($this->getOwner()->getIsNewRecord()) {
                if (!$this->can_create($this->getOwnerProperty($this->parent_property))) {
                    $this->getOwner()->addError('parent_id', UserModule::t("You can't create objects in this category"));
                    $event->isValid = false;
                    return false;
                }

                return true;
            } else {
                if (!$this->can($this->can_update)) {
                    $this->error();
                    return false;
                } elseif (!$this->nestedUpdateCheck()) {
                    $this->getOwner()->addError('parent_id', UserModule::t("You can't update category object"));
                    $event->isValid = false;
                    return false;
                }
                return true;
            }
        }
    }

    public function extendAdminSearch($MButtonColumn)
    {

        if (isset($MButtonColumn['template'])) {
            if (strpos($MButtonColumn['template'], '{perm}') === false) {
                $MButtonColumn['template'] .= '{perm}';
            }
        } else {
            $MButtonColumn['template'] = '{update}{delete}{perm}';
        }


        if (isset($MButtonColumn['buttons'])) {

            if (isset($MButtonColumn['buttons']['update']))
                if (isset($MButtonColumn['buttons']['update']['visible']))
                    $MButtonColumn['buttons']['update']['visible'] .= ' && $data->can_update()';
                else
                    $MButtonColumn['buttons']['update']['visible'] = '$data->can_update()';
            else {
                $MButtonColumn['buttons']['update'] = array();
                $MButtonColumn['buttons']['update']['visible'] = '$data->can_update()';
            }

            if (isset($MButtonColumn['buttons']['delete']))
                if (isset($MButtonColumn['buttons']['delete']['visible']))
                    $MButtonColumn['buttons']['delete']['visible'] .= ' && $data->can_delete()';
                else
                    $MButtonColumn['buttons']['delete']['visible'] = '$data->can_delete()';
            else {
                $MButtonColumn['buttons']['delete'] = array();
                $MButtonColumn['buttons']['delete']['visible'] = '$data->can_delete()';
            }

            if (!isset($MButtonColumn['buttons']['perm']))
                $MButtonColumn['buttons']['perm'] = array(
                    'options' => array('class' => 'mmodal'),
                    'label' => '<i class="icon-key" rel="tooltip" title="' . UserModule::t("Permissions") . '"></i>',
                    'url' => 'array("//user/admin/permission/custom", "name" => get_class($data), "id" => $data->id)',
                    'visible' => 'Yii::app()->user->isSuperuser'
                );

        } else {
            $MButtonColumn['buttons'] = array(
                'perm' => array(
                    'options' => array('class' => 'mmodal'),
                    'label' => '<i class="icon-key" rel="tooltip" title="' . UserModule::t("Permissions") . '"></i>',
                    'url' => 'array("//user/admin/permission/custom", "name" => get_class($data), "id" => $data->id)',
                    'visible' => 'Yii::app()->user->isSuperuser'
                ),
                'update' => array(
                    'visible' => '$data->can_update()'
                ),
                'delete' => array(
                    'visible' => '$data->can_delete()'
                )
            );
        }

        return $MButtonColumn;
    }
}