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

class SBMobileDetect
{    
    protected $detectors = array();
    
    public function __construct( $detectors = array() )
    {
        $this->detectors = $detectors;
    }
    
    public function isMobile()
    {
        foreach ( $this->detectors as $detector )
        {
            $is_mobile = $detector->isMobile();
            
            if ( ! is_null( $is_mobile ) )
            {
                return $is_mobile;
            }
        }
        
        return false;
    }
}