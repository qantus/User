<?php

/**
 * All rights reserved.
 *
 * @author Falaleev Maxim
 * @email max@studio107.ru
 * @version 1.0
 * @company Studio107
 * @site http://studio107.ru
 * @date 10/12/14 13:48
 */

namespace Modules\User\Commands;

use Mindy\Console\ConsoleCommand;
use Modules\User\Models\Profile;
use Modules\User\Models\User;

class BitrixCommand extends ConsoleCommand
{
    public function actionMigrate()
    {
        foreach (BitrixUser::objects()->batch(30) as $models) {
            foreach ($models as $model) {
                $user = new User([
                    'username' => $model->LOGIN,
                    'email' => $model->EMAIL,
                    'is_active' => $model->getIsActive(),
                    'password' => $model->PASSWORD,
                    'hash_type' => 'bitrix'
                ]);
                $user->save();

                $profile = new Profile();
            }
        }

        echo 'Memory in use: ' . memory_get_usage() . ' (' . memory_get_usage() / 1024 / 1024 . 'M)' . PHP_EOL;
        echo 'Peak usage: ' . memory_get_peak_usage() . ' (' . memory_get_peak_usage() / 1024 / 1024 . 'M)' . PHP_EOL;
    }
}
