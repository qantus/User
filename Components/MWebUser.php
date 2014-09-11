<?php

namespace Modules\User\Components;

use Mindy\Base\WebUser as BaseWebUser;
use Mindy\Base\Mindy;
use Modules\User\UserModule;

class WebUser extends BaseWebUser
{
    /**
     * @var array for cached permissions
     */
    private $_access = array();


    /**
     * @var \Mindy\Orm\Model logined user model
     */
    private $_model;

    /**
     * Returns the unique identifier for the user (e.g. username).
     * This is the unique identifier that is mainly used for display purpose.
     * @return string the user name. If the user is not logged in, this will be {@link guestName}.
     */
    public function getName()
    {
        if (($name = $this->getState('__name')) !== null)
            return $name;
        else
            return $this->getGuestName();
    }

    /**
     * @TODO: TWIG не обращается к магическому гет методу, поэтому это - костыль. Разобаться
     */
    public function getAvatar()
    {
        $user = $this->getState('__userInfo');

        if ((isset($user['avatar'])) && (($avatar = $user['avatar']) !== null)) {
            return $avatar;
        } else
            return false;
    }

    /**
     * Получаем имя пользователя с учетом перевода
     * @return string
     */
    public function getGuestName()
    {
        return UserModule::t($this->guestName);
    }

    /**
     * Returns a value indicating whether the user is a guest (not authenticated).
     * @return boolean whether the current application user is a guest.
     */
    public function getIsGuest()
    {
        return $this->getState('__id') == 0;
    }

    /**
     * Returns a value that uniquely represents the user.
     * @return mixed the unique identifier for the user. If null, it means the user is a guest.
     */
    public function getId()
    {
        return $this->getState('__id', 0);
    }

    /*
     * for compatible with yii logic only
     */
    public function checkAccess($operation, $params = array(), $allowCaching = true)
    {
        return $this->can($operation, $params = array(), $allowCaching = true);
    }

    /**
     * Performs access check for this user.
     * Overloads the parent method in order to allow superusers access implicitly.
     * @param string $operation the name of the operation that need access check.
     * @param array $params name-value pairs that would be passed to business rules associated
     * with the tasks and roles assigned to the user.
     * @param boolean $allowCaching whether to allow caching the result of access checki.
     * This parameter has been available since version 1.0.5. When this parameter
     * is true (default), if the access check of an operation was performed before,
     * its result will be directly returned when calling this method to check the same operation.
     * If this parameter is false, this method will always call {@link CAuthManager::checkAccess}
     * to obtain the up-to-date access result. Note that this caching is effective
     * only within the same request.
     * @return boolean whether the operations can be performed by this user.
     */
    public function can($operation, $params = array(), $allowCaching = true, $type = null)
    {
        $user = Mindy::app()->user;
        $cache = Mindy::app()->cache;

        // Пользователю суперадминистратор все разрешено по умолчанию
        if ($this->isSuperuser === true)
            return true;

        $cacheId = (is_array($operation) ? implode('|', $operation) : $operation) . $user->getId() . '_' . count($params);

        /**
         * Проверяем данную операцию для пользователя в кеше
         */
        if ($cache->get($cacheId) === false) {
            $access = Mindy::app()->getAuthManager()->can($operation, $this->getId(), $params, $type);
            $cache->set($cacheId, (int)$access, 60 * 60 * 10);
        } else {
            $access = (boolean)$cache->get($cacheId);
        }

        return $this->_access[$cacheId] = $access;
    }

    public function canObject($operation, $modelId, $params = array(), $allowCaching = true, $type = null)
    {
        $user = Mindy::app()->user;
        $cache = Mindy::app()->cache;

        // Пользователю суперадминистратор все разрешено по умолчанию
        if ($this->isSuperuser === true)
            return true;

        $cacheId = (is_array($operation) ? implode('|', $operation) : $operation) . $modelId . $user->getId() . '_' . count($params);

        /**
         * Проверяем данную операцию для пользователя в кеше
         */
        if ($cache->get($cacheId) === false) {
            $access = Mindy::app()->getAuthManager()->canObject($operation, $modelId, $this->getId(), $params);
            $cache->set($cacheId, (int)$access, 60 * 60 * 10);
        } else {
            $access = (boolean)$cache->get($cacheId);
        }

        return $this->_access[$cacheId] = $access;
    }

    /**
     * @param boolean $value whether the user is a superuser.
     */
    public function setIsSuperuser($value)
    {
        $this->setState('__permissions_is_superuser', $value);
    }

    /**
     * @param boolean $value whether the user is a superuser.
     */
    public function setIsStaff($value)
    {
        $this->setState('__permissions_is_staff', $value);
    }

    /**
     * @return boolean whether the user is a superuser.
     */
    public function getIsSuperuser()
    {
        return $this->getState('__permissions_is_superuser', false);
    }

    /**
     * @return boolean whether the user is a superuser.
     */
    public function getIsStaff()
    {
        return $this->getState('__permissions_is_staff', false);
    }

    /**
     * @return bool alias for getIsSuperuser method
     */
    public function getIsAdmin()
    {
        return $this->getIsSuperuser();
    }

