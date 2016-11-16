<?php


function Boc_toc_init($file) {

    require_once('Boc_toc_Plugin.php');
    $aPlugin = new Boc_toc_Plugin();

    // Add callbacks to hooks
    $aPlugin->addActionsAndFilters();

    if (!$file) {
        $file = __FILE__;
    }
    // Register the Plugin Activation Hook
    register_activation_hook($file, array(&$aPlugin, 'activate'));

    // Register the Plugin Deactivation Hook
    register_deactivation_hook($file, array(&$aPlugin, 'deactivate'));
}
