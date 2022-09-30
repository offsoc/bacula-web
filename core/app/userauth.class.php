<?php

/**
 * Copyright (C) 2010-2022 Davide Franco
 *
 * This file is part of Bacula-Web.
 *
 * Bacula-Web is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * Bacula-Web is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with Bacula-Web. If not, see
 * <https://www.gnu.org/licenses/>.
 */

namespace Core\App;

use App\Tables\UserTable;
use Core\Db\DatabaseFactory;
use Core\Db\Table;
use PDO;
use Exception;

class UserAuth extends Table
{
    protected $tablename = 'Users';
    protected $appDbBackend = BW_ROOT . '/application/assets/protected/application.db';
    protected $dsn;

    public function check()
    {
        $this->appDbBackend = BW_ROOT . '/application/assets/protected/application.db';
        // Throw an exception if PHP SQLite is not installed
        $pdoDrivers = PDO::getAvailableDrivers();
       
        if (! in_array('sqlite', $pdoDrivers)) {
            throw new Exception('PHP SQLite support not found');
        }

        // Check protected assets folder permissions$
        $webUser = array();
        exec('whoami', $webUser);
        $webUser = reset($webUser);
       
        $assetsOwner = posix_getpwuid(fileowner($this->appDbBackend));
      
        if ($webUser != $assetsOwner['name']) {
            throw new Exception('Bad ownership / permissions for protected assets folder (application/assets/protected)');
        }
    }

    public function checkSchema()
    {
        // Check if sqlite db file is writable
        if (!is_writable($this->appDbBackend)) {
            throw new Exception('Application backend database file is not writable, please fix it');
        }

        // Check if Users table exist
        $query = "SELECT name FROM sqlite_master WHERE type='table' AND name='Users';";

        $res = $this->run_query($query);
        $res = $res->fetchAll();

        // Users table do not exist, let's create it
        if (count($res) == 0) {
            # If Users table not found, raise an exception
            throw new Exception('Users authentication database not found, 
              have a look at the chapter <b>Installation / Finalize your setup</b> in the <a href="https://docs.bacula-web.org" target="_blank">documentation</a>');
        }
    }

    public function authUser($username, $password)
    {
        // TODO: FILTER_SANITIZE_STRING is deprecated as of PHP 8.1, replace by htmlspecialchars() instead

        // Sanitize username
        $username = filter_var($username, FILTER_SANITIZE_STRING, array( 'flags' => FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH));
        $username = trim($username, ' ');

        $userTable = new UserTable(DatabaseFactory::getDatabase('sqlite:' . $this->appDbBackend));
        $user = $userTable->findByName($username);

        if($user) {
            if(password_verify($password, $user->getPasswordHash())) {
                return 'yes';
            } else {
                return 'no';
            }
        }else {
            // TODO: Display a flash message like "User not found or password incorrect"
            return 'no';
        }
    }

    public function destroySession()
    {
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        }
        
        // Destroy the session.
        session_destroy();
    }
}
