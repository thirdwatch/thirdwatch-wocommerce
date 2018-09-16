<?php
/**
 * Thirdwatch setup
 *
 * @package Thirdwatch
 * @since   3.2.0
 */

defined( 'ABSPATH' ) || exit;

defined( 'DS' ) or define( 'DS', DIRECTORY_SEPARATOR );
define( 'THIRDWATCH_ROOT', dirname( __DIR__ ) . DS );

/**
 * Main Thirdwatch Class.
 *
 * @class Thirdwatch
 */
final class Thirdwatch {

    protected static $_instance = null;
    private $order;

    private $namespace;
    private $enabled;
    private $api_key;
    private $approve_status;
    private $review_status;
    private $reject_status;
    private $fraud_message;
    private $debug_log;
    public $version = '1.0.0';

    /**
     * Main Thirdwatch Instance.
     *
     * Ensures only one instance of Thirdwatch is loaded or can be loaded.
     *
     * @since 2.1
     * @static
     * @see WC()
     * @return Thirdwatch - Main instance.
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * WooCommerce Constructor.
     */
    public function __construct() {

        add_action( 'rest_api_init', array($this, 'score_postback' ));
        add_action( 'rest_api_init', array($this, 'action_postback' ));
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    /**
    * Define Thirdwatch Constants.
    */
    private function define_constants() {
        $upload_dir = wp_upload_dir( null, false );
        $this->define( 'TW_ABSPATH', dirname( TW_PLUGIN_FILE ) . DS );
        $this->define( 'TW_PLUGIN_BASENAME', plugin_basename( TW_PLUGIN_FILE ) );
        $this->define( 'TW_VERSION', $this->version );
        $this->define( 'TW_DELIMITER', '|' );
        $this->define( 'TW_LOG_DIR', $upload_dir['basedir'] . '/wc-logs/' );
        $this->namespace			= 'woocommerce-thirdwatch';
        $this->enabled				= $this->get_setting( 'enabled' );
        $this->api_key				= $this->get_setting( 'api_key' );
        $this->approve_status		= $this->get_setting( 'approve_status' );
        $this->review_status		= $this->get_setting( 'review_status' );
        $this->reject_status		= $this->get_setting( 'reject_status' );
        $this->fraud_message		= $this->get_setting( 'fraud_message' );
        $this->debug_log			= $this->get_setting( 'debug_log' );
    }

    /**
     * Define constant if not already set.
     *
     * @param string      $name  Constant name.
     * @param string|bool $value Constant value.
     */
    private function define( $name, $value ) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }

    public function includes() {
        /**
         * Class autoloader.
         */
        include_once TW_ABSPATH . 'includes/class-tw-autoloader.php';
        include_once TW_ABSPATH . 'includes/class-tw-install.php';
        include_once TW_ABSPATH . 'includes/libraries/thirdwatch-php/autoload.php';
    }

