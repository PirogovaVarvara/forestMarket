<?php
/**
 * CubeWP Custom Forms Templates.
 *
 * @version 1.0
 * @package cubewp/cube/classes/cubewp-forms-templates
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
/**
 * CubeWp_Forms_Templates
 */
class CubeWp_Forms_Templates {

    public function __construct() {
        add_action( 'cubewp_custom_form_templates', array( $this, 'cwp_templates_run' ) );
    }
    /**
     * Method cwp_templates_run
     *
     * @return void
     * @since  1.0.0
     */
    public static function cwp_templates_run(  ) {
		CubeWp_Enqueue::enqueue_style('cubewp-admin-templates');
		CubeWp_Enqueue::enqueue_script('cubewp-forms-admin');
		?>
		<div class="wrap cwp-leads-templates">
			<div class="cwp_import"></div>
			<button class="button-primary cwp_import_demo" name="cwp_import_demo" style="display:none">Import Data</button>
            <h2 class="cwp-leads-templates-title">Accelerate Your Form Creation with Our Ready-Made Templates</h2>
            <p class="cwp-leads-templates-des">Select a template to streamline the form-building process. Alternatively, begin with a blank canvas or design your own.</p>
			<div class="cwp-leads-templates-main-search">
				<h3>11 Total Forms</h3>
			</div>
			<div class="cwp-popup" role="alert" style="display:none">
				<div class="cwp-popup-container">
					<p>Are you sure you want to import this template?</p>
					<ul class="cwp-buttons">
						<li><a class="cwp-forms-import-template-confirmed">Yes</a></li>
						<li><a class="cwp-popup-no">No</a></li>
					</ul>
					<a class="cwp-popup-close img-replace">Close</a>
				</div>
			</div>
			<div class="cwp-leads-templates-content">
                <div class="cwp-leads-templates-main">
				<?php 
				$import_folder_path = CWP_FORMS_PLUGIN_DIR . 'cube/import/';

				// Check if the import folder exists
				if (is_dir($import_folder_path)) {
					// Open the import folder
					if ($import_folder_handle = opendir($import_folder_path)) {
						// Loop through each entry in the import folder
						while (($entry = readdir($import_folder_handle)) !== false) {
							// Check if the entry is a directory and not '.' or '..'
							if (is_dir($import_folder_path . $entry) && $entry != '.' && $entry != '..') {
								// Get the directory path of the forms folder within the current import folder
								$forms_folder_path = $import_folder_path . $entry;
								// Check if the forms folder exists
								if (is_dir($forms_folder_path)) {
									// Open the forms folder
									if ($forms_folder_handle = opendir($forms_folder_path)) {
										// Loop through each entry in the forms folder
										while (($template = readdir($forms_folder_handle)) !== false) {
											// Check if the entry is a directory and not '.' or '..'
											if (is_dir($forms_folder_path . '/' . $template) && $template != '.' && $template != '..') {
												// Get the path of the content.txt file
												$content_txt_path = $forms_folder_path  . '/' . $template . '/content.txt';
												// Check if content.txt file exists
												if (file_exists($content_txt_path)) {
													$content = file_get_contents($content_txt_path);
													$lines = explode("\n", $content);
													$template_name = trim($lines[0]);
													$description = trim($lines[1]);
													?>
													<div class="cwp-leads-templates-main-grids">
														<span><?php echo esc_html( ucwords( str_replace( "-", " ", $entry ) ) ); ?></span>
														<h3><?php echo esc_attr( $template_name ); ?></h3>
														<p><?php echo esc_attr( $description ); ?></p>
														<div class="cwp-leads-templates-main-grids-buttons">
															<a class="preview-link" href="https://cubewp.com/extensions/cubewp-forms-templates/?form_type=<?php echo esc_attr( $template); ?>" target="blank">Preview</a>
															<button class="cwp-templates-grids-buttons-form" data-import="<?php echo esc_attr( CWP_FORMS_PLUGIN_DIR . 'cube/import/'.$entry.'/'.$template ); ?>">Import Template</button>
														</div>
													</div>
													<?php
												}
											}
										}
										// Close the forms folder
										closedir($forms_folder_handle);
									}
								}
							}
						}
						// Close the import folder
						closedir($import_folder_handle);
					}
				}
						
				?>
                </div>

            </div>
        </div>
		
		<?php
    }
    
    public static function init() {
        $CubeClass = __CLASS__;
        new $CubeClass;
    }
}