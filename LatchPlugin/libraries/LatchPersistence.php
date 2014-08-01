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
 
$folder = basename(dirname($_SERVER['PHP_SELF']));

if ($folder == "libraries") {
    $storageFolder = "plugins/latch";
} else {
    $storageFolder = "libraries/plugins/latch";
}

$GLOBALS ['latchStorageFile'] = "$storageFolder/latchStorage.xml";
$GLOBALS ["latchStorage"] = simplexml_load_file($GLOBALS ['latchStorageFile']);

function getAccountIdFromDB($userName) {
    $accountId = null;
    $xml = $GLOBALS ["latchStorage"];

    foreach ($xml->PairingInfo as $info) {
        if ($info->Username == $userName) {
            $accountId = $info->AccountId;
            return (string) $accountId;
        }
    }
}

