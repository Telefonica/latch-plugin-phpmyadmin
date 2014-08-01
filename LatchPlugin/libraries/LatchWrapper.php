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
 
require_once 'libraries/plugins/latch/Error.php';
require_once 'libraries/plugins/latch/Latch.php';
require_once 'libraries/plugins/latch/LatchResponse.php';
require_once 'libraries/plugins/latch/latchConfiguration.php';

$storageFolder = "libraries/plugins/latch";
require_once 'LatchPersistence.php';

function pairLatchAccount($pairingToken, $user) {
    $api = getLatchAPIConnection();
    $xml = $GLOBALS ["latchStorage"];
    $pairingInfo = $xml->addChild("PairingInfo");

    if ($api != NULL) {
        $pairingResponseNode = $api->pair($pairingToken);
        if (containsAccountId($pairingResponseNode)) {
            $pairingInfo->addChild('Username', $user);
            $pairingInfo->addChild('AccountId', $pairingResponseNode->getData()->{'accountId'});
            $xml->asXML($GLOBALS ["latchStorageFile"]);
            return true;
        }
        $GLOBALS ['latchError'] = "There has been an error pairing your account.";
    }

    return false;
}

function unpairLatchAccount($user) {

    $api = getLatchAPIConnection();
    $accountId = getAccountIdFromDB($user);
    $xml = $GLOBALS ["latchStorage"];
    $dom = dom_import_simplexml($xml);

    $pairedCount = 0;

    if ($api != NULL && $accountId != null) {
        foreach ($dom->getElementsByTagName('PairingInfo') as $info) {
            if ($info->getElementsByTagName('Username')->item(0)->textContent == $user) {
                $dom->removeChild($info);
            } else if ($info->getElementsByTagName('AccountId')->item(0)->textContent == $accountId) {
                $pairedCount++;
            }
        }

        $xml = simplexml_import_dom($dom);
        $xml->asXML($GLOBALS ["latchStorageFile"]);

        if ($pairedCount == 0) {
            $api->unpair($accountId);
        }
        return true;
    }
    $GLOBALS ['latchError'] = "There has been an error unpairing your account.";
    return false;
}

function containsAccountId($pairingResponse) {
    return $pairingResponse->getData() != NULL && $pairingResponse->getData()->{"accountId"} != NULL;
}

//
function getLatchStatus($accountId) {
    $appId = LatchConfiguration::$applicationId;
    $api = getLatchAPIConnection();
    if ($api != NULL) {
        $statusResponse = $api->status($accountId);
        if (validateResponseStructure($statusResponse, $appId)) {
            $status = $statusResponse->getData()->{"operations"}->{$appId}->{"status"};
            $returnStatus = array(
                'accountBlocked' => ($status == 'off')
            );
            if (property_exists($statusResponse->getData()->{"operations"}->{$appId}, "two_factor")) {
                $returnStatus ['twoFactor'] = $statusResponse->getData()->{"operations"}->{$appId}->{"two_factor"}->{"token"};
            }
            return $returnStatus;
        }
    }
    return array(
        'accountBlocked' => false
    );
}

function validateResponseStructure($response, $applicationId) {
    $data = $response->getData();
    return $data != NULL && property_exists($data, "operations") && property_exists($data->{"operations"}, $applicationId) && $response->getError() == NULL;
}

function getLatchAPIConnection() {
    if (checkLatchConfiguration()) {
        setLatchHost();
        return new Latch(LatchConfiguration::$applicationId, LatchConfiguration::$applicationSecret);
    }
    return NULL;
}

function checkLatchConfiguration() {
    return !empty(LatchConfiguration::$applicationId) && !empty(LatchConfiguration::$applicationSecret);
}

function setLatchHost() {
    if (!empty(LatchConfiguration::$host)) {
        Latch::setHost(LatchConfiguration::$host);
    }
}

?>