import DynamicShortcodeInput from "./shortcode/dynamicShortcode";
import { escapeAttribute, escapeHTML } from "@wordpress/escape-html";
import { __ } from '@wordpress/i18n';
import { createBlock, registerBlockType } from '@wordpress/blocks';
import { PanelBody, PanelRow } from '@wordpress/components';
import { createElement, useEffect, useRef } from '@wordpress/element';
import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import ServerSideRender from '@wordpress/server-side-render';
const el = createElement;

/**
 * `useBlockProps` was introduced with Block API v2 in WordPress 5.6. Older
 * releases still wrap the block themselves, so fall back to empty props there.
 */
const useBlockPropsCompat = 'function' === typeof useBlockProps ? useBlockProps : () => ({});

/**
 * Matches `[woocatslider id="123"]` so legacy Shortcode blocks can be converted.
 */
const SHORTCODE_PATTERN = /\[woocatslider[^\]]*\bid=["']?(\d+)/;

/**
 * Register: WooCategory Gutenberg Block.
 */
registerBlockType("woo-category-slider/shortcode", {
	// Block API v3 marks the block as compatible with the iframed editor
	// canvas, which WordPress 7.1 uses for every editor.
	apiVersion: 3,
	title: escapeHTML(__("Reno Product Category", "woo-category-slider-grid")),
	description: escapeHTML(__(
		"Use Reno Product Category to insert a shortcode in your page.",
		"woo-category-slider-grid"
	)),
	icon: escapeAttribute(' wcsp-icon-wcs-icon'),
	category: "common",
	supports: {
		html: false,
	},
	transforms: {
		from: [
			{
				type: 'block',
				blocks: ['core/shortcode'],
				isMatch: ({ text }) => !! text && SHORTCODE_PATTERN.test(text),
				transform: ({ text }) => createBlock('woo-category-slider/shortcode', {
					shortcode: SHORTCODE_PATTERN.exec(text)[1],
				}),
			},
		],
		to: [
			{
				type: 'block',
				blocks: ['core/shortcode'],
				transform: ({ shortcode }) => createBlock('core/shortcode', {
					text: '[woocatslider id="' + shortcode + '"]',
				}),
			},
		],
	},
	edit: (props) => {
		const { attributes, setAttributes } = props;
		const shortCodeList = sp_woo_category_slider_load_script.shortCodeList;
		const isPreview = !! attributes.preview;
		const hasSelection = !! attributes.shortcode && 0 != attributes.shortcode;
		const previewRef = useRef(null);
		const blockProps = useBlockPropsCompat();

		/**
		 * Initialize the slider that `ServerSideRender` just rendered.
		 *
		 * Since WordPress 6.3 the block canvas is an iframe, so the rendered
		 * markup, jQuery and Swiper all live in `ownerDocument.defaultView` and
		 * not in the editor window. The canvas document and the server-side
		 * render both settle asynchronously, so wait for this block's own
		 * wrapper and for the canvas scripts, then hand over to the front-end
		 * initializers.
		 */
		useEffect(() => {
			if (isPreview || ! hasSelection) {
				return;
			}

			// `ServerSideRender` keeps the previous markup on screen while it
			// reloads, so match this block's own wrapper instead of any slider.
			const wrapperSelector = '#sp-wcsp-wrapper-' + String(attributes.shortcode).replace(/[^0-9]/g, '');
			const interval = 150;
			let attempts = 0;
			let waitedForScripts = 0;

			const timer = setInterval(() => {
				attempts++;

				// Bail out rather than polling forever when the render never arrives.
				if (attempts > 400) {
					clearInterval(timer);
					return;
				}

				const node = previewRef.current;
				const view = node && node.ownerDocument ? node.ownerDocument.defaultView : null;

				if (! node || ! view || ! view.jQuery || ! node.querySelector(wrapperSelector)) {
					return;
				}

				// The canvas scripts are parsed while the blocks mount, so make
				// sure they arrived before initializing. Stop waiting after five
				// seconds and run whatever is available in case a site filtered
				// one of the handles out.
				const scriptsReady = typeof view.Swiper !== 'undefined' && 'function' === typeof view.SPCategorySliderInit;

				if (! scriptsReady) {
					waitedForScripts += interval;

					if (waitedForScripts < 5000) {
						return;
					}
				}

				clearInterval(timer);

				// Initialize slider first.
				if ('function' === typeof view.SPCategorySliderInit) {
					view.SPCategorySliderInit();
				}

				// Hide preloader and show slider after initialization.
				const preloader = node.querySelector('.sp-wcsp-preloader');
				const sliderSection = node.querySelector('.sp-wcsp-slider-section');
				if (preloader) {
					preloader.style.opacity = '0';
					preloader.style.display = 'none';
				}
				if (sliderSection) {
					sliderSection.style.opacity = '1';
				}
			}, interval);

			return () => clearInterval(timer);
		}, [isPreview, hasSelection, attributes.shortcode]);

		let updateShortcode = (updateShortcode) => {
			setAttributes({shortcode: escapeAttribute(updateShortcode.target.value)});
		}

		let shortcodeUpdate = (e) => {
			updateShortcode(e);
		}

		if (isPreview) {
			return (
				el('div', {...blockProps, className: 'spwcsp_shortcode_block_preview_image'},
					el('img', { src: escapeAttribute(sp_woo_category_slider_load_script.path + "admin/GutenbergBlock/assets/wcs-block-preview.svg")})
				)
			)
		}

		if (shortCodeList.length === 0) {
			return (
				el('div', blockProps,
					el('div', {className: 'components-placeholder components-placeholder is-large'},
						el('div', {className: 'components-placeholder__label'},
							el('span', {className: 'block-editor-block-icon wcsp-icon-wcs-icon'}),
							escapeHTML(__("Reno Product Category", "woo-category-slider-grid"))
						),
						el('div', {className: 'components-placeholder__instructions'},
							escapeHTML(__("No shortcode found. ", "woo-category-slider-grid")),
							el('a', {href: escapeAttribute(sp_woo_category_slider_load_script.url)},
								escapeHTML(__("Create a shortcode now!", "woo-category-slider-grid"))
							)
						)
					)
				)
			);
		}

		if (! hasSelection) {
			return (
				el('div', blockProps,
					el(InspectorControls,
						el(PanelBody, {title: "Select a shortcode"},
							el(PanelRow, {},
								el(DynamicShortcodeInput, {
									attributes: attributes,
									shortCodeList: shortCodeList,
									shortcodeUpdate: shortcodeUpdate
								})
							)
						)
					),
					el('div', {className: 'components-placeholder components-placeholder is-large'},
						el('div', {className: 'components-placeholder__label'},
							el('span', {className: 'block-editor-block-icon wcsp-icon-wcs-icon'}),
							escapeHTML(__("Reno Product Category", "woo-category-slider-grid"))
						),
						el('div', {className: 'components-placeholder__instructions'}, escapeHTML(__("Select a shortcode", "woo-category-slider-grid"))),
						el(DynamicShortcodeInput, {
							attributes: attributes,
							shortCodeList: shortCodeList,
							shortcodeUpdate: shortcodeUpdate
						})
					)
				)
			);
		}

		return (
			el('div', blockProps,
				<InspectorControls>
					<PanelBody title="Select a shortcode">
						<PanelRow>
						<DynamicShortcodeInput
							attributes={attributes}
							shortCodeList={shortCodeList}
							shortcodeUpdate={shortcodeUpdate}
						/>
						</PanelRow>
					</PanelBody>
				</InspectorControls>,
				el('div', {ref: previewRef, className: 'spwcsp-block-preview'},
					el(ServerSideRender, {block: "woo-category-slider/shortcode", attributes: attributes})
				)
			)
		);
	},
	save() {
		// Rendering in PHP
		return null;
	},
});
