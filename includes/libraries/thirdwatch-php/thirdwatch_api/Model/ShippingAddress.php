<?php
/**
 * ShippingAddress
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
 * ShippingAddress Class Doc Comment
 *
 * @category Class
 * @description The Address field type represents a physical address. The value must be a nested object with the appropriate address subfields. We extract many geolocation features from these values. An address is represented as a nested JSON object.
 * @package  ai\thirdwatch
 * @author   Swagger Codegen team
 * @link     https://github.com/swagger-api/swagger-codegen
 */
class ShippingAddress implements ModelInterface, ArrayAccess
{
    const DISCRIMINATOR = null;

    /**
      * The original name of the model.
      *
      * @var string
      */
    protected static $swaggerModelName = 'ShippingAddress';

    /**
      * Array of property to type mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerTypes = [
        '_name' => 'string',
        '_phone' => 'string',
        '_address1' => 'string',
        '_address2' => 'string',
        '_city' => 'string',
        '_region' => 'string',
        '_country' => 'string',
        '_zipcode' => 'string',
        '_is_office_address' => 'bool',
        '_is_home_address' => 'bool'
    ];

    /**
      * Array of property to format mappings. Used for (de)serialization
      *
      * @var string[]
      */
    protected static $swaggerFormats = [
        '_name' => null,
        '_phone' => null,
        '_address1' => null,
        '_address2' => null,
        '_city' => null,
        '_region' => null,
        '_country' => null,
        '_zipcode' => null,
        '_is_office_address' => null,
        '_is_home_address' => null
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
        '_name' => '_name',
        '_phone' => '_phone',
        '_address1' => '_address1',
        '_address2' => '_address2',
        '_city' => '_city',
        '_region' => '_region',
        '_country' => '_country',
        '_zipcode' => '_zipcode',
        '_is_office_address' => '_isOfficeAddress',
        '_is_home_address' => '_isHomeAddress'
    ];

    /**
     * Array of attributes to setter functions (for deserialization of responses)
     *
     * @var string[]
     */
    protected static $setters = [
        '_name' => 'setName',
        '_phone' => 'setPhone',
        '_address1' => 'setAddress1',
        '_address2' => 'setAddress2',
        '_city' => 'setCity',
        '_region' => 'setRegion',
        '_country' => 'setCountry',
        '_zipcode' => 'setZipcode',
        '_is_office_address' => 'setIsOfficeAddress',
        '_is_home_address' => 'setIsHomeAddress'
    ];

