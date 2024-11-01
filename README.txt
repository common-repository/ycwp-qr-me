=== YCWP QR Me ===

Contributors: Nicola Mustone
Tags: qr code, image, widget, easing, jquery, css, google code, google charts, steam, twitter, facebook, uri 
Requires at least: 3.3.1
Tested up to: 3.4.2
Stable tag: 1.3.2

YCWP QR Me is a simple plugin that creates and displays QR Code in your blog pages.


== Description ==

YCWP QR Me is a simple plugin that creates and displays QR Code in your blog pages. It provides also a configurable and useful `widget` and `shortcodes`.
You can add your own QR Code in a widget ready sidebar, or in a post using shortcodes.
You can also automatically add your preconfigured QR Code at the end of each post and choose if you want to display it only on single posts pages.


== Suggestions ==

If you have suggestions about how to improve YCWP QR Me, you can [write to me](http://www.nicolamustone.it "Nicola Mustone") so i can bundle it into YCWP QR Me.


== Translators ==

* English: Paolo Mainieri
* Russian: Анна Анфиногенова

If you have created your own language pack, or have an update for an existing one, you can send [gettext PO and MO file](http://codex.wordpress.org/Translating_WordPress "Translating WordPress")
[to me](http://www.nicolamustone.it "Nicola Mustone") so i can bundle it into YCWP QR Me.
Download the latest [POT file](http://plugins.svn.wordpress.org/ycwp-qr-me/trunk/i18n/ycwp-qr-me.pot), or [PO files](http://plugins.svn.wordpress.org/ycwp-qr-me/branches/i18n/) in each language.


== Installation ==

1. Unzip the downloaded zip file.
2. Upload the `ycwp-qr-me` folder into the `wp-content/plugins/` directory of your WordPress site.
3. Activate `YCWP QR Me` from Plugins page


== Configure ==

1. If necessary, configure `YCWP QR Me settings` from `YCWP QR Me option` in Settings menu.
2. If necessary, override custom style.
2.1. Add a `ycwp-qr-me.css` file in your theme directory and put your custom CSS there.
2.2. Add your custom CSS in your `style.css` file.
2.3. Modify the `ycwp-qr-me.css` file located in `wp-content/plugins/ycwp-qr-me/css/`.
3. Configure YCWP QR Me widget settings from Widgets page.


== Shortcodes ==

YCWP QR Me provides many shortcodes to include QR Codes into your posts.
For details, please see `"YCWP QR Me Shortcodes.pdf"`


== Screenshots ==

1. Configuration page.
2. Widget settings.
3. QR Code into posts.


== Upgrade notice ==

= 1.3.2 =
Fixed some errors, updated the code and added a widget parameter for URL encoding.

= 1.3 =
This version adds new features like twitter, facebook and steam. Also fix some bugs.

= 1.2 = 
This version fixes some bug in the plugin and widget classes and provides many new and useful shortcodes (see Changelog for details).

Bugs:

* QRCode::_is_valid_url() uses filter_var() to validate URLs but does not validate some valid URLs. Replace it with a regular expression.


== Changelog ==

= 1.3.2 =
* Added: Encode URL parameter in YCWP QR Me Widget.
* Fixed: removed an extra parameter called in set_content() in YCWP QR Me widget.
* Fixed: post permalink and title retrieveing

= 1.3.1 =

* Fixed: 404 error with jquery-easing1.3.js

= 1.3 =

* Selectable content for QR Codes at the end of the posts between twitter share, facebook share or permalink
* Twitter URIs support
* Steam URIs support
* tinyMCE buttons added
* Improved maintenability of the code using a better organization of the classes
* Deprecated methods in QRCode class v1.2 removed
* Language fix.
* Russian language added. Partial translation from v1.2
* Minor bug fix
* `i18n` directory added for translators in `branches/`

= 1.2 =

* Shortcodes support for email, sms, tel, MeCard, contact, geolocation, android market, github, view-source, wifi
* PHP classes now respects [WordPress coding standards](http://codex.wordpress.org/WordPress_Coding_Standards "Wordpress coding standards")
* QR() method added in QRCode class (class.qrcode.php)
* QR_GET() and QR_POST() methods updated. They will be automatically invoked by QR() method
* QRCode class automatically chooses whether to use a GET or POST request
* Fixed a bug in YCWP_QR_Me_Widget::form()
* Source code documentation removed. phpDocumentor is not PHP5 compatible. Maybe, i will add a new documentation.

= 1.1.1 =

* Now _makeURL() method return a well encoded URL.
* Changelog section inserted again in `"YCWP QR Me.pdf"`
* README.txt updated

= 1.1 = 

* Italian language
* ycwp-qrme.pot and default.po now available for translators
* QR Codes are only displayed on blog pages ( category, archive, single, home, search )
* Minor bug fix
* Changelog section is no longer available in `"YCWP QR Me.pdf"`
* Well documented code
* HTML Documentation in "docs" folder
* README.txt updated

= 1.0 =

* English language
* Shortcode
* Widget
* Plugin
* Custom style