<?php
/**
 * AddToCart
 *
 * PHP version 5
 *
 * @category Class
 * @package  ai\thirdwatch
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */

/**
 * Thirdwatch API
 *
 * The first version of the Thirdwatch API is an exciting step forward towards making it easier for developers to pass data to Thirdwatch.  Once you've [registered your website/app](https://thirdwatch.ai) it's easy to start sending data to Thirdwatch.  All endpoints are only accessible via https and are located at `api.thirdwatch.ai`. For instance: you can send event at the moment by ```HTTP POST``` Request to the following URL with your API key in ```Header``` and ```JSON``` data in request body. ```   https://api.thirdwatch.ai/event/v1 ``` Every API request must contain ```API Key``` in header value ```X-THIRDWATCH-API-KEY```  Every event must contain your ```_userId``` (if this is not available, you can alternatively provide a ```_sessionId``` value also in ```_userId```).  JavaScript Fingerprinting module for capturing unique devices and tracking user interaction.  This script will identify unique devices with respect to the browser. For e.g., if chrome is opened in normal mode a unique device id is generated and this will be same if chrome is opened in incognito mode or reinstalled.  Paste the below script onto your webpage, just after the opening `<body>` tag. This script should be added to the page which is accessed externally by the user of your website. For e.g., If you want to track three different webpages then paste the below script onto each webpage, just after the opening `<body>` tag. This script should not be added onto internal tools or admin panels. ```   &lt;script id=\"thirdwatch\"     data-session-cookie-name=\"&lt;cookie_name&gt;\"     data-session-id-value=\"&lt;session_id&gt;\"     data-user-id=\"&lt;user_id&gt;\"     data-app-secret=\"&lt;app_secret&gt;\"     data-is-track-pageview=\"true\"&gt; (function() {         var loadDeviceJs = function() {         var element = document.createElement(\"script\");         element.async = 1;         element.src = \"https://cdn.thirdwatch.ai/tw.min.js\";         document.body.appendChild(element);         };         if (window.addEventListener) {              window.addEventListener(\"load\", loadDeviceJs, false);         } else if (window.attachEvent) {         window.attachEvent(\"onload\", loadDeviceJs);         }     })();   &lt;/script&gt; ``` * `data-session-cookie-name` -- The cookie name where you are saving the unique session id. We will pick the session id by reading its value from the cookie name. (Optional) * `data-session-id-value` -- In case you are not passing `data-session-cookie-name` than you can put session id directly in this parameter. In absence of both `data-session-cookie-name` and `data-session-id-value`, our system will generate a session Id. (Optional) * `data-user-id` -- Unique user id at your end. This can be email id or primary key in the database. In case of guest user, you can insert session Id here. * `data-app-secret` -- Unique App secret generated for you by Thirdwatch. * `data-is-track-pageview` -- If this is set to true, then the url on which this script is running will be sent to Thirdwatch, else the url will not be captured.   The Score API is use to get an up to date cutomer trust score after you have sent transaction event and order successful. This API will provide the riskiness score of the order with reasons. Some examples of when you may want to check the score are before:    - Before Shippement of a package   - Finalizing a money transfer   - Giving access to a prearranged vacation rental   - Sending voucher on mail  ```   https://api.thirdwatch.ai/neo/v1/score?api_key=<your api key>&order_id=<Order id> ```  According to Score you can decide to take action Approve or Reject. Orders with score more than 70 will consider as Riskey orders. We'll provide some reasons also in rules section.   ``` {   \"order_id\": \"OCT45671\",   \"user_id\": \"ajay_245\",   \"order_timestamp\": \"2017-05-09T09:40:45.717Z\",   \"score\": 82,   \"flag\": \"red\",     -\"reasons\": [     {         \"name\": \"_numOfFailedTransactions\",         \"display_name\": \"Number of failed transactions\",         \"flag\": \"green\",         \"value\": \"0\",         \"is_display\": true       },       {         \"name\": \"_accountAge\",         \"display_name\": \"Account age\",         \"flag\": \"red\",         \"value\": \"0\",         \"is_display\": true       },       {         \"name\": \"_numOfOrderSameIp\",         \"display_name\": \"Number of orders from same IP\",         \"flag\": \"red\",         \"value\": \"11\",         \"is_display\": true       }     ] } ```
 *
 * OpenAPI spec version: 0.0.1
 * 
 * Generated by: https://github.com/swagger-api/swagger-codegen.git
 * Swagger Codegen version: 2.3.1
 */

