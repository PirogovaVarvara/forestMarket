<?php
defined('ABSPATH') || exit;

/**
 * CubeWp_Forms_Emails
 */
class CubeWp_Forms_Emails
{

	public static $post_type = 'email_template_forms';

	private static $user_roles_for = array();

	private static $post_types_for = array();

	public function __construct()
	{
		CubeWp_Enqueue::enqueue_script('cubewp-forms-admin');
		add_filter('cubewp/posttypes/new', array($this, 'register_email_template_post_type'));
		add_filter('post_updated_messages', array($this, 'remove_view_email_template_action'), 10, 2);
		add_action('admin_init', array($this, 'remove_email_template_page'));
		add_action('admin_head', array($this, 'remove_post_actions'));
		add_action('save_post', array($this, 'restrict_email_template_submission'));
		add_action('add_meta_boxes', array($this, 'cubewp_email_template_shortcodes_metabox'));
		add_filter('enter_title_here', array($this, 'change_title_placeholder'), 10, 2);
		add_filter('default_content', array($this, 'default_post_content'), 10, 2);

		add_filter('manage_' . self::$post_type . '_posts_columns', array($this, 'email_post_type_columns'));
		add_action('manage_' . self::$post_type . '_posts_custom_column', array(
			$this,
			'email_post_type_column_content'
		), 10, 2);
	}


	/**
	 * Method remove_post_actions
	 *
	 * @return void
	 * @since  1.0.1
	 */
	public static function remove_post_actions()
	{
		global $post;
		if ($post && $post->post_type === 'email_template_forms') {
			// Remove  quick edit and view buttons
?>
			<style>
				.row-actions span.view,
				.row-actions span.inline {
					display: none;
				}
			</style>
		<?php
		}
	}

	public static function cubewp_send_email($email_to, $template_id, $form_data = array(), $headers = array())
	{

		if (is_array($template_id)) {
			$email_subject = isset($template_id['subject']) && ! empty($template_id['subject']) ? $template_id['subject'] : '';
			$email_content = isset($template_id['message']) && ! empty($template_id['message']) ? $template_id['message'] : '';
		} else {
			$email_subject = get_the_title($template_id);
			$email_content = get_the_content('', '', $template_id);
		}

		if (empty($email_subject) || empty($email_content)) {
			return false;
		}


		$email_subject = self::cubewp_render_email_shortcodes($email_subject, $form_data, true);
		$email_content = self::cubewp_render_email_shortcodes($email_content, $form_data);

		if (empty($email_subject) || empty($email_content)) {
			return false;
		}
		if (empty($headers)) {
			$headers = array();
			$website_name = get_bloginfo('name');
			$admin_email  = apply_filters("cubewp_forms_emails_from_mail", get_option('admin_email'));
			if (! empty($from_email_address)) {
				$headers[] = 'From: ' . esc_html($website_name) . ' <' . esc_html($admin_email) . '>';
			}
		}
		return cubewp_send_mail($email_to, $email_subject, $email_content, $headers);
	}

	private static function cubewp_render_email_shortcodes($content, $form_data, $html_entity_decode = false)
	{

		$content = str_replace('{website_title}', get_bloginfo('name'), $content);
		$content = str_replace('{website_url}', home_url(), $content);

		$pattern = '/\{([^}]+)\}/';
		// Use preg_replace_callback to replace each shortcode with its corresponding value
		$content = preg_replace_callback($pattern, function ($matches) use ($form_data) {
			$field_slug = $matches[1];
			// Check if the field slug exists in the fields array
			if (isset($form_data['fields'][$field_slug])) {
				// If yes, return the value
				return $form_data['fields'][$field_slug];
			} else {
				// If not found, return the original shortcode
				return $matches[0];
			}
		}, $content);

		$content = (string) apply_filters('cubewp/forms/email/render/shortcodes', $content, $html_entity_decode);
		if ($html_entity_decode) {
			$content = html_entity_decode($content, ENT_QUOTES, get_option('blog_charset'));
		}
		return $content;
	}

	public static function init()
	{
		$CubeClass = __CLASS__;
		new $CubeClass;
	}

	public function remove_email_template_page()
	{
		if (! current_user_can('administrator')) {
			remove_menu_page('edit.php?post_type=email_template_forms');
		}
	}

	public function remove_view_email_template_action($messages)
	{
		if (isset($messages[self::$post_type])) {
			$messages[self::$post_type][1]  = esc_html__('Email Template updated.', 'cubewp-forms');
			$messages[self::$post_type][6]  = esc_html__('Email Template published.', 'cubewp-forms');
			$messages[self::$post_type][8]  = esc_html__('Email Template submitted.', 'cubewp-forms');
			$messages[self::$post_type][10] = esc_html__('Email Template draft updated.', 'cubewp-forms');
		}

		return $messages;
	}

