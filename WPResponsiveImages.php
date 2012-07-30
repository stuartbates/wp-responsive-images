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

/**
 * WPResponsiveImages is a WordPress Plugin that silently serves mobile sized images
 * dynamically to mobile devices
 *
 * @category   WPResponsiveImages
 * @package    WPResponsiveImages
 * @author     Stuart Bates <hello@stuartbates.com>
 * @copyright  
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.01
 * @version    Release: 0.1
 * @link       http://www.stuartbates.com/wpresponsiveimages
 * @since      Class available since Release 1.0
 */

include 'WPPluginObject.php';

class WPResponsiveImages extends WPPluginObject
{
    // {{{ class constants
    
    const PERMALINK_OPTION =        'wpri_permalink';
    const ENVIRONMENT_OPTION =      'wpri_environment';
    const JAVASCRIPT_HANDLE =       'wpri_javascript';
    const IMAGES_REGEX =            '(.*)\.(gif|jpe?g|png)$';
    const IMAGE_HANDLER_SCRIPT =    'image_handler.php';
    const MOBILE_SIZE =             300;
    
    // }}}
    
    // {{{ properties
    
    /**
     * The value of the image handling URL returned by the WP option
     * 
     * @var string
     */
    protected $permalink = null;
    
    /**
     * Denotes the environment used to decide which method is used
     *
     * Potential values are 'htaccess', 'simpledom', and 'regex'.
     *
     * @var string
     */
    protected $environment = null;
    
    /**
     * Defines an array of allowed image types as supported by GD library
     *
     * @var array
     */
    protected $mimeTypes = array( 'png', 'gif', 'jpeg', 'jpg' );
    
    /**
     * Defines an path to where the images will be cached
     *
     * @var string
     */
    protected $pathToCache = null;
    
    // }}}
    
    /**
     * Does the basic plugin setup
     *
     * Detects the working environment and sets up required hooks and filters.
     *
     * @return void
     *
     * @access public
     * @since Method available since Release 1.0.0
     */
    public function __construct()
    {   
        if ( defined( 'WP_DEBUG' ) )
        {
            parent::__construct();
        
            $this->setEnvironment();
            $this->setPermalink();
            
            if ( $this->environment !== 'htaccess' )
            {    
                add_filter( 'the_content', array( $this, 'replaceImageSourceAttributes' ) );
            }
            
            add_action( 'wp_print_scripts', array( $this, 'addMobileDetectionJavascript' ) );
        }
        
        $this->setPathToCache();
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
        parent::install();
        
        $this->permalink = plugin_dir_path( __FILE__ ) . self::IMAGE_HANDLER_SCRIPT;
        $this->environment = $this->testEnvironment();
        
        add_option( self::PERMALINK_OPTION, $this->permalink );
        add_option( self::ENVIRONMENT_OPTION, $this->environment );
        
        if ( $this->environment == 'htaccess')
        {
            $this->createApacheDirectives( $this->permalink );
        }
        
        /* Please leave this in place so I can see how many people are using the plugin */
        ping( 'http://www.stuartbates.com/wpresponsiveimages/activate/' );
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
        parent::uninstall();
        
        $this->removeApacheDirectives(); // Do we need to test environment first?
        
        delete_option( self::PERMALINK_OPTION );
        delete_option( self::ENVIRONMENT_OPTION );
        
        /* Please leave this in place so I can see how many people are using the plugin */
        ping( 'http://www.stuartbates.com/wpresponsiveimages/deactivate/' );
    }
    
    /**
     * Dynamically calculates the path of the page that will handle images and 
     * saves as a WP option
     * 
     * @return string
     *
     * @access protected
     * @since Method available since Release 1.0.0
     */
    protected function setPermalink()
    {   
        $this->permalink = get_option( self::PERMALINK_OPTION );
    }
    
