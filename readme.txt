=== Small Package Quotes - Worldwide Express Edition ===
Contributors: enituretechnology
Tags: eniture. worldwide express,parcel rates, parcel quotes, shipping estimates
Requires at least: 6.4
Tested up to: 6.6.2
Stable tag: 5.2.16
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html


Real-time small package (parcel) shipping rates from Worldwide Express. Fifteen day free trial.

== Description ==

Worldwide Express (wwex.com ) is a third party logistics company that gives its customers access
to UPS and over 60 LTL freight carriers through a single account relationship. The plugin retrieves 
the UPS rates you negotiated Worldwide Express, takes action on them according to the plugin settings, and displays the 
result as shipping charges in your WooCommerce shopping cart. To establish a Worldwide Express account call 1-800-758-7447.

**Key Features**

* Includes negotiated shipping rates in the shopping cart and on the checkout page.
* Ability to control which UPS small package services to display
* Support for variable products.
* Option to include residential delivery surcharge
* Option to mark up shipping rates by a set dollar amount or by a percentage.

**Requirements**

* WooCommerce 6.4 or newer.
* A Worldwide Express account number.
* Your username and password to Worldwide Express's online shipping system.
* Your Worldwide Express web services authentication key.
* An API key from Eniture Technology.

== Installation ==

**Installation Overview**

Before installing this plugin you should have the following information handy:

* Your Worldwide Express account number.
* Your username and password to Worldwide Express's online shipping system.
* Your Worldwide Express web services authentication key.