	public function restrict_email_template_submission($post_id)
	{
		if (get_post_type($post_id) === self::$post_type) {
			if (! current_user_can('administrator')) {
				wp_die('You do not have permission to submit posts to the email_template post type.');
			}
		}
	}

	public function email_post_type_columns($columns)
	{
		$new_column['email_recipient'] = esc_html__('Email Recipient', 'cubewp-forms');
		$new_column['email_type']      = esc_html__('Email Type', 'cubewp-forms');
		$position                      = array_search('date', array_keys($columns));

		return array_slice($columns, 0, $position, true) + $new_column + array_slice($columns, $position, null, true);
	}

	public function email_post_type_column_content($column, $post_id)
	{
		if ('email_recipient' == $column || 'email_type' == $column) {
			$email_recipient = get_post_meta($post_id, 'forms_email_recipient', true);
			if ('email_recipient' == $column) {
				echo esc_html(ucfirst($email_recipient));
			} else {
				$email_type = get_post_meta($post_id, 'admin_email_post_types', true);
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				echo get_the_title($email_type);
			}
		}
	}

	public function default_post_content($content, $post)
	{
		$post_type = $post->post_type;
		if ($post_type == 'email_template_forms' && empty($content)) {
			return esc_html__('Email Content', 'cubewp-forms');
		}

		return $content;
	}

	public function change_title_placeholder($title_placeholder, $post)
	{
		$post_type = $post->post_type;
		if ($post_type == 'email_template_forms') {
			return esc_html__('Email Subject', 'cubewp-forms');
		}

		return $title_placeholder;
	}

	public function cubewp_email_template_shortcodes_metabox()
	{

		add_meta_box('cubewp-email-template-shortcodes-metabox', __('Shortcodes', 'cubewp-forms'), array(
			$this,
			'cubewp_email_template_shortcode_metabox_render'
		), self::$post_type, 'side');
	}

	public function cubewp_email_template_shortcode_metabox_render($post)
	{
		if (isset($post->ID)) {
			$shortcodes = $this->cubewp_email_template_shortcodes();
		?>
			<div class="cubewp-email-template-shortcodes">
				<?php
				if (! empty($shortcodes) && is_array($shortcodes)) {
					foreach ($shortcodes as $shortcode) {
						$value = $shortcode['shortcode'];
						$label = $shortcode['label'];
				?>
						<div class="cubewp-email-template-shortcode">
							<span class="cubewp-email-template-shortcode-label"><?php echo esc_html($label); ?></span>
							<span class="cubewp-email-template-shortcode-value"><?php echo esc_html($value); ?></span>
						</div>
				<?php
					}
				}
				?>
			</div>
<?php
		}
	}

	private function cubewp_email_template_shortcodes()
	{
		$shortcodes   = array();
		$shortcodes[] = array(
			'label'     => esc_html__('Website Title', 'cubewp-forms'),
			'shortcode' => '{website_title}',
		);
		$shortcodes[] = array(
			'label'     => esc_html__('Website URL', 'cubewp-forms'),
			'shortcode' => '{website_url}',
		);

		return (array) apply_filters('cubewp/forms/email/shortcodes', $shortcodes);
	}

	public function register_email_template_post_type($post_types)
	{

		$post_types[self::$post_type] = array(
			'label'               => esc_html__('Email Templates', 'cubewp-forms'),
			'singular'            => esc_html__('Email Template', 'cubewp-forms'),
			'icon'                => 'dashicons-email',
			'slug'                => self::$post_type,
			'description'         => '',
			'supports'            => array('title', 'editor'),
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'menu_position'       => false,
			'show_in_menu'        => 'cubewp-form-fields',
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => false,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'query_var'           => false,
			'rewrite'             => false,
			'rewrite_slug'        => '',
			'rewrite_withfront'   => false,
			'show_in_rest'        => false,
		);

		$this->email_template_custom_fields();
		return $post_types;
	}

