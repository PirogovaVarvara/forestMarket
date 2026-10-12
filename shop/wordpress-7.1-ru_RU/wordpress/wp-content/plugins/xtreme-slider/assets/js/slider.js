/**
 * XtremeSlider — Frontend JavaScript
 * Supports Default (full-bleed), Cool (track-based), and 3D (perspective) layouts.
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var sliders = document.querySelectorAll('.xs-slider-wrap');
        sliders.forEach(function (el) {
            if (el.dataset.layout === 'options') {
                initOptionsLayout(el);
            } else {
                new XtremeSlider(el);
            }
        });
    });

    /* ======================================================================
       OPTIONS LAYOUT — Clickable cards with HTML detail panel
       ====================================================================== */
    function initOptionsLayout(el) {
        var grid    = el.querySelector('.xs-options-grid');
        var detail  = el.querySelector('.xs-options-detail');
        var data    = el.querySelector('.xs-options-data');

        if (!grid || !detail || !data) return;

        var templates = {};
        data.querySelectorAll('template').forEach(function (tpl) {
            templates[tpl.dataset.index] = tpl.innerHTML;
        });

        function selectCard(card, animate) {
            if (!card) return;

            grid.querySelectorAll('.xs-option-card').forEach(function (c) {
                c.classList.remove('active');
            });
            card.classList.add('active');

            var idx  = card.dataset.index;
            var html = templates[idx] || '';

            var swap = function () {
                if (html && html.trim()) {
                    detail.innerHTML = '<div class="xs-options-detail-content">' + html + '</div>';
                } else {
                    detail.innerHTML = '<div class="xs-options-detail-empty">No content for this option.</div>';
                }
                detail.style.opacity = '1';
                detail.style.transform = 'translateY(0)';
            };

            if (animate === false) {
                swap();
                return;
            }

            detail.style.opacity = '0';
            detail.style.transform = 'translateY(8px)';
            setTimeout(swap, 200);
        }

        grid.addEventListener('click', function (e) {
            var card = e.target.closest('.xs-option-card');
            if (!card) return;
            selectCard(card, true);
        });

        // Preselect the first card on load
        var firstCard = grid.querySelector('.xs-option-card');
        if (firstCard) {
            selectCard(firstCard, false);
        }
    }

    function XtremeSlider(el) {
        this.el        = el;
        this.layout    = el.dataset.layout || 'cool';
        this.visible   = parseInt(el.dataset.visible, 10) || 3;
        this.autoplay  = el.dataset.autoplay === 'true';
        this.speed     = parseInt(el.dataset.speed, 10) || 4000;
        this.total     = parseInt(el.dataset.total, 10) || 0;
        this.fixedHeight = parseInt(el.dataset.fixedHeight, 10) || 0;

        this.track     = el.querySelector('.xs-slider-track');
        this.slides    = el.querySelectorAll('.xs-slide');
        this.prevBtn   = el.querySelector('.xs-nav-prev');
        this.nextBtn   = el.querySelector('.xs-nav-next');
        this.dots      = el.querySelectorAll('.xs-dot');

        this.current   = 0;
        this.timer     = null;

        if (this.total === 0) return;

        this.init();
    }

    XtremeSlider.prototype.init = function () {
        var self = this;

        if (this.layout === '3d') {
            this.init3D();
        } else if (this.layout === 'default') {
            this.initDefault();
        } else {
            this.initCool();
        }

        // Hide arrows when there is nothing to scroll
        this.updateArrowVisibility();

        // Arrow events
        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', function () {
                self.prev();
            });
        }
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', function () {
                self.next();
            });
        }

        // Dot events
        this.dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                var page = parseInt(this.dataset.page, 10);
                self.goTo(page);
            });
        });

        // Autoplay
        if (this.autoplay) {
            this.startAutoplay();

            this.el.addEventListener('mouseenter', function () {
                self.stopAutoplay();
            });
            this.el.addEventListener('mouseleave', function () {
                self.startAutoplay();
            });
        }

        // Touch support
        this.initTouch();

        // Responsive recalc
        window.addEventListener('resize', function () {
            self.recalcLayout();
        });

        // Cool-fixed mode derives slide widths from natural image dimensions, so
        // arrow visibility and track offsets aren't reliable until images have
        // loaded. Re-run the layout each time a slide image loads, plus once on
        // window.load as a final safety net.
        var imgs = this.el.querySelectorAll('.xs-slide-image');
        imgs.forEach(function (img) {
            if (img.complete && img.naturalWidth > 0) return;
            img.addEventListener('load', function () { self.recalcLayout(); }, { once: true });
            img.addEventListener('error', function () { self.recalcLayout(); }, { once: true });
        });
        window.addEventListener('load', function () { self.recalcLayout(); });
    };

    XtremeSlider.prototype.recalcLayout = function () {
        if (this.layout === '3d') {
            this.update3D();
        } else if (this.layout === 'default') {
            this.updateDefault();
        } else {
            this.updateCool();
        }
        this.updateArrowVisibility();
    };

    /* ======================================================================
       DEFAULT LAYOUT — infinite with cloned slides for seamless looping
       ====================================================================== */
    XtremeSlider.prototype.initDefault = function () {
        this.cloneSlides();
        this.updateDefault();
    };

    XtremeSlider.prototype.cloneSlides = function () {
        if (this._cloned) return;
        this._cloned = true;

        var vis = this.getResponsiveVisible();
        var slidesArr = Array.prototype.slice.call(this.slides);
        var count = slidesArr.length;

        // Clone last `vis` slides and prepend
        for (var i = count - vis; i < count; i++) {
            var idx = Math.max(0, i);
            var clone = slidesArr[idx].cloneNode(true);
            clone.classList.add('xs-clone');
            this.track.insertBefore(clone, this.track.firstChild);
        }

        // Clone first `vis` slides and append
        for (var j = 0; j < vis; j++) {
            var idx2 = Math.min(j, count - 1);
            var clone2 = slidesArr[idx2].cloneNode(true);
            clone2.classList.add('xs-clone');
            this.track.appendChild(clone2);
        }

        // Re-query all slides including clones
        this.allSlides = this.track.querySelectorAll('.xs-slide');
        this.cloneCount = vis;
    };

    XtremeSlider.prototype.updateDefault = function () {
        var viewport = this.el.querySelector('.xs-slider-viewport');
        var vpWidth  = viewport.offsetWidth;
        var vis      = this.getResponsiveVisible();
        // Slide width: vis full slides + 2 half-slides (peek) = vis + 1
        var slideW   = vpWidth / (vis + 1);

        var slides = this.allSlides || this.slides;
        for (var i = 0; i < slides.length; i++) {
            slides[i].style.width = slideW + 'px';
        }

        this.moveDefault(false);
    };

    XtremeSlider.prototype.moveDefault = function (animate) {
        var viewport = this.el.querySelector('.xs-slider-viewport');
        var vpWidth  = viewport.offsetWidth;
        var vis      = this.getResponsiveVisible();
        var slideW   = vpWidth / (vis + 1);
        var clones   = this.cloneCount || 0;

        // Offset: position at clone boundary + half-slide peek from the left
        var pos    = this.current + clones;
        var offset = -(pos * slideW) + (slideW * 0.5);

        if (animate === false) {
            this.track.style.transition = 'none';
        } else {
            this.track.style.transition = 'transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
        }

        this.track.style.transform = 'translateX(' + offset + 'px)';

        // Mark center slides with xs-center class
        var allSlides = this.allSlides || this.slides;
        var centerStart = pos;       // first fully visible slide index in allSlides
        var centerEnd   = pos + vis; // exclusive
        for (var s = 0; s < allSlides.length; s++) {
            if (s >= centerStart && s < centerEnd) {
                allSlides[s].classList.add('xs-center');
            } else {
                allSlides[s].classList.remove('xs-center');
            }
        }

        // After transition, silently jump if we're on a clone
        if (animate !== false) {
            var self = this;
            var total = this.total;
            clearTimeout(this._jumpTimer);
            this._jumpTimer = setTimeout(function () {
                var jumped = false;
                if (self.current >= total) {
                    self.current = 0;
                    jumped = true;
                } else if (self.current < 0) {
                    self.current = total - 1;
                    jumped = true;
                }
                if (jumped) {
                    self.moveDefault(false);
                    // Force reflow then restore transition
                    self.track.offsetHeight;
                    self.track.style.transition = 'transform 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                }
            }, 520);
        }
    };

    /* ======================================================================
       SIMPLE LAYOUT
       ====================================================================== */
    XtremeSlider.prototype.initCool = function () {
        this.updateCool();
    };

    XtremeSlider.prototype.updateCool = function () {
        if (this.fixedHeight > 0) {
            // Fixed-height ratio: clear any forced widths so each slide keeps the
            // natural width derived from its image aspect ratio at the fixed height.
            this.slides.forEach(function (slide) {
                slide.style.width = '';
            });
            this.moveCool();
            return;
        }

        var viewport = this.el.querySelector('.xs-slider-viewport');
        var vpWidth  = viewport.offsetWidth;
        var vis      = this.getResponsiveVisible();
        var slideW   = vpWidth / vis;

        this.slides.forEach(function (slide) {
            slide.style.width = slideW + 'px';
        });

        this.moveCool();
    };

    XtremeSlider.prototype.moveCool = function () {
        var offset;
        if (this.fixedHeight > 0) {
            offset = 0;
            for (var i = 0; i < this.current && i < this.slides.length; i++) {
                offset -= this.slides[i].offsetWidth;
            }
        } else {
            var viewport = this.el.querySelector('.xs-slider-viewport');
            var vpWidth  = viewport.offsetWidth;
            var vis      = this.getResponsiveVisible();
            var slideW   = vpWidth / vis;
            offset       = -(this.current * slideW);
        }

        this.track.style.transform = 'translateX(' + offset + 'px)';
        this.updateDots();
    };

    XtremeSlider.prototype.getResponsiveVisible = function () {
        var w = window.innerWidth;
        if (w <= 480) return Math.min(this.visible, 1);
        if (w <= 768) return Math.min(this.visible, 2);
        if (w <= 1024) return Math.min(this.visible, 3);
        return this.visible;
    };

    XtremeSlider.prototype.getMaxPage = function () {
        if (this.layout === '3d') {
            return this.total - 1;
        }
        if (this.layout === 'cool' && this.fixedHeight > 0) {
            var viewport = this.el.querySelector('.xs-slider-viewport');
            var vpWidth = viewport ? viewport.offsetWidth : 0;
            var totalWidth = 0;
            for (var i = 0; i < this.slides.length; i++) {
                totalWidth += this.slides[i].offsetWidth;
            }
            if (totalWidth <= vpWidth) return 0;
            // Smallest index from which the remaining slides still overflow the viewport.
            var sum = 0;
            for (var j = this.slides.length - 1; j >= 0; j--) {
                sum += this.slides[j].offsetWidth;
                if (sum > vpWidth) {
                    return j + 1;
                }
            }
            return 0;
        }
        var vis = this.getResponsiveVisible();
        return Math.max(0, this.total - vis);
    };

    /* ======================================================================
       3D LAYOUT
       ====================================================================== */
    XtremeSlider.prototype.init3D = function () {
        this.update3D();
    };

    XtremeSlider.prototype.update3D = function () {
        var self     = this;
        var viewport = this.el.querySelector('.xs-slider-viewport');
        var vpWidth  = viewport.offsetWidth;

        // How many slides on each side of the center slide
        var sideCount = Math.floor(this.visible / 2);
        if (sideCount < 1) sideCount = 1;

        // Adjust viewport height for fixed height
        if (this.fixedHeight > 0) {
            viewport.style.height = (this.fixedHeight + 60) + 'px';
        } else {
            viewport.style.height = '';
        }

        // For fixed height, don't force a slide width — let images keep their natural ratio
        var useAutoWidth = this.fixedHeight > 0;
        var slideW = Math.min(vpWidth * 0.55, 600);

        this.slides.forEach(function (slide, i) {
            var diff = i - self.current;
            var absDiff = Math.abs(diff);

            if (useAutoWidth) {
                slide.style.width = 'auto';
                slide.style.maxWidth = slideW + 'px';
            } else {
                slide.style.width = slideW + 'px';
                slide.style.maxWidth = '';
            }
            slide.classList.remove('xs-active');

            if (diff === 0) {
                // Center (active) slide
                slide.style.transform = 'translateX(-50%) translateZ(0) scale(1)';
                slide.style.left = '50%';
                slide.style.opacity = '1';
                slide.style.zIndex = String(sideCount + 1);
                slide.classList.add('xs-active');
            } else if (absDiff <= sideCount) {
                // Visible side slides — position dynamically based on distance
                var t = absDiff / sideCount; // 0..1 normalized distance
                var depthZ = -150 * absDiff;
                var rotateY = diff * -25;
                var scale = 1 - (0.15 * absDiff);
                if (scale < 0.4) scale = 0.4;
                var opacity = 1 - (0.3 * absDiff);
                if (opacity < 0.2) opacity = 0.2;
                var zIndex = sideCount + 1 - absDiff;

                // Spread slides evenly across viewport sides
                var spacing = 45 / sideCount; // percentage per step
                var leftPos = diff > 0
                    ? 50 + (spacing * absDiff) + '%'
                    : 50 - (spacing * absDiff) - slideW / vpWidth * 100 + '%';

                slide.style.transform = 'translateX(0) translateZ(' + depthZ + 'px) rotateY(' + rotateY + 'deg) scale(' + scale.toFixed(2) + ')';
                slide.style.left = leftPos;
                slide.style.opacity = String(opacity.toFixed(2));
                slide.style.zIndex = String(Math.max(0, zIndex));
            } else {
                // Hidden slides
                slide.style.transform = 'translateZ(-500px) scale(0.5)';
                slide.style.left = diff > 0 ? '100%' : '-55%';
                slide.style.opacity = '0';
                slide.style.zIndex = '0';
            }
        });

        this.updateDots();
    };

    /* ======================================================================
       ARROW VISIBILITY
       Hide prev/next arrows when all slides fit in the viewport (nothing to scroll).
       Re-evaluated on init and every resize so responsive breakpoints are respected.
       ====================================================================== */
    XtremeSlider.prototype.updateArrowVisibility = function () {
        var hidden = this.getMaxPage() === 0;
        if (this.prevBtn) { this.prevBtn.style.display = hidden ? 'none' : ''; }
        if (this.nextBtn) { this.nextBtn.style.display = hidden ? 'none' : ''; }
    };

    /* ======================================================================
       NAVIGATION
       ====================================================================== */
    XtremeSlider.prototype.next = function () {
        if (this.layout === 'default') {
            // Infinite: go beyond bounds, clone jump handles reset
            this.current = this.current + 1;
        } else {
            var max = this.getMaxPage();
            this.current = this.current >= max ? 0 : this.current + 1;
        }
        this.update();
    };

    XtremeSlider.prototype.prev = function () {
        if (this.layout === 'default') {
            this.current = this.current - 1;
        } else {
            var max = this.getMaxPage();
            this.current = this.current <= 0 ? max : this.current - 1;
        }
        this.update();
    };

    XtremeSlider.prototype.goTo = function (page) {
        this.current = Math.max(0, Math.min(page, this.getMaxPage()));
        this.update();
    };

    XtremeSlider.prototype.update = function () {
        if (this.layout === '3d') {
            this.update3D();
        } else if (this.layout === 'default') {
            this.moveDefault();
        } else {
            this.moveCool();
        }
    };

    XtremeSlider.prototype.updateDots = function () {
        var self = this;
        var activePage;

        if (this.layout === '3d') {
            activePage = this.current;
        } else {
            var vis = this.getResponsiveVisible();
            activePage = Math.floor(this.current / vis);
        }

        this.dots.forEach(function (dot, i) {
            dot.classList.toggle('active', i === activePage);
        });
    };

    /* ======================================================================
       AUTOPLAY
       ====================================================================== */
    XtremeSlider.prototype.startAutoplay = function () {
        var self = this;
        this.stopAutoplay();
        this.timer = setInterval(function () {
            self.next();
        }, this.speed);
    };

    XtremeSlider.prototype.stopAutoplay = function () {
        if (this.timer) {
            clearInterval(this.timer);
            this.timer = null;
        }
    };

    /* ======================================================================
       TOUCH / SWIPE
       ====================================================================== */
    XtremeSlider.prototype.initTouch = function () {
        var self       = this;
        var startX     = 0;
        var startY     = 0;
        var isDragging = false;

        // ── Touch ──────────────────────────────────────────────────────────
        this.el.addEventListener('touchstart', function (e) {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
            isDragging = true;
        }, { passive: true });

        this.el.addEventListener('touchmove', function (e) {
            // Allow vertical scroll if mostly vertical swipe
        }, { passive: true });

        this.el.addEventListener('touchend', function (e) {
            if (!isDragging) return;
            isDragging = false;

            var diffX = e.changedTouches[0].clientX - startX;
            var diffY = e.changedTouches[0].clientY - startY;

            if (Math.abs(diffX) > 40 && Math.abs(diffX) > Math.abs(diffY)) {
                if (diffX < 0) { self.next(); } else { self.prev(); }
            }
        }, { passive: true });

        // ── Mouse drag (default layout only) ───────────────────────────────
        if (this.layout !== 'default') return;

        var mouseDown  = false;
        var mouseMoved = false;
        var track      = this.track;

        this.el.addEventListener('mousedown', function (e) {
            // Prevent text selection and native image drag during mouse-drag
            e.preventDefault();
            startX     = e.clientX;
            mouseDown  = true;
            mouseMoved = false;
            track.style.transition = 'none';
        });

        window.addEventListener('mousemove', function (e) {
            if (!mouseDown) return;
            if (Math.abs(e.clientX - startX) > 5) {
                mouseMoved = true;
                self.el.style.cursor = 'grabbing';
            }
        });

        window.addEventListener('mouseup', function (e) {
            if (!mouseDown) return;
            mouseDown = false;
            self.el.style.cursor = '';
            track.style.transition = '';

            if (!mouseMoved) return;

            var diffX = e.clientX - startX;
            if (Math.abs(diffX) > 40) {
                if (diffX < 0) { self.next(); } else { self.prev(); }
            } else {
                // Snap back without navigating
                self.moveDefault(false);
            }
        });

        // Prevent click-through on links after a drag
        this.el.addEventListener('click', function (e) {
            if (mouseMoved) {
                e.preventDefault();
                e.stopPropagation();
                mouseMoved = false;
            }
        }, true);
    };

})();
