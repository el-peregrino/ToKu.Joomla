<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_upcomingevent
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

/**
 * Layout variables
 * -----------------
 * @var   array  $displayData  Array with all the given attributes for the countdown counter.
 *                             Contains [countdown, expired, labels]
 */

\defined('_JEXEC') or die;

// retrieve values from $display data
/** @var string[] $labels */
$labels = $displayData['labels'];

?>

<div data-js="countdown" data-countdown="<?= $displayData['countdown']; ?>" class="countdown">
    <div class="countdown-cont">
        <div class="countdown-block">
            <div data-js="countdown-day" class="countdown-digit">00</div>
            <div class="countdown-label"><?= $labels[0]; ?></div>
        </div>
        <div class="countdown-block">
            <div data-js="countdown-hour" class="countdown-digit">00</div>
            <div class="countdown-label"><?= $labels[1]; ?></div>
        </div>
        <div class="countdown-block">
            <div data-js="countdown-minute" class="countdown-digit">00</div>
            <div class="countdown-label"><?= $labels[2]; ?></div>
        </div>
        <div class="countdown-block">
            <div data-js="countdown-second" class="countdown-digit">00</div>
            <div class="countdown-label"><?= $labels[3]; ?></div>
        </div>
    </div>
    <div class="countdown-message" data-js="countdown-expired">
        <div class="countdown-text"><?= $displayData['expired']; ?></div>
    </div>
</div>