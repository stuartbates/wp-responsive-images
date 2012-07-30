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

class SBFileCache
{   
    protected $cachePath = null;
    
    public function __construct( $path )
    {
        if ( ! file_exists( $path ) )
        {
            mkdir( $path );
        }
                
        if ( ! is_writable( $path ) )
        {
            chmod( $path, 744 );
        }
        
        $this->cachePath = $path;
    }
    
   /**
     * Checks if the file supplied is cached
     *
     * Currently is simply a wrapper for file_exists but will add extra time checking 
     * code the method in the future
     *
     * @return bool
     *
     * @access public
     * @since Method available since Release 1.0.0
     * 
     * @todo Add cache lifetime check
     */
    public function isCached( $path )
    {
        if ( file_exists( $path ) )
        {
            return true;
        }
        
        return false;
    }

}