    /**
     * Array of attributes to getter functions (for serialization of requests)
     *
     * @var string[]
     */
    protected static $getters = [
        '_name' => 'getName',
        '_phone' => 'getPhone',
        '_address1' => 'getAddress1',
        '_address2' => 'getAddress2',
        '_city' => 'getCity',
        '_region' => 'getRegion',
        '_country' => 'getCountry',
        '_zipcode' => 'getZipcode',
        '_is_office_address' => 'getIsOfficeAddress',
        '_is_home_address' => 'getIsHomeAddress'
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
        $this->container['_name'] = isset($data['_name']) ? $data['_name'] : null;
        $this->container['_phone'] = isset($data['_phone']) ? $data['_phone'] : null;
        $this->container['_address1'] = isset($data['_address1']) ? $data['_address1'] : null;
        $this->container['_address2'] = isset($data['_address2']) ? $data['_address2'] : null;
        $this->container['_city'] = isset($data['_city']) ? $data['_city'] : null;
        $this->container['_region'] = isset($data['_region']) ? $data['_region'] : null;
        $this->container['_country'] = isset($data['_country']) ? $data['_country'] : null;
        $this->container['_zipcode'] = isset($data['_zipcode']) ? $data['_zipcode'] : null;
        $this->container['_is_office_address'] = isset($data['_is_office_address']) ? $data['_is_office_address'] : null;
        $this->container['_is_home_address'] = isset($data['_is_home_address']) ? $data['_is_home_address'] : null;
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
     * Gets _name
     *
     * @return string
     */
    public function getName()
    {
        return $this->container['_name'];
    }

    /**
     * Sets _name
     *
     * @param string $_name Provide the full name associated with the address here. Concatenate first name and last name together if you collect them separately in your system.
     *
     * @return $this
     */
    public function setName($_name)
    {
        $this->container['_name'] = $_name;

        return $this;
    }

    /**
     * Gets _phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->container['_phone'];
    }

    /**
     * Sets _phone
     *
     * @param string $_phone The phone number associated with this address. Provide the phone number as a string starting with the country code. Use E.164 format or send in the standard national format of number's origin.
     *
     * @return $this
     */
    public function setPhone($_phone)
    {
        $this->container['_phone'] = $_phone;

        return $this;
    }

    /**
     * Gets _address1
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->container['_address1'];
    }

    /**
     * Sets _address1
     *
     * @param string $_address1 Address first line, e.g., \"C802 Nirvana Courtyard\".
     *
     * @return $this
     */
    public function setAddress1($_address1)
    {
        $this->container['_address1'] = $_address1;

        return $this;
    }

    /**
     * Gets _address2
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->container['_address2'];
    }

    /**
     * Sets _address2
     *
     * @param string $_address2 Address second line, e.g., \"Nirvana Country, Sector 50\".
     *
     * @return $this
     */
    public function setAddress2($_address2)
    {
        $this->container['_address2'] = $_address2;

        return $this;
    }

    /**
     * Gets _city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->container['_city'];
    }

    /**
     * Sets _city
     *
     * @param string $_city The city or town name, e.g., \"Gurgaon\" .
     *
     * @return $this
     */
    public function setCity($_city)
    {
        $this->container['_city'] = $_city;

        return $this;
    }

    /**
     * Gets _region
     *
     * @return string
     */
    public function getRegion()
    {
        return $this->container['_region'];
    }

    /**
     * Sets _region
     *
     * @param string $_region The region portion of the address. In the India, this corresponds to the state.
     *
     * @return $this
     */
    public function setRegion($_region)
    {
        $this->container['_region'] = $_region;

        return $this;
    }

    /**
     * Gets _country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->container['_country'];
    }

    /**
     * Sets _country
     *
     * @param string $_country The [ISO-3166](https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2) country code for the billing address, e.g., \"IN\" in case of India.
     *
     * @return $this
     */
    public function setCountry($_country)
    {
        $this->container['_country'] = $_country;

        return $this;
    }

    /**
     * Gets _zipcode
     *
     * @return string
     */
    public function getZipcode()
    {
        return $this->container['_zipcode'];
    }

    /**
     * Sets _zipcode
     *
     * @param string $_zipcode The postal code associated with the address, e.g., \"122002\".
     *
     * @return $this
     */
    public function setZipcode($_zipcode)
    {
        $this->container['_zipcode'] = $_zipcode;

        return $this;
    }

    /**
     * Gets _is_office_address
     *
     * @return bool
     */
    public function getIsOfficeAddress()
    {
        return $this->container['_is_office_address'];
    }

    /**
     * Sets _is_office_address
     *
     * @param bool $_is_office_address Is user chosen this address as office address.
     *
     * @return $this
     */
    public function setIsOfficeAddress($_is_office_address)
    {
        $this->container['_is_office_address'] = $_is_office_address;

        return $this;
    }

    /**
     * Gets _is_home_address
     *
     * @return bool
     */
    public function getIsHomeAddress()
    {
        return $this->container['_is_home_address'];
    }

    /**
     * Sets _is_home_address
     *
     * @param bool $_is_home_address Is user chosen this address as home address.
     *
     * @return $this
     */
    public function setIsHomeAddress($_is_home_address)
    {
        $this->container['_is_home_address'] = $_is_home_address;

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