/**
 * NOTE: This class is auto generated by the swagger code generator program.
 * https://github.com/swagger-api/swagger-codegen
 * Do not edit the class manually.
 */

namespace ai\thirdwatch\Model;

use \ArrayAccess;
use \ai\thirdwatch\ObjectSerializer;

/**
 * AddToCart Class Doc Comment
 *
 * @category Class
 * @package  ai\thirdwatch
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class AddToCart implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'AddToCart';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        '_user_id' => 'string',
        '_session_id' => 'string',
        '_device_ip' => 'string',
        '_origin_timestamp' => 'string',
        '_item' => '\ai\thirdwatch\Model\Item',
        '_custom_info' => '\ai\thirdwatch\Model\CustomInfo'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        '_user_id' => null,
        '_session_id' => null,
        '_device_ip' => null,
        '_origin_timestamp' => null,
        '_item' => null,
        '_custom_info' => null
    ];

    /**
     * Array of property to type mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerTypes()
    {
        return self::$swaggerTypes;
    }

    /**
     * Array of property to format mappings. Used for (de)serialization
     *
     * @return array
     */
    public static function swaggerFormats()
    {
        return self::$swaggerFormats;
    }

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @var string[]
     */
    protected static $attributeMap = [
        '_user_id' => '_userId',
        '_session_id' => '_sessionId',
        '_device_ip' => '_deviceIp',
        '_origin_timestamp' => '_originTimestamp',
        '_item' => '_item',
        '_custom_info' => '_customInfo'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        '_user_id' => 'setUserId',
        '_session_id' => 'setSessionId',
        '_device_ip' => 'setDeviceIp',
        '_origin_timestamp' => 'setOriginTimestamp',
        '_item' => 'setItem',
        '_custom_info' => 'setCustomInfo'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        '_user_id' => 'getUserId',
        '_session_id' => 'getSessionId',
        '_device_ip' => 'getDeviceIp',
        '_origin_timestamp' => 'getOriginTimestamp',
        '_item' => 'getItem',
        '_custom_info' => 'getCustomInfo'
    ];

    /**
     * Array of attributes where the key is the local name,
     * and the value is the original name
     *
     * @return array
     */
    public static function attributeMap()
    {
        return self::$attributeMap;
    }

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @return array
     */
    public static function setters()
    {
        return self::$setters;
    }

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @return array
     */
    public static function getters()
    {
        return self::$getters;
    }

    /**
     * The original name of the model.
     *
     * @return string
     */
    public function getModelName()
    {
        return self::$swaggerModelName;
    }

    

    

    /**
     * Associative array for storing property values
     *
     * @var mixed[]
     */
    protected $container = [];

    /**
     * Constructor
     *
     * @param mixed[] $data Associated array of property values
     *                      initializing the model
     */
    public function __construct(array $data = null)
    {
        $this->container['_user_id'] = isset($data['_user_id']) ? $data['_user_id'] : null;
        $this->container['_session_id'] = isset($data['_session_id']) ? $data['_session_id'] : null;
        $this->container['_device_ip'] = isset($data['_device_ip']) ? $data['_device_ip'] : null;
        $this->container['_origin_timestamp'] = isset($data['_origin_timestamp']) ? $data['_origin_timestamp'] : null;
        $this->container['_item'] = isset($data['_item']) ? $data['_item'] : null;
        $this->container['_custom_info'] = isset($data['_custom_info']) ? $data['_custom_info'] : null;
    }

    /**
     * Show all the invalid properties with reasons.
     *
     * @return array invalid properties with reasons
     */
    public function listInvalidProperties()
    {
        $invalidProperties = [];

        return $invalidProperties;
    }

    /**
     * Validate all the properties in the model
     * return true if all passed
     *
     * @return bool True if all properties are valid
     */
    public function valid()
    {

        return true;
    }


    /**
     * Gets _user_id
     *
     * @return string
     */
    public function getUserId()
    {
        return $this->container['_user_id'];
    }

    /**
     * Sets _user_id
     *
     * @param string $_user_id The user's account ID according to your systems. Note that user IDs are case sensitive.
     *
     * @return $this
     */
    public function setUserId($_user_id)
    {
        $this->container['_user_id'] = $_user_id;

        return $this;
    }

    /**
     * Gets _session_id
     *
     * @return string
     */
    public function getSessionId()
    {
        return $this->container['_session_id'];
    }

    /**
     * Sets _session_id
     *
     * @param string $_session_id The user's current session ID, used to tie a user's action before and after login or account creation. Required if no user_id values is provided.
     *
     * @return $this
     */
    public function setSessionId($_session_id)
    {
        $this->container['_session_id'] = $_session_id;

        return $this;
    }

    /**
     * Gets _device_ip
     *
     * @return string
     */
    public function getDeviceIp()
    {
        return $this->container['_device_ip'];
    }

    /**
     * Sets _device_ip
     *
     * @param string $_device_ip IP address of the request made by the user. Recommended for historical backfills and customers with mobile apps.
     *
     * @return $this
     */
    public function setDeviceIp($_device_ip)
    {
        $this->container['_device_ip'] = $_device_ip;

        return $this;
    }

    /**
     * Gets _origin_timestamp
     *
     * @return string
     */
    public function getOriginTimestamp()
    {
        return $this->container['_origin_timestamp'];
    }

    /**
     * Sets _origin_timestamp
     *
     * @param string $_origin_timestamp Represents the time the event occured in your system. Send as a UNIX timestamp in milliseconds in string.
     *
     * @return $this
     */
    public function setOriginTimestamp($_origin_timestamp)
    {
        $this->container['_origin_timestamp'] = $_origin_timestamp;

        return $this;
    }

    /**
     * Gets _item
     *
     * @return \ai\thirdwatch\Model\Item
     */
    public function getItem()
    {
        return $this->container['_item'];
    }

    /**
     * Sets _item
     *
     * @param \ai\thirdwatch\Model\Item $_item _item
     *
     * @return $this
     */
    public function setItem($_item)
    {
        $this->container['_item'] = $_item;

        return $this;
    }

    /**
     * Gets _custom_info
     *
     * @return \ai\thirdwatch\Model\CustomInfo
     */
    public function getCustomInfo()
    {
        return $this->container['_custom_info'];
    }

    /**
     * Sets _custom_info
     *
     * @param \ai\thirdwatch\Model\CustomInfo $_custom_info _custom_info
     *
     * @return $this
     */
    public function setCustomInfo($_custom_info)
    {
        $this->container['_custom_info'] = $_custom_info;

        return $this;
    }
    /**
     * Returns true if offset exists. False otherwise.
     *
     * @param integer $offset Offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->container[$offset]);
    }

    /**
     * Gets offset.
     *
     * @param integer $offset Offset
     *
     * @return mixed
     */
    public function offsetGet($offset)
    {
        return isset($this->container[$offset]) ? $this->container[$offset] : null;
    }

    /**
     * Sets value based on offset.
     *
     * @param integer $offset Offset
     * @param mixed   $value  Value to be set
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->container[] = $value;
        } else {
            $this->container[$offset] = $value;
        }
    }

    /**
     * Unsets offset.
     *
     * @param integer $offset Offset
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->container[$offset]);
    }

    /**
     * Gets the string presentation of the object
     *
     * @return string
     */
    public function __toString()
    {
        if (defined('JSON_PRETTY_PRINT')) { // use JSON pretty print
            return json_encode(
                ObjectSerializer::sanitizeForSerialization($this),
                JSON_PRETTY_PRINT
            );
        }

        return json_encode(ObjectSerializer::sanitizeForSerialization($this));
    }
}


