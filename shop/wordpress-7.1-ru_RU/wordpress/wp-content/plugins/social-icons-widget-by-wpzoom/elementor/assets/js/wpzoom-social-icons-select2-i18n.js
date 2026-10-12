/**
 * Localise the messages Select2 renders inside the Elementor editor.
 *
 * Elementor initialises Select2 without a `language` option, so the library
 * always falls back to its bundled English messages. Select2 expects every
 * message to be a callable, which is why these cannot be handed over from PHP
 * through wp_localize_script() and have to be declared here instead.
 *
 * Keys left out below keep Select2's English fallback.
 *
 * @see https://select2.org/i18n
 */
( function ( $ ) {
	'use strict';

	if ( ! $ || ! $.fn || ! $.fn.select2 || ! $.fn.select2.defaults ) {
		return;
	}

	if ( ! window.wp || ! window.wp.i18n ) {
		return;
	}

	var __ = window.wp.i18n.__;
	var _n = window.wp.i18n._n;
	var sprintf = window.wp.i18n.sprintf;

	$.fn.select2.defaults.set( 'language', {
		errorLoading: function () {
			return __( 'The results could not be loaded.', 'social-icons-widget-by-wpzoom' );
		},
		inputTooLong: function ( args ) {
			var overChars = args.input.length - args.maximum;

			return sprintf(
				/* translators: %d: number of characters that have to be removed. */
				_n(
					'Please delete %d character',
					'Please delete %d characters',
					overChars,
					'social-icons-widget-by-wpzoom'
				),
				overChars
			);
		},
		inputTooShort: function ( args ) {
			var remainingChars = args.minimum - args.input.length;

			return sprintf(
				/* translators: %d: number of characters that still have to be typed. */
				_n(
					'Please enter %d or more character',
					'Please enter %d or more characters',
					remainingChars,
					'social-icons-widget-by-wpzoom'
				),
				remainingChars
			);
		},
		loadingMore: function () {
			return __( 'Loading more results…', 'social-icons-widget-by-wpzoom' );
		},
		maximumSelected: function ( args ) {
			return sprintf(
				/* translators: %d: maximum number of items that can be selected. */
				_n(
					'You can only select %d item',
					'You can only select %d items',
					args.maximum,
					'social-icons-widget-by-wpzoom'
				),
				args.maximum
			);
		},
		noResults: function () {
			return __( 'No results found', 'social-icons-widget-by-wpzoom' );
		},
		searching: function () {
			return __( 'Searching…', 'social-icons-widget-by-wpzoom' );
		},
		removeAllItems: function () {
			return __( 'Remove all items', 'social-icons-widget-by-wpzoom' );
		},
	} );
}( window.jQuery ) );
