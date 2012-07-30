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

class SBImage {
 
    protected $image = null;
    protected $image_type = null;
 
    function __construct( $filename )
    {
        if ( ! file_exists( $filename ) )
        {
           throw new Exception('The file does not exist'); // Or issue 404?
        }

        if ( ! function_exists('imagecreatefromstring') )
        {
            throw new Exception('The GD image library is not installed');
        }
 
        @ini_set( 'memory_limit', '256M' );
        
        $image = imagecreatefromstring( file_get_contents( $filename ) );
        
        if ( ! is_resource( $image ))
        {
           throw new Exception('The file supplied was not an image');
        }
        
        $this->image = $image;
    }
    
    
    
    public static function createImageFromPath( $path )
    {
        try
        {
            return new SBImage( $path );
        } 
        catch ( Exception $excp )
        {
            return null;
        }
    }
   
   
   
    function save( $filename, $image_type = IMAGETYPE_JPEG, $compression = 75, $permissions = null )
    {
        if ( $image_type == IMAGETYPE_JPEG )
        {
            imagejpeg( $this->image, $filename, $compression );
        } 
        elseif ( $image_type == IMAGETYPE_GIF )
        {
            imagegif( $this->image, $filename );
        } 
        elseif ( $image_type == IMAGETYPE_PNG )
        {
            imagepng( $this->image, $filename );
        }
      
        if ( $permissions != null)
        {
            chmod( $filename, $permissions );
        }
   }
   
   
   
   
   
    function output( $image_type = IMAGETYPE_JPEG )
    {
        if ( $image_type == IMAGETYPE_JPEG )
        {
            imagejpeg( $this->image );
        } 
        elseif ( $image_type == IMAGETYPE_GIF )
        {
            imagegif( $this->image );
        } 
        elseif ( $image_type == IMAGETYPE_PNG )
        {
            imagepng( $this->image );
        }
    }
    
    
    
    
    public function destroy()
    {
        if ( is_resource( $this->image ) )
        {
            imagedestroy( $this->image );
        }
    }
   
   
   
   
    function getWidth()
    {
        return imagesx( $this->image );
    }
    
    
    
    function getHeight()
    {
        return imagesy( $this->image );
    }
   
    
    
    function resizeToHeight( $height )
    {
        $ratio = $height / $this->getHeight();
        $width = $this->getWidth() * $ratio;
        $this->resize( $width, $height );
    }
 
    function resizeToWidth( $width )
    {
        $ratio = $width / $this->getWidth();
        $height = $this->getHeight() * $ratio;
        $this->resize( $width, $height );
    }
 
    function scale( $scale )
    {
        $width = $this->getWidth() * $scale/100;
        $height = $this->getHeight() * $scale/100;
        $this->resize( $width, $height );
    }
 
    function resize( $width, $height )
    {
        $new_image = imagecreatetruecolor( $width, $height );
        imagecopyresampled( $new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight() );
        $this->image = $new_image;
    }
}