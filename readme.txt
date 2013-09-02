=== ScrapeAZon ===

Contributors:      jhanbackjr
Plugin Name:       ScrapeAZon
Plugin URI:        http://www.timetides.com/scrapeazon-plugin-wordpress
Tags:              amazon.com,customer,reviews,asin,isbn
Author URI:        http://www.timetides.com
Author:            James R. Hanback, Jr.
Donate link: 	   http://www.timetides.com
Requires at least: 3.1 
Tested up to:      3.6
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
6. Add the [scrapeazon asin="<amazon.com product number>"] shortcode, where <amazon.com product number> is the ASIN or ISBN-10 of the product that contains the reviews you want to display, to the pages or posts you want. For example, if you wanted to display reviews for a product with the ASIN of 012345689, you would issue the shortcode [scrapeazon asin="0123456789"]

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

= Can I scrape reviews from sites other than Amazon? =

This plugin currently only accesses reviews on Amazon.

= Can I scrape reviews from Amazon's international sites? =

Yes, as of version 1.0.7. Configure the shortcode's <code>country</code> parameter with the appropriate two-character country code to change the Amazon site. For example, to retrieve reviews for ISBN 0123456789 from Amazon UK, you could issue the following shortcode:

<code>[scrapeazon asin="0123456789" country="UK"]</code>

The country codes are as follows:

* AT: Austria (uses the German site)
* CA: Canada
* CN: China
* DE: Germany
* ES: Spain
* FR: France
* IN: India
* IT: Italy
* JP: Japan
* UK: United Kingdom
* US: United States (default)

You can also globally configure a country code on the ScrapeAZon Settings page instead of specifying one for each shortcode used on your site. If you globally configure a country code and specify a country code in your shortcode, the country code in the shortcode will take precedence.

= I'm getting weird PHP errors when ScrapeAZon attempts to retrieve a review. What's wrong? =

Depending on your installation, your system might not support cURL, which is the default method of retrieval that ScrapeAZon uses. If your system does not support cURL, try selecting the checkbox on the ScrapeAZon settings page that configures the plugin to use file_get_contents instead.

As of ScrapeAZon version 1.0.8, the plugin will display messages on its Settings page that attempt to help you determine whether your system supports cURL, file_get_contents(), or neither of those features.

= ScrapeAZon isn't displaying *anything* on my page. What's up with that? =

Prior to version 1.0.7, if the AWS server returns an error from your API request, ScrapeAZon displays an HTML comment in your page's source code that includes an error message to assist you in troubleshooting. Some common reasons you might see an error are:

* Your AWS Access Key ID has not been set or is incorrect.
* Your AWS Secret Key has not been set or is incorrect.
* Your Amazon.com Associate ID has not been set or is incorrect.
* You have not allowed enough time for your keys or IDs to propagate at Amazon.com.
* Your AWS Access Key ID and Secret Key are associated with an incorrect Amazon.com Product Advertising API account.
* Versions 1.0.6 and earlier: There are no product reviews associated with the ASIN you used.

As of version 1.0.7, any error messages that are returned by the Amazon Advertising API should be displayed in the shortcode output on your page.

If you know that reviews exist for the product you specified, ensure that the ASIN/ISBN-10 you provided in the shortcode is correct. Also, ensure that you are not viewing a previously cached version of your page that does not contain the shortcode.

It is also possible that you have configured ScrapeAZon to use a Web retrieval method that is not available in your environment. By default, ScrapeAZon attempts to use cURL. If cURL is not enabled in your environment, you can try to use file_get_contents() instead by selecting the checkbox on the Settings page. However, if neither cURL nor file_get_contents() is supported by your PHP installation, you will not be able to use ScrapeAZon.

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

= Can I get rid of that annoying disclaimer at the bottom of the iframe? =

If you know how to edit your theme's CSS, you probably can. However, doing so is not recommended unless you already manually display the disclaimer on your site. As of this writing, Amazon Services requires the disclaimer as part of its Product Advertising API terms.

= Can I at least style the disclaimer differently? =

If you want to use a different font, font size, or otherwise style the disclaimer, add a class named scrape-api to your theme's CSS file and make the changes within that class. For example, if you'd like the disclaimer to be in 9-point Helvetica and 540 pixels wide, you could add the following class to your CSS:

.scrape-api {
   width: 540px;
   font-family: Helvetica;
   font-size: 9pt;
}

== Upgrade Notice ==

= 1.0.9 =
Upgrade to 1.0.9 to enable support for the Amazon API in China and India.

= 1.0.8 =
Upgrade to 1.0.8 to enable basic ScrapeAZon troubleshooting features, such as a sample iframe and PHP environment detection.

= 1.0.7 =
Upgrade to 1.0.7 to enable Amazon international compatibility and to display the Amazon "Be the first to review" page when no reviews are available for a specific product.

= 1.0.6 =
Upgrade to 1.0.6 to enable ScrapeAZon to automatically display the disclaimer required in the Amazon Product Services API Terms.

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

= 1.0.9 =
* Added support for Amazon China and India API calls.

= 1.0.8 =
* Added Settings page cues to better inform new users about their system environments as well as ScrapeAZon's system requirements.
* Added a test iframe on the Settings page so that users can see immediately whether their configurations are working.
* Added a Settings link to ScrapeAZon's settings from the Plugins page.

= 1.0.7 =
* Can now display results from Amazon international sites. Sites are configurable on a per-shortcode basis. Default is the US site.
* Now displays the Amazon "Be the first to review this" page instead of an HTML comment when no reviews exist.

= 1.0.6 =
* Added an Amazon Product Advertising API ToS compliance function that automatically displays Amazon Services LLC's required disclaimer below content retrieved via the API.

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

