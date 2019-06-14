=== Plugin Name ===
Contributors: denis_golovin, alex_schwabauer
Tags: images, photo proofing, proof, photography, photographer, client, customer, password, password-protected, wedding, gallery, zip, best gallery plugin
Requires at least: 3.6
Tested up to: 4.9.4
Requires PHP: 5.6
Stable tag: 4.1.4 
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provide your clients with links to (optionally password-protected) photo galleries and photo proofing

== Description ==
https://www.youtube.com/watch?v=2zQPJRCPNaY


> ## <strong>Checkout our demos:</strong><br>
 [Admin Demo](https://codeneric.com/products/photography-management/#demo) what you see<br>
 [Client Demo](http://demo.codeneric.com/project/fancy-photos/) what your clients see<br>

> ## Visit the [official photography management website](https://codeneric.com/products/photography-management) for more information!
> 
> <strong>Officially a Top 10 WordPress Plugin for Photographers</strong><br>
> If you're a professional photographer with a robust business, you are going to need Photography Management for convenience and added security.
>
> [<small>read full review</small>](http://www.sitepronews.com/2015/06/30/best-wordpress-plugins-for-photographers/)

Photography Management is a plugin for professional photographers and designers, who need to provide their clients/customers with images
and photographs. It also allows to manage the designer's clients and projects.
Your photographs are only uploaded to your own Wordpress server, where you can easily password protect your galleries, such that only selected people can access it.
Just send your customer a mail with the link to the gallery and the password which you have set previously in Photography Management.
Now only this specific customer can access your gallery, watch it and download a zip file of the gallery.

This plugin is popular amongst wedding photographers, Web designers and artists. Clients can proof your photographs be simply clicking on a star icon.


Main Features:

*   GDPR compliant
*   Upload images/designs/photographs to Wordpress
*   Create projects
*   Create galleries of images in your projects
*   Write a short description
*   Password protect your projects
*   Manage your projects
*   Create clients
*   Manage your clients
*   Let your clients download your images (optional)
*   Photo Proofing
*   Comments for each image
*   Shortcodes
*   Get notified via email of your client's actions
*   Easily setup a login portal



Just install it, click on Clients and start creating projects for your clients. Fill them with our uploads/images/photographs
and password protect them if necessary! Enable the "Favoritable" option to provide a proofing functionality.

<!---
This plugin uses analytics tools to track user behavior such as clicks on the plugin elements.
We use this data to make the plugin more user friendly and define new features.
It DOES NOT track any sensitive data like your client's name, email or similar data.
-->

== Frequently Asked Questions ==
=== Is Photography Management compliant with the GDPR? ===
Yes, make sure that you have WordPress version 4.9.6 or later installed.

== Installation ==

This section describes how to install the plugin and get it working.


1. Click on Plugins/Add New
2. Search for Photography Management, click on 'install'
3. Activate the plugin through the 'Plugins' menu in WordPress

== Screenshots ==

1. Manage your client, the Wordpress style, as easy as creating posts!
2. Easily create optionally downloadable projects with a description, title and your photographs.
3. Password protect the project if you only want to make the project only accessible to a special group.
Otherwise, just leave it public and let all of your website visitors see your great work.
4. Specify the global settings for Photography Management.
5. This is what your client sees after she clicks on the project link and successfully logs in (if necessary).
6. When clicking on an image, it is displayed in a sleek and modern looking image slider.
If your theme already supports this kind of image handling and you want to keep everything consistent
then just disable the image box in the global settings.

== Changelog ==


= 4.1.4 =
* Use protocol relative URLs to handle special TLS cases

= 4.1.3 =
* Fix single image download
* Discourage display of prev/next links for galleries

= 4.1.2 =
* Automatically adjust htaccess when site URL changes
* Only use predefined image sizes
* Improve performance

= 4.1.1 =
* Hotfix back button in gallery
* Hotfix logout button in gallery
* Improve performance

= 4.1.0 =
* GDPR compliance enforced (requires WP version 4.9.6 or later)
* Fixed migration from version 3 to 4 on low-capacity servers
* Fixed download zip feature on sites with non-standard base URLs
* Fixed download interactions csv on sites with non-standard base URLs
* Lazy load gallery in admin front end
* Clean up code
* Improve performance
* Improve robustness
* Fix settings issues
* Remove client on wp-user deletion

= 4.0.6 =
* Fix watermark display issue
* Enhance migration process

= 4.0.5 =
* Back button fix
* Fix project names in interaction page
* Fixed filename and caption features
* Fixed watermark setting
* Improve theme compatibility

= 4.0.4 =
* Include shortcode for client, project and portal
* Fix conflict with Yoast SEO
* Fix conflict with Jetpack

= 4.0.3 =
* Fix: use mime type only if available

= 4.0.2 =
* Deactivate plugin gracefully if PHP is older than 5.6

= 4.0.1 =
* Fixing errors when updating base with old premium plugin installed

= 4.0.0 =
* Polished user interface
* Split client from projects
* **New Feature:** One project to many clients: You can assign the same project to multiple clients
* Comment feature is official now
* **Interactions Page**: On this page, every interaction (proofing, commenting) of your clients with their projects is listed. From this page you can download a list of proofed images and see/answer to comments.
* Enhanced Canned email feature: You can have multiple canned email templates, set in **Settings**
* New settings: **Accent color**: Define a theme color for your pages to integrate better with your theme
* New project settings **Access**: You can make your project publicy available or only accessible for clients. In the latter, you can additionally add a password for a guest login
* Single image download possible
* Major rewrite of most code
* Performance and stability improvements

= 3.6.4 =
* Hotfix the gallery ordering feature

= 3.6.3 =
* Hotfix thumbnails

= 3.6.2 =
* Fixed bug in project overview related to thumbnails

= 3.6.1 =
* Fixed bug related to zip download

= 3.6.0 =
* Better project overview in admin area
* Fix bugs related to the project view
* Split image downloads into multiple zips to dodge server timeouts

= 3.5.0 =
* Moving watermark feature out of beta. Customizing the watermark image, its position and scale is now possible in PHMM > Settings
* Including option to permanently delete all images of a project when deleting the project. Can be toggled at PHMM > Settings
* Sort starred images in the notification email

= 3.4.1 =
* Fix a message string to be internationalized

= 3.4.0 =
* Significantly better theme integration
* Page template selection in PHMM > Settings
* Lightbox Theme Option Light/Dark in PHMM > Settings
* Hotfixes of invalid login attempts

= 3.3.2 =
* More hotfixes

= 3.3.1 =
* Hotfixes

= 3.3.0 =
* Introducing reordering of gallery images: drag&drop, reverse, order by name

= 3.2.6 =
* Do not load wp-load
* Sanitize input data where needed
* Manipulate .htaccess with insert_with_markers to make future releases more robust

= 3.2.5 =
* Removed semi-automatic premium plugin install feature

= 3.2.4 =
* Hide 'No images favorited' text

= 3.2.3 =
* Make PHMM more robust against external code influence. Fixes issues with thumbnail display.

= 3.2.2 =
* Include brazilian portuguese language. Many thanks to [Diego Meneghetti](http://www.estudioteca.com) who made it possible!

= 3.2.1 =
* Fix collisions with themes

= 3.2.0 =
* Send canned email feature [premium]

= 3.1.0 =
* comment beta release

= 3.0.8 =
* minor bug fixes

= 3.0.7 =
* Undefined string offset fixes

= 3.0.6 =
* solve bug from last release

= 3.0.5 =
* remove global jQuery dependency
* hotfixes

= 3.0.4 =
* drastically improved slider performance
* add legacy support for older jQuery versions
* dynamic images per row depending on available width
* add description for projects

= 3.0.3 =
* Fix gallery issue in safari

= 3.0.2 =
* Include Back and Logout buttons
* fix issues with several WP installations

= 3.0.1 =
* Visual hotfixes
* fix issue with portal and shortcodes

= 3.0.0 =
* Complete rework of the project view for clients
* dynamic image fetching for fast page load
* mobile-friendly responsive design
* faster slider
* image name and caption displayed in the slider
* images can be liked within the slider

= 2.7.4 =
* Selective download [premium]

= 2.7.3. =
* Fix password autofill in chrome

= 2.7.2. =
* Paypal subscription fix
* Launched facebook group

= 2.7.1. =
* Critical safari hotfix

= 2.7.0. =
* PHMM is going multilingual! We now support, apart from english, german and french
* Watermarking for images which were uploaded via PHMM
* Validate email

= 2.6.3. =
* Now compatible with Jetpack/Photon
* Zipstream library updated (PHP >= 5.3 required)

= 2.6.2. =
* Safari fix
* New zipping strategy
* Hotfixes

= 2.6.1. =
* IE admin error fix
* Fav counter fix
* Hotfixes

= 2.6.0. =
* New settings option: Define the email addresses, that should receive the favorite notifications (multiple addresses allowed) [premium feature]
* Display filenames to your clients [premium feature]
* Hotfixes

= 2.5.0. =
* Toggle the visbiblity of image captions for your client
* Many hotfixes

= 2.4.2. =
* Portal fix

= 2.4.1. =
* Hotfix tackling older PHP version

= 2.4.0. =
* Launched official photography management website!
* Email notification on client favorite [premium feature]
* Display image file names in gallery [premium feature]
* Set your project preview image individually [premium feature]
* Display the number of favorited images to the client [premium feature]

= 2.3.6. =
* Internet Explorer fix
* Safari fix
* Dequeue foreign CSS and JS

= 2.3.5. =
* changes in username policy

= 2.3.4. =
* Username bug fix number 2

= 2.3.3. =
* Username bug fix
* Premium feature: image captions/title

= 2.3.2. =
* Display zipping progress
* Zipping more robust for many images

= 2.3.1. =
* Multi-projects fix
* Design fixes

= 2.3.0. =
* Login portal page for your clients
* Client and project views can be displayed on any page via shortcode
* Design improvements

= 2.2.3. =
* Fixed add new client bug

= 2.2.2. =
* Fixed zipping process for some servers
* Do not reload whole page on save
* Improve performance

= 2.2.1. =
* Fixed favorite display issues
* Design improvements

= 2.2.0. =
* Additional client info
* Design improvements

= 2.1.2. =
* PHP warning fixed.

= 2.1.1. =
* Important bug fixes.

= 2.1.0. =
* Speed improvements. Bug fixes.

= 2.0.0. =
* New feature: Let your client choose his/her favorite photos. The resulting selection can be viewed in the admin gallery view. Further, the filenames of the favorited photos can be exported as a .txt file
* Strongly improved gallery appearance
* Adding photos will only add those that are not already in the gallery
* The pick order of your image upload is respected in the gallery
* Performance enhancements and bug fixes

= 1.2.2. =
* Too-many-images gallery bug fix

= 1.2.1. =
* Permalink fix for the setting 'standard'

= 1.2.0. =
* Optionally, you can serve only low-res images in the client view page
* Optionally disable right-click behaviour for fullsize images

= 1.1.2. =
* Bug fixes

= 1.1.1. =
* Project overview page
* Prettier URLs

= 1.1.0. =
* Bug fixes
* Better integration into themes
* Added settings page
* Download-Button can be turned on/off and the text can be set
* Make the Image Lightbox feature optional in case the theme already uses a plugin
* Container Width will be adjusted automatically. If it fails, there is a input in the settings page where the exact pixel value can be set
* Support Page added

= 1.0.1. =
* Bug fixes
* Enhanced usability
* Better compatibility

= 1.0.0 =
* Fixed Featured Image issue