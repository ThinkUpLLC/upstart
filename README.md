Upstart
=======

ThinkUp.com's member and installation management system.

Installation
------------

Upstart doesn't have ThinkUp's easy web-based installer, but its innards are very similar. Here's how to get it installed locally.

1. First, clone this repository:
```
$ git clone git@github.com:ThinkUpLLC/upstart.git
```

2. Get the submodules. Upstart uses one, called Isosceles, a simple MVC framework extracted from ThinkUp. After you run this command, make sure there are files in ```webapp/extlibs/isosceles/```.
```
$ git submodule init
$ git submodule update
```

3. Build the database tables. In your MySQL admin of choice, create an Upstart database and build the required tables using this file: ```sql/build_db.sql```

4. Symlink Upstart's webapp directory to a web-accessible folder on your local web server. On ThinkUp.com, that directory is currently ```join```. So if you symlink webapp to join on localhost, you'd load ```http://localhost/join/``` in your browser.

5. Create your config file. This file lives in the Isosceles directory. Copy over the sample using this command:
```$ cp webapp/extlibs/isosceles/libs/config.sample.inc.php webapp/extlibs/isosceles/libs/config.inc.php```

6. Fill in the config.inc.php file basic values from your setup, particularly site_root_path and db_name, db_user, and db_password. Upstarts datadir_path must be writable by the web server (for caching Smarty output).

7. Add Upstart's custom config values, listed below. Get any API keys from Gina via LastPass.


Custom Configuration
--------------------
Set the following required configuration variables in webapp/extlibs/isosceles/libs/config.inc.php:

```
// Twitter
$ISOSCELES_CFG['oauth_consumer_key']
$ISOSCELES_CFG['oauth_consumer_secret']

//Facebook
$ISOSCELES_CFG['facebook_app_id']
$ISOSCELES_CFG['facebook_api_secret']
$ISOSCELES_CFG['facebook_max_crawl_time']   = 5;

//Mailchimp
$ISOSCELES_CFG['mailchimp_api']
$ISOSCELES_CFG['mailchimp_list_id']

//Expand URLS
$ISOSCELES_CFG['expandurls_links_to_expand_per_crawl']         = 50;

//Amazon Payments
//This next line ensures you're testing using the Amazon Payments sandbox, which simulates interactions but doesn't actually move money
$ISOSCELES_CFG['amazon_sandbox']            = true;
$ISOSCELES_CFG['AWS_ACCESS_KEY_ID']
$ISOSCELES_CFG['AWS_SECRET_ACCESS_KEY']
//$ISOSCELES_CFG['amazon_payment_auth_validity_start']     = time();

//Mandrill
$ISOSCELES_CFG['mandrill_api_key']
$ISOSCELES_CFG['mandrill_notifications_template']			= 'ThinkUp LLC Member Insight Notifications';

//User installations - Don't worry about this unless you're working with installation
$ISOSCELES_CFG['app_source_path']
$ISOSCELES_CFG['master_app_source_path']
$ISOSCELES_CFG['chameleon_app_source_path']
$ISOSCELES_CFG['data_path']
$ISOSCELES_CFG['admin_email']
$ISOSCELES_CFG['admin_password']
$ISOSCELES_CFG['user_password']
$ISOSCELES_CFG['user_installation_url']     = "http://{user}.upstart.com/sandbox/";
$ISOSCELES_CFG['user_installation_db_prefix'] = "thinkupstart_";


//User installations database configuration - Needed for user installations and for testing signup checkout

$ISOSCELES_CFG['tu_db_host']
$ISOSCELES_CFG['tu_db_user']
$ISOSCELES_CFG['tu_db_password']
$ISOSCELES_CFG['tu_db_socket']
$ISOSCELES_CFG['tu_db_port']
$ISOSCELES_CFG['tu_table_prefix']


//Dispatch - Don't worry about this unless you're working with installation
$ISOSCELES_CFG['dispatch_endpoint'] = 'https://www.thinkup.com/dispatch/';
$ISOSCELES_CFG['dispatch_auth_token']
$ISOSCELES_CFG['dispatch_socket']
$ISOSCELES_CFG['dispatch_timezone']
$ISOSCELES_CFG['dispatch_http_username']
$ISOSCELES_CFG['dispatch_http_passwd']
```


