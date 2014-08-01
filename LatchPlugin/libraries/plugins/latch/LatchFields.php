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

include "LatchWrapper.php";

if (isset($_POST['operation'])) {
    if (isset($_POST['pairingToken'])) {
        pairLatchAccount($_POST['pairingToken'], $_POST['user']);
    } else if ($_POST['operation'] == "unpair") {
        unpairLatchAccount($_POST['user']);
    }
}

if (LatchConfiguration::$applicationId != "" && $cfg ['Servers'] [1] ['auth_type'] == "Latch") {
    $accountId = getAccountIdFromDB($GLOBALS ['PHP_AUTH_USER']);
    if (ctype_alnum($_REQUEST['token'])) {
        $token = $_REQUEST['token'];
    }
    else{
        $token = $_SESSION[' PMA_token '];
    }
    ?>
    <form action="index.php" method="post">
        <div class="group">
            <h2>Latch Settings</h2>
            <ul>
                <h3 style="color:red"><?php echo $GLOBALS ['latchError'] ?></h3>
                <input type="hidden" name="user" value="<?php echo $GLOBALS ['PHP_AUTH_USER'] ?>"/>
                <input type="hidden" name="token" value="<?php echo htmlentities($token) ?>"/>
                <?php
                if (strlen($accountId) == 0) {
                    ?>
                    <label for="pairingToken">Latch Pairing Token:</label>
                    <input type="text" name="pairingToken" id="pairingToken" />
                    <input type="hidden" name="operation" value="pair"/>
                    <input type="submit" value="Pair" />
                    <?php
                } else {
                    ?>
                    <label for="pairingToken">You are already paired with Latch.</label>
                    <input type="hidden" name="operation" value="unpair"/>
                    <input type="submit" value="Unpair" />
                    <?php
                }
            }
            ?>
        </ul>
    </div>
</form>