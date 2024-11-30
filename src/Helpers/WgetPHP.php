<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    Massimiliano Palermo <maxx.palermo@gmail.com>
 * @copyright Since 2016 Massimiliano Palermo
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

namespace MpSoft\MpMassImport\Helpers;

use Fawno\wget\wget;

class WgetPHP
{
    /**
     * wget just like linux wget command
     * 
     * @author  Mohammed Al Ashaal <is.gd/alash3al>
     *
     * @version 1.0.0
     *
     * @license MIT License
     * 
     * @param string $url example 'http://site.com/xxx?k=v'
     * @param string $method example 'GET'
     * @param array $headers example array( 'Cookie: k1=v1; k2=v2' )
     * @param string $body only if the $methd is not GET
     * 
     * @return array|string "success" | string "failure"
     */
    public static function wget($url, $method = 'GET', array $headers = [], $body = '')
    {
        // prevent timeout
        set_time_limit(0);

        // get the url components
        $url = (object) array_merge([
            'scheme' => '',
            'host' => '',
            'port' => 80,
            'path' => '/',
            'query' => '',
        ], parse_url($url));

        if ( empty($url->host) ) {
            $url->host = $url->path;
            $url->path = '/';
        }

        // open socket connection
        $socket = fsockopen($url->host, $url->port, $errno, $errstr, 30);

        // if there is any error
        // exit and print its string
        if ( $errno ) {
            return $errstr;
        }

        // generate the headers
        $headers = array_merge(
            [
                sprintf('%s %s?%s HTTP/1.1', strtoupper($method), $url->path, $url->query),
                sprintf('Host: %s', $url->host),
                'Connection: Close',
            ],
            $headers
        );

        // coninue for non-get methods
        if ( strtolower($method) !== 'get' ) {
            $headers[] = sprintf('Content-Length: %s', strlen($body));
            $headers[] = '';
            $headers[] = '';
            $headers[] = $body;
        } else {
            $headers[] = '';
        }

        $headers = join(PHP_EOL, $headers) . PHP_EOL;

        // write the headers to the target
        fwrite($socket, $headers);

        // headers and body from the server
        $headers = '';
        $body = '';

        // generating the headers and the body
        if ( ($pos = strpos($response = stream_get_contents($socket), PHP_EOL . PHP_EOL)) !== FALSE ) {
            $headers = substr($response, 0, $pos);
            $body = substr($response, $pos + 1);
        } else {
            $headers = $response;
        }

        // close the socket connection
        fclose($socket);

        // return the result
        return ['headers' => trim($headers), 'body' => trim($body)];
    }

    public static function get($url, $destination)
    {
        $class = new wget();
        $content = $class->get_file($url, $destination);
    }
}
