<?php
defined( 'ABSPATH' ) || exit;

class XS_Deactivator {
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
