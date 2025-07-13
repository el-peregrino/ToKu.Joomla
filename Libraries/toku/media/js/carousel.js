/**
 * ToKu.Joomla
 * Library for Joomla 5
 *
 * (C) 2025 ToKu <https://www.toku.cz>
 * GNU General Public License version 3 or later
 */

/**
 * The infinite carousel script
 * version: 1.0.2
 */
jQuery(function ($) {
  $('[data-js="carousel-infinite"]').each(function () {
    // get the carousel elements
    const $carousel = $(this);
    const $container = $carousel.find('[data-js="container"]');
    const $prev = $carousel.find('[data-js="prev"]');
    const $next = $carousel.find('[data-js="next"]');

    // get carousel items
    const $boxes = $container.find('[data-js="box"]');
    const boxCount = $boxes.length;
    if (boxCount < 2) return; // prevents issues with only 1 item
    
    // get box size
    const boxWidth = $boxes.first().outerWidth(true);

    // prepare variables for the loop logic
    let currentIndex = 0;
    let prevIndex = 0;
    const interval = parseInt($carousel.data('interval') || 3000, 10);
    const autoplay = $carousel.data('autoplay') !== false;
    const indicators = $carousel.data('indicators') !== false;
    const direction = ($carousel.data('direction') || 'left').toLowerCase();
    let timer = null;

    function updateIndicators() {
        if (!indicators) return;
        const $dots = $carousel.find('[data-js="indicators"]').find('li');
        $dots.removeClass('active');
        $dots.eq(currentIndex % boxCount).addClass('active');
    }

    function slideLeft() {
        $container.addClass('sliding-transition');
        // move index
        prevIndex = currentIndex;
        currentIndex = (currentIndex + 1) % boxCount;

        // transform the container
        $container.css('transform', `translateX(-${boxWidth}px)`);

        setTimeout(() => {
            $boxes.eq(prevIndex).appendTo($container);
            $container.removeClass('sliding-transition');
            $container.css('transform', '');
        }, 500);

        updateIndicators();
    }

    function slideRight() {
        // move index
        prevIndex = currentIndex;
        currentIndex = (currentIndex - 1 + boxCount) % boxCount;

        // transform the container
        $container.css('transform', `translateX(-${boxWidth}px)`);
        $boxes.eq(currentIndex).prependTo($container);

        // animation (the overall transition take 500ms)
        setTimeout(() => {
            $container.css('transform', '');
            $container.addClass('sliding-transition');
        }, 100);

        setTimeout(() => {
            $container.removeClass('sliding-transition');
        }, 400);

        updateIndicators();
    }

    function startAutoplay() {
        if (!autoplay) return;
        clearInterval(timer);
        timer = setInterval(()=> {
            direction === 'right' ? slideRight() : slideLeft();
        }, interval);
    }

    function pauseAutoplay() {
        clearInterval(timer);
    }

    $container.on('mouseenter', pauseAutoplay);
    $container.on('mouseleave', startAutoplay);

    // manual controls
    $prev.on('click', () => {
        slideRight();
        startAutoplay();
    });

    $next.on('click', () => {
        slideLeft();
        startAutoplay();
    });

    // swipe support
    let startX = 0;
    $container.on('touchstart', e => {
      startX = e.originalEvent.touches[0].clientX;
    });

    $container.on('touchend', e => {
      const deltaX = e.originalEvent.changedTouches[0].clientX - startX;
      if (Math.abs(deltaX) > 30) {
        deltaX < 0 ? slideLeft() : slideRight();
        startAutoplay();
      }
    });

    // autoplay
    startAutoplay();
  });
});
