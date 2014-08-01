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

if (!isset($_POST['setparam'])){
?>
<p>To get a custom uninstallation set the path to the directory where index.php and config.inc.php files are, then click next. For a basic uninstallation click next.</p>
<form action="LatchInstallation.php" method="POST">
    <label for="pathindex">Path to index.php file: </label>
    <input type="text" name="pathindex" id="pathindex" /><br>
    <label for="pathconfig">Path to config.inc.php file: </label>
    <input type="text" name="pathconfig" id="pathconfig" />
    <input type="hidden" name="setparam" value="ok"/><br>
    <input type="submit" value="Next" />
</form>

<?php
}
else{
	
	$pathindex = $_POST['pathindex'];
	$pathconfig = $_POST['pathconfig'];
	echo "<h1>Latch uninstallation started.</h1>";

	if (file_exists("libraries/LatchWrapper.php")) {
		unlink("libraries/LatchWrapper.php");
	}
	if (file_exists("libraries/LatchPersistence.php")) {
		unlink("libraries/LatchPersistence.php");
	}
	if (file_exists("libraries/plugins/auth/AuthenticationLatch.class.php")) {
		unlink("libraries/plugins/auth/AuthenticationLatch.class.php");
	}
	echo "<ol>";

	if(delTree("libraries/plugins/latch")){
		echo "<li>Files deleted.</li>";
	};

	if (file_exists($pathindex . "index.php.bak")) {
		unlink($pathindex . "index.php");
		rename($pathindex . "index.php.bak", $pathindex . "index.php");
		echo "<li>index.php restored.</li>";
	}

	if (file_exists($pathconfig . "config.inc.php.bak")) {
		unlink($pathconfig . "config.inc.php");
		rename($pathconfig . "config.inc.php.bak", $pathconfig . "config.inc.php");
		echo "<li>config.inc.php restored.</li>";
	}

	echo "</ol>";

	echo "<h2>Latch uninstallation completed, you may now delete this script.</h2>";

	function delTree($dir) {
		if (is_dir($dir)) {
			$files = array_diff(scandir($dir), array('.', '..'));
			foreach ($files as $file) {
				if ($file != null) {
					is_dir("$dir/$file") ? delTree("$dir/$file") : unlink("$dir/$file");
				}
			}
			rmdir($dir);
			return true;
		}
	}
}
