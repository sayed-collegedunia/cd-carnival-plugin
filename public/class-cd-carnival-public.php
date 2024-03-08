<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://sayedakhtar.github.io
 * @since      1.0.0
 *
 * @package    Cd_Carnival
 * @subpackage Cd_Carnival/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cd_Carnival
 * @subpackage Cd_Carnival/public
 * @author     Sayed Akhtar <sayed.akhtar@collegedunia.com>
 */
class Cd_Carnival_Public
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $functions;

	private $generator;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->functions = new Cd_Carnival_Functions();
		$this->generator = new Cd_Carnival_Pdf_Generator();
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cd_Carnival_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cd_Carnival_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/cd-carnival-public.css', array(), $this->version, 'all');
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts()
	{

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cd_Carnival_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cd_Carnival_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/cd-carnival-public.js', array('jquery'), $this->version, false);
	}

	public function elementor_form_submission($record, $handler)
	{
		$form_name = $record->get_form_settings('form_name');
		$options = get_option($this->plugin_name);
		$lead_form_name = (isset($options['lead_form_name']) && !empty($options['lead_form_name'])) ? esc_attr($options['lead_form_name']) : '';
		$capture_update_page = (isset($options['capture_update']) && !empty($options['capture_update'])) ? esc_attr($options['capture_update']) : '';

		// Replace MY_FORM_NAME with the name you gave your form
		if (!empty($capture_update)) {
			return;
		}

		$raw_fields = $record->get('fields');
		$fields = [];
		foreach ($raw_fields as $id => $field) {
			$fields[$id] = $field['value'];
		}

		if ('visitor_number_form' == $form_name) {
			$meta = $record->get_form_meta(['page_url']);
			$pageUrl = $meta['page_url']['value'];
			$reg_no = $this->functions->getRegNumberFromUrl($pageUrl);
			$this->functions->updateLeadVisitors($reg_no, $fields['number_of_visitor']);
		} elseif ($lead_form_name == $form_name) {
			$fields["form_name"] = $form_name;
			$reg_no = $this->functions->insertLead($fields);
			$fields['registration'] = $reg_no;
			$updateLink = $this->functions->getUpdatePageLink($capture_update_page, $reg_no);
			$qrPath = $this->generator->generateQR($reg_no);
			$fields['pdfLink'] = $this->generator->generatePdf($fields['name'], $reg_no, $qrPath, $updateLink);
			$this->functions->sendSms($fields);
			$this->functions->updateUserTicketUrl($fields);
		}
	}


	public function register_custom_end_points()
	{
		add_rewrite_rule( 'mark-visitor/?$', 'index.php?mark_visitor=true', 'top' );
		add_rewrite_tag( '%my_custom_endpoint%', '([^&]+)' );
		flush_rewrite_rules();
	}

	function custom_end_points_query_vars( $query_vars ) {
		$query_vars[] = 'mark_visitor';
		return $query_vars;
	}
	
	function custom_endpoint_handler( $wp ) {
		if ( isset( $wp->query_vars['mark_visitor'] ) && $wp->query_vars['mark_visitor'] === 'true' ) {
			// Check if it's a POST request
			if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
				// Process POST request
				$data = json_decode( file_get_contents( 'php://input' ), true );

				$res = $this->functions->markVisitor($data['registration_number']);
	
				// Your processing logic here
				$response = $res;
			} else {
				// Invalid request method
				$response = array(
					'success' => false,
					'message' => 'Invalid request method. Only POST requests are allowed.',
				);
			}
	
			// Set headers and output JSON response
			header( 'Content-Type: application/json' );
			echo json_encode( $response );
			exit;
		}
	}

	function update_visitor_form_function($atts)
	{

		$args = shortcode_atts(
			array(
				'query_param'   => '',
			),
			$atts
		);

		$var = (strtolower($args['query_param']) != "") ? strtolower($args['query_param']) : 'reg_no';
		$value = isset($_GET[$var]) && !empty($_GET[$var]) ? $_GET[$var] : '';
		if (empty($value)) {
			return 'Invalid page params';
		}
		$html = '<input type="hidden" name="form_fields[registration_number]" id="form-field-registration_number" class="elementor-field elementor-size-sm  elementor-field-textual" value="' . $value . '"/>';
		return $html;
	}

}
