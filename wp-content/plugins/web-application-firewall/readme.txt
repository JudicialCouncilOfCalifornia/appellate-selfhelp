=== Web Application Firewall - website security ===

Contributors: cyberlord92, miniOrangeSecurity
Tags: firewall, IP blocking, IP whitelisting, firewall security, country blocking, htaccess, Crawler, Rate limiting, Bot Net Protection, real-time IP blocking, Restrict access, SQL injection, local file inclusion, remote file inclusion, cross-site scripting, Remote code execution
Donate link: https://miniorange.com
Requires at least: 4.6
Tested up to: 6.1
Requires PHP: 5.3.0
Stable tag: 2.1.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Web Application Firewall allows you to easily and secure firewall protection to your site via htaccess file. Although it can be installed and configured just like a plugin, it is a stand-alone firewall that stands in front of WordPress.

== Privacy Policy ==

This firewall security plugin may collect IP addresses for security reasons such as mitigating brute force login threats and malicious activity, the collected information is stored on your server. No information is transmitted to third parties or remote server locations via firewall security.


== Description ==

A firewall security is a network security application that monitors and filters incoming and outgoing network traffic (IP Address). A firewall security, is a barrier that sits between a private internal network and the public Internet.

= FEATURE-RICH, EASY TO USE, STABLE, SECURE AND WELL SUPPORTED WORDPRESS FIREWALL SECURITY PLUGIN =

Firewall reduces security risk by checking for vulnerabilities, and by implementing and enforcing the latest recommended WordPress security practices and techniques.
Our security and firewall rules are categorized into "essential(basic)" and "advanced". This way you can apply the firewall rules progressively without breaking your site's functionality.
Easy way to block country and to block IP. Using firewall security plugin admin can protect the website from unwanted traffic, and bad bots. The firewall protects your website from different kinds of attacks. And provides a security layer on your website.
*GDPR Compliant*

What does a Web Application Firewall (WAF) exactly do?

A WAF/firewall keeps a track of the HTTP traffic that comes to your website/web application. Basically, it monitors all the requests that are coming to your web application/website. If the WAF feels that the incoming requests are suspicious i.e. if the incoming request can harm your website (eg. the request may contain some code that can make some changes to your database or an unauthorized person/hacker would be able to gain access to your web application) WAF blocks those requests and prevents your website from unwanted attacks. Basically WAF filters and blocks suspicious or unwanted HTTP traffic to and from a web application.

The following is a list of the security and firewall features provided by miniorange firewall security plugin:


= User Login Security =

* The login locked out  protects against "Brute Force Login Attacks." Users with a specific IP address or range will be locked out of the system for a predetermined period of time-based on the configuration settings, and you can also opt to be notified via email whenever someone is locked out due to too many login attempts.
* As the administrator, you can view a list of all locked out users in an easily readable and navigable table, as well as unblock individual or bulk IP addresses with the click of a button.
* Monitor/view failed login attempts, which include the user's IP address, User ID/Username, and the date and time of the failed login attempt.
* Keep track of the username, IP address, login date/time, and logout date/time for all user accounts on your system to monitor/view their account activity.
* Allows you to add one or more IP addresses to a whitelist.
* Add Google reCaptcha to your WP Login form and forget password form.

= File System Security =

* Identify files or folders with insecure permission settings and, change the permissions to the recommended secure values.
* Protect your PHP code by disabling file editing and prevent people from accessing the readme.html, license.txt, and wp-config.php files of your WordPress site.

= htaccess and wp-config.php File Backup and Restore =

* Easily backup your original .htaccess and wp-config.php files in case you need to use them to restore broken functionality and also you can modify the contents of the current htaccess or wp-config.php file.

= Blacklist Functionality =
* Users can be blocked by specifying their user agents or IP addresses  by using a wildcard to specify IP ranges.

= Firewall Functionality =

* This plugin makes it simple to add a lot of firewall protection to your site via the htaccess file. Your web server processes a htaccess file before loading any other code on your site.
* Access control facility.
* Instantly activate a selection of firewall settings ranging from basic, intermediate, and advanced.
* Deny bad or malicious query strings.
* Protect against Cross-Site Scripting and more.

= Brute force login attack prevention =
* This firewall feature will prevent all login attempts from humans and bots.  It is possible to hide the admin login page. Change the URL of your WordPress login page so that bots and hackers cannot access your actual WordPress login URL. You can use this feature to change the default login page (wp-login.php) to something you specify.

= Security Scanner =
* If any files in your WordPress system have changed, the file change detection scanner will notify you. You can then investigate to see if the change was legitimate or if malicious code was injected.

= Comment SPAM Security =
* Monitor the most active IP addresses which persistently produce the most SPAM comments using google reCaptcha and instantly block them with the click of a button.