Using Upstart
-------------

There are two interfaces for Upstart: a user-facing area, and the internal admin area.

User-facing signup is what you see exposed on ThinkUp.com. It's the user flow which brings you through Amazon Payments, Twitter/Facebook authorization, and ThinkUp.com account creation. UPDATE: There's now a work-in-progress user area in the /user/ folder.

The internal admin area is located in the admin directory and allows us to browse, search and manage members who have signed up. If your Upstart installation is at ```http://localhost/join/``` then you can see the admin interface at ```http://localhost/join/admin/```. There will be zero data in admin when you first install Upstart. To get test data in there, sign up via the user flow a few times using various email addresses. If you're using Gmail, you can add a +test to your email address to get multiple addresses, that is, ginatrapani+test1@gmail.com and ginatrapani+test2@gmail.com all go to ginatrapani@gmail.com but count as different accounts in Upstart, which enforces email address uniqueness. (With the amazon_sandbox config value set to true, you won't be actually authorizing ThinkUp to charge you for these authorizations. No actual money moves in the sandbox.)

If you see a red error that reads something like "Dispatch status: 15 running worker(s) found - NOT OK" - ignore it. Dispatch is our crawler queue system and it's currently not running while we're in development.


Using/Installing Grunt
----------------------

[Grunt](http://gruntjs.com/) is a task-runner used to automate development, er, tasks. We use it for three things (right now):

* Convert [LESS](http://lesscss.org) files to CSS
* Convert [Coffeescript](http://coffeescript.org/) files to Javascript
* Inline styles in emails using [Premailer](https://github.com/premailer/premailer/)

### Dependencies

In order to compile, you need to have NodeJS and Ruby installed on your machine. This has been tested on a Mac, but
should work on any machine that supports Ruby and Node. We'll cover installation below, but these are the required
packages.

#### NodeJS modules

* grunt-cli
* grunt
* grunt-contrib-watch
* grunt-contrib-less
* grunt-contrib-coffee
* grunt-premailer

#### Ruby gems

* hpricot
* premailer

### Installation

1.  If you haven't done so already, install [NodeJS](http://nodejs.org/) via homebrew [Homebrew](http://brew.sh/)
    (brew install node) or [their installer](http://nodejs.org/download/).

2.  Assuming you have ruby on your system, install the two required gems with `gem install hpricot premailer`.

4.  Navigate to the root directory of your Upstart repo and run `npm install`. This should install all of the NodeJS
    modules we use in ThinkUp (the command installs everything in `Upstart/package.json`).

5.  In order to run the inliner, you'll need the grunt command line interface, which must be installed gloablly.
    Install that with `npm install -g grunt-cli`.

### Usage

If you want to run every command, just navigate to the root directory of the repository and run `grunt` from your
command line. It’s more likely you’ll want to do something specific, so refer to the guides below.


## Rendering Email Styles

HTML rendering in email clients is pretty poor, but it does pretty well if you use inline styles. Since it's a pain
to write your CSS rules inline, we don’t; we use software.

The precompiled HTML email templates live at `extras/dev/precompiled-emails/*.html`
and are written to `webapp/libs/view/_email.*.tpl`.

*If you haven't already, install Grunt (see above).*

1. Make your changes to the precompiled template (location is listed above).
2. Navigate to the root directory of your repository from the command line and run `grunt email`.

There are also two fancier options.

* Run `grunt email_dev` and it will create an *.html file in your webapp directory for testing in addition to rendering
  the Smarty template.
* Run `grunt watch:email` or `grunt watch:email_dev` and it will automatically compile every time you save.

Now, your CSS styles should be precompiled. Good work!


## Compiling LESS and Coffeescript

Our styles and Javascript are written using LESS and Coffeescript, respectively. Sadly, browsers can’t read them, so we
must convert. Here’s the easiest way to do that.

*If you haven't already, install Grunt (see above).*

1. Navigate to the root directory of your repository and run `grunt coffee` or `grunt less`.

That’s it. Do you want the files to compile every time you save a .coffee or .less file? Run `grunt watch`.