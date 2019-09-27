=== Site Reset ===
Contributors: Mahesh901122
Tags: site reset
Tested up to: 5.2
Stable tag: 1.0.1
Requires at least: 4.4

Set active plugins and default theme before site reset.

== Description ==

Set active plugins and default theme before site reset.

Note: 'Reset Site' plugin not support multisite.

= How to use? =
* Goto <code>tools -> Site Reset</code>.
* Select theme which you want to activate after site reset.
* Select plugins which you want to activate after site reset.
* Type <code>reset</code> in input box
* From popup confirmation box select <code>Ok</code>
* Done! Your site will be reset with your selected theme and plugins.

Extend Site Reset plugin on [Github](https://github.com/maheshwaghmare/site-reset/)

== Installation ==

1. Install the <code>Site Reset</code> plugin either via the WordPress plugin directory, or by uploading the files to your server at <code>wp-content/plugins</code>.
2. After activating, you can reset the site from <code>/wp-admin/tools.php?page=site-reset</code>

== Frequently Asked Questions ==

= Multisite Support? =

No! Currently we not support multisite reset. So, Do not use 'Site Reset' on multisite.

== Changelog ==

= 1.0.1 =
* Fix: PHP error from plugin listing page.

= 1.0.0 =
* Initial release.
