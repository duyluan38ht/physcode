# wpml-support-toolbox

A toolbox plugin for the WPML Support Team. The idea is to create a plugin with some functions that we can use to speed up our day-to-day support/debugging tasks and even automate some of them.

Please note that this plugin is in its early stages and may contain bugs. If you find any, please report it on our git. Everyone is welcome to submit suggestions or even contribute with code.

**Important**
We recommend using them only on test sites or copies, and not on production. If you need to use it in production, please take the following precautions:
- Make sure the client have a complete backup of the website (files and database).
- Make sure you have FTP credentials, so you can disable the plugin if any error occurs.
- Disable and remove the plugin after using it.


# How to use
After activating the plugin, the item "WPML Support Toolbox" will be added to the WordPress menu, which contains the following sub pages:

**WPML Support Toolbox:** This is the main page and the plugin control panel. All features and options are displayed on this page. We'll see more details about each feature in the next item.

**PHP info:** A PHP info page, with all details about the server settings.


# Features
**System Info:** 

A widget that shows server information at a glance, such as PHP version, memory, etc. It also displays the disk size of some WordPress folders (such as wp-content, uploads, plugins and themes) and the WP_DEBUG status. More details can be found on the PHPinfo page.


**Support Plugins Installer:** 

Here we have a manager with a list of plugins we often use. You can download and activate multiple plugins at once by selecting them from the list.This feature can be also found on Appearance > Plugin Installer.

We use the TGM class to create this list (http://tgmpluginactivation.com/), so if a theme uses the same class (many use), the theme recommended plugins will also be displayed in this list.



**Disable/Enable Plugins (troubleshooting):** 

Use this option to quickly disable and enable the no-essential plugins, leaving only WPML/Woocommerce activated. It will save the list of active plugins on the database, so you will be able to re-active all the plugins again with one click. This is a really nice feature to use when we need to run tests with a minimal install, like compatibility issues.



**Generate WPML XML code for a shortcode**

It will generate the WPML XML code to translate a custom shortcode and and its attributes (see https://wpml.org/documentation/support/language-configuration-files/). You need to copy the shortcode and paste on this field. You may need to edit it to add encoding, type, etc on some attributes. It's useful on these tickets with custom elements not displaying on the translation editor. 



**Create missing database tables and run icl_sitepress_activate()**

It will check missing WPML tables and runs the icl_sitepress_activate function to create them (check this errata for more infos: https://wpml.org/errata/missing-_icl_strings-_icl_string_translations-data-tables/).



**(EXPERIMENTAL) Bulk move posts and pages from one language to another**

Go to Posts > All Posts (or Pages > All Pages), select the posts you want then choose a language to move on the bulk options dropdown. - You need to activate this feature on the plugin options.



**(EXPERIMENTAL) Connect with Translations using "Quick Edit" (beta)**

Go to Posts > All Posts (or Pages > All Pages) then click on "quick edit" at the post you want to edit. A option to connect it to a original post will be available. - You need to activate this feature on the plugin options.



# Third-Party Features
We also included some third party plugins that contain simple but useful features for our work. Some of these features are enabled by default, others may need to be enabled in the plugin settings.


**ADMINER (original PHP file - https://www.adminer.org/):** - https://wordpress.org/plugins/ari-adminer/

The original PHP file from https://www.adminer.org. You will need database credentials to use it (you can find on the wp-config.php file).

**ARI ADMINER:** - https://wordpress.org/plugins/ari-adminer/

This is a very useful plugin which enables access and management of the website database. We have used a lot in the past, but it has been removed from the WordPress repository. We have downloaded the latest version and included it in the plugin, with a switch for security reasons. Therefore enable it only temporarily and disable after use.


**Quick Update plugins** - https://wordpress.org/plugins/easy-theme-and-plugin-upgrades/

We included the easy-theme-and-plugin-upgrades plugin to speed-up a manual update process of plugins and themes. You just need to upload the .zip file of the plugin on "Plugins > Add New" and it will be automatically updated. - enabled by default.


**View Debug Log** - https://github.com/norcross/debug-quick-look

You can view the content of the debug.log file using the links at the top admin bar. WP_DEBUG_LOG should be set as TRUE. - enabled by default.


**WP Theme Plugin Download** - https://wordpress.org/plugins/wp-theme-plugin-download/

Download plugins and themes on your site as a .zip file, directly from the admin panel. - You need to activate this feature on the plugin options.



# Changelog
1.0.5:
- Added these plugins to the list of support plugins:
-- https://wordpress.org/plugins/duplicate-post/
-- https://wordpress.org/plugins/debug-bar-rewrite-rules/
-- https://wordpress.org/plugins/jsm-show-post-meta/
-- https://wordpress.org/plugins/quick-edit-template-link/
-- https://wordpress.org/plugins/wp-rollback/

- Added write_log() function to the functions.php file. See this link for more details on how to use it: https://www.elegantthemes.com/blog/tips-tricks/using-the-wordpress-debug-log

1.0.6:
- Added these plugins to the list of support plugins:
-- https://wordpress.org/plugins/debug-bar-localization

- Added adminer.php file from https://www.adminer.org/

1.0.7:
- Added database credentials to the plugin screen