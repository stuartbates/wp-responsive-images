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

include 'SBOutputFile.php';

/**
 * @todo Add support for X-SendFile
 * @todo Add Nginx support for X-SendFile variant
 */
class SBOutputImage extends SBOutputFile
{
    /**
     * Detect the mime type of the file
     *
     * @param string $path Path to the file
     * @return string to ISO mime type
     */
    protected function getFilesMimeType( $path )
    {
        $mimeType = getimagesize( $path );
        
        return $mimeType['mime'];
    }
}