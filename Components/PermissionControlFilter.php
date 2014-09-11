<?php

namespace Modules\User\Components;

use Mindy\Base\Filter;
use Mindy\Base\Mindy;
use Modules\Core\Controllers\BackendController;

class PermissionControlFilter extends Filter
{
    protected $_allowedActions = [];

    /**
     * Проверка разрешенеия текущего действия (action) на выполнение
     * @param $action
     * @return bool
     */
    public function isAllowedAction($action)
    {
        if($this->_allowedActions === ['*'] || in_array($action, $this->_allowedActions)) {
            return true;
        }

        return false;
    }

    protected function preFilter($filterChain)
    {
        $user = Mindy::app()->getUser();
        $controller = $filterChain->controller;
        $action = $filterChain->action->id;

        // Проверка на разрешенные actions
        if ($this->isAllowedAction($action, $filterChain) === true) {
            return true;
        }

        $reflect = new \ReflectionClass($controller);
        $code = strtolower(str_replace('\\', '.', $reflect->getNamespaceName()) . '.' . $reflect->getShortName());

        // Проверяем права доступа на все actions контроллера или проверяем имя текущего action
        if($user->can($code . '.' . $action) || $user->can($code . '.*')) {
            return true;
        }

        // Если ни одна проверка не была пройдена успешно - возвращаем 403 ошибку
        return $controller->accessDenied();
    }

    /**
     * Устанавливаем разрешенные actions. array('*') означает разрешение на выполнение
     * всех actions выполняемого контроллера
     * @param $allowedActions
     */
    public function setAllowedActions($allowedActions)
    {
        if(is_array($allowedActions)) {
            $this->_allowedActions = $allowedActions;
        }
    }
}
