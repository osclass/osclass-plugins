Supertoolbar
============

Add a toolbar on the header with extra information and links. Also it could be modified/improved by the support of other plugins


How to add new options to the toolbar
=====================================
SuperToolbar trigger its own hook, *'supertoolbar_hook'* in the middle of the menu.
To add new options, link a function to that hook and use the following code inside : 

SuperToolBar::newInstance()->addOption('your text here, usually a \<a\> tag');


You probably want to include some checks to know if the user is logged, in which page the user is, ... to know more about it, please refer to the Wiki ( http://wiki.osclass.org/ ) or the documentation ( http://doc.osclass.org/ )


The whole code, should look like


function myplugin_toolbar() {

SuperToolBar::newInstance()->addOption('your text here, usually a \<a\> tag');

}

osc_add_hook("supertoolbar_hook", "myplugin_toolbar");