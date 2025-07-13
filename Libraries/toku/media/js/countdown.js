/**
 * ToKu.Joomla
 * Library for Joomla 5
 *
 * (C) 2025 ToKu <https://www.toku.cz>
 * GNU General Public License version 3 or later
 */

/**
 * The countdown script
 * version: 1.0.10
 */
jQuery(function ($) {
    $('[data-js="countdown"]').each(function () {
        const $box = $(this);
        const targetTime = new Date($box.data('countdown')).getTime();

        if (isNaN(targetTime)) return;

        const $days = $box.find('[data-js="countdown-day"]');
        const $hours = $box.find('[data-js="countdown-hour"]');
        const $minutes = $box.find('[data-js="countdown-minute"]');
        const $seconds = $box.find('[data-js="countdown-second"]');

        function update() {
            const now = new Date().getTime();
            let diff = targetTime - now;

            if (diff <= 0) {
                $days.add($hours).add($minutes).add($seconds).text('00');
                $box.attr('data-expired', 'true').addClass('countdown-expired');
                clearInterval(tid);
                return;
            }

            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
            const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
            const minutes = Math.floor((diff / (1000 * 60)) % 60);
            const seconds = Math.floor((diff / 1000) % 60);

            $days.text(String(days).padStart(2, '0'));
            $hours.text(String(hours).padStart(2, '0'));
            $minutes.text(String(minutes).padStart(2, '0'));
            $seconds.text(String(seconds).padStart(2, '0'));
        }

        update();
        const tid = setInterval(update, 1000);
    });
});