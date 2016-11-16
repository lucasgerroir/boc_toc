<?php
/*
   Plugin Name: boc_toc
   Plugin URI: http://wordpress.org/extend/plugins/boc_toc/
   Version: 0.1
   Author: Lucas Gerroir
   Description: A dynamic table of contents
   Text Domain: boc_toc
   License: GPLv3
  */


/**
 * Initialize internationalization (i18n) for this plugin.
 * References:
 *      http://codex.wordpress.org/I18n_for_WordPress_Developers
 *      http://www.wdmac.com/how-to-create-a-po-language-translation#more-631
 * @return void
 */
function Boc_toc() {
    $pluginDir = dirname(plugin_basename(__FILE__));
    load_plugin_textdomain('boc_toc', false, $pluginDir . '/languages/');
}


//////////////////////////////////
// Run initialization
/////////////////////////////////

// Initialize i18n
add_action('plugins_loadedi','Boc_toc');

// Run the version check.
// If it is successful, continue with initialization for this plugin
    // Only load and run the init function if we know PHP version can parse it
    include_once('boc_toc_init.php');
    Boc_toc_init(__FILE__);
