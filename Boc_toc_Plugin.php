<?php

require "boc_toc_widget.php";


class Boc_toc_Plugin  {

    /**
     * See: http://plugin.michael-simpson.com/?page_id=31
     * @return array of option meta data.
     */
    public function getOptionMetaData() {
        //  http://plugin.michael-simpson.com/?page_id=31
        return array(
            //'_version' => array('Installed Version'), // Leave this one commented-out. Uncomment to test upgrades.
            'ATextInput' => array(__('Enter in some text', 'my-awesome-plugin')),
            'AmAwesome' => array(__('I like this awesome plugin', 'my-awesome-plugin'), 'false', 'true'),
            'CanDoSomething' => array(__('Which user role can do something', 'my-awesome-plugin'),
                                        'Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber', 'Anyone')
        );
    }

    protected function initOptions() {
        $options = $this->getOptionMetaData();
        if (!empty($options)) {
            foreach ($options as $key => $arr) {
                if (is_array($arr) && count($arr > 1)) {
                    $this->addOption($key, $arr[1]);
                }
            }
        }
    }

    public function getPluginDisplayName() {
        return 'boc_toc';
    }

    protected function getMainPluginFileName() {
        return 'boc_toc.php';
    }

    public function addActionsAndFilters() {

        // Add options administration page
        add_action('admin_menu', array(&$this, 'addSettingsSubMenuPage'));

        add_action( 'widgets_init', function(){
            register_widget( 'Boc_Toc_Widget' );
        }); 

        // script & style just for the options administration page
       if (is_admin()) {
            wp_enqueue_script( 'wp-color-picker' ); 
            wp_enqueue_style( 'wp-color-picker' );  
            wp_enqueue_script('color_picker', plugins_url('/js/color_picker.js', __FILE__));
       }
               
    }

       /**
     * Puts the configuration page in the Plugins menu by default.
     * Override to put it elsewhere or create a set of submenus
     * Override with an empty implementation if you don't want a configuration page
     * @return void
     */
    public function addSettingsSubMenuPage() {
         add_submenu_page('plugins.php',
                         "BOC TOC Plugin",
                         "BOC TOC Plugin",
                         'manage_options',
                         'boc_toc_plugin',
                         array(&$this, 'settingsPage'));
    }


}
