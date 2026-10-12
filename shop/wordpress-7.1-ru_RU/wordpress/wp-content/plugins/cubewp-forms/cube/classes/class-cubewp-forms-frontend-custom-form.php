<?php

/**
 * Post Type's frontend forms shortcode.
 *
 * @package cubewp-addon-forms/cube/classes
 * @version 1.0
 * 
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * CubeWp_Frontend_Post_Types_Form
 */
class CubeWp_Forms_Frontend_Custom_Form
{

    public $custom_fields;
    private $wp_default_fields;
    private $form_edit_class;

    public function __construct()
    {
		global $cwpOptions;
        add_shortcode('cwpCustomForm', array($this, 'frontend_form'));
        add_action('wp_ajax_cubewp_submit_custom_form', array($this, 'cubewp_submit_custom_form'));
        add_action('wp_ajax_nopriv_cubewp_submit_custom_form', array($this, 'cubewp_submit_custom_form'));
		if ( isset( $cwpOptions['cubewp_forms_mailchimp'] ) && $cwpOptions['cubewp_forms_mailchimp'] == '1' ) {
			add_action('cubewp_submit_custom_form_after', array($this, 'cubewp_submit_custom_form_after_callback') , 10 , 2);
		}
        $this->custom_fields =  CWP()->get_custom_fields('custom_forms');
    }

    /**
     * Method repeating_field_form_name
     *
     * @param array $args
     *
     * @return string
     * @since  1.0.0
     */
    public function repeating_field_form_name($args = array())
    {
        $args['custom_name'] = str_replace("cwp_user_form[cwp_meta]", "cwp_custom_form[fields]", $args['custom_name']);
        return $args;
    }
    /**
     * Method frontend_form
     *
     * @param array $params
     * @param null $content
     *
     * @return string
     * @since  1.0.0
     */
    public function frontend_form($params = array(), $content = null)
    {
        // default parameters
        extract(
            shortcode_atts(array(
                'form_id' => 0,
            ), $params)
        );
        $form_login_only = get_post_meta($form_id, '_cwp_group_login', true);
        if ($form_login_only == 1) {
            if (!is_user_logged_in()) {
                return cwp_alert_ui("You Must Login For Submission.", 'info');
            }
        }

        CubeWp_Enqueue::enqueue_style('select2');
        CubeWp_Enqueue::enqueue_style('frontend-fields');
        CubeWp_Enqueue::enqueue_style('cwp-timepicker');
        CubeWp_Enqueue::enqueue_style('cubewp-frontend-forms');

        CubeWp_Enqueue::enqueue_script('select2');
        CubeWp_Enqueue::enqueue_script('cwp-timepicker');
        CubeWp_Enqueue::enqueue_script('jquery-ui-datepicker');
        CubeWp_Enqueue::enqueue_script('cwp-form-validation');
        CubeWp_Enqueue::enqueue_script('cubewp-custom-form-submit');
        CubeWp_Enqueue::enqueue_script('cwp-frontend-fields');

        $cwpform_custom =  get_post_meta($form_id, '_cwp_group_fields', true);
        $form_fields  =  !empty($cwpform_custom) ? explode(",", $cwpform_custom) : array();
        if (empty($form_fields) || count($form_fields) == 0) {
            return cwp_alert_ui('Sorry! No fields available for this form.', 'info');
        }
        $section_data = array();
        $recaptcha_meta           =   get_post_meta($form_id, '_cwp_group_recaptcha', true);

        $submit_form_id           =   get_post_meta($form_id, '_cwp_group_form_id', true);
        $submit_form_id           =   !empty($submit_form_id) ? $submit_form_id : 'cwp-from-' . $form_id;

        
        $form_style      =   get_post_meta($form_id, '_cwp_group_style', true);
        $form_style      =   !empty($form_style) ? $form_style  : '';
		$form_class               =   'cwp-user-form-submit '.$form_style;
		
        $submit_button_title      =   get_post_meta($form_id, '_cwp_group_button_text', true);
        $submit_button_title      =   !empty($submit_button_title) ? $submit_button_title  : esc_html__("Submit", "cubewp-forms");

        $submit_button_width      =   get_post_meta($form_id, '_cwp_group_button_width', true);
        $submit_button_width      =   !empty($submit_button_width) ? 'style="width:' . $submit_button_width . '"' : '';

        $submit_button_position   =   get_post_meta($form_id, '_cwp_group_button_position', true);
        $submit_button_position   =   !empty($submit_button_position) ? ' position-' . $submit_button_position : '';

        $submit_button_class      =   get_post_meta($form_id, '_cwp_group_button_class', true);
        $submit_button_class      =   !empty($submit_button_class) ? $submit_button_class . ' cwp-from-submit' . $submit_button_position : 'cwp-from-submit ' . $submit_button_position;

        add_filter('cubewp/frontend/post/repeating_field/args', array($this, 'repeating_field_form_name'));

        $single_input = is_single() || is_page() ? '<input type="hidden" value="' . get_the_ID() . '" name="cwp_custom_form[single_post]" >' : '';
        $author_input = '';
        if (is_author()) {
            $author = get_queried_object();
            $author_id = isset($author->ID) ? absint($author->ID) : 0;
            if (!empty($author_id)) {
                $author_input = '<input type="hidden" value="' . esc_attr($author_id) . '" name="cwp_custom_form[author_user]" >';
            }
        }

        $output = '<div class="cwp-frontend-form-container">
        <form method="post" id="' . esc_attr($submit_form_id) . '" class="cwp-custom-form ' . esc_attr($form_class) . '" action="" enctype="multipart/form-data">
        <input type="hidden" name="cwp_custom_form[form_id]" value="' . esc_attr($form_id) . '">
        <input type="hidden" name="cwp_custom_form[form_data_id]" value="' . uniqid(wp_rand()) . '">
        ' . $single_input . '
        ' . $author_input . '
        <div class="tab-content">';
        $section_data['fields'] = $form_fields;
        $output .= $this->frontend_form_section($section_data, $form_id);
        $output .= '</div>';
        $recaptcha = !empty($recaptcha_meta) ? $recaptcha_meta : 'disabled';
        $output .= CubeWp_Frontend_Recaptcha::cubewp_captcha_form_attributes($recaptcha);
        $submitBTN = '<div class="cwp-form-submit-container"><input ' . $submit_button_width . ' class="' . esc_attr($submit_button_class) . '" type="submit" value="' . esc_attr($submit_button_title) . '"></div>';
        $output .= apply_filters("cubewp/frontend/form/{$form_id}/button", $submitBTN, $submit_button_title, $submit_button_class);
        $output .= '</form>
        </div>';

        $args = array(
            'form_fields'  =>   $form_fields,
            'form_id'    =>   $form_id,
        );
        $output = apply_filters("cubewp/frontend/form/{$form_id}", $output, $args);
        return $output;
    }

