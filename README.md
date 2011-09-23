What is jelix-stylusAssetic-plugin ?
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






Usage
=====

When including a CSS file (e.g. with addCSSLink()) you should set 'stylus'=>true as a param.

E.g. in your response :

`$this->addCSSLink($gJConfig->urlengine['basePath'].'themes/'.$gJConfig->theme.'/Css/style.styl', array( 'stylus' => true ));`

Your config file must activate stylusAssetic plugin :

    [jResponseHtml]
    plugins=stylusAssetic

N.B. : the directories containing stylus files should be writable by your web server ! Indeed, compiled files will be written in that very same directory so that relative urls go on working ...



Config
======

You can configure stylus's behviour regarding compilation:

    [jResponseHtml]
    ;...
    ; always|onchange|once
    stylusAssetic_compile=always

If stylusAssetic\_compile's value is not valid or empty, its default value is onchange.

* always : compile stylus file on all requests
* onchange : compile stylus file only if it has changed
* once : compile stylus file once and never compile it again (until compiled file is removed)

You can also set path to node.js binary and node.js modules :

    [jResponseHtml]
    ;...
    ;default value is /usr/bin/node : MUST be changed for MS Windows and may be also for *Unix
    stylusAssectic_node_bin_path="/usr/local/bin/node"
    
    ;default value is empty. Must be comma-separated if several paths needed (why the hell would we ?)
    stylusAssectic_node_paths="/usr/local/lib/node_modules"


About this plugin
=================

This plugin may be enhanced by a better PHP autoload behaviour.

This plugin may also be a base for other specific Assetic usage (less with node.js, YUICompressor, ...) or may be a more dynamic one ...

