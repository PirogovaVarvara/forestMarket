<?php
/**
 * User Dashboard Frontend Custom Forms Shortcode.
 *
 * @package cubewp-addon-forms/cube/classes
 * @version 1.0
 * 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * CubeWp Forms Dashboard Class.
 *
 * @class CubeWp_Forms_Dashboard
 */
class CubeWp_Forms_Dashboard
{
    
    public function __construct() {
        add_action( 'wp_ajax_cwp_forms_data', array( $this ,'cwp_get_forms_data' ) );
	}
    /**
	 * Method cwp_leads Main function to load data at the dashboard
	 *
	 * @return string
	 * @since  1.0.0
	 */
    public static function cwp_leads() {

        //Loading JS and CSS files
        CubeWp_Enqueue::enqueue_style('cubewp-dashboard-leads');
        CubeWp_Enqueue::enqueue_script('cwp-tabs');
        CubeWp_Enqueue::enqueue_script('cubewp-forms-dashboard');

        $data = [];
        $form_data = cwp_forms_all_leads_by_post_author(get_current_user_id());
      
        if(isset($form_data) && !empty($form_data)){
            $form_data = array_reverse($form_data);
            foreach($form_data as $leads){
                $leadid = $leads['lead_id'];
                $form_fields =  CWP()->get_custom_fields( 'custom_forms' );
                $data[$leads['form_id']]['form_name'] = $leads['form_name'];
                $data[$leads['form_id']][$leadid]['post_id'] = $leads['single_post'];
                $data[$leads['form_id']][$leadid]['user_id'] = $leads['user_id'];
                $data[$leads['form_id']][$leadid]['post_author'] = $leads['post_author'];
                if(isset($leads['dete_time']) && !empty(isset($leads['dete_time']))){
                    $data[$leads['form_id']][$leadid]['dete_time'] = $leads['dete_time'];
                }
                $fields = unserialize($leads['fields']);
                foreach($fields as $key=> $lead){
                    if(isset($form_fields[$key])){
                        $field_data = $form_fields[$key];
                        $data[$leads['form_id']][$leadid][$key]['label'] = $field_data['label'];
                        $data[$leads['form_id']][$leadid][$key]['type'] = $field_data['type'];
                        $data[$leads['form_id']][$leadid][$key]['value'] = $lead;
                    }
                }
            }
        }
        $form_details=$data;
        $output = '<div class="cwp-dashboard-leads-container">';
        $output .= self::lead_details($form_details);
        $output .= self::cwp_form_data_sidebar();
        $output .= '</div>';
        return $output;
    }
    
    /**
	 * Method lead_details
	 *
	 * @param array $form_details
	 *
	 * @return string
	 * @since  1.0.0
	 */
    public static function lead_details($form_details) {
        $output=''; 
        if(!empty(self::cwp_lead_tabs_content($form_details))){
            if(count($form_details) > 1 ){
                $output .= '<div class="cwp-dashboard-leads-head">';
                $output.=self::cwp_lead_tabs($form_details);
                $output .= '</div>';
            }
            $output .= '<div class="cwp-dashboard-leads-content">';
            $output.=self::cwp_lead_tabs_content($form_details);
            $output .='</div>';
        }else{
            $output ='<div class="cwp-empty-posts"><img class="cwp-empty-img" src="'.esc_url(CWP_PLUGIN_URI . 'cube/assets/frontend/images/no-result.png').'" alt="">
                <h2>'.esc_html__("No Leads Found", "cubewp-forms").'</h2>
                <p>'.esc_html__("There are no leads found.", "cubewp-forms").'</p>
            </div>';
        }
        return $output;
    }
    
    /**
     * Method cwp_lead_tabs
     *
     * @param array $form_details
     *
     * @return string
     * @since  1.0.0
     */
    public static function cwp_lead_tabs($form_details)
    {
        $output = '';
        $output .= '<div class="cwp-leads-tabs">
                    <ul class="cwp-tabs" role="tablist">';
        $keyind = 0;
        foreach ($form_details as $form_id => $form_detail) {
            $form_name = $form_detail['form_name'];
            $active_class = $keyind == 0 ? 'cwp-active-tab' : '';
            $output .= '<li class="cwp-author-' . $form_id . '-tab ' . $active_class . '">
                        <a class="list-group-item" data-toggle="tab" href="#cwp-author-' . $form_id . '">' . $form_name . '</a>
                        </li>';
            $keyind++;
        }
        $output .= '</ul></div>';
        return $output;
    }

