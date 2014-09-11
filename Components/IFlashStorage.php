<?php

namespace Modules\User\Components;

interface IFlashStorage
{
    public function add($key, $value);

    public function count();

    public function clear();

    /**
     * @return array
     */
    public function getData();
}