If you need assistance obtaining any of the above information, contact your local Worldwide Express office
or call the [Worldwide Express](http://wwex.com) corporate headquarters at 1-800-758-7447.

A more extensive and graphically illustrated set of instructions can be found on the *Documentation* tab at
[eniture.com](https://eniture.com/woocommerce-worldwide-express-small-package-plugin/).

**1. Install and activate the plugin**
In your WordPress dashboard, go to Plugins => Add New. Search for "eniture small package quotes", and click Install Now.
After the installation process completes, click the Activate Plugin link to activate the plugin.

**2. Get an API key from Eniture Technology**
Go to [Eniture Technology](https://eniture.com/woocommerce-worldwide-express-small-package-plugin/) and pick a
subscription package. When you complete the registration process you will receive an email containing your license key and
your login to eniture.com. Save your login information in a safe place. You will need it to access your customer dashboard
where you can manage your API keys and subscriptions. A credit card is not required for the free trial. If you opt for the free
trial you will need to login to your [Eniture Technology](http://eniture.com) dashboard before the trial period expires to purchase
a subscription to the API key. Without a paid subscription, the plugin will stop working once the trial period expires.

**3. Establish the connection**
Go to WooCommerce => Settings => Speedship. Use the *Connection* link to create a connection to your Worldwide Express
account; and the *Setting* link to configure the plugin according to your preferences.

**4. Enable the plugin**
Go to WooCommerce => Settings => Shipping. Click on the link for Speedship and enable the plugin.

== Frequently Asked Questions ==

= How do I get a Worldwide Express account number? =

Worldwide Express is a US national franchise organization. Check your phone book for local
listings or call its corporate office at 1-800-758-7447 and ask how to contact the sales office serving your area.

= Where do I find my Worldwide Express username and password? =

Usernames and passwords to Worldwide Express’s online shipping system are issued by Worldwide Express.
Contact the Worldwide Express office servicing your account to request them. If you don’t have a Worldwide Express account, 
contact the Worldwide Express corporate office at 1-800-758-7447.

= Where do I get my Worldwide Express authentication key? =

You can can request an authentication key by logging into Worldwide Express’s online shipping system ( speedship.wwex.com ) 
and navigating to Services > Web Services. An authentication key will be emailed to you, usually within the hour.

= How do I get an API key for my plugin? =

You must register your installation of the plugin, regardless of whether you are taking advantage of the trial period or 
purchased an API key outright. At the conclusion of the registration process an email will be sent to you that will include 
the API key. You can also login to eniture.com using the username and password you created during the registration process 
and retrieve the API key from the My API keys tab.

= How do I change my plugin API key from the trail version to one of the paid subscriptions? =

Login to eniture.com and navigate to the My API keys tab. There you will be able to manage the licensing of all of your Eniture Technology plugins.

= How do I install the plugin on another website? =

The plugin has a single site API key. To use it on another website you will need to purchase an additional API key. If you want 
to change the website with which the plugin is registered, login to eniture.com and navigate to the My API keys tab. There you will 
be able to change the domain name that is associated with the API key.

= Do I have to purchase a second API key for my staging or development site? =

No. Each API key allows you to identify one domain for your production environment and one domain for your staging or 
development environment. The rate estimates returned in the staging environment will have the word “Sandbox” appended to them.

= Why isn’t the plugin working on my other website? =

If you can successfully test your credentials from the Connection page (WooCommerce > Settings > Speedfreight > Connections) 
then you have one or more of the following licensing issue(s): 1) You are using the API key on more than one domain. 
The API keys are for single sites. You will need to purchase an additional API key. 2) Your trial period has expired. 
3) Your current API key has expired and we have been unable to process your form of payment to renew it. Login to eniture.com and 
go to the My API keys tab to resolve any of these issues.

= Why were the shipment charges I received on the invoice from Worldwide Express different than what was quoted by the plugin? =

Common reasons include one of the shipment parameters (weight, dimensions) is different, or additional services (such as residential 
delivery) were required. Compare the details of the invoice to the shipping settings on the products included in the shipment. 
Consider making changes as needed. Remember that the weight of the packing materials is included in the billable weight for the shipment. 
If you are unable to reconcile the differences call your local Worldwide Express office for assistance.

= Why do I sometimes get a message that a shipping rate estimate couldn’t be provided? =

There are several possibilities:

* UPS has restrictions on a shipment’s maximum weight, length and girth which your shipment may have exceeded.
* There wasn’t enough information about the weight or dimensions for the products in the shopping cart to retrieve a shipping rate estimate.
* The Worldwide Express web service isn’t operational.
* Your Worldwide Express account has been suspended or cancelled.
* Your Eniture Technology API key key for this plugin has expired.

== Screenshots ==

1. Plugin options page
2. Connection settings page
3. Quotes returned to cart

== Changelog ==

= 5.2.16 =
* Update: Fixed conflict with cron jobs 

= 5.2.15 =
* Update: Introduced an error management feature.
* Update: Introduced a liftgate weight restriction rule.
* Update: Introduced backup rates.**
* Fix: Corrected the order of the plugin tabs.
* Fix: Resolved issues with the calculation of live shipping rates in draft orders.

= 5.2.14 =
* Update: Updated connection tab according to wordpress requirements 

= 5.2.13 =
* Update: Compatibility with WordPress version 6.5.1
* Update: Compatibility with PHP version 8.2.0
* Update: Introduced additional option to packaging method when standard boxes is not in use

= 5.2.12 =
* Fix: Fixed a CSS conflict within the order detail widget.

= 5.2.11 =
* Update: Display "Free Shipping" at checkout when handling fee in the quote settings is  -100% .
* Fix: Added validation on the handling fee field 
* Update: Changed text on "Enable Log" description text. 

= 5.2.10 =
* Fix: Markup fee applied to shipping quotes in the following order; 1) Product-specific Mark Up (Product settings);  2) Location-specific Handling Fee / Mark Up (Warehouse settings)) 3) Service-specific Mark Up (Quote settings); and 4) General Handling Fee / Mark Up (Quote settings).

= 5.2.9 =
* Update: Implement a parameter to differentiate between old and new API requests in the logs.

= 5.2.8 =
* Update: Changed required plan from standard to basic for delivery estimate options

= 5.2.7 =
* Update: Compatibility with WooCommerce HPOS(High-Performance Order Storage)

= 5.2.6 =
* Update: updated get Worldwide account number link
* Fix: Fixed a special characters issue in the error message on the connection tab 

