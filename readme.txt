=== ScrapeAZon ===

Contributors:      jhanbackjr
Plugin Name:       ScrapeAZon
Plugin URI:        http://www.timetides.com/scrapeazon-plugin-wordpress
Tags:              amazon.com,amazon,customer,reviews,asin,isbn
Author URI:        http://www.timetides.com
Author:            James R. Hanback, Jr.
Donate link: 	   http://www.timetides.com
Requires at least: 3.3 
Tested up to:      3.9
Stable tag:        2.0.0
License:           GPL3

Display Amazon.com customer reviews for products you specify in WordPress pages or posts, or as a widget.

== Description ==

The ScrapeAZon plugin displays Amazon.com customer reviews of specific products that you choose. You must be a participant in both the Amazon.com Affiliate Program and the Amazon.com Product Advertising API in order to use this plugin. You can join the Amazon.com Affiliate Program by following the instructions at the program page. You can join the Product Advertising API by following the instructions at the Product Advertising API page.

Features:

* Uses a shortcode to display customer reviews for a specific Amazon product.
* Reviews iframe can be styled manually from the shortcode, via custom CSS, or via a built-in responsive style sheet.
* Includes an "Amazon Reviews" widget that can be used in place of or in addition to the shortcode.
* Supports WordPress localization (i18n)
* Can pull reviews from Amazon International sites, not just Amazon.com (US). 
* Implements the latest version of the Amazon.com API.

== Installation ==

This section describes how to install the plugin and get it working.

1. Obtain an AWS Access Key ID, an AWS Secret Key, and an Amazon.com Affiliate ID, if you don't already have them
2. If you have a previous version of ScrapeAZon installed, deactivate and delete it from the '/wp-content/plugins/' directory
3. Upload the ScrapeAZon folder to the '/wp-content/plugins/' directory
4. Activate ScrapeAZon by using the 'Plugins' menu
5. Click ScrapeAZon from the Wordpress Settings menu and configure the appropriate settings
6. Add the [scrapeazon asin="<amazon.com product number>"] shortcode, where <amazon.com product number> is the ASIN or ISBN-10 of the product that contains the reviews you want to display, to the pages or posts you want. For example, if you wanted to display reviews for a product with the ASIN of 012345689, you would issue the shortcode [scrapeazon asin="0123456789"]

== Frequently Asked Questions ==

= Why would I want to use this plugin? =

ScrapeAZon serves a very specific requirement. It was primarily developed to enable an Amazon vendor to display Amazon.com customer reviews on a product page that is styled independently of other item and product information that is available by using the Amazon.com API.

If you don't want to insert an entire Amazon.com product entry into your site, you can use this plugin to simply incorporate Amazon.com customer reviews onto your existing product page.

= What makes version 2.x different from the 1.x version of ScrapeAZon? =

ScrapeAZon has been rewritten from the ground up to more closely integrate with Wordpress. Additionally, this version of the plugin include several new features, such as:

* An option to style the plugin output for sites that use a responsive design.
* A widget that can be used in place of or in addition to the shortcode.
* An exponential backoff mechanism that attempts to mitigate throttling of high-traffic sites by the Amazon API. 

= What is an ASIN? =

An ASIN is an Amazon.com product identification number. ScrapeAZon uses this identifier to download the correct customer reviews that are associated with a product. An ASIN can be assigned by Amazon.com or, in case of a book, the 10-character version of the ISBN.

= Can I use an identifier other than an ASIN to retrieve reviews? =

Yes, as of ScrapeAZon 2.0.1, you can replace the asin parameter in a shortcode with any of the following parameters:

* isbn
* upc
* ean

The isbn parameter enables you to retrieve reviews for a book or an ebook by using an International Standard Book Number (ISBN). For example, you could use an ISBN retrieve reviews for Stephen King's 11/22/63 by using the following shortcode:

[scrapeazon isbn="9781451627299"]

The upc parameter enables you to retrieve reviews for a product based on that product's Universal Product Code (UPC).

The ean parameter enables you to retrieve reviews for a product based on that product's European Article Number (EAN).

= Why do I need to sign up for an Amazon Affiliate account? =

Amazon's API requires an affiliate account id in order to correctly process requests and download information about item lookups.

= Why do I need to sign up for Amazon Product Advertising API? =

Amazon's API requires an AWS Access Key ID and an AWS Secret Key in order to correctly process requests and download information about item lookups. You cannot obtain this information unless you sign up for an account.

= Can I retrieve reviews from sites other than Amazon by using this plugin? =

No. This plugin currently only accesses reviews for products that are available through the Amazon.com Product Advertising API.

= Can I retrieve reviews from Amazon's international sites? =

Yes. Configure the shortcode's <code>country</code> parameter with the appropriate two-character country code to change the Amazon site. For example, to retrieve reviews for ISBN 0123456789 from Amazon UK, you could issue the following shortcode:

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

= How do I use the ScrapeAZon Widget? =

Similar to most WordPress widgets, first click Appearance > Widgets from the Admin menu. Next, drag the widget named "Amazon Reviews" to the location in which you want it to display. Once you have placed the widget, you must fill in the "ASIN" field with the ASIN of the product that contains the reviews you want to display. You can optionally fill in the Height, Width, and Border fields. You can also retitle the widget if you like. Note that whatever global settings you have configured on the ScrapeAZon Settings pages also apply to the widget. Therefore, if you have selected Responsive mode, the widget will attempt to use a responsive style.

