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
 * @todo Add support for X-SendFile
 * @todo Add Nginx support for X-SendFile variant
 */
class SBOutputFile
{
    /**
     * Send a file for download
     *
     * @param string $path Path to the file
     * @param array $options
     * @return bool Whether the headers and file were sent
     */
    public function sendFile( $path, $options = array() )
    {   
        if ( ! is_readable( $path ) || headers_sent() )
        {
            return false;
        }
        
        // Set the cache-control
        if ( isset( $options['cache'] ) )
        {
            $this->setCacheHeaders( $options['cache'] );
        }
        
        // Set the content disposition
        if ( isset( $options['disposition'] ) && $options['disposition'] == 'attachment' ) {
            $disposition = 'attachment';
        } else {
            $disposition = 'inline';
        }
        
        // Set the file name
        if ( isset( $options['filename'] ) && !empty( $options['filename'] ) )
        {
            $filename = $options['filename'];
        } else {
            $filename = basename($path);
        }
        
        // Get the last modified time
        if ( isset( $options['modified'] ) )
        {
            $modified = (int) $options['modified'];
        } else {
            $modified = filemtime( $path );
        }

        $type = $this->getFilesMimeType( $path );
        
        header( "Content-Type: $type\n" );
        header( "Content-Disposition: $disposition; filename=\"$filename\"\n");
        header( "Last-Modified: " . gmdate( 'r', $modified ) . "\n" );
        header( "Content-Length: " . filesize( $path ) . "\n" );
        readfile( $path );
        
        return true;
    }
    
    /**
     * Detect the mime type of the file
     *
     * @param string $path Path to the file
     * @return string to ISO mime type
     */
    protected function getFilesMimeType( $path )
    {
        if ( function_exists( 'finfo_file' ) )
        {
            $finfo = finfo_open( FILEINFO_MIME_TYPE );
            $mime_type = finfo_file( $finfo, $path );
            finfo_close( $finfo );
            
            return $mime_type;
        }
        
        return mime_content_type( $path );
    }
    
    /**
     * Set cache headers
     *
     * @param array $options
     */
    public function setCacheHeaders( $options )
    {
        $cache_control = array();
        
        if ( isset( $options['max-age'] ) )
        {
            $cache_control[] = 'max-age=' . (int) $options['max-age'];
            $response->setHeader( 'Expires', gmdate( 'r', time() + $options['max-age'] ), true );
        }
        
        if ( isset( $options['must-revalidate'] ) && $options['must-revalidate'])
        {
            $cache_control[] = 'must-revalidate';
        }
        
        if ( isset( $options['no-cache'] ) && $options['no-cache'])
        {
            $cache_control[] = 'no-cache';
        }
        
        if ( isset( $options['no-store'] ) && $options['no-store'])
        {
            $cache_control[] = 'no-store';
        }
        
        if ( isset( $options['proxy-validation'] ) && $options['proxy-validation'])
        {
            $cache_control[] = 'proxy-validation';
        }
        
        if ( isset( $options['public'] ) && $options['public'] )
        {
            $cache_control[] = 'public';
        }
        
        if ( isset( $options['s-maxage'] ) )
        {
            $cache_control[] = 's-maxage=' . (int) $options['s-maxage'];
        }

        header( "Cache-Control: " . implode( ',', $cache_control ) . "\n" );
        header( "Pragma: public\n" );
    }
}