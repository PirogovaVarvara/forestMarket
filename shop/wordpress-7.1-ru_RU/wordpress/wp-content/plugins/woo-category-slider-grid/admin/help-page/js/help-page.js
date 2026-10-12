; (function ($) {
	'use strict';

	var $help = $('.sp-woo-cat-slider-help');

	if (!$help.length) {
		return;
	}

	var $adminMenu = $('#menu-posts-sp_wcslider');

	// Hash (URL) to tab section id. Single source of truth for both directions.
	var tabs = {
		'get-start': 'get-start-tab',
		'lite-to-pro': 'lite-to-pro-tab',
		'recommended': 'recommended-tab',
		'about-us': 'about-us-tab'
	};

	// Tabs that own a dedicated admin submenu item. Everything else belongs to "Get Started".
	var adminMenuItem = {
		'lite-to-pro-tab': 'spwoocs-menu-lite-vs-pro'
	};

	function highlightAdminMenu(tabId) {
		var itemClass = adminMenuItem[tabId] || 'spwoocs-menu-get-started';

		$adminMenu.find('ul li').removeClass('current');
		$adminMenu.find('ul li.' + itemClass).addClass('current');
	}

	function activateTab(tabId) {
		var $section = $('#' + tabId);

		if (!tabId || !$section.length) {
			return;
		}

		$help.find('.spwoocs-header-nav-menu a').each(function () {
			var $link = $(this);

			$link.removeClass('active');
			$('#' + $link.attr('data-id')).hide();
		});

		$help.find('.spwoocs-header-nav-menu a[data-id="' + tabId + '"]').addClass('active');
		$section.show();
		highlightAdminMenu(tabId);
	}

	function activateFromHash() {
		var hash = window.location.hash.replace('#', '');

		if (tabs[hash]) {
			activateTab(tabs[hash]);
		}
	}

	// Help page tab menu script.
	$help.on('click', '.spwoocs-header-nav-menu a', function () {
		activateTab($(this).attr('data-id'));
	});

	// Keep the admin submenu links in sync without a full page reload.
	$adminMenu.on('click', 'ul li a', function (e) {
		var href = $(this).attr('href') || '';
		var hash = href.split('#')[1] || 'get-start';

		if (href.indexOf('page=wcsp_help') === -1 || !tabs[hash]) {
			return;
		}

		e.preventDefault();
		window.location.hash = hash;
		activateTab(tabs[hash]);
	});

	$(window).on('hashchange', activateFromHash);
	activateFromHash();

	$('body').on('click', '.install-now', function (e) {
		var _this = $(this);
		var _href = _this.attr('href');

		_this.addClass('updating-message').html('Installing...');

		$.get(_href, function () {
			location.reload();
		});

		e.preventDefault();
	});

})(jQuery);