    /**
     * @param array $value return url.
     */
    public function setRightsReturnUrl($value)
    {
        $this->setState('__return_url', $value);
    }

    /**
     * Returns the URL that the user should be redirected to
     * after updating an authorization item.
     * @param string $defaultUrl the default return URL in case it was not set previously. If this is null,
     * the application entry URL will be considered as the default return URL.
     * @return string the URL that the user should be redirected to
     * after updating an authorization item.
     */
    public function getRightsReturnUrl($defaultUrl = null)
    {
        if (($returnUrl = $this->getState('__return_url')) !== null)
            $this->returnUrl = null;

        return $returnUrl !== null ? CHtml::normalizeUrl($returnUrl) : CHtml::normalizeUrl($defaultUrl);
    }

    /**
     * Возвращаем аттрибуты пользователя (основная информация и пользовательские поля)
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        if ($this->hasState('__userInfo')) {
            $user = $this->getState('__userInfo', array());
            if (array_key_exists($name, $user)) {
                return $user[$name];
            }
        }

        return parent::__get($name);
    }

    /**
     * Инициализация компонента
     */
    public function init()
    {
        /*
        // @TODO: Для авторизации в 1 приложении со всех поддоменов
        $conf = Mindy::app()->session->cookieParams;
        $this->identityCookie = array(
            'path' => $conf['path'],
            'domain' => $conf['domain'],
        );
        */

        parent::init();

        $this->stateKeyPrefix = md5(Mindy::app()->name);
    }

    /*
     * @TODO: добавить логирование информации о попытке или успешной авторизации с ip
     * @TODO: обратное кодирование ip - long2ip($int_ip);
     */
    protected function beforeLogin($id, $states, $fromCookie)
    {
        /*
         * Обновляем информацию о последнем визите пользователя
         */
        $this->updateLastVisit($id);
        return true;
    }

    /**
     * This method is called after the user is successfully logged in.
     * You may override this method to do some postprocessing (e.g. log the user
     * login IP and time; load the user permission information).
     * @param boolean $fromCookie whether the login is based on cookie.
     * @since 1.1.3
     */
    protected function afterLogin($fromCookie)
    {
        if ($fromCookie) {
            $this->_model = User::model()->findByPk($this->id);
            $userIdentity = new MUserIdentity($this->_model->username, null);
            $this->setState('__userInfo', $userIdentity->setModel($this->_model));
        }

        // Mark the user as a superuser if necessary.
        $this->setIsSuperuser((int)$this->getModel()->is_superuser == User::IS_SUPERUSER);

        // Mark the user as a superuser if necessary.
        $this->setIsStaff((int)$this->getModel()->is_staff == User::IS_STAFF);

        // Сохранение групп пользователя
        $this->updateGroups();
    }

    /**
     * Обновление даты последней авторизации
     * @param $id
     */
    private function updateLastVisit($id)
    {
        User::model()->updateByPk($id, array('last_visit' => time()));
    }

    /**
     * Авторизация пользователя
     * @param IUserIdentity $identity
     * @param int $duration
     * @return bool
     */
    public function login($identity, $duration = 0)
    {
        // Сохранение информации о пользователе и профиле
        $this->setState('__userInfo', $identity->getUser());

        // Авторизация
        return parent::login($identity, $duration);
    }

    /**
     * Возвращаем роут до авторизации
     * @return array
     */
    public function getLoginUrl()
    {
        return Mindy::app()->getModule('user')->loginUrl;
    }

    /**
     * Возвращаем роут до logout
     * @return array
     */
    public function getLogoutUrl()
    {
        return Mindy::app()->getModule('user')->logoutUrl;
    }

    /**
     * Возвращаем роут до просмотра профиля
     * @return array
     */
    public function getProfileUrl()
    {
        return Mindy::app()->getModule('user')->profileUrl;
    }

    /**
     * @return array сохраненных групп пользователя на момент авторизации
     */
    public function getGroups()
    {
        return $this->getState('__groups', array());
    }

    /**
     * @param $groups array групп пользователей для сохранения в момент авторизации
     */
    public function setGroups($groups)
    {
        $this->setState('__groups', $groups);
    }

    /**
     * Обновляем группы пользователя после авторизации и после редактирования пользователя
     */
    public function updateGroups()
    {
        $model = $this->getModel();
        $groups = $model->getGroupArray();
        $this->setGroups($groups);
    }

    public function getDefaultGroup()
    {
        $group = $this->getModel()->getDefaultGroup();
        if ($group === null)
            $group = UserModule::t('No group');
        return $group;
    }

    public function getGroup()
    {
        $groups = $this->getModel()->groups;
        $group = array_shift($groups);
        if ($group === null)
            $group = UserModule::t('No group');
        return $group;
    }

    /**
     * Получаем модель текущего пользователя
     * @return array|CActiveRecord|mixed|null
     */
    public function getModel()
    {
        return User::model()->findByPk($this->getId());
    }

    /**
     * @TODO: не реализовано
     * Обновляем информацию о текущем пользователе после авторизации и после редактирования пользователя
     */
    public function updateProfile()
    {
    }
}
