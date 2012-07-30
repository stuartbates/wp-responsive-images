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

function ping( $url )
{   
    if ( function_exists( 'fsockopen' ) )
    {
        $url_pieces = parse_url( $url );

        $path = (isset($url_pieces['path'])) ? $url_pieces['path'] : '/';
        $port = (isset($url_pieces['port'])) ? $url_pieces['port'] : '80';

        if ( $fp = @fsockopen( $url_pieces['host'], $port, $errno, $errstr, 30 ) )
        {
            $send = "HEAD $path HTTP/1.1\r\n";
            $send .= "HOST: {$url_pieces['host']}\r\n";
            $send .= "CONNECTION: Close\r\n\r\n";

            fwrite( $fp, $send );

            $data = fgets( $fp, 128 );

            fclose( $fp );

            list ( $response, $code ) = explode( ' ', $data );

            return $code;
        } 

        return false;    
    }

    if ( ini_get( 'allow_url_fopen' ) && function_exists( 'file_get_contents' ) )
    {
        @file_get_contents( $url );
        return;
    }

    if ( function_exists( 'curl_init' ) )
    {
        $curl = curl_init( $url );
        curl_setopt( $curl, CURLOPT_FAILONERROR );
        curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, 1 );
        curl_setopt( $curl, CURLOPT_TIMEOUT, 5 );
        $r = @curl_exec( $curl );

        curl_close($curl);
        return;
    }   
}