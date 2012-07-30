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

if (document.cookie.indexOf('wpri_width') < 0) {
    
    var density = undefined !== window.devicePixelRatio ? window.devicePixelRatio : 1;
    document.cookie = 'wpri_density=' + density + ';';
    
    var width = undefined !== window.screen.width ? window.screen.width : 1000;
    document.cookie = 'wpri_width=' + width + ';';
}