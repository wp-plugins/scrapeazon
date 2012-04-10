=== ScrapeAZon ===

Contributors:      jhanbackjr
Plugin Name:       ScrapeAZon
Plugin URI:        http://www.timetides.com/scrapeazon-plugin-wordpress
Tags:              amazon.com,customer,reviews,asin,isbn
Author URI:        http://www.timetides.com
Author:            James R. Hanback, Jr.
Donate link: 	   http://www.timetides.com
Requires at least: 3.1 
Tested up to:      3.3.1
Stable tag:        trunk

Display Amazon.com customer reviews for products you specify on any page or post.

== Description ==

The ScrapeAZon plugin displays Amazon.com customer reviews of specific products that you choose. You must be a participant in both the Amazon.com Affiliate Program and the Amazon.com Product Advertising API in order to use this plugin. You can join the Amazon.com Affiliate Program by following the instructions at the program page. You can join the Product Advertising API by following the instructions at the Product Advertising API page.

Features:

* Allows use of a shortcode to display customer reviews for a specific product in any page or post.
* Returns an iframe that can be styled by adding a class to your theme's CSS file.
* Uses the latest version of the Amazon.com API.

== Installation ==

This section describes how to install the plugin and get it working.

1. Obtain an AWS Access Key ID, an AWS Secret Key, and an Amazon.com Affiliate ID, if you don't already have them
2. If you have a previous version of ScrapeAZon installed, deactivate and delete it from the '/wp-content/plugins/' directory
3. Upload the ScrapeAZon folder to the '/wp-content/plugins/' directory
4. Activate ScrapeAZon by using the 'Plugins' menu
5. Under the Wordpress 'Settings' menu, click ScrapeAZon and configure the appropriate settings
6. Add the [scrapeazon asin="<amazon.com product number>"] shortcode, where <amazon.com product number> is the ASIN or ISBN-10 of the product that contains the reviews you want to display, to the pages or posts you want

== Frequently Asked Questions ==

= Why would I want to use this plugin? =

ScrapeAZon serves a very specific requirement. It was primarily developed to enable an Amazon vendor to display Amazon.com customer reviews on a product page that is styled independently of other item and product information that is available by using the Amazon.com API.

If you don't want to insert an entire Amazon.com product entry into your site, you can use this plugin to simply incorporate Amazon.com customer reviews onto your existing product page.

= What is an ASIN? =

An ASIN is an Amazon.com product identification number. ScrapeAZon uses this identifier to download the correct customer reviews that are associated with a product. An ASIN can be assigned by Amazon.com or, in case of a book, the 10-character version of the ISBN.

= Why do I need to sign up for an Amazon Affiliate account? =

Amazon's API requires an affiliate account id in order to correctly process requests and download information about item lookups.

= Why do I need to sign up for Amazon Product Advertising API? =

Amazon's API requires an AWS Access Key ID and an AWS Secret Key in order to correctly process requests and download information about item lookups. You cannot obtain this information unless you sign up for an account.

= Can I scrape reviews from sites other than Amazon.com? =

This plugin currently only accesses reviews on Amazon.com.

= I'm getting weird PHP errors when ScrapeAZon attempts to retrieve a review. What's wrong? =

Depending on your installation, your system might not support cURL, which is the default method of retrieval that ScrapeAZon uses. If your system does not support cURL, try selecting the checkbox on the ScrapeAZon settings page that configures the plugin to use file_get_contents instead.

= The ScrapeAZon shortcode displays an error message on my page. What's up with that? =

If the AWS server returns an error from your API request, ScrapeAZon displays that error to assist you in troubleshooting. Some common reasons you might see an error are:

* Your AWS Access Key ID has not been set or is incorrect.
* Your AWS Secret Key has not been set or is incorrect.
* Your Amazon.com Associate ID has not been set or is incorrect.
* You have not allowed enough time for your keys or IDs to propagate at Amazon.com.

ScrapeAZon will **not** display an error if the product you include in the shortcode does not contain reviews. In that case, an HTML comment explaining that no reviews are available will be inserted into your page.

= ScrapeAZon isn't displaying *anything* on my page. What's up with that? =

ScrapeAZon will **not** display information on your page if the product you include in the shortcode does not contain reviews. In that case, an HTML comment explaining that no reviews are available will be inserted into your page.

If you know that reviews exist for the product you specified, ensure that the ASIN/ISBN-10 you provided in the shortcode is correct. Also, ensure that you are not viewing a previously cached version of your page that does not contain the shortcode.

= The default iframe is really small. How do I change that? =

There are two ways that you can style the scrapeazon-reviews frame: by editing your theme's stylesheet or by adding parameters to each shortcode.

To style the iframe in your theme's stylesheet, add a class named scrapeazon-reviews to your stylesheet, then add the width, height, border, and other parameters you want to style to that class. For example, copy and paste the following into your stylesheet to make the iframe a 540x540 pixel square with no border:

.scrapeazon-reviews {
   width: 540px;
   height: 540px;
   border: none;
}

To style the iframe by using the shortcode, add width, height, and border as parameters to the shortcode. For example, to accomplish the same formatting as above in shortcode format, use the following shortcode:

[scrapeazon asin="<your asin>" width="540" height="540" border="false"]

The border parameter currently only accepts a value of "false."

== Upgrade Notice ==

= 1.0.5 =
Upgrade to 1.0.5 to fix options page so that it does not conflict with other plugins.

= 1.0.4 =
Upgrade to 1.0.4 to be able to use ScrapeAZon in non-standard Wordpress environments.

= 1.0.3 =
Upgrade to version 1.0.3 to get more granular control over the appearance of the reviews iframe on your pages or posts. Additionally, ScrapeAZon is now compatible only with PHP installations that support cURL.

= 1.0.2 =
Upgrade to version 1.0.2 if you use Amazon API keys that contain special characters, such as the "/" character. (Thanks, Bryan.)

= 1.0.1 =
You should upgrade to version 1.0.1 to reduce calls to the Product Advertising API and increase performance.

= 1.0 =
This is the first version of the plugin.

== Screenshots ==

1. The settings page
2. A look at the plugin in action

== Changelog ==

= 1.0.5 =
* Modified options page so that it uses a unique name that will not conflict with other plugins.

= 1.0.4 =
* Modified include path so that it works with uniquely named content directories

= 1.0.3 =
* Added shortcode parameters to style the iframe
* Added information to the FAQ about styling the iframe
* Added option that enables you to retrieve reviews via either cURL or file_get_contents
* Strengthened input validation

= 1.0.2 =
* Fixed secret key character validation that prevented some API keys from working

= 1.0.1 =
* Reduced calls to the AWS Product Advertising API to increase performance
* Fixed typo in the FAQ
* Fixed typo in the tags

= 1.0 =
* Initial release