= Regular updates and additions of new security features =
* WordPress security is a living thing that changes over time. Our Firewall Security will regularly update with new security features, so you can be confident that your site will be up to the mark of security protection techniques.

<h4>FREE Plugin Feature</h4>
* **Plugin Level Waf:** IPs blocked by admin will be blocked on WordPress site load. It is less secure than htaccess level WAF.
* **Rate Limiting:** It helps to prevent DoS attacks on your site. You can set hit/min for each IP.
* ** SQL Attack Detection and Blocking:** Cyber attacks and suspicious activities will be detected and access to the site for that IP will be blocked.
= htaccess and wp-config.php File Backup and Restore =
* Easily backup your original .htaccess and wp-config.php files in case you will need to use them to restore broken functionality.
* Modify the contents of the currently active .htaccess or wp-config.php files from the admin dashboard with only a few clicks
* **Email Notification:** Admin can get a notification on email for any suspicious activity detected on site.
* **Report:** Admin can see the login failed/success, attacks report in the report.
* **reCaptcha Protection** Google services are used to provide ReCaptcha protection.


<h4>Premium Plugin Feature</h4>
* **htaccess Level WAF:** IPs blocked by admin will be blocked on the server only. These IPs won't able to access the site.
* **Real-Time IP Blocking:** This firewall feature protects your site from those IPs which are marked as spam by miniOrange WAF.
* **Rate Limiting for Crawler:** Web crawler crawls your Website to increase your ranking in the search engine. But sometimes they can make so many requests to the server that the service can get damaged. By enabling this feature you can provide a limit at which a crawler can visit your site.
* **Advance Blocking:** You can block particular country, IP range, Single IP, browser, and HTTP referrers from gaining access to your site.
* **Fake Web Crawler Protection:** Web Crawlers are used for scanning the Website and indexing it. Google, Bing, etc. are the top crawlers that increase your site's indexing in the search engine. There are several fake crawlers that can damage your site.
* **Whitelist Crawler:** You can whitelist the top crawler which increases the indexing of your website in the search engine. By enabling this feature the whitelisted crawler will not get throttled/blocked by rate-limiting.
* **BotNet Protection:** BotNet is a network of robots or an army of robots. The BotNet is used for Distributed denial of service attacks. The attacker sends too many requests from multiple IPs to a service so that the legitimate traffic can not get the service.
* **Remote File Inclusion Protection:** It protects from adding files from a remote server to your server.
* **Remote Code Execution Protection:** It Protects from executing malicious commands on your server.
* **Bot Detection** detect bots with malicious intent and stop them from accessing and affecting your site.
* **Live Monitoring and Auditing** Tracking activity all the requests realtime can help you check activities on your sites on important events

= Plugin Support =

* If you have a question or problem with the Web Application Firewall Security plugin, post it on the support forum and we will help you.
Customized solutions and Active support are available. Email us at 2fasupport@xecurify.com or call us at +1 9786589387.

Check the following page for F.A.Q (see the FAQ section):
https://security.miniorange.com/

== Installation ==

= From your WordPress dashboard =

1. Navigate to `Plugins > Add New` from your WP Admin dashboard.

2. Search for `Web Application Firewall`.

3. Install `Web Application Firewall` and Activate the plugin.

= From WordPress.org =

1. Search for `Web Application Firewall` and download it.

2. Unzip and upload the `Web Application Firewall` directory to your `/wp-content/plugins/` directory.

3. Activate Web Application Firewall from the Plugins tab of your admin dashboard.

== Frequently Asked Questions ==

= Once Activated =

1. Select miniOrange Web Application Firewall from the left menu and follow the instructions.

2. You can configure Web Application Firewall settings.


== Screenshots ==

1. Web Application Firewall Dashboard

2. IP Blocking

3. Tracking

4. Email alert


== Changelog ==

= 2.1.1 =

Added Feedback form

= 2.1.0 =

Feature guides added. UI changes.

= 2.0.0 =

WordPress 6.0 version compatibility , readme update and some bug fixes

= 1.1.1 =

WordPress 5.9.3 version compatibility , readme update and some bug fixes

= 1.1.0 =

WordPress 5.9 version compatibility update, typo corrections

= 1.0.4 =

WordPress 5.8 version compatibility update, typo corrections

= 1.0.3 =

WordPress 5.7 version compatibility update, typo corrections

= 1.0.2 =

WordPress 5.6 version compatibility update

= 1.0.1 =

WordPress 5.5 version compatibility update

= 1.0.0 =

The first version of WordPress Web Application Firewall Plugin with basic Wordpress network security.

==  Upgrade Notice  ==
None