    /**
     * Fetches the environment option and sets property
     *
     * @return void
     *
     * @access public
     * @since Method available since Release 1.0.0
     */
    protected function setEnvironment()
    {
        $this->environment = get_option( self::ENVIRONMENT_OPTION );
    }
    
    /**
     * Dynamically calculates the path of the page that will handle images and 
     * saves as a WP option
     * 
     * @return string
     *
     * @access protected
     * @since Method available since Release 1.0.0
     */
    protected function setPathToCache()
    {   
        $this->pathToCache = dirname( __FILE__ ) . '/cache/';
    }

    /**
     * Detects the environment and sets the environment property
     *
     * Detects whether we can:
     * - Use mod_rewrite to redirect image requests
     * - Use simple DOM to replace image srcs
     * - Use regular expressions to replace image srcs
     *
     * @return string
     *
     * @access public
     * @since Method available since Release 1.0.0
     */
    protected function testEnvironment()
    {
        if ( got_mod_rewrite() ) // Not sure that's sufficient to know?
        {
            return 'htaccess';
        }
        else if ( $this->environmentSupportsSimpleDOM() )
        {
            return 'simpledom';
        }
       
        return 'regex';
    }
    
    /**
     * Adds javascript that detects the screen size and sets a cookie
     *
     * @return void
     *
     * @access public
     * @since Method available since Release 1.0.0
     */
    public function addMobileDetectionJavascript()
    {   
        wp_enqueue_script( self::JAVASCRIPT_HANDLE, $this->pluginUrl . '/wpri_mobile_detection.js' );
    }
    
    /**
     * Outputs the appropriate image
     *
     * Handles image requests by detecting whether the user is on a mobile device
     * then serving the appropriate image size back to the browser.
     *
     * @return void
     *
     * @access public
     * @since Method available since Release 1.0.0
     */
    public function serveRequestedImage( $requested_filename )
    {   
        if ( ! in_array( strtolower( pathinfo( parse_url( $requested_filename, PHP_URL_PATH ), PATHINFO_EXTENSION ) ), $this->mimeTypes ) )
        {
            header( "Status: 403 Forbidden" );
        }
        
        if ( ! file_exists( $requested_filename ) || ! is_file( $requested_filename ))
        {
            header("Status: 404 Not Found");
        }
        
        $feteched_filename = $requested_filename;
        
        list ( $width ) = getimagesize( $requested_filename );
        
        if ( $width > self::MOBILE_SIZE )
        {
            header( "Vary: User-Agent" );
            
            /* Setup mobile detection */
            $detectors = array();

            $detectors[] = new SBMobileDetectByCookie();
            $detectors[] = new SBMobileDetectByUserAgent();

            $detect = new SBMobileDetect( $detectors );

            if ( $detect->isMobile() )
            {
            
                /* Serve from cache if possible */
                $cache = new SBFileCache( $this->pathToCache );
                
                $feteched_filename = self::MOBILE_SIZE . $requested_filename;
                $path_to_cached_file = $this->pathToCache . md5( $feteched_filename );
                
                if ( ! $cache->isCached( $path_to_cached_file ) )
                {
                    /* Create the mobile image dynamically */
                    if ( $this->createMobileImage( $requested_filename, $path_to_cached_file ) )
                    {
                        $requested_filename = $path_to_cached_file;
                    }
                }
                else
                {
                    $requested_filename = $path_to_cached_file;
                }
            }
        }
        
        /* Output the file to the browser */
        $file = new SBOutputImage();
        $file->sendFile( $requested_filename );
    }
    
   /**
     * Creates the mobile version of an image and saves
     *
     * @return void
     *
     * @access protected
     * @since Method available since Release 1.0.0
     */
    protected function createMobileImage( $source, $destination )
    {
        if ( $image = SBImage::createImageFromPath( $source ) )
        {
            $image->resizeToWidth( self::MOBILE_SIZE );
            $image->save( $destination );
            $image->destroy();
            
            return true;
        }
        
        return false;
    }


