<?php

/**
 * The internal functionality of the plugin including inserting/updataing db records, generating PDFs, sending SMS.
 *
 * @link       https://sayedakhtar.github.io
 * @since      1.0.0
 *
 * @package    Cd_Carnival
 * @subpackage Cd_Carnival/includes
 */



class Cd_Carnival_Functions
{

    /**
     * Loads $wpdb to the class accessor.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */

    protected $db;

    /**
     * The table name to be modified.
     *
     * @since    1.0.0
     * @access   private
     * @var      string    $plugin_name    The ID of this plugin.
     */
    protected $table_name;

    public function __construct()
    {
        global $wpdb;
        $this->db = $wpdb;
        $this->table_name = $wpdb->prefix . CD_CARNIVAL_DB_TABLE;
        if (!defined('WP_TIMEZONE')) {
            define('WP_TIMEZONE', 'Asia/Kolkata');
        }
    }

    public function insertLead($data)
    {
        extract($data);
        if (empty($name) || empty($phone) || empty($email)) {
            return;
        }
        $registration_number = $this->generate_unique_number(9, 'CD');
        $this->db->insert(
            $this->table_name,
            array(
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'city' => !empty($city) ? $city : null,
                'school' => !empty($School) ? $School : null,
                'course' => !empty($Course) ? $Course : null,
                'qualification' => !empty($Qualification) ? $Qualification : null,
                'ref_code' => !empty($referral) ? $referral : null,
                'registration' => $registration_number,
                'number_of_attendents' => 1,
                'visited' => 0,
            )
        );
        return $registration_number;
    }

    public function updateLeadVisitors($registration, $numberOfVisitors)
    {
        $result = $this->db->update($this->table_name, array('number_of_attendents' => $numberOfVisitors), array('registration' => $registration));
        error_log(json_encode($result));
        return $result;
    }

    public function updateUserTicketUrl($data)
    {
        extract($data);
        $result = $this->db->update($this->table_name, array('ticket_url' => $pdfLink), array('registration' => $registration));
        return $result;
    }

    public function markVisitor($registration)
    {
        $registration = sanitize_text_field($registration);
        $query = $this->db->prepare("SELECT visited FROM {$this->table_name} WHERE registration = %s", $registration);
        $result = $this->db->get_row($query);
        error_log(json_encode($result));
        if(empty($result)){
            return ['status' => false, 'message' =>'User with registration not found'];
        }
        if($result->visited){
            return ['status' => false, 'message' =>'User already marked as visited'];
        }
        $result = $this->db->update($this->table_name, array('visited' => 1), array('registration' => $registration));
        if ( $result === false ) {
            $error_message = $this->db->last_error;
            return ['status' => false, 'message' =>$error_message];
        } else {
            return ['status' => true, 'message' =>"User successfully marked"];
        }
        return json_encode($result);
    }

    public function getUpdatePageLink($page_id, $registration_number)
    {
        $page_url = get_permalink($page_id);
        $page_url = rtrim($page_url, '/');
        $page_url = $page_url . '?reg_no=' . $registration_number;
        return esc_url($page_url);
        error_log("Debug Log: " . $page_url);
    }

    public function sendSms($data)
    {
        extract($data);
        $campaign_name = "Mega Career Carnival";
        $payload = [[
            'name' => $name,
            'email' => $email,
            'phn' => $phone,
            'registration' => $registration,
        ]];

        $url = 'https://api.messagetextify.com/send-sms';
        $args = array(
            'headers'     => array(
                'apikey' => 'b5d1229f-e314-4627-bc2b-30666669fe1c',
                'Content-Type' => 'application/json',
            ),
            'body'        => json_encode(array(
                "payload" => $payload,
                "channelId" => "6220a40e7aecc061ae97e4ea",
                "message" => "Registration Successful! Get Counselling for Admissions in Top Universities/Colleges with Mega Career Carnival click on {{link}} to download your ticket Collegedunia.",
                "senderId" => "6220a9e7df691361cf255887",
                "campaignName" => $campaign_name.' '.$registration,
                "domainId" => "632c05026187054f2bba241a",
                "type" => "PROMO",
                "meta" => array(),
                "links" => array($pdfLink)
            )),
        );
        // error_log("\n============ SMS Debug LOGS ========== \n" . json_encode($args));
        // die();
        $response = wp_remote_post($url, $args);

        if (is_wp_error($response)) {
            $error_message = $response->get_error_message();
            error_log("\n============ SMS ERROR LOGS ========== \n" . json_encode($error_message));
            return true;
        } else {
            $response_body = wp_remote_retrieve_body($response);
            if($response_body['message'] == 'Error'){
                error_log("\n============ SMS ERROR LOGS ========== \n" . json_encode($response['errors']));
                return false;
            }
            error_log("\n============ SMS SUCCESS LOGS ========== \n" . json_encode($response_body));
            return true;
        }
    }

    private function generate_unique_number($nod = 9, $prefix = "")
    {
        $timestamp = time();
        $random_number = mt_rand(10000000000, 99999999999);
        $unique_number = $timestamp . $random_number;
        $unique_number = substr($unique_number, 0, $nod);
        return $prefix . $unique_number;
    }

    public function getRegNumberFromUrl($url)
	{
		$url_parts = parse_url($url);
		if (isset($url_parts['query'])) {
			parse_str($url_parts['query'], $query_params);
			if (isset($query_params['reg_no']) && !empty($query_params['reg_no'])) {
				// Get the value of 'reg_no' parameter
				$reg_no = $query_params['reg_no'];

				// Output the value of 'reg_no'
				return $reg_no;
			} 
		}
		else false; 
	}
}
