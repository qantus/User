<?php

class PermissionController extends FrontendController
{
    public function init()
    {
        $result = Yii::app()->user->can('job.profile.can_update');

        m::d($result);
    }
}