    /**
     * Method frontend_form_section
     *
     * @param array $section_data
     * @param string $type
     *
     * @return string
     * @since  1.0.0
     */
    public function frontend_form_section($section_data = array(), $type = '')
    {
        extract(
            shortcode_atts(array(
                'fields'              =>  '',
            ), $section_data)
        );
        $form_header = get_post_meta($type, '_cwp_group_display', true);
        $section_title       = get_post_field('post_title', $type);
        $section_description     = get_post_field('post_content', $type);
        $output  = '<div class="cwp-frontend-section-container">';
        if ($form_header == 1) {
            $output .= '<div class="cwp-frontend-section-heading-container">';
            if (isset($section_title) && $section_title != '') {
                $output .= '<h2>' . esc_attr($section_title) . '</h2>';
            }
            if (isset($section_description) && $section_description != '') {
                $output .= '<p>' . apply_filters('cubewp_forms/the_content', $section_description) . '</p>';
            }
            $output .= '</div>';
        }
        if (isset($section_data['fields']) && !empty($section_data['fields'])) {
            $output .= '<div class="cwp-frontend-section-content-container">';
            $output .= $this->fields($fields);
            $output .= '</div>';
        }
        $output .= '</div>';

        return $output;
    }

    /**
     * Method fields
     *
     * @param array $fields
     *
     * @return string
     * @since  1.0.0
     */
    public function fields($fields = array())
    {
        $output = '';
        foreach ($fields as $field_name) {

            if (isset($this->custom_fields[$field_name]) && !empty($this->custom_fields[$field_name])) {
                $field_options = $this->custom_fields[$field_name];
                $field_options['custom_name'] =   'cwp_custom_form[fields][' . $field_name . ']';
                $field_options['id']          =   isset($this->custom_fields[$field_name]['id']) ? $this->custom_fields[$field_name]['id'] : $field_name;
                if ($field_options['type'] == 'google_address') {
                    $field_options['custom_name_lat'] =   'cwp_custom_form[fields][' . $field_options['name'] . '_lat' . ']';
                    $field_options['custom_name_lng'] =   'cwp_custom_form[fields][' . $field_options['name'] . '_lng' . ']';
                }
                $field_options['value'] = isset($this->custom_fields[$field_name]['default_value']) ? $this->custom_fields[$field_name]['default_value'] : '';
                $field_options = wp_parse_args($field_options, $this->custom_fields[$field_name]);
                if (isset($field_options['sub_fields']) && !empty($field_options['sub_fields'])) {
                    $sub_fields = explode(',', $field_options['sub_fields']);
                    $field_options['sub_fields'] = array();
                    foreach ($sub_fields as $sub_field) {
                        $field_options['sub_fields'][] = $this->custom_fields[$sub_field];
                    }
                }

                $output .=  apply_filters("cubewp/frontend/{$field_options['type']}/field", '', $field_options);
            }
        }
        return $output;
    }

