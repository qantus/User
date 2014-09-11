<?php

namespace Modules\User\Commands;

use Mindy\Base\ConsoleCommand;
use Mindy\Helper\Password;
use Modules\User\Models\User;

class UserCommand extends ConsoleCommand
{
    protected function getStdinLine()
    {
        $handle = fopen("php://stdin", "r");
        return str_replace("\n", "", fgets($handle));
    }

    protected function getPasswordPrompt()
    {
        echo "Password: ";
        $password = $this->getStdinLine();

        echo "Confirm password: ";
        $confirmPassword = $this->getStdinLine();

        return [$password, $confirmPassword];
    }

    protected function getPassword()
    {
        list($password, $confirmPassword) = $this->getPasswordPrompt();

        while ($password != $confirmPassword) {
            echo "Incorrect data, please try again:\n";
            list($password, $confirmPassword) = $this->getPasswordPrompt();
        }

        return $password;
    }

    public function actionChangepassword($username)
    {
        $user = User::objects()->get(['username' => $username]);
        if ($user === null) {
            echo "User does not exists\n";
            exit(1);
        }
        $password = $this->getPassword();
        $user->objects()->setPassword($password);
        echo "Password updated\n";
        exit(Password::verifyPassword($password, $user->password) ? 1 : 0);
    }

    public function actionCreatesuperuser($username = null, $email = null)
    {
        if ($username === null) {
            echo "Username: ";
            $username = $this->getStdinLine();
        }

        // TODO check correct email
        if ($email === null) {
            echo "Email: ";
            $email = $this->getStdinLine();
        }

        $has = User::objects()->get(['username' => $username, 'email' => $email]);

        if ($has === null) {
            $password = $this->getPassword();

            $model = User::objects()->createSuperUser($username, $password, $email);

            if (is_array($model)) {
                echo implode("\n", $model);
                exit(1);
            } else {
                echo "Created\n";
            }
            exit(0);
        } else {
            echo "User already exists\n";
            exit(0);
        }
    }
}