= 5.2.5 =
* Update: Add programming to switch the Worldwide account to New/Old API.   

= 5.2.4 =
* Update: Changed endpoint URL for logs. 

= 5.2.3 =
* Update: Added programming to automatically switch Worldwide Express account on new API.

= 5.2.2 =
* Update: Fixed grammatical mistakes in "Ground transit time restrictions" admin settings.

= 5.2.1 =
* Update: Format expected delivery message at front-end new API.

= 5.2.0 =
* Update: Introduced optimizing space utilization. 
* Update: Modified expected delivery message at front-end from “Estimated number of days until delivery” to “Expected delivery by”.
* Fix: nherent Flat Rate value of parent to variations.

= 5.1.1 =
* Update: Added username and password on connection settings for new API. 

= 5.1.0 =
* Update: Introduced shipping rate comparison feature with ShipEngine. 

= 5.0.0 =
* Update: Introduced Worldwide Express new API OAuth process with client ID and client secret.

= 4.14.5 =
* Update:  Text changes in FreightDesk.Online coupon expiry notice.

= 4.14.4 =
* Update:  Introduced a settings on product page to Exempt ground Transit Time restrictions.

= 4.14.3 =
* Update: Added compatibility with "Address Type Disclosure" in Residential address detection 

= 4.14.2 =
* Fix: Fixed PHP warning message.

= 4.14.1 =
* Update: Compatibility with WordPress version 6.1
* Update: Compatibility with WooCommerce version 7.0.1

= 4.14.0 =
* Update: Added origin level markup.
* Update: Added product level markup.

= 4.13.2 =
* Fix: Fixed conflict with Micro-warehouse

= 4.13.1 =
* Update: Introduced connectivity from the plugin to FreightDesk.Online using Company ID

= 4.13.0 =
* Update: Introduced coupon code for freightdesk.online and validate-addresses.com.

= 4.12.4 =
* Update: Compatibility with PHP version 8.1.
* Update: Compatibility with WordPress version 5.9.
* Fix: Fixed support link. 

= 4.12.3 =
* Update: Product level markup support in Rental Products Addon.

= 4.12.2 =
* Adds: In-store pickup and local delivery support in Rental Products Addon.

= 4.12.1 =
* Update: Introduced debug logs tab.
* Fix: In case of multiply shipment, wil show rates if all shipments will return rates. 

= 4.12.0 =
* Update:  Added feature "Shipment days of the week".

= 4.11.6 =
* Fix: Corrected time in transit issue.

= 4.11.5 =
* Fix: Corrected quotes issue.

= 4.11.4 =
* Update: Included compatibility with custom work addon product level markup.

= 4.11.3 =
* Update: Compatibility with PHP version 8.0
* Update: Compatibility with WordPress version 5.8
* Fix: Corrected product page URL in connection settings tab

= 4.11.2 =
* Update: Included compatibility with custom work addon.

= 4.10.2 =
* Update: Added feature "Weight threshold limit".
* Update: Added feature In-store pickup with terminal information.

= 4.8.2 =
* Update: Added weight threshold feature for LTL freight on the quote settings page.
* Update: Added feature to show In-store pickup and local delivery with terminal address.

= 4.6.2 =
* Update: Added images URL for freightdesk.online portal.
* Update: CSV columns updated.
* Update: Virtual product details added in order meta data.
* Update: Shippable addon.
* Update: Compatibility Micro-warehouse addon.

= 4.3.2 =
* Update: Added compatibility with WP 5.7, compatibility with shippable ad-don, compatibility with account number ad-don fields showing on the checkout page.

= 4.3.1 =
* Update: Sync orders to freightdesk.online

= 4.2.4 =
* Fix: Fixed In Store and Local delivery as an default selection.

= 4.2.3 =
* Update: Compatibility with custom work  

= 4.2.2 =
* Update: Added a link in plugin to get updated plans from eniture.com

= 4.2.1 =
* Fix: Fixes in compatibility with shipping solution freightdesk.online

= 4.2.0 =
* Update: Compatibility with shipping solution freightdesk.online

