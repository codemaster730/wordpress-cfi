=== Headless SSO Plugin for WP ===
Contributors: miniOrange
Donate link: http://miniorange.com
Tags: Wordpress Headless, Headless React, Headless Single Sign On, SSO, Headless SSO, Gatsby, React, Headless CMS, Flutter, Angular, Node
Requires at least: 3.7
Tested up to: 5.9
Requires PHP: 5.7.2
Stable tag: 1.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Headless SSO Plugin allows SSO login into any frontend application React, Flutter, Angular, Gatsby, etc via WordPress and Identity Providers.

== Description ==

Our <a href="https://plugins.miniorange.com/wordpress-headless-sso" >Headless Single Sign-On (SSO)</a> provides one-click login into any Progressive Web App <a href="https://plugins.miniorange.com/wordpress-headless-sso#frontend">Frameworks</a> via WordPress and IdPs with JWT Authentication.


We provide integration with <a href="https://plugins.miniorange.com/wordpress-single-sign-on-sso">SAML Single Sign-On (SAML SSO)</a>, CAS, Radius and many more which helps in providing SSO into your wordpress sites by the configured Identity Provider that allows user to authenticate and SSO into the <a href="https://plugins.miniorange.com/wordpress-headless-sso#frontend">Progressive Web Application</a> which can be based on any <a href="https://plugins.miniorange.com/wordpress-headless-sso#frontend">Frontend technology</a>.

WordPress Single Sign-On (SSO) with our <a href="https://wordpress.org/plugins/miniorange-saml-20-single-sign-on/">SAML Single Sign On – SSO</a> Login plugin allows SSO with <a href="https://plugins.miniorange.com/saml-single-sign-on-sso-wordpress-using-azure-ad">Azure AD</a>, <a href="https://plugins.miniorange.com/saml-single-sign-on-sso-wordpress-using-azure-b2c">Azure AD B2C</a>, <a href="https://plugins.miniorange.com/keycloak-single-sign-on-wordpress-sso-saml">Keycloak</a>, <a href="https://plugins.miniorange.com/saml-single-sign-on-sso-wordpress-using-adfs">ADFS</a>, <a href="https://plugins.miniorange.com/saml-single-sign-on-sso-wordpress-using-okta">Okta</a>, <a href="https://plugins.miniorange.com/saml-single-sign-on-sso-wordpress-using-shibboleth-2">Shibboleth</a>, <a href="https://plugins.miniorange.com/saml-single-sign-on-sso-wordpress-using-salesforce">Salesforce</a>, GSuite / Google Apps, Office 365, SimpleSAMLphp, OpenAM, Centrify, Ping, RSA, IBM, Oracle, OneLogin, Bitium, WSO2, NetIQ and all SAML 2.0 capable Identity Providers into your WordPress site.

Our SAML /OAuth will handle Response from the Identity provider and create the user in WordPress if it doesn’t exist, create a session of WordPress and with our JWT plugin, convert SAML Response into JWT and post on the Angular application.

== Headless Mode ==

The headless mode allows you to disables the WP frontend experience and allows you to and let's you integrate with any front-end frameworks like Gatsby, Vue, Angular,React, NextJS, Flutter using REST API. This allows you to use WordPress only for managing the content and fetch the content in the frontend environment via APIs.

== Features Include ==

<strong>Single Sign-On Integration</strong>: miniOrange provides Single Sign-On (SSO) integrations with all types of protocols like SAML, OAuth2.0, OpenID connect, CAS, LDAP, WS-Fed, Radius, etc.

<strong>Frontend technology Support</strong>: Easy to Configure : It allows any Frontend technology like React JS, Angular JS, Flutter, Gatsby banking on Headless WordPress to be able to Single Sign-On via the described Identity Providers.

<strong>JWT Signing</strong>: Support for Signing JWT token using algorithms like HS512, RS512, etc.

<strong>Attribute Mapping</strong>: Get user attributes from your Provider and map them to WordPress user attributes like firstname, last name with support for custom attributes

<strong>Link to add IDP Login</strong>: Add a link anywhere on your frontend to allow users to authenticate via their Identity Provider

<strong>Multiple IDP Support</strong>: Configure multiple IDPs to perform Single Sign-On (SSO) into WordPress

<strong>Stateful and Stateless session Support</strong>: Allows maintaining session on only frontend, only WordPress or both.

<strong>Protect Your Complete Site</strong>: Restrict your WordPress site to only logged-in users by redirecting the users to your Identity Provider if logged in session is not found

<strong>Code for Signature Verification</strong>: Code templates for JWT signature verification in all frontend technologies (React JS, Angular JS, Flutter, Gatsby, Vue, etc.)

<strong>Existing User store integrations (SSO)</strong>: Provides real time Headless Single Sign-On(SSO) access for users without having to move users from their existing user stores.

