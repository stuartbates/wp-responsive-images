<?php

/*  
Copyright 2012  Stuart Bates  (email : hello@stuartbates.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class WPPluginObject
{
    /**
     * Defined for convienience on all plugins
     *
     * @var string
     */
    protected $pluginUrl = null;
    
    /**
     * Defined for convienience on all plugins
     *
     * @var string
     */
    protected $pluginPath = null;
    
    /**
     * Does the basic plugin setup
     *
     * Sets up activation and deactivation hooks, detects the working environment
     * and sets up required hooks and filters.
     *
     * @return void
     *
     * @access public
     * @since Method available since Release 1.0.0
     */
    public function __construct()
    {
        register_activation_hook( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'index.php', array(  &$this, 'install' ) );
        register_deactivation_hook( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'index.php', array(  &$this, 'uninstall' ) );
        
        $this->setPluginUrl();
        $this->setPluginPath();
    }
    
   /**
     * Method run on activation
     *
     * Dynamically creates the image handling page, saves the option and if 
     * mod_rewrite is enabled updates the htaccess file with the new rules
     *
     * @return void
     *
     * @access public
     * @since Method available since Release 1.0.0
     */
    public function install()
    {
        // Register with my server?
    }
    
    /**
     * Method run on deactivation
     *
     * Cleans up the htacces file and deletes the WP option
     *
     * @return void
     *
     * @access public
     * @since Method available since Release 1.0.0
     */
    public function uninstall()
    {   
        // Register with my server?
    }
    
    protected function setPluginUrl()
    {
        $this->pluginUrl = trailingslashit( get_bloginfo( 'wpurl' ) ) . PLUGINDIR . '/' . dirname( plugin_basename( __FILE__ ) );
    }
    
    protected function setPluginPath()
    {
        $this->pluginPath = dirname( __FILE__ ) . '/cache/';
    }
}