<?php

/*
  Latch phpMyAdmin plugin - Integrates Latch into the phpMyAdmin authentication process.
  Copyright (C) 2013 Eleven Paths

  This library is free software; you can redistribute it and/or
  modify it under the terms of the GNU Lesser General Public
  License as published by the Free Software Foundation; either
  version 2.1 of the License, or (at your option) any later version.

  This library is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  Lesser General Public License for more details.

  You should have received a copy of the GNU Lesser General Public
  License along with this library; if not, write to the Free Software
  Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 */

if (!defined('PHPMYADMIN')) {
    exit;
}

$cfg['Server']['auth_target'] = strtolower($cfg['Server']['auth_target']);
$GLOBALS['auth_type_class'] = "Authentication" . ucfirst($cfg['Server']['auth_target']);

$auth_target_url = 'libraries/plugins/auth/' . $GLOBALS['auth_type_class'] . '.class.php';
if (!file_exists($auth_target_url)) {
    PMA_fatalError(__('Invalid authentication target set in configuration:') . ' ' . $cfg['Server']['auth_target']);
}

include $auth_target_url;

$GLOBALS['auth_instance'] = new $GLOBALS['auth_type_class'](null);

class AuthenticationLatch extends AuthenticationPlugin {

    public function auth() {
        return $GLOBALS['auth_instance']->auth();
    }

    public function authCheck() {
        return $GLOBALS['auth_instance']->authCheck();
    }

    public function authSetUser() {
        global $userlink, $cfg;

        $user = $GLOBALS['PHP_AUTH_USER'];

        $otp_enabled = true;

        $pma_version = explode(".", $cfg['PMA_VERSION']);

        $GLOBALS['auth_instance']->authSetUser();

        if (isset($_SESSION['OTP'])) {
            $server_otp = $_SESSION['OTP'];
            $_SESSION['OTP'] = NULL;

            if (isset($_POST['OTP']) && ctype_alnum($_POST['OTP']) && ($server_otp == $_POST['OTP'])) {
                $_SESSION['logged_in'] = true;
                return true;
            } else {
                $this->authFails();
            }
        }

        //phpMyAdmin versions before 4.1 doesn't support OTP system. 
        if (((integer) $pma_version[1] < 1 && (integer) $pma_version[0] == 4) || (integer) $pma_version[0] < 4) {
            $otp_enabled = false;
        }

        //Switches the authentication method depending on the phpMyAdmin version.
        if ((integer) $pma_version[1] == 1) {
            if ((integer) $pma_version[2] < 7) {
                $userlink = PMA_DBI_connect(
                        $cfg['Server']['user'], $cfg['Server']['password'], false
                );
            } else {
                $userlink = $GLOBALS['dbi']->connect(
                        $cfg['Server']['user'], $cfg['Server']['password'], false
                );
            }
        }

        //The error code not being 0 means the login failed.
        if ($userlink->errno != 0) {
            return true;
        }

        $logged_in = isset($_SESSION['logged_in']) && $_SESSION['logged_in'];

        if (!$logged_in) {
            $accountId = getAccountIdFromDB($user);

            $status = getLatchStatus($accountId);

            if ($status != null) {
                if ($status['accountBlocked']) {
                    $this->authFails();
                    return false;
                } else if (isset($status['twoFactor']) && $otp_enabled) {
                    $_SESSION['OTP'] = $status['twoFactor'];
                    include_once("libraries/plugins/latch/secondFactorForm.php");
                    die();
                }
            }
        }
        $_SESSION['logged_in'] = true;
    }

    public function authFails() {
        unset($_SESSION['logged_in']);

        return $GLOBALS['auth_instance']->authFails();
    }

}