<strong>Unauthorized error message when accessing front-end</strong>: The Headless mode option displays a 403 Unauthorized error message when users access the frontend of your website. If the users want to access the WordPress backend, they can do so by visiting ‘site_url/wp-login.php’

<strong>Redirect non-logged users trying to access the site</strong>: The Headless mode option redirects the non-logged in users to the WordPress login screen so that the access to the frontend is disabled. Logged-in users are redirected to the editor screen for the post which allows sharing a readable link straight to the editor so that they can easily edit the post.

== Why people prefer miniOrange Headless SSO  ==

<strong>Support for Customization</strong>: Customization in the Single Sign-On (SSO) flow based on your customized IDP or additional requirements.

<strong>Cost-effective</strong>: Get access to Headless SSO with saving cost and time avoiding password fatigue with streamlining the user experience and adoption rates more.

<strong>24/7 Active Support</strong>: We provide world-class support and customers vouch for our support, ensuring you best services all the time.

== Use Cases ==
<strong>Login into Gatsby / Gatsby SSO  login</strong>:  The app based on Gatsby’s endpoint needs to be entered in the WordPress Headless SSO Plugin.
The Authentication request from WordPress is redirected to the Identity Provider, and complete authentication process occurs at IdP end. The plugin receives a SAML / OAuth Response from the IdP. A signed JWT response is sent to Gatsby via WordPress Headless SSO plugin.

<strong>Login into AngularJS App / Angular js app login (SSO)</strong>:
We introduced Angular SSO using Azure AD as identity Provider (IDP). We have configured SSO with Azure AD on our WordPress site backend using WordPress SSO Plugin .Websites use WordPress as a headless CMS so we can't use SSO widget or buttons on the frontend to initiate SSO. Integrating Single Sign-On (SSO) functionality for your Headless WordPress environment allows your users to enable SSO login for any Headless decoupled frontend framework like AngularJS clubbed with WordPress backend using a single set of login credentials of your IDP Azure AD. 

<strong>We also offer Headless SSO into various frontend technologies like</strong>:

* Login into Flutter / Flutter App Login (Headless SSO) 

* Login into Vue / Vue App Login (Headless SSO) 

* Login into React / React App Login (Headless SSO)


== Documentation ==

Our Headless SSO plugin for WordPress – Headless SSO plugin for WordPress comes with detailed guidelines with ensured content, expectations to make sure you don’t get lost along the way.
<a href="https://plugins.miniorange.com/wordpress-headless-sso">https://plugins.miniorange.com/wordpress-headless-sso</a>


== Contact Support ==
If you are still nervous about your website security or how the plugin would work for you specifically, customized solutions and Active support are available. You can always Contact Us, or Email us at <a href="mailto:samlsupport@xecurify.com">samlsupport@xecurify.com</a> and we would be happy to help you out.


== Website ==
Check out our website for other plugins <a href="http://miniorange.com/plugins" >http://miniorange.com/plugins</a> or <a href="https://wordpress.org/plugins/search.php?q=miniorange" >click here</a> to see all our listed WordPress plugins.
For more support or info email us at <a href="mailto:info@xecurify.com">info@xecurify.com</a> or <a href="http://miniorange.com/contact" >Contact us</a>.


== Installation ==

= From WordPress.org =
1. Download Headless SSO Plugin.
2. Unzip and upload the `headless-sso` directory to your `/wp-content/plugins/` directory.
3. Activate  Headless SSO from your Plugins page.

= From your WordPress dashboard =
1. Visit `Plugins > Add New`.
2. Search for `Headless SSO Plugin`. Find and Install `Headless SSO Plugin`.
3. Activate the plugin from your Plugins page.

= For any query/problem/request =
Visit Help & FAQ section in the plugin OR email us at <a href="mailto:info@xecurify.com">info@xecurify.com</a> or <a href="http://miniorange.com/contact">Contact us</a>. You can also submit your query from plugin's configuration page.

== Screenshots ==
1. WP Headless SSO | Workflow | Web app frameworks authentication.

== Changelog ==

= 1.4 =
* Added SAML SSO Support 
* Updated plugin's UI

= 1.3 =
* Enable Headless CMS Mode
* Added Support form and feedback form
* WordPress 5.9 Compatibility

= 1.2 =
* Bug fixes
* Readme Updates
* WordPress 5.8 Compatibility

= 1.1 =
* Updated Plugin description

= 1.0 =
* This is the first release

== Upgrade Notice ==

= 1.4 =
* Added SAML SSO Support 
* Updated plugin's UI

= 1.3 =
* Enable Headless CMS Mode
* Added Support form and feedback form
* WordPress 5.9 Compatibility

= 1.2 =
* Bug fixes
* Readme Updates
* WordPress 5.8 Compatibility

= 1.1 =
* Updated Plugin description

= 1.0 =
* This is the first release
