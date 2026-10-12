/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

/**
 * Untranslated default labels shipped for each sharing platform.
 *
 * These mirror the block defaults in block.js and the default block content
 * created by WPZOOM_Social_Sharing_Buttons. They stay in English in the saved
 * markup so it keeps validating against Save.js in every locale — translation
 * happens on display only.
 *
 * Keep this in sync with wpzoom_social_sharing_get_default_platform_labels()
 * in includes/social-sharing-icons.php.
 */
const DEFAULT_LABELS = {
	facebook: ['Facebook'],
	x: ['Share on X', 'X'],
	threads: ['Threads'],
	linkedin: ['LinkedIn'],
	pinterest: ['Pinterest'],
	reddit: ['Reddit'],
	pocket: ['Pocket'],
	telegram: ['Telegram'],
	whatsapp: ['WhatsApp'],
	bluesky: ['Bluesky'],
	email: ['Email'],
	'copy-link': ['Copy Link'],
	print: ['Print'],
};

/**
 * Translate one of the default sharing labels.
 *
 * Brand names are returned untouched; only real phrases and common nouns go
 * through gettext.
 *
 * @param {string} label A default label.
 * @returns {string} The translated label, or the label itself if it is a brand name.
 */
const translateDefaultLabel = (label) => {
	switch (label) {
		case 'Share on X':
			return __('Share on X', 'social-icons-widget-by-wpzoom');
		case 'Email':
			return __('Email', 'social-icons-widget-by-wpzoom');
		case 'Copy Link':
			return __('Copy Link', 'social-icons-widget-by-wpzoom');
		case 'Print':
			return __('Print', 'social-icons-widget-by-wpzoom');
		default:
			return label;
	}
};

/**
 * Get the label to display for a sharing platform.
 *
 * Labels are editable per block, so a stored name is only translated when it
 * still matches one of the untranslated defaults for that platform. Anything
 * the user typed themselves is returned verbatim.
 *
 * @param {string} id         The platform ID.
 * @param {string} storedName The label stored in the block attributes.
 * @returns {string} The label to display.
 */
export function getPlatformLabel(id, storedName = '') {
	const known = DEFAULT_LABELS[id] || [];

	if (typeof storedName !== 'string' || storedName === '') {
		return known.length ? translateDefaultLabel(known[0]) : '';
	}

	if (known.includes(storedName)) {
		return translateDefaultLabel(storedName);
	}

	// Custom label set by the user — leave it alone.
	return storedName;
}
