<?php
/**
 * @package     ZOOlanders Framework
 * @version     4.0.0-beta11
 * @author      ZOOlanders - http://zoolanders.com
 * @license     GNU General Public License v2 or later
 */

namespace Zoolanders\Framework\Request;
use Zoolanders\Framework\Data\Data;

/**
 * Class Request
 * @package Zoolanders\Framework\Request
 */
class Request extends \JInput
{
    /**
     * @var array|false Request headers
     */
    protected $headers;

    /**
     * Request constructor.
     */
    public function __construct()
    {
        parent::__construct();

        // Capture HTTP Request headers:
        $this->getHeaders();

        // Parse raw json data:
        if(($this->headers->get('Content-Type') == 'application/json') || ($this->getCmd('format', false) == 'json')){
            $json = json_decode(@file_get_contents('php://input'), true);
            $this->data = array_merge($this->data, (array)$json);
        }

    }

    /**
     * Get http request headers
     *
     * @return array
     */
    public function getHeaders(){

        if(empty($this->headers)){
            $headers = [];

            if(function_exists('getallheaders')){
                $headers = getallheaders();
            } else {
                foreach($_SERVER as $key => $value) {
                    if ( substr($key,0,5) == "HTTP_" ) {
                        $key = str_replace(" ", "-", ucwords(strtolower(str_replace("_"," ",substr($key,5)))));
                        $headers[$key] = $value;
                    }else{
                        $headers[$key] = $value;
                    }
                }
            }

            $this->headers = new Data($headers);
        }

        return $this->headers;
    }

    /**
     * isAjax
     *
     * @return bool True if an ajax call is being made
     */
    public function isAjax()
    {
        // Joomla way
        if (in_array($this->getCmd('format'), ['json', 'raw'])) {
            return true;
        }

        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }
}