    /**
     * Method cwp_lead_tabs_content
     *
     * @param array $form_details
     *
     * @return string
     * @since  1.0.0
     */
    public static function cwp_lead_tabs_content($form_details)
    {
        $keyind = 0;
        $output = '';
        foreach ($form_details as $form_id => $form_detail) {
            unset($form_detail['form_name']);
            $active_class = $keyind == 0 ? 'cwp-active-tab-content' : '';
            if (count($form_details) > 1) {
                $output .= '<div class="cwp-tab-content ' . $active_class . '" id="cwp-author-' . $form_id . '">';
            }
            $output .= '<div class="cwp-dashboard-content-table-container">
                    <table class="cwp-dashboard-content-table">
                    <tbody>';
            $count = 0;
            $dete_time = '';
            foreach ($form_detail as $lead_id => $form_desc) {

                $output .= self::cwp_form_table_data($form_id, $lead_id, $form_desc);
                $count++;
            }
            $output .= '</tbody></table></div>';
            if (count($form_details) > 1) {
                $output .= '</div>';
            }
            $keyind++;
        }
        return $output;
    }

    /**
     * Method cwp_form_table_data
     *
     * @param int $form_id
     * @param int $lead_id
     * @param array $form_desc
     * @param int $post_id
     * @param int $author_id
     * @param int $dete_time
     *
     * @return string
     * @since  1.0.0
     */
    public static function cwp_form_table_data($form_id, $lead_id, $form_desc)
    {
        ob_start();
        $post_id = $form_desc['post_id'];
        $user_id = $form_desc['user_id'];
        $time = $date = '';
        if (isset($form_desc['dete_time']) && !empty(isset($form_desc['dete_time']))) {
            $dete_time = $form_desc['dete_time'];
            $dete_time = $form_desc['dete_time'];
            $date_time_obj = new DateTime();
            $date_time_obj->setTimestamp($dete_time);
            $time = $date_time_obj->format('g:i a');
            $date = $date_time_obj->format('j F, Y');
        }
        if (!empty($user_id)) {
            $user_name = get_the_author_meta('display_name', $user_id);
        } else {
            $user_name = '-';
        }
        ?>
        <tr>
            <td>
                <div class="cwp-dashboard-lead">
                    <div class="cwp-dashboard-lead-top">
                        <div class="cwp-dashboard-lead-post">
                            <?php if (!empty($post_id)) {
                                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                echo CubeWp_Frontend_User_Dashboard::get_post_details($post_id);
                            }
                            ?>
                        </div>
                        <div class="cwp-lead-dates-container">
                            <div class="cwp-lead-dates cwp-lead-rating">
                                <h6><?php if (!empty($user_name)) {
                                        echo esc_html($user_name);
                                    } else {
                                        echo '-';
                                    } ?></h6>
                                <p><?php echo esc_html_e('Submitted By', 'cubewp-forms') ?></p>
                            </div>
                            <div class="cwp-lead-dates cwp-lead-date">
                                <span><?php if (!empty($time)) {
                                            echo esc_html($time);
                                        } ?></span>
                                <h6><?php if (!empty($date)) {
                                        echo esc_html($date);
                                    } else {
                                        echo '-';
                                    } ?></h6>
                                <p><?php echo esc_html_e('Date', 'cubewp-forms') ?></p>
                            </div>
                        </div>
                        <div class="cwp-dashboard-lead-action-buttons">
                            <div class="cwp-dasboard-lead-action">
                                <a class="cwp-form-action-view cwp-dashboard-button-filled" type="button" target="_blank" data-form_id="<?php echo esc_attr($form_id); ?>" data-lead_id="<?php echo esc_attr($lead_id); ?>">
                                    <?php esc_html_e('View Details', 'cubewp-forms'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </td>
        </tr>
<?php
        return ob_get_clean();
    }

    /**
     * Method cwp_get_forms_data
     *
     * @return array json
     * @since  1.0.0
     */
    public function cwp_get_forms_data()
    {

        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash	
        if ( ! isset( $_POST['security_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( $_POST['security_nonce'] ), "cubewp_forms_dashboard" ) ) {
            wp_send_json(
                array(
                    'type' => 'error',
                    'msg'  => esc_html__('Sorry! Security Verification Failed.', 'cubewp-forms'),
                )
            );
        }
        global $wpdb;
        
        $leadid = '';
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash	
        if ( isset( $_POST['lead_id'] ) ) {
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.MissingUnslash	
            $leadid = sanitize_text_field( $_POST['lead_id'] );
        }
        $form_data = cwp_forms_all_leads_by_lead_id($leadid);
        $form_output = '';
        if (isset($form_data['fields']) && !empty($form_data['fields'])) {
            $form_fields =  CWP()->get_custom_fields('custom_forms');
            $fields = unserialize($form_data['fields']);
            foreach ($fields as $key => $lead) {
                $field_data = isset($form_fields[$key]) ? $form_fields[$key] : '';
                $field_label = isset($field_data['label']) ? $field_data['label'] : '';
                $field_type = isset($field_data['type']) ? $field_data['type'] : '';
                if ($field_type == 'repeating_field') {
                    if (is_array($lead) && !empty($lead)) {
                        $form_output .= '<div class="cwp-forms-field">';
                        $form_output .= '<h6>' . $field_data['label'] . '</h6>';
                        $form_output .= '<div class="cwp-forms-repeating-fields">';
                        foreach ($lead as $k => $val) {
                            foreach ($val as $_k => $_val) {
                                $type = $form_fields[$_k]['type'];
                                if (isset($_val) && !empty($_val)) {
                                    $label = isset($form_fields[$_k]['label']) ? $form_fields[$_k]['label'] : '';
                                    $form_output .= '<div class="cwp-forms-repeating-field">';
                                    $form_output .= '<h6>' . $label . '</h6>';
                                    $form_output .= '<p>' . CubeWp_Forms_Leads::cwp_forms_render_value($type, $_val) . '</p>';
                                    $form_output .= '</div>';
                                }
                            }
                        }
                        $form_output .= '</div></div>';
                    }
                } else {
                    $lead = CubeWp_Forms_Leads::cwp_forms_render_value($field_type, $lead);
                    if (isset($lead) && !empty($lead)) {
                        $label = isset($field_data['label']) ? $field_data['label'] : '';
                        $form_output .= '<div class="cwp-forms-field">';
                        $form_output .= '<h6>' . $label . '</h6>';
                        $form_output .= '<p>' . $lead . '</p>';
                        $form_output .= '</div>';
                    }
                }
            }
            wp_send_json(array('output' => $form_output));
        }
        die();
    }

    /**
     * Method cwp_form_data_sidebar
     *
     * @return string
     * @since  1.0.0
     */
    public static function cwp_form_data_sidebar()
    {
        $output = '<div class="cwp-form-sidebar">
        <div class="cwp-form-head">
            <h4>' . __('Lead Details', 'cubewp-forms') . '</h4>
            <span class="cwp-close-sidebar"><span class="dashicons dashicons-no"></span><p>' . __('Close', 'cubewp-forms') . '</p></span>
        </div>
        <div class="cwp-form-data-content"></div>
        </div>';
        return $output;
    }

    /**
     * Method lead_details
     *
     * @param string $field_type
     * @param $field_value
     *
     * @return string
     * @since  1.0.0
     */
    public static function cwp_forms_render_value($field_type, $field_value)
    {
        if ($field_type == 'date_picker') {
            $value = wp_date(get_option('date_format'), $field_value);
        }
        if ($field_type == 'time_picker') {
            $value = wp_date(get_option('time_format'), $field_value);
        }
        if ($field_type == 'date_time_picker') {
            $value = wp_date(get_option('date_format') . ' H:i:s', $field_value);
        }
        if ($field_type == 'terms') {
            $value = render_taxonomy_value($field_value);
        }
        if ($field_type == 'post') {
            $value = render_post_value($field_value);
        }
        if ($field_type == 'user') {
            $value = render_user_value($field_value);
        }
        if ($field_type == 'image' || $field_type == 'gallery') {
            $value = render_media_value($field_value);
        }
        if ($field_type == 'file') {
            $value = render_file_value($field_value);
        }

        return $value;
    }
    
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
}