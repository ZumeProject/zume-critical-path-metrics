<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Goals {
    public static function get() {
        // Goals need to be divided by 365 to get the daily goal.
        return [
            'visitors' => 365 / 2,
            'candidates' => 365 / 2,
            'registered' => 365 / 2,
            'pre_training_trainees' => 365 / 2,
            'active_trainees' => 365 / 2,
            'post_training_trainees' => 365 / 2,
            'l1_practitioners' => 365 / 6,
            'l2_practitioners' => 365 / 6,
            'l3_practitioners' => 365 / 6,
        ];
    }
}
