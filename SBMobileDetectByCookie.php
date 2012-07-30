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

include_once 'IDetect.php';

class SBMobileDetectByCookie implements IDetect
{    
    public function isMobile()
    {
        if (isset ($_COOKIE['wpri_width']))
        {
            if ($_COOKIE['wpri_width'] < 400)
            {
                return true;
            }
            
            return false;
        }
        
        return null;
    }
    
    public function isTablet()
    {
        return false;
    }
    
    public function isHighDensity()
    {
        if (isset ($_COOKIE['wpri_density']))
        {
            if ($_COOKIE['wpri_density'] > 1)
            {
                return true;
            }
        }
        
        return false;
    }
}