    /**
     * Hook into actions and filters.
     *
     * @since 2.3
     */
    private function init_hooks() {
        register_activation_hook( TW_PLUGIN_FILE, array( 'TW_Install', 'install' ));
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
        add_action( 'wp_ajax_thirdwatch_woocommerce_admin_notice', array( $this, 'plugin_dismiss_admin_notice' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );

        // Hooks for WooCommerce
        add_filter( 'manage_shop_order_posts_columns', array( $this, 'add_column' ), 11 );
        add_action( 'manage_shop_order_posts_custom_column', array( $this, 'render_column' ), 3 );
        add_action( 'woocommerce_created_customer', array( $this, 'register' ), 99, 3 );
        add_action( 'wp_login', array( $this, 'login'), 99, 2 );
        add_action( 'woocommerce_thankyou', array($this, 'get_orders'), 99, 1);
        add_action( 'woocommerce_order_status_changed', array( $this, 'order_status_changed' ), 99, 3 );
    }

    function score_postback() {
        register_rest_route( 'thirdwatch/api', 'scorepostback', array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'score_postback_route'),
        ));
    }

    function action_postback() {
        register_rest_route( 'thirdwatch/api', 'actionpostback', array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => array($this, 'action_postback_route'),
        ));
    }

    public function score_postback_route( WP_REST_Request $request ) {
        global $wpdb;
        $dt = new DateTime();
        $this->write_debug_log("Scoreback Called");
        $response_json = $request->get_json_params();

        try {
            if ($response_json){
                $order_id = $response_json['order_id'];
                $flag = $response_json['flag'];
                $score = $response_json['score'];

                if ($flag ==  "red"){
                    $status = "FLAGGED";
                    $flag = "RED";
                }
                elseif ($flag == "green"){
                    $status = "APPROVED";
                    $flag = "GREEN";
                }
                else{
                    $status = "HOLD";
                    $flag = "";
                }
                $customers = $wpdb->update("wp_tw_orders", array("flag"=>$flag, "status" => $status,"score" => (string) $score , "date_modified"=>$dt->format('Y-m-d H:i:s')), array("order_id" => $order_id));
            }
            $this->order = wc_get_order( $order_id );

            if ( $flag ==  "RED") {
                if ( $this->review_status && $this->review_status != $this->order->get_status() ) {
                    $this->order->update_status( $this->review_status, __( '', $this->namespace ) );
                }
            }
            elseif ( $flag == "GREEN" ) {
                if ( $this->approve_status && $this->approve_status != $this->order->get_status() ) {
                    $this->order->update_status( $this->approve_status, __( '', $this->namespace ) );
                }
            }
        }
        catch (\Throwable $e) {
            $this->write_debug_log($e->getMessage());
        }
        return $this->send_response('200 OK', json_decode());
    }


    public function action_postback_route( WP_REST_Request $request ) {
        global $wpdb;
        $dt = new DateTime();
        $this->write_debug_log("Action Postback Called");
        $response_json = $request->get_json_params();

        try {
            if ($response_json){
                $order_id = $response_json['order_id'];
                $action_type = $response_json['action_type'];
                $action_message = $response_json['action_message'];
                if ($action_type ==  "approved"){
                    $action = "APPROVED";
                    $comment = $action_message;
                }
                elseif ($action_type == "declined"){
                    $action = "DECLINED";
                    $comment = $action_message;
                }
                else{
                    $action = "";
                    $comment = "";
                }
                $customers = $wpdb->update("wp_tw_orders", array("action"=>$action, "message" => $comment, "date_modified"=>$dt->format('Y-m-d H:i:s')), array("order_id" => $order_id));

                $this->order = wc_get_order( $order_id );

                if ( $action_type == "declined" ) {
                    if ( $this->reject_status && $this->reject_status != $this->order->get_status() ) {
                        $this->order->update_status( $this->reject_status, __( '', $this->namespace ) );
                    }
                }
                elseif ( $action_type ==  "approved" ) {
                    if ( $this->approve_status && $this->approve_status != $this->order->get_status() ) {
                        $this->order->update_status( $this->approve_status, __( '', $this->namespace ) );
                    }
                }
            }
        }
        catch (\Throwable $e) {
            $this->write_debug_log($e->getMessage());
        }
        return $this->send_response('200 OK', json_decode());
    }

    public function login( $user_login, $user ) {
        $config = \ai\thirdwatch\Configuration::getDefaultConfiguration()->setApiKey('X-THIRDWATCH-API-KEY', $this->api_key);
        $customerInfo = array();
        $sessionInfo = array();

        try{
            $customerInfo['_user_id'] = (string) $user->get('ID');
            $customerInfo['_session_id'] = (string) WC()->session->get_customer_id();
            $customerInfo['_device_ip'] = (string) $_SERVER['REMOTE_ADDR'];
            $customerInfo['_origin_timestamp'] = (string) (time() * 1000);
            $customerInfo['_login_status'] = "_success";

            $api_instance = new \ai\thirdwatch\Api\LoginApi(new GuzzleHttp\Client(), $config);
            $json = new \ai\thirdwatch\Model\Login($customerInfo);
        }
        catch (\Throwable $e) {
            $this->write_debug_log($e->getMessage());
        }

        try {
            $result = $api_instance->login($json);
        }
        catch (\Throwable $e) {
            $this->write_debug_log($e->getMessage());
        }

        try{
            $sessionInfo['_user_id'] = (string) $user->get('ID');
            $sessionInfo['_session_id'] = (string) WC()->session->get_customer_id();
            $api_instance2 = new \ai\thirdwatch\Api\LinkSessionToUserApi(new GuzzleHttp\Client(), $config);
            $json2 = new \ai\thirdwatch\Model\LinkSessionToUser($sessionInfo);
            $result2 = $api_instance2->linkSessionToUser($json2);
        }
        catch (\Throwable $e) {
            $this->write_debug_log($e->getMessage());
        }
    }

    public function register($customer_id, $new_customer_data, $password_generated){
        $config = \ai\thirdwatch\Configuration::getDefaultConfiguration()->setApiKey('X-THIRDWATCH-API-KEY', $this->api_key);
        $customerInfo = array();
        $sessionInfo = array();

        try{
            $customerInfo['_user_id'] = (string) $customer_id;
            $customerInfo['_session_id'] = (string) WC()->session->get_customer_id();
            $customerInfo['_device_ip'] = (string) $_SERVER['REMOTE_ADDR'];
            $customerInfo['_origin_timestamp'] = (string) (time() * 1000);
            $customerInfo['_user_email'] = (string) $new_customer_data['user_email'];
            $customerInfo['_account_status'] = '_active';

            $api_instance = new \ai\thirdwatch\Api\CreateAccountApi(new GuzzleHttp\Client(), $config);
            $json = new \ai\thirdwatch\Model\CreateAccount($customerInfo);
            $result = $api_instance->createAccount($json);
        }
        catch (\Throwable $e) {
            $this->write_debug_log($e->getMessage());
        }

        try{
            $sessionInfo['_user_id'] = (string) $customer_id;
            $sessionInfo['_session_id'] = (string) WC()->session->get_customer_id();
            $api_instance2 = new \ai\thirdwatch\Api\LinkSessionToUserApi(new GuzzleHttp\Client(), $config);
            $json2 = new \ai\thirdwatch\Model\LinkSessionToUser($sessionInfo);
            $result2 = $api_instance2->linkSessionToUser($json2);
        }
        catch (\Throwable $e) {
            $this->write_debug_log($e->getMessage());
        }
    }

    /** Response Handler
     *	This sends a JSON response to the browser
     */
    protected function send_response($msg, $pugs = ''){
        $response['message'] = $msg;
        if($pugs)
            $response['pugs'] = $pugs;
        header('content-type: application/json; charset=utf-8');
        echo json_encode($response)."\n";
        exit;
    }

    public function get_orders($order_id){
        $this->order = wc_get_order( $order_id );
        $this->tw_order_transaction();
    }

    public function order_status_changed($order_id, $old_status, $new_status){
        $config = \ai\thirdwatch\Configuration::getDefaultConfiguration()->setApiKey('X-THIRDWATCH-API-KEY', $this->api_key);
        $this->order = wc_get_order( $order_id );
        global $wpdb;
        $tablename = $wpdb->prefix.'tw_orders';
        $result = $wpdb->get_results ( "SELECT * FROM  ".$tablename ." WHERE order_id = '".$order_id."'" );
        $orderInfo = array();

        if ($result){
            try{
                $orderInfo['_order_id'] = (string) $this->order->get_order_number();
                $orderInfo['_order_status'] = (string) "_wo_".$new_status;
                $api_instance = new \ai\thirdwatch\Api\OrderStatusApi(new GuzzleHttp\Client(), $config);
                $json = new \ai\thirdwatch\Model\OrderStatus($orderInfo);
                $result2 = $api_instance->orderStatus($json);
            }
            catch (\Throwable $e) {
                $this->write_debug_log($e->getMessage());
            }
        }
    }

    public function tw_order_transaction(){
        $isPrepaid = false;
        $ip = $_SERVER['REMOTE_ADDR'];

        if (($this->order->get_payment_method() == "cod") || ($this->order->get_payment_method() == "robu_cod")){
            $isPrepaid = false;
        }
        else {
            $isPrepaid = true;
        }

        $items = $this->order->get_items();
        $lineItems = array();

        foreach ( $items as $key => $value ) {
            $lineItemData = array();
            $lineItemData['_price'] = (string) $value->get_total();
            $lineItemData['_quantity'] = intval($value->get_quantity());
            $lineItemData['_product_title'] = (string) $value->get_name();
            $lineItemData['_item_id'] = (string) $value->get_product_id();
            $lineItemData['_currency_code'] = (string)"INR";
            $lineItemData['_country'] = (string)"IN";
            $itemJson = new \ai\thirdwatch\Model\Item($lineItemData);
            $lineItems[] = $itemJson;
        }

        $config = \ai\thirdwatch\Configuration::getDefaultConfiguration()->setApiKey('X-THIRDWATCH-API-KEY', $this->api_key);
        $orderData = array();

        if (is_user_logged_in()){
            $orderData['_user_id'] = (string) get_current_user_id();
        }
        else {
            $orderData['_session_id'] = (string) WC()->session->get_customer_id();
        }

        $orderData['_device_ip'] = (string) $ip;
        $orderData['_origin_timestamp'] = (string) ($this->order->get_date_created()->getTimestamp() * 1000);
        $orderData['_order_id'] = (string) $this->order->get_order_number();
        $orderData['_user_email'] = (string) ($this->order->get_billing_email() ) ? $this->order->get_billing_email() : "";
        $orderData['_amount'] = (string) $this->order->get_total();
        $orderData['_currency_code'] = (string) $this->order->get_order_currency();
        $orderData['_is_pre_paid'] = $isPrepaid;

        $addrArray = array();
        $addrArray['_name'] = $this->order->get_billing_first_name() . " ". $this->order->get_billing_last_name();
        $addrArray['_address1'] = $this->order->get_billing_address_1();
        $addrArray['_address2'] = $this->order->get_billing_address_2();
        $addrArray['_city'] = $this->order->get_billing_city();
        $addrArray['_country'] = $this->order->get_billing_country();
        $addrArray['_region'] = $this->order->get_billing_state();
        $addrArray['_zipcode'] = $this->order->get_billing_postcode();
        $addrArray['_phone'] =$this->order->get_billing_phone();

        $addrArray2 = array();
        $addrArray2['_name'] = $this->order->get_shipping_first_name() . " ". $this->order->get_shipping_last_name();
        $addrArray2['_address1'] = $this->order->get_shipping_address_1();
        $addrArray2['_address2'] = $this->order->get_shipping_address_2();
        $addrArray2['_city'] = $this->order->get_shipping_city();
        $addrArray2['_country'] = $this->order->get_shipping_country();
        $addrArray2['_region'] = $this->order->get_shipping_state();
        $addrArray2['_zipcode'] = $this->order->get_shipping_postcode();
        $addrArray2['_phone'] =$this->order->get_billing_phone();


        if ($this->order->has_shipping_address()){
            $shipping_json = new \ai\thirdwatch\Model\ShippingAddress($addrArray2);
        }
        else {
            $shipping_json = new \ai\thirdwatch\Model\ShippingAddress($addrArray);
        }

        $billing_json = new \ai\thirdwatch\Model\BillingAddress($addrArray);
        $orderData['_billing_address'] = $billing_json;
        $orderData['_shipping_address'] = $shipping_json;

        $paymentData = array();
        $paymentData['_payment_type'] = (string) $this->order->get_payment_method();
        $paymentData['_amount'] = (string) $this->order->get_total();
        $paymentData['_currency_code'] = (string) "INR";
        $paymentData['_payment_gateway'] = (string) $this->order->get_payment_method_title();
        $paymentData['_accountName'] = (string) $this->order->get_billing_first_name() . " ". $this->order->get_billing_last_name();
        $paymentJson = new \ai\thirdwatch\Model\PaymentMethod($paymentData);

        $orderData['_items'] = $lineItems;
        $orderData['_payment_methods'] = array($paymentJson);

        try {
            $api_instance = new \ai\thirdwatch\Api\CreateOrderApi(new GuzzleHttp\Client(), $config);
            $json = new \ai\thirdwatch\Model\CreateOrder($orderData);
            $result = $api_instance->createOrder($json);
        }
        catch (\Throwable $e) {
            $this->write_debug_log($e->getMessage());
        }

        $txnData = array();
        if (is_user_logged_in()){
            $txnData['_user_id'] = (string) get_current_user_id();
        }
        else {
            $txnData['_session_id'] = (string) WC()->session->get_customer_id();
        }

        $txnData['_device_ip'] = (string) $ip;
        $txnData['_origin_timestamp'] = (string) ($this->order->get_date_created()->getTimestamp() * 1000);
        $txnData['_order_id'] = (string) $this->order->get_order_number();
        $txnData['_user_email'] = (string) ($this->order->get_billing_email() ) ? $this->order->get_billing_email() : "";
        $txnData['_amount'] = (string) $this->order->get_total();
        $txnData['_currency_code'] = (string) 'INR';
        $txnData['_billing_address'] = $billing_json;
        $txnData['_shipping_address'] = $shipping_json;
        $txnData['_items'] = $lineItems;
        $txnData['_payment_method'] = $paymentJson;
        $txnData['_transaction_id'] = (string) $this->order->get_order_number();
        $txnData['_transaction_type'] = '_sale';
        $txnData['_transaction_status'] = '_success';

        try {
            $api_instance = new \ai\thirdwatch\Api\TransactionApi(new GuzzleHttp\Client(), $config);
            $jsonTxn = new \ai\thirdwatch\Model\Transaction($txnData);
            $result = $api_instance->transaction($jsonTxn);
        }
        catch (\Throwable $e) {
            $this->write_debug_log($e->getMessage());
        }

        $dt = new DateTime();

        global $wpdb;
        $customers = $wpdb->insert("wp_tw_orders", array("order_id"=>$this->order->get_order_number(), "status" => "HOLD", "date_created"=> $dt->format('Y-m-d H:i:s'), "date_modified"=>$dt->format('Y-m-d H:i:s')));
    }

    /**
     * Add risk score column to order list.
     */
    public function add_column( $columns ) {
        if ( $this->enabled != 'yes' ) {
            return $columns;
        }

        $columns = array_merge( array_slice( $columns, 0, 5 ), array( 'thirdwatch_score' => 'Thirdwatch Status' ), array_slice( $columns, 5 ) );
        return $columns;
    }

    /**
     * Fill in Thirdwatch score into risk score column.
     */
    public function render_column( $column ) {
        if ( $this->enabled != 'yes' ) {
            return;
        }

        if ( $column != 'thirdwatch_score' ) {
            return;
        }

        global $post;
        global $wpdb;
        $tablename = $wpdb->prefix.'tw_orders';
        $result = $wpdb->get_results ( "SELECT * FROM  ".$tablename ." WHERE order_id = '".$post->ID."'" );
        foreach ( $result as $page )
        {
            echo "Status: ". $page->status.'<br/>';
            echo "Flag: ". $page->flag.'<br/>';
            echo "Action: ". $page->action.'<br/>';
            echo "Message: ". $page->message.'<br/>';
        }
    }

    /**
     * Write to debug log to record details of process.
     */
    public function write_debug_log($message) {
        if ( $this->debug_log != 'yes' ) {
            return;
        }
        if ( is_array( $message ) || is_object( $message ) ) {
            file_put_contents( THIRDWATCH_ROOT . 'debug.log', gmdate('Y-m-d H:i:s') . "\t" . print_r( $message, true ) . "\n", FILE_APPEND );
        } else {
            file_put_contents( THIRDWATCH_ROOT . 'debug.log', gmdate('Y-m-d H:i:s') . "\t" . $message . "\n", FILE_APPEND );
        }
    }

    /**
     * Get plugin settings.
     */
    private function get_setting( $key ) {
        return get_option( 'wc_settings_woocommerce-thirdwatch_' . $key );
    }

    /**
     * Includes required scripts and styles.
     */
    public function admin_enqueue_scripts( $hook ) {
        if ( is_admin() ) {
            wp_enqueue_script( 'thirdwatch_woocommerce_admin_script', plugins_url( '/assets/js/script.js', TW_PLUGIN_FILE ), array( 'jquery' ), '1.0', true );
        }

        if ( is_admin() && get_user_meta( get_current_user_id(), 'thirdwatch_woocommerce_admin_notice', true ) !== 'dismissed' ) {
            wp_localize_script( 'thirdwatch_woocommerce_admin_script', 'thirdwatch_woocommerce_admin', array( 'thirdwatch_woocommerce_admin_nonce' => wp_create_nonce( 'thirdwatch_woocommerce_admin_nonce' ), ) );
        }

        wp_enqueue_style( 'thirdwatch_woocommerce_admin_menu_styles', untrailingslashit( plugins_url( '/', TW_PLUGIN_FILE ) ) . '/assets/css/style.css', array() );

        if ( $hook != 'toplevel_page_woocommerce-thirdwatch ' ) {
            return;
        }
    }

    /**
     * Add notification in dashboard.
     */
    public function admin_notices() {
        if ( get_user_meta( get_current_user_id(), 'thirdwatch_woocommerce_admin_notice', true ) === 'dismissed' ) {
            return;
        }

        $current_screen = get_current_screen();

        if ( 'plugins' == $current_screen->parent_base ) {
            if ( ! $this->api_key ) {
                echo '
                <div id="thirdwatch-woocommerce-notice" class="error notice is-dismissible">
                    <p>
                        ' . __( 'Thirdwatch setup is not complete. Please go to <a href="' . admin_url( 'admin.php?page=woocommerce-thirdwatch' ) . '">setting page</a> to enter your API key.', $this->namespace ) . '
                    </p>
                </div>
                ';
            }
        }
    }

    /**
     *  Dismiss the admin notice.
     */
    function plugin_dismiss_admin_notice() {
        if ( ! isset( $_POST['thirdwatch_woocommerce_admin_nonce'] ) || ! wp_verify_nonce( $_POST['thirdwatch_woocommerce_admin_nonce'], 'thirdwatch_woocommerce_admin_nonce' ) ) {
            wp_die();
        }
        update_user_meta( get_current_user_id(), 'thirdwatch-woocommerce-notice', 'dismissed' );
    }

        /**
     * Admin menu.
     */
    public function admin_menu() {
        add_menu_page( 'Thirdwatch', 'Thirdwatch', 'manage_options', 'woocommerce-thirdwatch', array( $this, 'settings_page' ), 'dashicons-admin-thirdwatch', 30 );
    }

    private function update_setting( $key, $value = null ) {
        return update_option( 'wc_settings_woocommerce-thirdwatch_' . $key, $value );
    }

    /**
     * Settings page.
     */
    public function settings_page() {
        if ( !is_admin() ) {
            $this->write_debug_log( 'Not logged in as administrator. Settings page will not be shown.' );
            return;
        }

        $form_status = '';
        $wc_order_statuses = array();

        if ( ! tw_is_osm_active() ) {
            $wc_order_statuses = array(
                ''              => 'No Status Change',
                'wc-pending'    => 'Pending Payment',
                'wc-processing' => 'Processing',
                'wc-on-hold'    => 'On Hold',
                'wc-completed'  => 'Completed',
                'wc-cancelled'  => 'Cancelled',
                'wc-refunded'   => 'Refunded',
                'wc-failed'     => 'Failed',
            );
        }
        else {
            global $wpdb;
            $tablename = $wpdb->prefix.'posts';
            $result = $wpdb->get_results ( "SELECT post_title, post_name FROM  ".$tablename ." WHERE post_type = 'wc_order_status' and post_status = 'publish'" );
            foreach ( $result as $value){
                $wc_order_statuses[$value->post_name] = $value->post_title;
            }
        }

        $enable_wc_tw = ( isset( $_POST['submit'] ) && isset( $_POST['enable_wc_tw'] ) ) ? 'yes' : ( ( ( isset( $_POST['submit'] ) && !isset( $_POST['enable_wc_tw'] ) ) ) ? 'no' : $this->get_setting( 'enabled' ) );

        $api_key = ( isset( $_POST['api_key'] ) ) ? $_POST['api_key'] : $this->get_setting( 'api_key' );

        $approve_status = ( isset( $_POST['approve_status'] ) ) ? $_POST['approve_status'] : $this->get_setting( 'approve_status' );
        $review_status = ( isset( $_POST['review_status'] ) ) ? $_POST['review_status'] : $this->get_setting( 'review_status' );
        $reject_status = ( isset( $_POST['reject_status'] ) ) ? $_POST['reject_status'] : $this->get_setting( 'reject_status' );
        $fraud_message = ( isset( $_POST['fraud_message'] ) ) ? $_POST['fraud_message'] : $this->get_setting( 'fraud_message' );

        $enable_wc_tw_debug_log = ( isset( $_POST['submit'] ) && isset( $_POST['enable_wc_tw_debug_log'] ) ) ? 'yes' : ( ( ( isset( $_POST['submit'] ) && !isset( $_POST['enable_wc_tw_debug_log'] ) ) ) ? 'no' : $this->get_setting( 'debug_log' ) );

        if ( isset( $_POST['submit'] ) ) {
            if ( empty( $form_status ) ) {
                $this->update_setting( 'enabled', $enable_wc_tw );
                $this->update_setting( 'api_key', $api_key );
                $this->update_setting( 'approve_status', $approve_status );
                $this->update_setting( 'review_status', $review_status );
                $this->update_setting( 'reject_status', $reject_status );
                $this->update_setting( 'fraud_message', $fraud_message );
                $this->update_setting( 'debug_log', $enable_wc_tw_debug_log );

                $form_status = '<div id="message" class="updated"><p>Changes saved.</p></div>';

                $url = site_url();
                $actionPostback = $url . "/wp-json/thirdwatch/api/actionpostback/";
                $scorePostback = $url . "/wp-json/thirdwatch/api/scorepostback/";

                $jsonRequest = array(
                    'score_postback'=>$scorePostback,
                    'action_postback'=>$actionPostback,
                    'secret'=>$api_key
                );
                $response = wp_remote_post("https://staging.thirdwatch.co/neo/v1/addpostbackurl/", array(
                    'method' => 'POST',
                    'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
                    'httpversion' => '1.0',
                    'sslverify' => false,
                    'body' => json_encode($jsonRequest)
                ));
            }
        }

        if ( isset( $_POST['purge'] ) ) {
            global $wpdb;
            $wpdb->query('DELETE FROM ' . $wpdb->prefix . 'postmeta WHERE meta_key LIKE "%thirdwatch%"');
            $form_status = '<div id="message" class="updated"><p>All data have been deleted.</p></div>';
        }

        echo '
        <div class="wrap">
            <h1>Thirdwatch for WooCommerce</h1>

            ' . $form_status . '

            <form id="form_settings" method="post" novalidate="novalidate">
                <table class="form-table">
                    <tr>
                        <td colspan=2>
                            If you would like to learn more about the setup process, please visit <a href="https://www.thirdwatch.ai" target="_blank">Thirdwatch for WooCommerce</a>.
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for="enable_wc_tw">Enable Thirdwatch Validation</label>
                        </th>
                        <td>
                            <input type="checkbox" name="enable_wc_tw" id="enable_wc_tw"' . ( ( $enable_wc_tw == 'yes' ) ? ' checked' : '' ) . '>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="api_key">API Key</label>
                        </th>
                        <td>
                            <input type="text" name="api_key" id="api_key" maxlength="32" value="' . $api_key . '" class="regular-text code" />
                            <p class="description">
                                You can sign up for a free API key at <a href="https://www.thirdwatch.ai/https://www.thirdwatch.ai/pricing.html?utm_source=module&utm_medium=banner&utm_term=woocommerce&utm_campaign=module%20banner" target="_blank">Thirdwatch</a>.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="approve_status">Approve Status</label>
                        </th>
                        <td>
                            <select name="approve_status" id="approve_status">';

        foreach ( $wc_order_statuses as $key => $status ) {
            echo '
                                <option value="' . $key . '"' . ( ( $approve_status == $key ) ? ' selected' : '' ) . '> ' . $status . '</option>';
        }

        echo '
                            </select>
                            <p class="description">
                                Change order status when order has been approved by Thirdwatch, or Thirdwatch <strong>Approve</strong> button has been pressed in order details page.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="review_status">Review Status</label>
                        </th>
                        <td>
                            <select name="review_status" id="review_status">';

        foreach ( $wc_order_statuses as $key => $status ) {
            echo '
                                <option value="' . $key . '"' . ( ( $review_status == $key ) ? ' selected' : '' ) . '> ' . $status . '</option>';
        }

        echo '
                            </select>
                            <p class="description">
                                Change order status when order has been marked as <strong>FLAGGED</strong> by Thirdwatch.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="reject_status">Reject Status</label>
                        </th>
                        <td>
                            <select name="reject_status" id="reject_status">';

        foreach ( $wc_order_statuses as $key => $status ) {
            echo '
                                <option value="' . $key . '"' . ( ( $reject_status == $key ) ? ' selected' : '' ) . '> ' . $status . '</option>';
        }

        echo '
                            </select>
                            <p class="description">
                                Change order status when order has been rejected by Thirdwatch, or Thirdwatch <strong>Decline</strong> button has been pressed in order details page.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="fraud_message">Fraud Message</label>
                        </th>
                        <td>
                            <textarea name="fraud_message" id="fraud_message" class="large-text" rows="3">' . $fraud_message . '</textarea>
                            <p class="description">
                                Display this messasge to customer if the order failed the validation (<strong>REJECT</strong> case).
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="enable_wc_tw_debug_log">Enable Debug Log for development purposes</label>
                        </th>
                        <td>
                            <input type="checkbox" name="enable_wc_tw_debug_log" id="enable_wc_tw_debug_log"' . ( ( $enable_wc_tw_debug_log == 'yes' ) ? ' checked' : '' ) . '>
                        </td>
                    </tr>

                </table>

                <p class="submit">
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes" />
                </p>
            </form>

            <p>
                <form id="form-purge" method="post">
                    <input type="hidden" name="purge" value="true">
                    <p>Remove <strong>all Thirdwatch for WooCommerce data</strong> from storage.</p>
                    <input type="button" name="button" id="button-purge" class="button button-primary" value="Delete All Data" />
                </form>
            </p>

        </div>';
    }
}