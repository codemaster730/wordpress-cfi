=== Member Swipe for BuddyPress ===
Contributors: themosaurus
Tags: buddypress, bp, swipe, swap, member, community, tinder, dating
Requires at least: 4.6
Tested up to: 5.7
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

Swipe through your BuddyPress members with a flick of your finger!

== Description ==
Swipe through your BuddyPress members with a flick of your finger on your touch devices, or with a click of your mouse on desktop.

All you need to do is to add the dedicated Gutenberg block "Swipe Directory" to easily create a swipe directory on any of your pages!
The block also includes the following options:
    * Filter members with specific profile fields
    * Choose the order in which the members are displayed

This plugin requires BuddyPress.

Plugin Icons made by [Freepik](http://www.freepik.com/) from [Flaticon](www.flaticon.com)

== Installation ==
= AUTOMATIC INSTALLATION =
Automatic installation is the easiest option — WordPress will handles the file transfer, and you won’t need to leave your web browser. To do an automatic install of Member Swipe for BuddyPress, log in to your WordPress dashboard, navigate to the Plugins menu, and click “Add New.”

In the search field type “Member Swipe for BuddyPress,” then click “Search Plugins.” Once you’ve found us, you can view details about it such as the point release, rating, and description. Most importantly of course, you can install it by clicking “Install Now,” and WordPress will take it from there.

= MANUAL INSTALLATION =
Manual installation method requires downloading the Member Swipe for BuddyPress plugin and uploading it to your web server via your favorite FTP application. The WordPress codex contains [instructions on how to do this here](https://codex.wordpress.org/Managing_Plugins#Manual_Plugin_Installation).

= UPDATING =
Automatic updates should work smoothly, but we still recommend you back up your site.

== Frequently Asked Questions ==
= How do I add a member swipe directory in my page ? =
+ Open the page where you want to add your member swipe
+ Search for a block named "Swipe Directory" and add that block to your page
+ Save your page
If you are using the Classic Editor, you can also use the [bms_directory] shortcode

== Screenshots ==
1. The "Swipe Directory" block in the Gutenberg editor
2. Member swipe in a WordPress page
3. The swipe can be inserted in any page or post

== Changelog ==
= 1.1.6 =
* Fix compatibility with Elementor's carousel
* Add compatibility with BP Maps for Members
= 1.1.5 =
* Fix pagination on mobile.
* Fix random order in Member Swipe block to avoid repeating users in the query.
= 1.1.4 =
* Fix swipe arrows position on RTL websites
= 1.1.3 =
* Fix unprecise profile field filters in certain cases
= 1.1.2 =
* Add a minimum swipe distance required before actually triggering the swipe to avoid accidental swipes.
= 1.1.1 =
* Minor fix in loop template
= 1.1 =
* Added new options in the Swipe Directory block :
    - Filter members with specific profile fields
    - Choose in which order members are displayed
* Changed default member order to random
* Changed swipe handler to swiper.js for a smoother swipe
= 1.0 =
* Initial release