    /**
     * Method cubewp_submit_custom_form
     *
     * @return array json
     * @since  1.0.0
     */
    public function cubewp_submit_custom_form()
    {
 
        $security_nonce = isset($_POST['security_nonce']) ? sanitize_text_field( wp_unslash( $_POST['security_nonce'] ) ) : '';
        if ( empty( $security_nonce ) || ! wp_verify_nonce( $security_nonce, "cubewp_forms_submit" ) ) {
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => esc_html__('Sorry! Security Verification Failed.', 'cubewp-forms'),
                )
            );
        }
        if (isset($_POST['g-recaptcha-response'])) {
            CubeWp_Frontend_Recaptcha::cubewp_captcha_verification("cubewp_captcha_custom_form_submission", cubewp_core_data(sanitize_text_field(wp_unslash($_POST['g-recaptcha-response']))));
        }
        if (isset($_POST['cwp_custom_form'])) {
			
            global $cwpOptions;
            $data_id        = isset($_POST['cwp_custom_form']['form_data_id'])    ? sanitize_text_field(wp_unslash($_POST['cwp_custom_form']['form_data_id'])) : 0;
            $form_id        = isset($_POST['cwp_custom_form']['form_id'])    ? sanitize_text_field(wp_unslash($_POST['cwp_custom_form']['form_id'])) : 0;
            $single_form    = isset($_POST['cwp_custom_form']['single_post']) ? sanitize_text_field(wp_unslash($_POST['cwp_custom_form']['single_post'])) : '';
            $author_user    = isset($_POST['cwp_custom_form']['author_user']) ? absint(wp_unslash($_POST['cwp_custom_form']['author_user'])) : 0;
           // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash , WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $metas          = isset($_POST['cwp_custom_form']['fields'])  ? CubeWp_Forms_Sanitize_Fields_Array($_POST['cwp_custom_form']['fields'], 'custom_forms') : '';


            $form_data = [];
            $form_data['lead_id'] = $data_id;
            $form_data_id = json_decode(get_post_meta($form_id, '_cwp_custom_form_data_id', true));
            $form_data_id = is_object($form_data_id) ? $form_data_id : (object) $form_data_id;
            $form_data_id->$data_id = $data_id;
            update_post_meta($form_id, '_cwp_custom_form_data_id', json_encode($form_data_id));
            $form_data['user_id'] = get_current_user_id();
            //////////////
            $current_time =  strtotime("now");
            $dt_object_current = new DateTime("@$current_time");
            $form_data['dete_time'] = strtotime( $dt_object_current->format('Y-m-d H:i:s') );
            ////////////
            if (!empty($single_form)) {
                if (get_post_type($single_form) != 'page') {
                    $single_author  = get_post_field('post_author', $single_form);
                    $form_data['post_author'] = $single_author;
                    $user_form_data_id = json_decode(get_user_meta($single_author, '_cwp_custom_form_data_id', true));
                    $user_form_data_id = is_object($user_form_data_id) ? $user_form_data_id : (object) $user_form_data_id;
                    $user_form_data_id->$data_id = $single_form;
                    update_user_meta($single_author, '_cwp_custom_form_data_id', json_encode($user_form_data_id));
                }
                $form_data['single_post'] = $single_form;
            }
            if (!empty($author_user)) {
                $form_data['post_author'] = $author_user;
                $user_form_data_id = json_decode(get_user_meta($author_user, '_cwp_custom_form_data_id', true));
                $user_form_data_id = is_object($user_form_data_id) ? $user_form_data_id : (object) $user_form_data_id;
                $user_form_data_id->$data_id = $author_user;
                update_user_meta($author_user, '_cwp_custom_form_data_id', json_encode($user_form_data_id));
            }

            /////////////////
            $pageID = get_post_meta($form_id, '_cwp_group_page_id', true);
            $single_form  = !empty($pageID)  ? $pageID : $single_form;
            /////////////////  

            $form_data['form_id'] = $form_id;
            $form_data['form_name'] = get_post_field('post_name', $form_id);
            if (!empty($data_id)) {
                $message = '<div>';
                $message .= '<h2>' . esc_html__('Form Entry Details', 'cubewp-forms') . '</h2>';
                if (isset($metas) && !empty($metas)) {
                    $fieldOptions = $this->custom_fields;
                    foreach ($metas as $key => $val) {
                        $singleFieldOptions = isset($fieldOptions[$key]) && isset($fieldOptions[$key]['type']) ? $fieldOptions[$key] : array();

                        if (isset($singleFieldOptions['type']) && $singleFieldOptions['type'] == 'gallery') {
                            $attachment_ids = cwp_upload_custom_form_gallery_images($key, $val, $_FILES, $form_id);
                            if (isset($attachment_ids) && !empty($attachment_ids)) {
                                $form_data['fields'][$key] = $attachment_ids;
                            }
                        } else if ((isset($singleFieldOptions['type']) && $singleFieldOptions['type'] == 'file') ||
                            (isset($singleFieldOptions['type']) && $singleFieldOptions['type'] == 'image')
                        ) {
                            $attachment_id = cwp_upload_custom_form_file($key, $val, $_FILES, $form_id);
                            if (isset($attachment_id) && !empty($attachment_id)) {
                                $form_data['fields'][$key] = $attachment_id;
                            }
                        } else if (isset($singleFieldOptions['type']) && $singleFieldOptions['type'] == 'repeating_field') {
                            $arr = $this->cubewp_repeating_field_save($key, $val, $_FILES, $form_id);
                            if (isset($arr) && !empty($arr)) {
                                $_arr = array_filter($arr);
                                $form_data['fields'][$key] = $_arr;
                            }
                            // new
                        } else {
                            if (isset($singleFieldOptions['type']) && ($singleFieldOptions['type'] == 'checkbox' || $singleFieldOptions['type'] == 'radio' || $singleFieldOptions['type'] == 'dropdown')) {
                                $oprator =  $singleFieldOptions['files_save_separator'];
                                $checkValues =  $singleFieldOptions['options'];
                                $checkValues = json_decode($checkValues, true);
                                $correspondingLabels = [];
                            
                                if (is_array($val)) {
                                    foreach ($val as $value) {
                                        $valueIndex = array_search($value, $checkValues['value']);
                                        if ($valueIndex !== false && isset($checkValues['label'][$valueIndex])) {
                                            $correspondingLabels[] = $checkValues['label'][$valueIndex];
                                        }
                                    }

                                    array_pop($correspondingLabels);
                                    if($oprator  == 'array' ){
                                       $val = implode(', ', $correspondingLabels);
                                    }else{
                                        $val = implode(' '.$oprator.' ', $correspondingLabels);
                                    }
                                    
                                } else {
                                    $valueIndex = array_search($val, $checkValues['value']);
                                    if ($valueIndex !== false && isset($checkValues['label'][$valueIndex])) {
                                        $val = $checkValues['label'][$valueIndex];
                                    }
                                }
                            }
                            
                            ///////////// change full this condition
                            if (isset($singleFieldOptions['type']) && ($singleFieldOptions['type'] == 'date_picker' || $singleFieldOptions['type'] == 'date_time_picker' || $singleFieldOptions['type'] == 'time_picker')) {
                                $val = strtotime($val);
                                $timestamp = $val;
                                $dt_object = new DateTime("@$timestamp");

                                if ($singleFieldOptions['type'] == 'date_picker') {
                                    $formatted_date = $dt_object->format('Y-m-d');
                                } elseif ($singleFieldOptions['type'] == 'date_time_picker') {
                                    $formatted_date = $dt_object->format('Y-m-d h:i:s a'); // Use 'a' for AM/PM format
                                } elseif ($singleFieldOptions['type'] == 'time_picker') {
                                    $formatted_date = $dt_object->format('h:i:s a'); // Use 'a' for AM/PM format
                                }
                                $val = $formatted_date;
                            }
                            /////////////
                            $form_data['fields'][$key] = $val;
                            if (isset($singleFieldOptions['label'])) {
                                $message .= '<h3>' . esc_html($singleFieldOptions['label']) . '</h3>';
                            }
                            $message .= '<p>' . esc_html($val) . '</p>';
                        }
                    }

                    cwp_insert_leads($form_data);
                }

				self::cubewp_forms_submit_email($form_id, $form_data, $message);
               
				/* update 1.0.2 mailchimp integration */
				do_action( 'cubewp_submit_custom_form_after', $form_id , $form_data);
                
                $return = apply_filters(
                    "cubewp/custom/form/{$form_id}/after/submit/actions",
                    array(
                        'type'  =>  'success',
                        'msg'   =>  esc_html__('Success! The submission was successful.', 'cubewp-forms'),
                        'redirectURL'   =>  get_the_permalink($single_form),
                    ),
                    array(
                        'form_id' => $form_id
                    )
                );

                wp_send_json($return);
            }
        }
    }

	
	/**
     * Method cubewp_forms_submit_email
     *
     * @return array json
     * @since  1.0.0
     */
    public function cubewp_forms_submit_email( $form_id, $form_data, $message  ){
		$_emails = get_post_meta($form_id, '_cwp_group_emails', true);
		$_emails = explode(',', $_emails);
		$emails = array();
		foreach ($_emails as $email) {
			$emails[] = sanitize_email(trim($email));
		}
		$headers = array();
		$user_email = get_post_meta($form_id, '_cwp_group_user_email', true);
		$reply_to = '';
		if (!empty($user_email)) {
			$reply_to = isset($form_data['fields'][$user_email]) ? $form_data['fields'][$user_email] : '';
			$reply_to = sanitize_email($reply_to);
			if (!empty($reply_to) && is_email($reply_to)) {
				$headers = array("Reply-To: $reply_to");
			}
		}
		$user_templates 	=	cubewp_forms_get_email_template( 'user' , $form_id );
		$admin_templates	=	cubewp_forms_get_email_template( 'admin' , $form_id );
		if( !empty($user_templates) && !empty( $reply_to ) ){
			foreach( $user_templates as $user_template){
				CubeWp_Forms_Emails::cubewp_send_email(  $reply_to, $user_template, $form_data, $headers );
			}
		}
        $lead_author_email = '';
        if (!empty($form_data['post_author'])) {
            $lead_author = get_userdata((int) $form_data['post_author']);
            if ($lead_author && !empty($lead_author->user_email) && is_email($lead_author->user_email)) {
                $lead_author_email = sanitize_email($lead_author->user_email);
                if (!in_array($lead_author_email, $emails, true)) {
                    $emails[] = $lead_author_email;
                }
            }
        }
        if( !empty($admin_templates) && !empty($emails) ){
			foreach( $admin_templates as $admin_template){
				CubeWp_Forms_Emails::cubewp_send_email( $emails, $admin_template, $form_data, $headers );
			}
		}else if( !empty($emails) ){
			cubewp_send_mail(
				$emails,
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                // translators: %s: Form title.
				sprintf(esc_html__('You have received a new entry from %s', 'cubewp-forms'), get_the_title( $form_id)),
				$message,
				$headers
			);
		}
		
	}
	
	
	/**
     * Method cubewp_submit_custom_form_after_callback
     *
     * @return array json
     * @since  1.0.0
     */
    public function cubewp_submit_custom_form_after_callback( $form_id , $lead_data  ){
		global $cwpOptions;
		$api_key = isset($cwpOptions['cubewp_forms_mailchimp_key']) ? $cwpOptions['cubewp_forms_mailchimp_key'] : '';
		$api_prefix = isset($cwpOptions['cubewp_forms_mailchimp_prefix']) ? $cwpOptions['cubewp_forms_mailchimp_prefix'] : '';
		$list_id = get_post_meta($form_id, '_cwp_group_mailchimp_list_id', true);
		$mailchimp = get_post_meta( $form_id, '_cwp_group_mailchimp', true);
		if( $mailchimp && !empty($api_key) && !empty($api_prefix) && !empty($list_id) ){
			$field_options = $this->custom_fields;
			
			$data = array();
			$metas = isset($lead_data['fields'])  ? CubeWp_Sanitize_Fields_Array($lead_data['fields'], 'custom_forms') : '';
			if (isset($metas) && !empty($metas)) {
				foreach ($metas as $key => $val) {
					if( isset(  $field_options[$key]['cubewp_mailchimp_field_key'] ) && !empty( $field_options[$key]['cubewp_mailchimp_field_key'] ) ){
						if( $field_options[$key]['cubewp_mailchimp_field_key'] == 'email_address' ){
							$data[ $field_options[$key]['cubewp_mailchimp_field_key'] ] = $val;
						}else{
							$data['merge_fields'][ $field_options[$key]['cubewp_mailchimp_field_key'] ] = $val;
						}
						
					}
				}
			}
			$data['status'] =  'subscribed';
			$args = array(
				'method' => 'POST',
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode('user:'.$api_key),
					'Content-Type' => 'application/json',
				),
				'body' => json_encode($data),
				'timeout' => 30,
			);

			$url = "https://"  . $api_prefix . ".api.mailchimp.com/3.0/lists/".$list_id."/members"; // Replace <dc> with your Mailchimp data center prefix

			$response = wp_remote_post($url, $args);
			$response_code = wp_remote_retrieve_response_code( $response );
			if (is_wp_error($response) || $response_code  == 400 ) {
				
				if( $response_code  == 400 ){
					$error_body = json_decode( wp_remote_retrieve_body( $response ), true );
					$error_message = isset( $error_body['detail'] ) ? $error_body['detail'] : 'Unknown Error';
				}else{
					$error_message = $response->get_error_message();
				}

				// Check if the table exists, if not, create it
				global $wpdb;
				$table_name = $wpdb->prefix . 'cubewp_mailchimp_errors'; 
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery , PluginCheck.Security.DirectDB.UnescapedDBParameter, WordPress.DB.DirectDatabaseQuery.NoCaching , WordPress.DB.PreparedSQL.NotPrepared ,	WordPress.DB.PreparedSQL.InterpolatedNotPrepared ,	WordPress.DB.PreparedSQL.InterpolatedNotPrepared 
				if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange	
                    $sql = "CREATE TABLE $table_name (
						id mediumint(9) NOT NULL AUTO_INCREMENT,
						error_message text NOT NULL,
						error_date datetime NOT NULL,
						PRIMARY KEY (id)
					) $charset_collate;";
					
					require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
					dbDelta($sql);
				}
                // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery ,	WordPress.DB.DirectDatabaseQuery.NoCaching , WordPress.DB.PreparedSQL.NotPrepared ,	WordPress.DB.PreparedSQL.InterpolatedNotPrepared ,	WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				$wpdb->insert($table_name, array(
					'error_message' => $error_message,
					'error_date' => current_time('mysql'),
				));
			}
			
		}
	}
	
    /**
     * Method cubewp_repeating_field_save
     *
     * @param int $key             
     * @param array $val
     * @param array $FILES
     * @param int $postID
     *
     * @return array
     * @since  1.0.0
     */
    private function cubewp_repeating_field_save($key = '', $val = array(), $FILES = array(), $postID = '')
    {
        $fieldOptions = $this->custom_fields;
        $arr = array();
        if (empty($val)) return $arr;
        foreach ($val as $_key => $_val) {
            $singleFieldOptions = isset($fieldOptions[$_key]) ? $fieldOptions[$_key] : array();
            foreach ($_val as $field_key => $field_val) {
                if ((isset($singleFieldOptions) && $singleFieldOptions['type'] == 'gallery')) {
                    $field_val = cwp_upload_custom_form_repeating_gallery_images($key, $_key, $field_key, $field_val, $FILES, $postID);
                }
                if ((isset($singleFieldOptions) && $singleFieldOptions['type'] == 'file') ||
                    (isset($singleFieldOptions) && $singleFieldOptions['type'] == 'image')
                ) {
                    if (isset($FILES['cwp_custom_form']['name']['fields'][$key][$_key][$field_key]) && $FILES['cwp_custom_form']['name']['fields'][$key][$_key][$field_key] != '') {
                        $file = array(
                            'name'     => $FILES['cwp_custom_form']['name']['fields'][$key][$_key][$field_key],
                            'type'     => $FILES['cwp_custom_form']['type']['fields'][$key][$_key][$field_key],
                            'tmp_name' => $FILES['cwp_custom_form']['tmp_name']['fields'][$key][$_key][$field_key],
                            'error'    => $FILES['cwp_custom_form']['error']['fields'][$key][$_key][$field_key],
                            'size'     => $FILES['cwp_custom_form']['size']['fields'][$key][$_key][$field_key]
                        );
                        $field_val = cwp_handle_attachment($file, $postID);
                    }
                    if ($field_val != 0) {
                        $arr[$field_key][$_key] = $field_val;
                    }
                } else {

                    $arr[$field_key][$_key] = $field_val;
                }
            }
        }
        return $arr;
    }

    public static function init()
    {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
}
