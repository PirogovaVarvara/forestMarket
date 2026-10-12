<?php
/**
 * display fields of custom fields.
 *
 * @version 1.0
 * @package /cube/CubeWp_Forms_Fields_Display
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * CubeWp_Forms_UI
 */
class CubeWp_Forms_UI extends CubeWp_Custom_Fields_Processor {

    const META_PREFIX = '_cwp_group_';

    /**
     * Method cwp_custom_fields_run
     *
     * @return void
     * @since  1.0.0
     */
    public static function cwp_custom_fields_run() {
        CubeWp_Enqueue::enqueue_style('cubewp-admin-leads');
        add_filter( 'cubewp/custom_fields/type', array(__CLASS__, 'custom_form_name'), 9 );
        add_filter( 'cubewp/custom-fields/group/data', array(__CLASS__, 'group_data'), 9 );
        self::save_group();
        self::group_display();
        self::add_new_group();
    }

    /**
     * Method custom_form_name
     *
     * @return string html
     * @since  1.0.0
     */
    public static function custom_form_name() {
        return 'custom_forms';
    }
   
    /**
     * Method add_new_group
     *
     * @return void
     * @since  1.0.0
     */
    public static function add_new_group() { 
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if(isset($_GET['action']) && ('new' == sanitize_text_field( wp_unslash( $_GET['action'] ) ) || 'edit' == sanitize_text_field( wp_unslash( $_GET['action'] ) ))){
            // Verify nonce for edit action
            // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if ( 'edit' === sanitize_text_field( wp_unslash( $_GET['action'] ) ) ) {
                // phpcs:ignore WordPress.Security.NonceVerification.Recommended
                if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'cwp_edit_group' ) ) {
                    wp_die( esc_html__( 'Security check failed. Please try again.', 'cubewp-forms' ) );
                }
            }
            self::edit_group();
        }
    }    
    /**
     * Method group_display
     *
     * @return string html
     * @since  1.0.0
     */
    public static function group_display() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if(isset($_GET['action']) && ('new' == sanitize_text_field( wp_unslash( $_GET['action'] ) ) || 'edit' == sanitize_text_field( wp_unslash( $_GET['action'] ) ))){
            return;
        }
        $customFieldsGroupTable = new CubeWp_Forms_Table();
        ?>
        <div class="wrap cwp-post-type-wrape">
            <h1 class="wp-heading-inline"><?php esc_html_e("CubeWP Forms", 'cubewp-forms'); ?></h1>
            <a href="<?php echo esc_url( CubeWp_Submenu::_page_action('cubewp-form-fields','new') ); ?>" class="page-title-action"><?php esc_html_e('Add New', 'cubewp-forms'); ?></a>
            <hr class="wp-header-end">
            <?php $customFieldsGroupTable->prepare_items(); ?>
            <form method="post">
                <input type="hidden" name="page" value="custom-fields">
                <?php wp_nonce_field( 'cubewp-forms-form', '_cubewp_forms_nonce' );?>
                <?php $customFieldsGroupTable->display(); ?>
            </form>
        </div>
        <?php
    }    

    /**
     * Method group_data
     *
     * @return array
     * @since  1.0.0
     */
    public static function group_data($group = array()){
        if(isset($group['id'])){
            $group['login'] = get_post_meta($group['id'], '_cwp_group_login', true);
            $group['display'] = get_post_meta($group['id'], '_cwp_group_display', true);
            $group['form_id'] = get_post_meta($group['id'], '_cwp_group_form_id', true);
            $group['button_text'] = get_post_meta($group['id'], '_cwp_group_button_text', true);
            $group['button_class'] = get_post_meta($group['id'], '_cwp_group_button_class', true);
            $group['button_width'] = get_post_meta($group['id'], '_cwp_group_button_width', true);
            $group['button_position'] = get_post_meta($group['id'], '_cwp_group_button_position', true);
            $group['recaptcha'] = get_post_meta($group['id'], '_cwp_group_recaptcha', true);
            $group['emails'] = get_post_meta($group['id'], '_cwp_group_emails', true);
            $group['user_email'] = get_post_meta($group['id'], '_cwp_group_user_email', true);
			$group['mailchimp'] = get_post_meta($group['id'], '_cwp_group_mailchimp', true);
			$group['mailchimp_list_id'] = get_post_meta($group['id'], '_cwp_group_mailchimp_list_id', true);
			$group['style'] = get_post_meta($group['id'], '_cwp_group_style', true);
            // new code 
            $group['page_id'] = get_post_meta($group['id'], '_cwp_group_page_id', true);
        }
        return $group;
    }    

    /**
     * Method edit_group
     *
     * @return string
     * @since  1.0.0
     */
    public static function edit_group() {
        $group = self::get_group();
        $defaults = array(
            'id'           => '',
            'name'         => '',
            'slug'         => '',
            'fields'       => '',
            'sub_fields'   => '',
            'user_roles'   => '',
            'description'  => '',
            'login'        => '',
            'display'      => '1',
            'form_id'      => '',
            'button_text'  => '',
            'button_class' => '',
            'button_width' => '',
            'button_position'  => '',
            'recaptcha'  => '',
            'emails'  => '',
            'settings'     => array(),
            'user_email'  => '',
			'mailchimp'  => '',
			'mailchimp_list_id'  => '',
			'style'  => '',
            // new code
            'page_id'  => '',
        );
        $group = wp_parse_args( $group, $defaults );
        ?>
        <div class="wrap cubewp-wrap">
        <form id="post" class="cwpgroup" method="post" action="" enctype="multipart/form-data">
            
            <div class="cwpform-title-outer  margin-bottom-0 margin-left-minus-20  margin-right-0">
                <?php 
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo self::_title();	?>			
            </div>
            <input type="hidden" name="cwp_form_nonce" value="<?php
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo wp_create_nonce( basename( __FILE__ ) ); ?>">
            <input type="hidden" class="" name="cwp[form][id]" value="<?php 
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo esc_attr($group['id']); ?>">
            <div id="poststuff"  class="padding-0">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="postbox-container-1" class="postbox-container">
                    <div id="side-sortables" class="meta-box-sortables ui-sortable">
                        <div class="postbox">
                            <div class="postbox-header">
                                <h2 class="hndle"><?php esc_html_e("Save Your Form", 'cubewp-forms'); ?></h2>
                            </div>
                            <div class="inside">
                                <div id="major-publishing-actions">
                                    <div id="publishing-action" style="float:none">
                                        <?php 
                                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        echo self::save_button(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="postbox">
                            <div class="postbox-header">
                                <h2 class="hndle"><?php esc_html_e("Form's Frontend Settings", 'cubewp-forms'); ?></h2>
                            </div>
                            <div class="inside">
                                <div class="main">
                                    <?php
                                    echo '<input type="hidden" name="cwp[form][settings][login]" value="0" />';
                                    echo '<input type="hidden" name="cwp[form][settings][display]" value="0" />';
									echo '<input type="hidden" name="cwp[form][settings][mailchimp]" value="0" />';
                                    ?>
									<!-- updates code for cube forms -->
                                    <div class="setting-block">
                                    <?php
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo apply_filters('cubewp/admin/text/customfield', '', array(
                                        'label' => esc_html__('Email', 'cubewp-forms'),
                                        'name' => 'cwp[form][settings][emails]',
                                        'value' => $group['emails'],
                                        'placeholder' => esc_html__('Enter Email', 'cubewp-forms'),
                                        'type' => 'text',
                                        'class' => 'field-emails',
                                        'id' => 'field-emails-' . $group['emails'],
                                        'tooltip' => esc_html__('Please enter email or emails separated by ",". These emails will receive submission notifications.', 'cubewp-forms'),
                                    ));
                                    ?>
                                    </div>
									<div class="setting-block">
                                    <?php
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo apply_filters('cubewp/admin/text/customfield', '', array(
                                        'label' => esc_html__('Form CSS ID', 'cubewp-forms'),
                                        'name' => 'cwp[form][settings][form_id]',
                                        'value' => $group['form_id'],
                                        'placeholder' => '',
                                        'type' => 'text',
                                        'class' => 'field-form-id',
                                        'id' => 'field-form-id-' . $group['form_id'],
                                        'tooltip' => esc_html__('This option will add CSS id for this form on submission.', 'cubewp-forms'),
                                    ));
                                    ?>
                                    </div>
                                    <div class="setting-block">
                                    <?php
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo apply_filters('cubewp/admin/text/customfield', '', array(
                                        'label' => esc_html__('User Email Field Name', 'cubewp-forms'),
                                        'name' => 'cwp[form][settings][user_email]',
                                        'value' => $group['user_email'],
                                        'placeholder' => esc_html__('Enter Custom Field Name', 'cubewp-forms'),
                                        'type' => 'text',
                                        'class' => 'field-user-email',
                                        'id' => 'field-user-email-' . $group['user_email'],
                                        'tooltip' => esc_html__('Please specify the custom field name that will be used to retrieve the user\'s email value.', 'cubewp-forms'),
                                    ));
                                    ?>
                                    </div>
									 <div class="setting-block">
                                    <?php
									$pages = get_pages();

									// Initialize options array with the default "Select Page" option
									$pages_options = array();

									// Format pages data for dropdown options
									foreach ($pages as $page) {
										$pages_options[$page->ID] = $page->post_title;
									}
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo apply_filters('cubewp/admin/dropdown/customfield', '', array(
                                        'label' => esc_html__('Redirect After Submission', 'cubewp-forms'),
                                        'name' => 'cwp[form][settings][page_id]',
                                        'value' => $group['page_id'],
										'options' => $pages_options,
                                        'placeholder' => esc_html__('Select Page', 'cubewp-forms'),
                                        'type' => 'dropdown',
                                        'class' => 'field-emails',
                                        'id' => 'field-emails-' . $group['page_id'],
                                        'tooltip' => esc_html__('Provide the page ID for redirection after form submission, or leave blank for Same Page redirection.', 'cubewp-forms'),
                                    ));
                                    ?>
                                    </div>
                                    
                                    <?php 
                                    global $cwpOptions;
                                    if ( isset( $cwpOptions['recaptcha'] ) && $cwpOptions['recaptcha'] == '1' ) {
                                    ?>
                                    <div class="setting-block">
                                    <?php
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo apply_filters('cubewp/admin/dropdown/customfield', '', array(
                                        'label' => esc_html__('Recaptcha', 'cubewp-forms'),
                                        'name' => 'cwp[form][settings][recaptcha]',
                                        'options' => array(
                                            'enabled' => esc_html__("Enable", "cubewp-forms"),
                                            'disabled' => esc_html__("Disable", "cubewp-forms"),
                                        ),
                                        'value' => $group['recaptcha'],
                                        'placeholder' => esc_html__('Enable/Disable Recaptcha', 'cubewp-forms'),
                                        'option-class' => 'form-option option',
                                        'type' => 'dropdown',
                                        'class' => 'field-recaptcha',
                                        'id' => 'field-recaptcha-' . $group['recaptcha'],
                                        'tooltip' => esc_html__('This option will enable recaptcha only on this form. ', 'cubewp-forms'),
                                    ));
                                    ?>
                                    </div>
                                    <?php } ?>
                                    <div class="setting-block">
                                    <?php
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo apply_filters('cubewp/admin/text/customfield', '', array(
                                        'label' => esc_html__('Form Submit Button Text', 'cubewp-forms'),
                                        'name' => 'cwp[form][settings][button_text]',
                                        'value' => $group['button_text'],
                                        'placeholder' => '',
                                        'type' => 'text',
                                        'class' => 'field-button-text',
                                        'id' => 'field-button-text-' . $group['button_text'],
                                        'tooltip' => esc_html__('This option will replace submit button text for this form on submission.', 'cubewp-forms'),
                                    ));
                                    ?>
                                    </div>
                                    <div class="setting-block">
                                    <?php
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo apply_filters('cubewp/admin/text/customfield', '', array(
                                        'label' => esc_html__('Form Submit Button Class', 'cubewp-forms'),
                                        'name' => 'cwp[form][settings][button_class]',
                                        'value' => $group['button_class'],
                                        'placeholder' => '',
                                        'type' => 'text',
                                        'class' => 'field-button-class',
                                        'id' => 'field-button-class-' . $group['button_class'],
                                        'tooltip' => esc_html__('This option will add class for submit button on form submission.', 'cubewp-forms'),
                                    ));
                                    ?>
                                    </div>
                                    <div class="setting-block">
                                    <?php
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo apply_filters('cubewp/admin/text/customfield', '', array(
                                        'label' => esc_html__('Form Submit Button Width', 'cubewp-forms'),
                                        'name' => 'cwp[form][settings][button_width]',
                                        'value' => $group['button_width'],
                                        'placeholder' => '',
                                        'type' => 'text',
                                        'class' => 'field-button-width',
                                        'id' => 'field-button-width-' . $group['button_width'],
                                        'tooltip' => esc_html__('This option will add width for submit button on form submission. Give width in % or px.', 'cubewp-forms'),
                                    ));
                                    ?>
                                    </div>
                                    <div class="setting-block">
                                    <?php
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo apply_filters('cubewp/admin/dropdown/customfield', '', array(
                                        'label' => esc_html__('Form Submit Button Position', 'cubewp-forms'),
                                        'name' => 'cwp[form][settings][button_position]',
                                        'options' => array(
                                            'center' => esc_html__("Center", "cubewp-forms"),
                                            'left' => esc_html__("Left", "cubewp-forms"),
                                            'right' => esc_html__("Right", "cubewp-forms"),
                                        ),
                                        'value' => $group['button_position'],
                                        'placeholder' => esc_html__('Select Position', 'cubewp-forms'),
                                        'option-class' => 'form-option option',
                                        'type' => 'dropdown',
                                        'class' => 'field-button-position',
                                        'id' => 'field-button-position-' . $group['button_position'],
                                        'tooltip' => esc_html__('This option will select position for submit button on form submission. ', 'cubewp-forms'),
                                    ));
                                    ?>
                                    </div>
									 <div class="setting-block">
                                    <?php
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo apply_filters('cubewp/admin/text/customfield', '', array(
                                        'label' => esc_html__('Restrict form submission only for logged-in users', 'cubewp-forms'),
                                        'name' => 'cwp[form][settings][login]',
                                        'value' => '1',
                                        'placeholder' => '',
                                        'type' => 'text',
                                        'checked' => $group['login'],
                                        'type_input' => 'checkbox',
                                        'class' => 'field-multiple-checkbox checkbox cwp-switch-check',
                                        'id' => 'field-login-' . $group['login'],
                                        'tooltip' => esc_html__('By enabling this option, only logged-in users will be able to submit this form.', 'cubewp-forms'),
                                    ));
                                    ?>
                                    </div>
                                    <div class="setting-block">
                                    <?php
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo apply_filters('cubewp/admin/text/customfield', '', array(
                                        'label' => esc_html__('Display Form name and description', 'cubewp-forms'),
                                        'name' => 'cwp[form][settings][display]',
                                        'value' => '1',
                                        'placeholder' => '',
                                        'type' => 'text',
                                        'checked' => $group['display'],
                                        'type_input' => 'checkbox',
                                        'class' => 'field-multiple-checkbox checkbox cwp-switch-check',
                                        'id' => 'field-display-' . $group['display'],
                                        'tooltip' => esc_html__('By enabling this option, form title and form description will be visible on single page.', 'cubewp-forms'),
                                    ));
                                    ?>
                                    </div>
									<?php 
                                    global $cwpOptions;
                                    if ( isset( $cwpOptions['cubewp_forms_mailchimp'] ) && $cwpOptions['cubewp_forms_mailchimp'] == '1' ) {
                                    ?>
									 <div class="setting-block">
                                    <?php
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo apply_filters('cubewp/admin/text/customfield', '', array(
                                        'label' => esc_html__('Mailchimp Integration', 'cubewp-forms'),
                                        'name' => 'cwp[form][settings][mailchimp]',
                                        'value' => '1',
                                        'placeholder' => '',
                                        'type' => 'text',
                                        'checked' => $group['mailchimp'],
                                        'type_input' => 'checkbox',
                                        'class' => 'field-multiple-checkbox checkbox cwp-switch-check',
                                        'id' => 'field-mailchimp-' . $group['mailchimp'],
                                        'tooltip' => esc_html__('By enabling this option, mailchimp integration will be enabled for this form', 'cubewp-forms'),
                                    ));
                                    ?>
                                    </div>
									 <div class="setting-block">
                                    <?php
                                    // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                    echo apply_filters('cubewp/admin/text/customfield', '', array(
                                        'label' => esc_html__('Mailchimp Audience ID', 'cubewp-forms'),
                                        'name' => 'cwp[form][settings][mailchimp_list_id]',
                                        'value' => $group['mailchimp_list_id'],
                                        'placeholder' => '',
                                        'type' => 'text',
                                        'class' => 'field-mailchimp-list',
                                        'id' => 'field-mailchimp_list_id',
										'required' => array(
											array( 'mailchimp', 'equals', '1' )
										),
                                        'tooltip' => esc_html__('Please add audience list id in which you want to send user data of this form to mailchimp list.', 'cubewp-forms'),
                                    ));
                                    ?>
                                    </div>
									 <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="postbox-container-2" class="postbox-container postbox-container-top">
                    <div class="postbox">
                        <div class="postbox-header">
                            <h2><span><?php esc_html_e('Basic Settings', 'cubewp-forms'); ?></span></h2>
                        </div>
                        <div class="inside">
                            <div class="main">
                                <table class="form-table cwp-validation">
                                    <tbody>
                                        <?php
                                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        echo apply_filters('cubewp/admin/group/text/field', '', array(
                                            'id'             =>    '',
                                            'name'           =>    'cwp[form][name]',
                                            'value'          =>    $group['name'],
                                            'class'          =>    'cwp-group',
                                            'placeholder'    =>    esc_html__('Type new group name here..', 'cubewp-forms'),
                                            'label'          =>    esc_html__('Form Name', 'cubewp-forms'),
                                            'required'       =>    true,
                                            'extra_attrs'    =>    'maxlength=20',
                                            'tooltip'        =>    'Give a name for this form. Which will be used as form title.',
                                        ));
                                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        echo apply_filters('cubewp/admin/group/text/field', '', array(
                                            'id'             =>    '',
                                            'type'           =>    'text',
                                            'name'           =>    'cwp[form][slug]',
                                            'value'          =>    $group['slug'],
                                            'class'          =>    'cwp-group field-name',
                                            'placeholder'    =>    esc_html__('Set group order', 'cubewp-forms'),
                                            'label'          =>    esc_html__('Form Slug', 'cubewp-forms'),
                                            'required'       =>    true,
                                            'extra_attrs'    =>    'maxlength=20',
                                            'tooltip'        =>    'Give slug for this form. It should be unique.',
                                        ));
                                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                        echo apply_filters('cubewp/admin/group/text/field', '', array(
                                            'id'             =>    '',
                                            'name'           =>    'cwp[form][description]',
                                            'value'          =>    $group['description'],
                                            'class'          =>    'cwp-group',
                                            'placeholder'    =>    esc_html__('Write group description to identify the group', 'cubewp-forms'),
                                            'label'          =>    esc_html__('Description', 'cubewp-forms'),
                                            'required'       =>    true,
                                            'extra_attrs'    =>    'maxlength=100',
                                            'tooltip'        =>    'Give a description for this form. Which will be used to show under the form title',
                                        ));
                                        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										echo apply_filters('cubewp/admin/dashboard/dropdown/field', '', array(
                                            'id'             =>    '',
                                            'name'           =>    'cwp[form][settings][style]',
                                            'value'          =>    $group['style'],
                                            'class'          =>    'cwp-group',
                                            'placeholder'    =>    esc_html__('Default', 'cubewp-forms'),
                                            'label'          =>    esc_html__('Form Style', 'cubewp-forms'),
                                            'required'       =>    false,
											 'options' => array(
												'cwp-forms-style1' 	=> esc_html__("Style 1", "cubewp-forms"),
												'cwp-forms-style2' 	=> esc_html__("Style 2", "cubewp-forms"),
												'cwp-forms-style3' 	=> esc_html__("Style 3", "cubewp-forms"),
												'cwp-forms-style4' 	=> esc_html__("Style 4", "cubewp-forms"),
												'cwp-forms-style5' 	=> esc_html__("Style 5", "cubewp-forms"),
												'cwp-forms-style6' 	=> esc_html__("Style 6", "cubewp-forms"),
												'cwp-forms-style7' 	=> esc_html__("Style 7", "cubewp-forms"),
												'cwp-forms-style8' 	=> esc_html__("Style 8", "cubewp-forms"),
												'cwp-forms-style9' 	=> esc_html__("Style 9", "cubewp-forms"),
												'cwp-forms-style10' => esc_html__("Style 10", "cubewp-forms"),
											),
											'option-class' => 'form-option option',
											'type' => 'dropdown',
                                            'tooltip'        =>    'You can select style to give a styling to your form.',
                                        ));
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="cwp-group-fields cwp-validation">
                        <div class="cwp-group-fields-head">
                            <div class="cwp-field-order-head">
                            </div> 
                            <div class="cwp-fields-title-head">   
                                <div class="cwp-field-label-head">
                                    <?php esc_html_e('Field Label', 'cubewp-forms'); ?>
                                </div>
                                <div class="cwp-field-name-head">
                                    <?php esc_html_e('Field Name', 'cubewp-forms'); ?>
                                </div>
                                <div class="cwp-field-type-head">
                                    <?php esc_html_e('Field Type', 'cubewp-forms'); ?>
                                </div>
                            </div>
                            <div class="cwp-fields-actions-head">
                                <?php esc_html_e('Actions', 'cubewp-forms'); ?>
                            </div>
                        </div>
                        <div class="cwp-group-fields-content">
                           
                            <?php 
                            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                            echo self::get_fields($group['fields'], $group['sub_fields']); ?>
                        </div>
                    </div>
                </div>
                <?php self::add_new_field_btn(); ?>
                <div class="clear"></div>
            </div>
            </div>
            </form>
        </div>
        <?php
    }
    /**
     * page title
     * page title split for edit or add post type form. 
     * @since 1.0
     */  
    private static function _title() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if (isset($_GET['action']) && ('edit' == sanitize_text_field( wp_unslash( $_GET['action'] ) ) && !empty($_GET['groupid']))) {
            return '<h1>'. esc_html(__('Edit Form', 'cubewp-forms')) .'</h1>';
        } else {
            return '<h1>'. esc_html(__('Create New Form', 'cubewp-forms')) .'</h1>';
        }
    }     

    /**
     * Method save_button
     *
     * @return string html
     * @since  1.0.0
    */
     private static function save_button() {
        // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if(isset($_GET['action']) && ('edit' == sanitize_text_field( wp_unslash( $_GET['action'] ) ) && !empty($_GET['groupid']))){            
            $name = 'cwp_edit_group';
        }else{
            $name = 'cwp_save_group';
        }
        return '<input type="submit" class="cwp-custom-fields-group-btn button button-primary button-large cwp-save-button" name="'.$name.'" value="'.__( 'Save', 'cubewp-forms' ).'" />';
	}

    /**
     * Method save_group
     *
     * @return void
     * @since  1.0.0
     */
    protected static function save_group() {
        // Verify nonce for form submission
        if ( ! isset( $_POST['cwp_form_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['cwp_form_nonce'] ) ), basename( __FILE__ ) ) ) {
            return;
        }
        
        if (isset($_POST['cwp']['form'])) {
            $groupID     = isset($_POST['cwp']['form']['id']) ? sanitize_text_field( wp_unslash( $_POST['cwp']['form']['id'] ) ) : '';
            $groupName   = isset($_POST['cwp']['form']['name']) ? sanitize_text_field( wp_unslash( $_POST['cwp']['form']['name'] ) ) : '';
            $groupDesc   = isset($_POST['cwp']['form']['description']) ? wp_strip_all_tags( sanitize_text_field( wp_unslash( $_POST['cwp']['form']['description'] ) ) ) : '';
            $groupslug   = isset($_POST['cwp']['form']['slug']) ? sanitize_text_field( wp_unslash( $_POST['cwp']['form']['slug'] ) ) : '';
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
            $group_settings   = isset($_POST['cwp']['form']['settings']) ? CubeWp_Sanitize_text_Array( wp_unslash( $_POST['cwp']['form']['settings'] ) ) : '';
            if (!empty($groupName)) {
                if (isset($_POST['cwp_save_group'])) {
                    $post_id = wp_insert_post(array(
                        'post_type' => 'cwp_forms',
                        'post_title' => $groupName,
                        'post_content' => $groupDesc,
                        'post_status' => 'publish',
                        'post_slug' => $groupslug,
                    ));
                } else if (isset($_POST['cwp_edit_group']) && !empty($groupID)) {
                    wp_update_post(array(
                        'ID' => $groupID,
                        'post_title' => $groupName,
                        'post_content' => $groupDesc,
                    ));
                    $post_id = $groupID;
                }
            }
        }
        if (!empty($post_id)) {
            if(!empty($group_settings) && is_array($group_settings)){
                foreach($group_settings as $key => $value){
                    update_post_meta( $post_id, self::META_PREFIX . $key, $value );
                }
            }
            // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
            self::save_custom_fields($_POST['cwp'],$post_id,'');
            wp_safe_redirect( CubeWp_Submenu::_page_action('cubewp-form-fields') );            
        }
        
    }
    
}