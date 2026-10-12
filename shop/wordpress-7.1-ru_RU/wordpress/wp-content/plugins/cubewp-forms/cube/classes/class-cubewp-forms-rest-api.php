<?php

/**
 * CubeWp Custom Form rest api for Form Data.
 *
 * @version 1.0
 * @package cubewp-forms/cube/classes
 */

if (!defined('ABSPATH')) {
	exit;
}

/**
 * CubeWp_Rest_API
 */
class CubeWp_Forms_Rest_API extends WP_REST_Controller
{

	//Query CWP_QUERY.
	const CWP_QUERY = 'cwp_query';

	public $namespace = '';

	/**
	 * 
	 * Constructor.
	 */
	public function __construct()
	{
		$this->namespace = 'cubewp-custom-form/v1';
		$this->register_routes();
	}

	/**
	 * Registers the routes for the objects of the controller.
	 *
	 * @see register_rest_route()
	 */
	public function register_routes()
	{
		register_rest_route(
			$this->namespace,
			'submit/',
			array(
				array(
					'methods'             => WP_REST_Server::READABLE,
					'callback'            => array( $this, 'cubewp_update_forms' ),
					'permission_callback' => array( $this, 'get_without_permission_check' ),
					'args'                => $this->get_render_params(),
				),
			)
		);
	}
/**
	 * Checks if a given request has permission to access content.
	 *
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return true|WP_Error True if the request has search access, WP_Error object otherwise.
	 */
	public function get_permission_check( $request ) {
		return current_user_can( 'edit_posts' );
	}
	public function get_without_permission_check( $request ) {
		return true;
	}

	/**
	 * Retrieves the query params for the search results.
	 *
	 * @return array Collection parameters.
	 */
	public function get_render_params()
	{
		$query_params  = parent::get_collection_params();

		$query_params[self::CWP_QUERY] = array(
			'description' => __('The Array with all data.', 'cubewp-forms'),
			'type'        => '',
		);
		return $query_params;
	}


	/**
	 * Retrieves cubewp Forms.
	 *
	 * @param WP_REST_Request $request Full details about the request.
	 * @return WP_REST_Response|WP_Error Response object on success, or WP_Error object on failure.
	 */

	public function cubewp_update_forms($request)
	{
		$CWP_QUERY = $request->get_param(self::CWP_QUERY);

		if (!empty($CWP_QUERY) && count($CWP_QUERY) > 0) {
			// Backup the original $_POST array
			//$original_post = $_POST;
			$CWP_QUERY['security_nonce'] = wp_create_nonce('cubewp_forms_submit');
			// Simulate the $_POST array with the received $CWP_QUERY data
			$_POST = $CWP_QUERY;
			
			// Call the form submission function
			$frontend_form = new CubeWp_Forms_Frontend_Custom_Form();
			$frontend_form->cubewp_submit_custom_form();

			// Restore the original $_POST array
			//$_POST = $original_post;
		}
	}
	 


	public static function init()
	{
		$CubeClass = __CLASS__;
		new $CubeClass;
	}
}
