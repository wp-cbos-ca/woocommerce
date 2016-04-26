=== BEMO A-Z Index ===
Contributors: bemoore
Donate link:  http://www.bemoore.com/bemo-a-z-index/
Tags: A-Z Index, Index, Alphabetical Index, Letters Filter, A-Z filter, AZ Filter
Requires at least: 3.0.1
Tested up to: 4.5
Stable tag: 4.5
License: GPL2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This is a plugin that provides a customizable A-Z index of the posts displayed on a particular page, category or product listing.

== Description ==
This is a plugin that provides a customizable A-Z index of the posts displayed on a particular page, category or product listing.

I have added a new interface to make it easier to use this plugin. All you have to do is open up your page editor and you will see an A-Z index icon (see screenshots).

This plugin works in 3 situations now:
1. An existing page that has some sort of a list on it already (e.g. a testimonials page, or a WooCommerce product listing)
2. An existing category page (e.g. a blog category)
3. A blank page that you want to list something in WordPress on.

For more detailed info, please visit : http://www.bemoore.com/bemo-a-z-index-pro/

== Installation ==
This plugin is installed in the standard way.

== Frequently Asked Questions ==

= I want to have a page that is a list of artists. Each artists page will have a picture and a description. I also want to have a page that has lyrics. 

What do I need to do to set this up? I can't figure it out. Do I create a page for each artist and then do what to make them show up on a page called artists?

So basically it will be a page called http://xxxxx/artists

On that page will be the A-Z

When you click on the appropriate letter, it shows the artists pictures under that letter.

How do I do this? =

You should put your artists in A-Z Index Posts (left sidebar) and set the featured image for each post.  Try 3 or 4 to start.

Then make a page called Artists (or whatever you want to call it).

Then use the A-Z Index button on the Page editor, click "Display Content" and set the Post Type to azindexcustom.

It should generate something like this:
[azindex content="true" posttype="azindexcustom"]

The first code will display the A-Z for all categories.  The second one will display the actual listings.

In order to get the format you want, you would need to know something about PHP to get the picture appearing etc. 
where you could make a file like listing.php in the templates subdirectory of your theme and refer to that.
You can put it in wp-content/themes/YourTheme/bemo-a-z-index and refer to it in the dialog.

== Screenshots ==
1. Sample of the A-Z Index listing in action
2. Click the A-Z index button to get the interface to easily add A-Z indexes 
3. Generate content on a blank page, or a page with no existing listing.
4. Filter existing list on a page
5. Category filter

== Changelog ==
= 1.1.19 =
* FIX: Bizarrely, just writing target to the console log makes it work ???

= 1.1.18 =
* FIX: Target was STILL not inserting - moved it below Generate Content box. Maybe that will fix it

= 1.1.17 =
* FIX: Target was STILL not inserting - tried renaming the element

= 1.1.16 =
* FIX: Target was STILL not inserting

= 1.1.15 =
* FIX: Target was still not inserting

= 1.1.14 =
* FIX: Target was not inserting

= 1.1.13 =
* FIX: GUI was showing the wrong things

= 1.1.12 =
* FIX: Greying out doesn't work properly - did it backwards.

= 1.1.11 =
* FIX: Greying out doesn't work properly.

= 1.1.10 =
* FIX: Echoing out SQL.

= 1.1.9 =
* FIX: Make the greying out not work for categories as it interferes with everything.

= 1.1.8 =
* FIX: Default target to 2 instead of 1.

= 1.1.7 =
* ADD: When content is generated, now it will show links which don't have any contents greyed out.
Doesn't work for non content generated or categories etc yet.

= 1.1.6 =
* FIX: When you are on a page like H with more than one page, the pagination was added to the links too, causing an error.

= 1.1.5 =
* Properly fixed bug where shortcode was echoed instead of returned

= 1.1.4 =
* The result of the shortcode is now returned by a "return" and not by "echo".
* Didn't pass the "template" variable in the function bemoazindex_load_plugin_template($settings['template']);
(file bemoazindex_content.php). Now fixed. 
* Thanks to absoluteweb for pointing these out.

= 1.1.3 =
* Added prefixes and suffixes to the index

= 1.1.2 =
* Fixed issue that form was showing in background.

= 1.1.1 =
* Fixed issue that when in a category, the A-Z index widget doesn't work properly.

= 1.1.0 =
* Added ordering and direction

= 1.0.9 =
* Changed ALL text

= 1.0.8 =
* Fixed template location

= 1.0.7 =
* Changed content display target to default to 1 instead of 3.
* Changed the content filter to call the where filter.

= 1.0.6 =
* Left called in when content printed

= 1.0.5 =
* Changed names of functions from add_button and register_button to azindex_add_button etc to stop conflicts
* Fixed content filtering

= 1.0.4 =
* Removed debugging message that was causing issues

= 1.0.3 =
* Temp file in root dir causing funny issues

= 1.0.2 =
* Small file update

= 1.0.1 =
* Small FAQ update

= 1.0.0 =
* Major new release with UI and category / tag filtering

= 0.1.6 =
* Fixed a few more bugs.

= 0.1.5 =
* Disabled function that stopped multiple queries being affected as it doesn't work right.

= 0.1.4 =
* Lots more fixes for 404 errors.
* order by working properly.

= 0.1.3 =
* Fixed this not working in category pages.

= 0.1.2 =
* Fixed pagination in template

= 0.1.1 =
* Fixed some more bugs in the listings

= 0.1.0 =
* Fixed some bugs in the listings

= 0.0.9 =
* Added more info to the FAQ.
* Tidied up the description.

= 0.0.8 =
* Tested for 4.0
* Release to have the same as pro except for watermark.

= 0.0.7 =
* Fixed error in the FAQ

= 0.0.6 =
* Added css file for proper styling

= 0.0.5 =
* Bug with protected function fixed
* Added some FAQ items

= 0.0.4 =
* Added a widget

= 0.0.3 =
* Added custom indexes to the original index.
* Added filter types

= 0.0.2 =
* Added post types to the original index.

= 0.0.1 =
* The very first release

== Upgrade Notice ==
