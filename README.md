Osclass Routes Example
==============

This is an example of how to use "routes" in Osclass to create your own unique URLs for your plugins


What are the routes for?
=
You could extend Osclass with plugins, and sometimes you need to create a special page, for example to show more options to your users. In previous versions, the url will look like domain.tld/index.php?page=custom&file=your_plugin/page.php which isn't the prettiest url you could see, and also the file path are visible which is not a problem, but it's not good. In 3.2 we added "routes" that will transform that ugly url into a more beauty one, like domain.tld/your_plugin_page , they even works with regular expressions to accept variables on the url.

The route functions
==
To make routes works, we first need to create them:


**osc_add_route($id, $regexp, $url, $file)**

* $id - Shortname of the route
* $regexp - Regular expression of the url
* $url - Required to be able to create the nice-looking url
* $file - file that will be loaded



Later we just need to get the url:

**osc_route_url($id, [$args]) - For public routes**

**osc_route_admin_url($id, [$args]) - For routes in the admin panel**

* $id - Shortname of the previously created route
* $args - Optional, only required if your url accept parameters



Here is an example

// Create route

osc_add_route('dynamic-route', 'dynamic-route/([0-9]+)/(.+)', 'dynamic-route/{my-numeric-param}/{my-own-param}', osc_plugin_folder(__FILE__).'mydynamicroute.php');

// Show link to it

echo osc_route_url('dynamic-route', array('my-numeric-param' => '12345', 'my-own-param' => 'my-own-value'))

Notes
==
* Parameters in the $url should be enclosed between "{" and "}", example "{parameter}"
* Parameters should have the same name (case sensitive) in both, osc_add_route and osc_route_url
* Additionally, any file located in a folder called "admin" will be opened in admin panel, but show a 404 error in the public site

