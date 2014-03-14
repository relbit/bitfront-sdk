<h1>evia PHP SDK</h1>
<h4>ver 1.0</h4>
evia PHP SDK can be used as a standalone application in CLI.
<pre>
$ php evia.php -h
bitfront-API CLI
SYNTAX:
	evia.php OPTIONS METHOD [PARAMS]
USAGE EXAMPLE:
	php evia.php -e=account@prg0.relbitapp.com --password=topsecret -u=https://api.prg0.relbitapp.com getApps

	php evia.php -e=account@prg0.relbitapp.com --password=topsecret -u=https://api.prg0.relbitapp.com addApp test_app
OPTIONS:
	-h, --help		Prints this help
	-e, --email		API login email
	-p, --password		API password
	-u, --url		API base url
</pre>

As well as it can be used in any PHP application as a library.
<pre>
<?php
	require 'evia.php';
	
	$evia = new Evia('account@prg0.relbitapp.com', 'topsecret', 'https://api.prg0.relbitapp.com');

	// if you encounter SSL verification error, please disable SSL. Some Linux distributions do not support RapidSSL
	$evia->disableVerification();

	// print all apps from my account
	$apps = $evia->getApps();
	foreach ($apps as $app) {
		print_r($app);
	}

	
?>
</pre> 

For more information about API and documentation look inside evia.php and visit <a href="http://docs.eviaproject.org/eviaproject:bitfront:api">docs.eviaproject.org - bitfront - API Documentation</a>