= 4.1.5 =
* Fix: Compatibility with Eniture Technology Freight plugins

= 4.1.4 =
* Update: Compatibility with WordPress 5.4

= 4.1.3 =
* Fix: Fixed selected shipping option reverted to default shipping.

= 4.1.2 =
* Update: Improved cache of Standard Box Sizes and Quotes.

= 4.1.1 =
* Update: Introduced new feature, Cut Off Time & Ship Date Offset.

= 4.1.0 =
* Update: Introduced Features: 1) Multi Packaging. 2) Box Fee. 3) Programming improved for order detail widget

= 4.0.10 =
* Fix: Conflict of order detail widget with WooCommerce.

= 4.0.9 =
* Fix: Fix UI of quote settings tab.

= 4.0.8 =
* Update: Introduce auto correct origin city name for warehouses tab.

= 4.0.7 =
* Fix: Fixed compatibility issue with Eniture Technology LTL Freight Quotes plugins.

= 4.0.6 =
* Update: Removed PHP warnings checkout page.

= 4.0.5 =
* Update: Compatibility with PHP version 7.0 and above

= 4.0.4 =
* Update: Removed PHP warnings checkout page.

= 4.0.3 =
* Update: Removed PHP warnings.

= 4.0.2 =
* Update: Compatibility with WordPress 5.1

= 4.0.1 =
* Fix: Identify one warehouse and multiple drop ship locations in basic plan.

= 4.0.0 =
* Update: Introduced new features and Basic, Standard and Advanced plans.

= 3.2.2 =
* Fix: Fixed issue with opcache.

= 3.2.1 =
* Update: Compatibility with WordPress 5.0

= 3.2.0 =
* Update: Added ability to get quotes on manual orders.

= 3.1.1 =
* Update: Compatibility with WooCommerce 3.4.2 and PHP 7.1.

= 3.1.0 =
* Fix: Added new subscription options for Residential Address Detection plug-in and Standard Box Sizes plug-in

= 3.0.1 =
* Fix: Corrected headings CSS

= 3.0.1 =
* Fix: Corrected user guide link.

= 3.0.0 =
* Update: Introduction of Standard Box Sizes and Residential Address Detection features which are enabled though the installation of plugin add ons.

= 2.1.1 =
* Fix: Fixed issue with new reserved word in PHP 7.1

= 2.1.0 =
* Update: Compatibility with WordPress 4.9

= 2.0.10 =
* Update: Standardization of shipping parameter units of measure for API requests

= 2.0.9 =
* Update: Compatibility with WooCommerce 3.0

= 2.0.8 =
* Fix: Grouping Small and Freight products.

= 2.0.7 =
* Update: Compatibility with worldwide LTL Freight.

= 2.0.6 =
* Update: Move handling fee from shipment level to cart level. 

= 2.0.5 =
* Update: Drop ship locations on variations 

= 2.0.4 =
* Fix:  Fixed jQuery error in warehouse tab

= 2.0.3 =
* Fix:  Set default param in calculate_shipping

= 2.0.2 =
* Update: Compatibility with WooCommerce 2.6

= 2.0.1 =
* Fix:  Fix for query error in previous update

= 2.0 =
* Update:  Introduced multiple warehouses and drop ship locations.

= 1.2.5 =
* Fix: Fixes an issues that arose when the order contained different billing and shipping addresses.

= 1.2.4 =
* Update: All communication sent and received is through Secured Communications.

= 1.2.3 =
* Update: Clarified test connection response for an expired API key.

= 1.2.2 =
* Update: shipping zone added

= 1.2.1 =
* Update: Compatibility with WordPress 4.6

= 1.1.3 =
* Update: Backward compatibility PHP 5.3 

= 1.1.2 =
* Update: WooCommerce 2.6x compatibility added

= 1.1.1 =
* Fix: Detect if WooCommerce was installed.

= 1.1.0 =
* New feature: Permit/Prevent user from checking out if plugin is unable to display shipping rates.
 
= 1.0 =
* Initial release.

== Upgrade Notice ==