    /**
     * Updates the htaccess file to redirect image requests to the image handler
     *
     * @return bool
     *
     * @access protected
     * @since Method available since Release 1.0.0
     */
    protected function createApacheDirectives( $permalink )
    {
        $home_path = get_home_path();
        $htaccess_file = $home_path . '.htaccess';
        $rules = array(
            '<IfModule mod_rewrite.c>',
            'RewriteEngine On',
            'RewriteRule ' . self::IMAGES_REGEX . ' ' . $permalink . ' [L]',
            '</IfModule>',
        );
        
        return insert_with_markers( $htaccess_file, get_class(), $rules );
    }
    
    /**
     * Cleans up the htaccess file when the plugin is deactivated
     *
     * @return bool
     *
     * @access protected
     * @since Method available since Release 1.0.0
     */
    protected function removeApacheDirectives()
    {
        $home_path = get_home_path();
        $htaccess_file = $home_path . '.htaccess';
        
        return insert_with_markers( $htaccess_file, get_class(), array() );
    }
    
    /**
     * Dynamically replaces the src attributes of images to point to image handler
     * 
     * Method called from an action hook and uses simple DOM to dynamically update 
     * the src attribute
     *
     * @return string
     *
     * @access public
     * @since Method available since Release 1.0.0
     */
    public function replaceImageSourceAttributes( $content )
    {
        if ( $this->environment == 'simpledom')
        {
            return $this->replaceImagesUsingSimpleDOM( $content );
        }
        else
        {
            return $this->replaceImagesUsingRegex( $content );
        }
    }
    
    /**
     * Replaces image src attributes dyanmically using DOM parsing 
     *
     * @return string
     *
     * @access protected
     * @since Method available since Release 1.0.0
     */
    protected function replaceImagesUsingSimpleDOM( $content )
    {
        $html = new simple_html_dom();
        
        $html->load( $content );
        
        foreach ( $html->find( 'img' ) as $element )
        {
            $element->src = $this->processImageURL( $element->src );
        }
        
        return $html->__toString();
    }
    
    /**
     * Replaces image src attributes dyanmically using REGEX 
     *
     * @return bool
     *
     * @access protected
     * @since Method available since Release 1.0.0
     */
    protected function replaceImagesUsingRegex( $content )
    {   
        $pattern = '/< *img[^>]*src *= *["\']?([^"\']*)/i';
        $content = preg_replace_callback( $pattern, array($this, extractSrcForProcessing), $content);
        
        return $content;
    }
    
    /**
     * Proxy method to manipulate input ready for processImageURL
     *
     * @return string
     *
     * @access protected
     * @since Method available since Release 1.0.0
     */
    protected function extractSrcForProcessing( $matches )
    {
        return str_replace( $matches[1], $this->processImageURL( $matches[1] ), $matches[0] );
    }

   /**
     * Manipulates the src string passed in to point to image handler URL
     *
     * @return string
     *
     * @access protected
     * @since Method available since Release 1.0.0
     */
    protected function processImageURL( $src )
    {
        // External resource?
        if ( ( substr($src, 0, 7) == 'http://' || substr($src, 0, 8) == 'https://' ) && strpos( $src, site_url()) === false )
        {
            return $src;
        }
        
        return $this->pluginUrl . '/' . self::IMAGE_HANDLER_SCRIPT . '?src=' . $src;
    }

    /**
     * Detects whether PHP5 is availble for Simple DOM
     *
     * @return bool
     *
     * @access protected
     * @since Method available since Release 1.0.0
     */
    protected function environmentSupportsSimpleDOM()
    {
        if ( defined( 'PHP_MAJOR_VERSION' ) )
        {
            return PHP_MAJOR_VERSION >= 5 ? true : false;
        } 
        else 
        {
            return function_exists( 'http_build_query' ) ? true : false;
        }
    }
}