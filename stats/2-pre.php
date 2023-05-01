<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

add_filter( 'zume_range_stats', function( $stats, $request_range  ) {
    if ( ! ( 'none' === $request_range['filter'] || 'pre' === $request_range['filter'] ) ) {
        return $stats;
    }


    $stats[] = [
        'key' => 'pre_training_trainees',
        'label' => 'Pre-Training Trainees',
        'description' => '',
        'value' => 0,
        'goal' => 0,
        'trend' => 0,
        'category' => 'pre_training_trainees',
        'type' => 'number',
        'public' => true,
    ];


    return $stats;
}, 20, 2 );