= ScrapeAZon keeps showing me error notices about fopen wrappers. What's wrong? =

Depending on your PHP installation, your system might not support client URL (cURL), which is the default method of retrieval that ScrapeAZon uses. If your system does not support cURL, try selecting the checkbox on the ScrapeAZon settings page that configures the plugin to use file_get_contents instead. However, you should be aware that fopen wrappers can be a security risk to your site.

The plugin will display messages on its Settings page that attempt to help you determine whether your system supports cURL, fopen wrappers, or neither of those features.

= ScrapeAZon isn't displaying *anything* on my page. What's up with that? =

Some common reasons you might see an error or nothing at all are:

* Your AWS Access Key ID has not been set or is incorrect.
* Your AWS Secret Key has not been set or is incorrect.
* Your Amazon.com Associate ID has not been set or is incorrect.
* You have not allowed enough time for your keys or IDs to propagate at Amazon.com.
* Your AWS Access Key ID and Secret Key are associated with an incorrect Amazon.com Product Advertising API account.
* Your site's HTTP client (cURL or fopen wrappers) was not able to connect to the Amazon API.
* Your site has sent too many requests per second to the Amazon Product Advertising API and Amazon has throttled your access.
* Your site caches the pages that display reviews for an extended period of time (longer than 24 hours).
* Your site's server date, time, or time zone are not properly configured.

If you know that reviews exist for the product you specified, ensure that the ASIN/ISBN-10 you provided in the shortcode is correct. Also, ensure that you are not viewing a previously cached version of your page that does not contain the shortcode.

It is also possible that you have configured ScrapeAZon to use a Web retrieval method that is not available in your environment. By default, ScrapeAZon attempts to use cURL. If cURL is not enabled in your environment, you can try to use file_get_contents() instead by selecting the checkbox on the Settings page. However, if neither cURL nor fopen wrappers is supported by your PHP installation, you will not be able to use ScrapeAZon.

= How do I style the iframe? =

There are several ways that you can style the scrapeazon-reviews iframe: by editing your theme's stylesheet, by adding parameters to each shortcode, or by using the plugin's built-in responsive style sheet.

To style the iframe in your theme's stylesheet, add classes named scrapeazon-reviews and scrapeazon-api to your stylesheet, then add the width, height, border, and other parameters you want to style to those classes. For example, copy and paste the following into your stylesheet to make the iframe a 540x540 pixel square with no border:

.scrapeazon-reviews {
   width: 540px;
   height: 540px;
   border: none;
}
.scrapeazon-api {
   width: 540px;
}

To style the iframe by using the shortcode, add width, height, and border as parameters to the shortcode. For example, to accomplish the same formatting as above in shortcode format, use the following shortcode:

[scrapeazon asin="<your asin>" width="540" height="540" border="false"]

To style the iframe by using the built-in responsive style sheet (if your site has a responsive design/theme), select the "Use Responsive Style" checkbox on the ScrapeAZon Settings page.

= I'm using the responsive stylesheet. Why does the content in the iframe scroll horizontally on very small screens? =

Because the iframe content comes from a different source than the iframe itself, the content does not always scale in a responsive way. On very small screens (such as a vertically held iPhone), the iframe itself will scale to the width of the screen in responsive mode. However, the content inside the iframe might need to be scrolled horizontally as well as vertically in order to read it.

= Can I get rid of that annoying disclaimer at the bottom of the iframe? =

If you know how to edit your theme's CSS, you probably can. However, doing so is not recommended unless you already manually display the disclaimer on your site. As of this writing, Amazon Services requires the disclaimer as part of its Product Advertising API terms.

= Can I at least style the disclaimer differently? =

If you want to use a different font, font size, or otherwise style the disclaimer, add a class named scrape-api to your theme's CSS file and make the changes within that class. For example, if you'd like the disclaimer to be in 9-point Helvetica and 540 pixels wide, you could add the following class to your CSS:

.scrapeazon-api {
   width: 540px;
   font-family: Helvetica;
   font-size: 9pt;
}

== Upgrade Notice ==

= 2.0.2 =
Upgrade to 2.0.2 to enable ScrapeAZon to retrieve reviews by using ISBN, UPC, EAN, or ASIN parameters.

= 2.0.0 =
Upgrade to 2.0.0 to enable better Wordpress Settings API integration, better API throttling protection, the possibility of using responsive styles, and the ability to use ScrapeAZon as a widget.

= 1.1.0 =
Upgrade to 1.1.0 to enable automatic detection of HTTPS.

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

1. The Settings page
2. The plugin in action
3. The shortcode in a post

== Changelog ==

= 2.0.2 =
* Added support for retrieving reviews by ISBN to shortcode and widget.
* Added support for retrieving reviews by UPC to shortcode and widget.
* Added support for retrieving reviews by EAN to shortcode and widget.
* Updated FAQ.
* Updated help.

= 2.0.0 =
* Plugin has been completely rewritten to better integrate with the Wordpress Settings API.
* Added support for styling the iframe in a more responsive way.
* Added support for WordPress localization (i18n).
* Added support for HTTP retries and an exponential backoff method of dealing with throttling problems.
* Added support for context-sensitive help on the Settings page.
* Added support for an uninstall process that removes all settings and plugin files.

= 1.1.0 =
* Added support for the automatic detection of HTTPS sites.

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

