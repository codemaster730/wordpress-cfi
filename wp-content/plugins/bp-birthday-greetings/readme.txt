=== BP Birthday Greetings ===
Contributors: prashantvatsh, poojasahgal
Tags: buddypress, birthday, members birthday, birthday notification, members birthday notification, birthday widget, birthday wishes
Requires at least: 4.9.0
Tested up to: 5.3.2
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

BP Birthday Greetings will send birthday greeting notification to the member from community.

== Description ==

BP Birthday Greetings plugin will send a birthday greeting notification to members. You just need to create a DOB field and have to map in the plugin settings, that you can find under options tab of BuddyPress settings.

We have one widget called BuddyPress Birthdays that you can use in sidebars to display the list of member birthdays and can wish them as well using private message functionality of BuddyPress. One shortcode [ps_birthday_list] is also added which can be used to list birthdays as well.

== Installation ==

= From your WordPress dashboard =

1. Visit 'Plugins > Add New'
2. Search for 'BP Birthday Greetings'
3. Activate BP Birthday Greetings from your Plugins page. 

= From WordPress.org =

1. Download BP Birthday Greetings.
2. Upload the 'bp-birthday-greetings' directory to your '/wp-content/plugins/' directory, using your favorite method (ftp, sftp, scp, etc...)
3. Activate BP Birthday Greetings from your Plugins page.

== Frequently Asked Questions ==

= How this plugin works?

After the activation of this plugin just go to the BuddyPress settings(options tab) page and there select the profile field, in 'Select DOB Field' option, that you have created for date of birth.

= Can we change the text of notification? =

Yes! It can be translated easily.

= How to change the message when there is no birthday? =

There is one filter code that you can add in your child theme or any custom plugin:

add_filter('bp_birthday_empty_message', 'ps_change_birthday_message');
function ps_change_birthday_message(){
	_e('Your Changed Message Here', 'bp-birthday-greetings');
}

= Is there any shortcode to list birthdays? =

Yes there is one shortcode [ps_birthday_list].

= How to use new settings? =

Width and height is to define profile picture width and height. Type is to define the type of the image you want to display. 

BuddyPress has two sets of avatar sizes:

Thumb – defaults to 50px square
Full – defaults to 150px square

Note: If you chose type full or thumbnail but defined width and height lesser or higher than the default values then profile pic will be displayed according to the defined size.
Also, if defined width and height is not reflecting then most probably any other plugin or your theme's CSS rules are overriding it.

= How to change the image of the cake? =

Now, we have a filter for this. You can paste the below given code in your child theme's functions file or any site specific plugin's file:

add_filter('bp_birthday_cake_img', 'ps_change_birthday_img');
function ps_change_birthday_img(){
	$img = '<img src="your_image_url" class="your_class_name">';
	return $img;
}


== Screenshots ==

1. Setting to select profile field

2. Birthday Greeting Notification

3. Birthday Widget

4. Listing With Member Avatar

5. New Settings

== Changelog ==

= 1.0.0 =
Initial Release

= 1.0.1 =
Fixed Typo

= 1.0.2 =
Added Birthday Widget

= 1.0.3 =
* Added Member Avatar
* Fixed Translation Issue
* Added Shortcode For Birthday Listing

= 1.0.4 =
* Added Setting For Profile Pic Size Changes
* Added Filter To Change The Image Of The Cake
 
== Upgrade Notice ==
= Initial Release =