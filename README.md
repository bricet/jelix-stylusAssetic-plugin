What is jelix-lessphp-plugin ?
==============================

This project is a plugin for [Jelix](http://jelix.org) PHP framework. It allows you to use easily [stylus](http://learnboost.github.com/stylus/) dynamic stylesheet language in Jelix (using [Assetic](https://github.com/kriswallsmith/assetic)).

This is an htmlresponse plugin.



Installation
============

Under Jelix default configuration, create an "htmlresponse" directory in your project's "plugins" directory.

Clone this repository in that directory.

Download [Assetic v1.0.2](https://github.com/kriswallsmith/assetic/zipball/v1.0.2) or newer.

Unzip /kriswallsmith-assetic-f829ad2/src/Assetic/ directory in your htmlresponse/stylusAssetic directory previously created.



This plugin needs node.js with stylus module to be installed.

Note : in Assetic's Filter/StylusFilter.php file, I needed to add the following line so that everything works fine (just before the line containing `require('stylus')`) :
`require.paths.push('/usr/local/lib/node_modules');`

I am not Assetic and/or node.js aware enough to tell if this is really needed ...





Usage
=====

When including a CSS file (e.g. with addCSSLink()) you should set 'stylus'=>true as a param.

E.g. in your response :

`$this->addCSSLink($gJConfig->urlengine['basePath'].'themes/'.$gJConfig->theme.'/Css/style.styl', array( 'stylus' => true ));`

Your config file must activate lessphp plugin :

    [jResponseHtml]
    plugins=stylusAssetic

N.B. : the directories containing less files should be writable by your web server ! Indeed, compiled files will be written in that very same directory so that relative urls go on working ...



Config
======

You can configure lessphp's behviour regarding compilation:

    [jResponseHtml]
    ;...
    ; always|onchange|once
    stylus_assetic_compile=always

If stylus\_assetic\_compile's value is not valid or empty, its default value is onchange.

* always : compile stylus file on all requests
* onchange : compile stylus file only if it has changed
* once : compile stylus file once and never compile it again (until compiled file is removed)



About this plugin
=================

This plugin may be enhanced by a better PHP autoload behaviour.

This plugin may also be a base for other specific Assetic usage (less with node.js, YUICompressor, ...) or may be a more dynamic one ...

