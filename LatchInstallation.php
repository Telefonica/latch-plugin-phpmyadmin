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

if (file_exists("libraries/plugins/latch/LatchConfiguration.php")) {
	echo "<h1>Latch is already installed.</h1>";
	die();
}
if (!isset($_POST['setparam'])){
?>
<p>To get a custom installation set the path to the directory where index.php and config.inc.php files are, then click next. For a basic installation click next.</p>
<form action="LatchInstallation.php" method="POST">
    <label for="pathindex">Path to index.php file: </label>
    <input type="text" name="pathindex" id="pathindex" /><br>
    <label for="pathconfig">Path to config.inc.php file: </label>
    <input type="text" name="pathconfig" id="pathconfig" />
    <input type="hidden" name="setparam" value="ok"/><br>
    <input type="submit" value="Next" />
</form>

<?php
$install = false;
}
else{

    $pathindex = $_POST['pathindex'];
    $pathconfig = $_POST['pathconfig'];
	
	echo "<h1>Latch installation started.</h1>";

	echo "<ol>";
	try{
		if (file_exists($pathindex . "index.php")) {
			copy($pathindex . "index.php", $pathindex . "index.php.bak");
			echo "<li> index.php backed up.</li>";
		}
		if (file_exists($pathconfig . "config.inc.php")) {
			copy($pathconfig . "config.inc.php", $pathconfig . "config.inc.php.bak");
			echo "<li> config.inc.php backed up.</li>";
		} else if (file_exists($pathconfig . "config.sample.inc.php")) {
			copy($pathconfig . "config.sample.inc.php", $pathconfig . "config.inc.php");
			copy($pathconfig . "config.inc.php", $pathconfig . "config.inc.php.bak");
			echo "<li> created config.inc.php from sample and backed up.</li>";
		}
	} catch (Exception $e){
		echo "<b> there has been an error copying index.php or config.inc.php, please set the correct permissions. </b>";
		die();
	}
	try {
		copy("LatchPlugin/libraries/LatchWrapper.php", "libraries/LatchWrapper.php");
		copy("LatchPlugin/libraries/LatchPersistence.php", "libraries/LatchPersistence.php");
		copy("LatchPlugin/libraries/plugins/auth/AuthenticationLatch.class.php", "libraries/plugins/auth/AuthenticationLatch.class.php");

		echo "<li> files copied </li>";

		$src = "LatchPlugin/libraries/plugins/latch";
		$dst = $pathindex . "libraries/plugins/latch";
		$dir = opendir($src);
		@mkdir($dst);
		while (false !== ( $file = readdir($dir))) {
			if (( $file != '.' ) && ( $file != '..' )) {
				if (is_dir($src . '/' . $file)) {
					recurse_copy($src . '/' . $file, $dst . '/' . $file);
				} else {
					copy($src . '/' . $file, $dst . '/' . $file);
				}
			}
		}
		closedir($dir);
	} catch (Exception $e) {
		echo "<b> there has been an error copying Latch files, please Uninstall the plugin and reinstall it. </b>";
		die();
	}

	try {

		$lineToWrite = 0;
		$textToInclude = "include_once 'libraries/plugins/latch/LatchFields.php';";
		$textToFind = 'main_pane_right';
		$content = file($pathindex . "index.php");

		for ($line = 0; $line < count($content); $line++) {

			if (strpos($content[$line], $textToFind) !== false) {

				$return = false;
				$lineToWrite = --$line;
				while (!$return) {
					$prevLine = $content[$lineToWrite];

					if (trim($prevLine) == "") {
						$lineToWrite--;
					} else {
						$content[$lineToWrite] = $textToInclude . "\n" . $content[$lineToWrite];
						file_put_contents($pathindex . "index.php", $content);
						$return = true;
					}
				}

				break;
				echo "<li>index.php adapted. </li>";
			}
		}
	} catch (Exception $e) {
		echo "<b>there has been an error modifying index.php, please uninstall the plugin and reinstall it again. </b>";
		die();
	}

	try {
		$config = file($pathconfig . "config.inc.php");
		$auth_target = "";

		for ($line = 0; $line < count($config); $line++) {
			if (strpos($config[$line], '$cfg[\'Servers\'][$i][\'auth_type\']') !== false) {
				$auth_ex = explode(" ", trim($config[$line]));
				if ($auth_ex != null && count($auth_ex) > 0) {
					$auth_target = $auth_ex[2];

					$config[$line] = '$cfg[\'Servers\'][$i][\'auth_type\'] = \'Latch\';' . "\n";
					$config[$line].= '$cfg[\'Servers\'][$i][\'auth_target\'] = \'cookie\';' . "\n";
					$config[$line].= 'include "libraries/LatchWrapper.php";' . "\n";

					file_put_contents($pathconfig . "config.inc.php", $config);
				}
				echo "<li>config.inc.php adapted.</li>";
				break;
			}
		}
	} catch (Exception $e) {
		echo "<b>there has been an error modifying config.inc.php, please uninstall the plugin and reinstall it again. </b>";
		die();
	}
	echo "</ol>";
	echo "<h2>Latch installation completed, you may now delete this script and the LatchPlugin folder.</h2>";
}
?>