	private function email_template_custom_fields()
	{
		$save_fields = false;
		//$save_fields = true;
		$settings    = get_posts(array(
			'name'        => 'email_form_template_settings',
			'post_type'   => 'cwp_form_fields',
			'post_status' => 'private',
			'fields'      => 'id',
			'numberposts' => 1,
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key	 ,WordPress.DB.SlowDBQuery.slow_db_query_meta_value	
			'meta_key'    => '_cwp_group_types',
			// phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value	
			'meta_value'  => 'email_template_forms',
		));

		$settings_id = count($settings) > 0 ? $settings[0]->ID : '';
		$setting_fields         = $this->email_template_setting_fields($settings_id);
		if (empty($settings_id)) {
			$settings = array(
				'post_title'   => wp_strip_all_tags(__('Email Template Settings', 'cubewp-forms')),
				'post_name'    => 'email_form_template_settings',
				'post_content' => 'Custom fields for email template settings.',
				'post_status'  => 'private',
				'post_author'  => 1,
				'post_type'    => 'cwp_form_fields'
			);
			// Insert the post into the database
		
			$settings_id = wp_insert_post($settings);
			update_post_meta($settings_id, '_cwp_group_visibility', 'secure');
			update_post_meta($settings_id, '_cwp_group_types', 'email_template_forms');
			update_post_meta($settings_id, '_cwp_group_order', 1);
			$save_fields = true;
		}
		$form_fields = get_option('cwp_custom_fields');
		if (!empty($settings_id)) {
			$forms = self::cubewp_email_forms();
			
			$existing_options = array();
			if (isset($form_fields['forms_types']['options'])) {
				$decoded = json_decode($form_fields['forms_types']['options'], true);
				if (is_array($decoded)) {
					$existing_options = $decoded;
				}
			}
			$has_forms    = isset($forms['label']) && is_array($forms['label']) && count($forms['label']) > 0;
			$has_existing = isset($existing_options['label']) && is_array($existing_options['label']) && count($existing_options['label']) > 0;

			// Regenerate when no stored options exist yet, or when counts differ
			if ($has_forms && ( ! $has_existing || count($forms['label']) !== count($existing_options['label']) )) {
                $save_fields = true;
            } elseif( ! $has_forms && $has_existing ) {
				// No forms exist but options do, so we need to clear them out
				$save_fields = true;
			}
		}

		if ($save_fields) {
			$setting_fields         = $this->email_template_setting_fields($settings_id);
			$settings_custom_fields = array();
			foreach ($setting_fields as $key => $setting_field) {
				$settings_custom_fields[] = $key;
				CubeWp_Custom_Fields_Processor::set_option($key, $setting_field);
			}
			update_post_meta($settings_id, '_cwp_group_fields', implode(',', $settings_custom_fields));
		}
	}

	private function email_template_setting_fields($settings_id)
	{
		$fields                           = array();
		$fields['forms_email_recipient']        = array(
			'label'                => __('Email Recipient', 'cubewp-forms'),
			'name'                 => 'forms_email_recipient',
			'type'                 => 'dropdown',
			'description'          => '',
			'default_value'        => '',
			'placeholder'          => '',
			'options'              => json_encode(array(
				'label' => array(
					esc_html__('Admin', 'cubewp-forms'),
					esc_html__('User', 'cubewp-forms'),
				),
				'value' => array(
					'admin',
					'user'
				),
			)),
			'filter_post_types'    => '',
			'filter_taxonomy'      => '',
			'filter_user_roles'    => '',
			'appearance'           => '',
			'required'             => true,
			'validation_msg'       => '',
			'id'                   => 'forms_email_recipient',
			'class'                => '',
			'container_class'      => '',
			'conditional'          => false,
			'conditional_operator' => '!empty',
			'conditional_value'    => '',
			'group_id'             => $settings_id
		);
		$forms                       = self::cubewp_email_forms();
		$description = '';
		if ( empty( $forms['label'] ) ) {
			$description = esc_html__('No forms found. Please create a form first.', 'cubewp-forms');
		}
		$fields['forms_types'] = array(
			'label'                => __('Form Type', 'cubewp-forms'),
			'name'                 => 'forms_types',
			'type'                 => 'dropdown',
			'description'          => $description,
			'default_value'        => '',
			'placeholder'          => '',
			'options'              => json_encode($forms),
			'filter_post_types'    => '',
			'filter_taxonomy'      => '',
			'filter_user_roles'    => '',
			'appearance'           => '',
			'required'             => true,
			'validation_msg'       => '',
			'id'                   => 'forms_types',
			'class'                => '',
			'container_class'      => '',
			'conditional'          => false,
			'group_id'             => $settings_id
		);
		return $fields;
	}

	private static function cubewp_email_forms()
{
    $cwp_forms_posts = get_posts( array(
        'post_type'      => 'cwp_forms',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'orderby'        => 'title',
        'order'          => 'ASC',
    ) );

    // Always ensure both keys exist so json_encode() in the caller works consistently
    $cwp_forms = array(
        'label' => array(),
        'value' => array(),
    );

    if ( ! empty( $cwp_forms_posts ) && is_array( $cwp_forms_posts ) ) {
        foreach ( $cwp_forms_posts as $post ) {
            $title = get_the_title( $post );           // properly get the title
            $id    = (int) $post->ID;                  // use ID as the option value (safer)
            // If you prefer slug: $value = sanitize_title( $post->post_name );

            $cwp_forms['label'][] = esc_html( $title );
            $cwp_forms['value'][] = $id;
        }
    }
    return $cwp_forms;
}

}
