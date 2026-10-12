<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
} 
class CubeWp_Forms_Elementor
{

    /**
     * Addon Version
     *
     * @since 1.0.0
     * @var string The addon version.
     */
    const VERSION = '1.0.0';

    /**
     * Minimum Elementor Version
     *
     * @since 1.0.0
     * @var string Minimum Elementor version required to run the addon.
     */
    const MINIMUM_ELEMENTOR_VERSION = '3.5.0';

    /**
     * Minimum PHP Version
     *
     * @since 1.0.0
     * @var string Minimum PHP version required to run the addon.
     */
    const MINIMUM_PHP_VERSION = '7.0';

    /**
     * Instance
     *
     * @since  1.0.0
     * @access private
     * @static
     * @var CubeWp_Forms_Elementor The single instance of the class.
     */
    private static $_instance = null;

    /**
     * Constructor
     *
     * Perform some compatibility checks to make sure basic requirements are meet.
     * If all compatibility checks pass, initialize the functionality.
     *
     * @since  1.0.0
     * @access public
     */



    public function __construct()
    {

        if ($this->is_compatible()) {
            add_action('elementor/widgets/register', array($this, 'register_widgets'));
        }
    }


    /**
     * Compatibility Checks
     *
     * Checks whether the site meets the addon requirement.
     *
     * @since  1.0.0
     * @access public
     */
    public function is_compatible()
    {
        // Check if Elementor installed and activated
        if (! cubewp_check_if_elementor_active()) {
            return false;
        }
        // Check for required Elementor version
        if (! version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            $message = sprintf(/* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'cubewp-forms'), '<strong>' . esc_html__('CubeWP', 'cubewp-forms') . '</strong>', '<strong>' . esc_html__('Elementor', 'cubewp-forms') . '</strong>', self::MINIMUM_ELEMENTOR_VERSION);
            new CubeWp_Admin_Notice("elementor-version", $message, 'warning');

            return false;
        }
        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            $message = sprintf(/* translators: 1: Plugin name 2: PHP 3: Required PHP version */esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'cubewp-forms'), '<strong>' . esc_html__('CubeWP', 'cubewp-forms') . '</strong>', '<strong>' . esc_html__('PHP', 'cubewp-forms') . '</strong>', self::MINIMUM_PHP_VERSION);
            new CubeWp_Admin_Notice("elementor-php-version", $message, 'warning');

            return false;
        }

        return true;
    }

    public function register_widgets($widgets_manager)
    {
        // Define list of widget base names (excluding "class-" and "-widget.php" parts)
        $files_to_include = array(
            'cubewp-forms',
        );

        // Loop through each widget file base name and include the class file if it exists
        foreach ($files_to_include as $relative_path) {
            // Build the full path to the widget class file
            $full_path = CWP_FORMS_PLUGIN_DIR . 'cube/classes/elementor-widgets/class-' . $relative_path . '-widget.php';

            // Check if file exists before including
            if (file_exists($full_path)) {
                require_once $full_path;
            }
        }

        // Define an array of widget class names to register with Elementor
        $classes = array(
            'CubeWP_Forms_Widget',
        );

        // Allow other plugins/themes to filter and add more widget classes dynamically
        $classes = apply_filters("cubewp_booking/elementor/widgets/classes", $classes);

        // Register each widget class with Elementor
        if (!empty($classes) && is_array($classes)) {
            foreach ($classes as $class) {
                if (class_exists($class)) {
                    // Register the widget instance
                    $widgets_manager->register(new $class());
                } else {
                    // If the class doesn't exist, stop execution with an error 
                    // translators: 1: Opening strong tag, 2: Class name, 3: Closing strong tag.
                    wp_die(sprintf(esc_html__("%1\$s%2\$s%3\$s Class Doesn't Exist.", "cubewp-forms"), "<strong>", esc_html($class), "</strong>"));
                }
            }
        }
    }



    public static function init()
    {
        new self();
    }
}