#LATCH INSTALATION GUIDE FOR phpMyAdmin


##PREREQUISITES
* Any version of phpMyAdmin between 4.0.4 and 4.2.11

* User installing the plugin should have write permissions in php.

* Curl extensions active in PHP (uncomment "extension=php_curl.dll" or "extension=curl.so" in Windows or Linux php.ini respectively. 

* To get the **"Application ID"** and **"Secret"**, (fundamental values for integrating Latch in any application), it’s necessary to register a developer account in [Latch's website](https://latch.elevenpaths.com). On the upper right side, click on **"Developer area"**.


##DOWNLOADING THE phpMyAdmin PLUGIN
* When the account is activated, the user will be able to create applications with Latch and access to developer documentation, including existing SDKs and plugins. The user has to access again to [Developer area](https://latch.elevenpaths.com/www/developerArea), and browse his applications from **"My applications"** section in the side menu.

* When creating an application, two fundamental fields are shown: **"Application ID"** and **"Secret"**, keep these for later use. There are some additional parameters to be chosen, as the application icon (that will be shown in Latch) and whether the application will support OTP (One Time Password) or not. This plugin allows for OTP from version 4.07 and later.

* From the side menu in developers area, the user can access the **"Documentation & SDKs"** section. Inside it, there is a **"SDKs and Plugins"** menu. Links to different SDKs in different programming languages and plugins developed so far, are shown.


##INSTALLING THE PLUGIN IN phpMyAdmin
* Once the administrator has downloaded the plugin, copy its content in phpMyAdmin root folder.**LatchPlugin** directory, **LatchInstall.php** and **LatchUninstall.php** files will be added.

* Execute **LatchInstall.php** directly from the server.

* **Index.php** and **config.inc.php** are modified, so they are backed up to index.php.bak and config.inc.php.bak. Execute **LatchInstall.php** just once. 

* After executing this file you should eliminate from the root directory the **“LatchInstall.php”** file, and the **“LatchPlugin”** folder. 

* Include **"Application Id"** and **"Secret"** in the **LatchConfiguration.php** file located in **libraries/plugins/latch**

* After this, the plugin is ready to be used by the users.


##UNINSTALLING THE PLUGIN IN phpMyAdmin
* You have to rename the uninstall file: **"LatchUninstall.php.back(rename to LatchUninstall.php to enable)"** to **"LatchUninstall.php"**. You must include the uninstallation file **“LatchUninstall.php”** included in the plugin under the root directory, and execute it in the same way as the **“LatchInstall.php”** file.
 
* During unistalling process, the files previously created will be remoded, and the backed up files will be restored.

* Then eliminate such file from the uninstallation.



##USE OF LATCH MODULE FOR THE USERS

**Latch does not affect in any case or in any way the usual operations with an account. It just allows or denies actions over it, acting as an independent extra layer of security that, once removed or without effect, will have no effect over the accounts, which will remain with their original state.**

###Pairing a user in PhpMyAdmin
The user needs the Latch application installed on the phone, and follow these steps:

* **Step 1:** Pairing phpMyAdmin account with Latch. The user has to log on into his phpMyAdmin account. In "control panel" there will be a form where the pairing token generated by Latch should be introduced 

* **Step 2:** From the Latch app on the phone, the user has to generate the token, pressing on **“Add a new service"** at the bottom of the application, and pressing **"Generate new code"** will take the user to a new screen where the pairing code will be displayed.

* **Step 3:** The user has to type the characters generated on the phone into the text box displayed on the web page. Click on **"Pair"** button.

* **Step 4:** Now the user may lock and unlock the account, preventing any unauthorized access.
 

###Unpairing a user in PhpMyAdmin
* The user has to log on into his phpMyAdmin account. In **"control panel"** click on **"Unpair”** button. He will receive a notification indicating that the service has been unpaired.



##RESOURCES
- You can access Latch´s use and installation manuals, together with a list of all available plugins here: [https://latch.elevenpaths.com/www/developers/resources](https://latch.elevenpaths.com/www/developers/resources)

- Further information on de Latch´s API can be found here: [https://latch.elevenpaths.com/www/developers/doc_api](https://latch.elevenpaths.com/www/developers/doc_api)

- For more information about how to use Latch and testing more free features, please refer to the user guide in Spanish and English:
	1. [English version](https://latch.elevenpaths.com/www/public/documents/howToUseLatchNevele_EN.pdf)
	1. [Spanish version](https://latch.elevenpaths.com/www/public/documents/howToUseLatchNevele_ES.pdf)


