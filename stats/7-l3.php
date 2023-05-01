<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

add_filter( 'zume_range_stats', function( $stats, $request_range ) {
    if ( ! ( 'none' === $request_range['filter'] || 'l3' === $request_range['filter'] ) ) {
        return $stats;
    }

    $stats[] = [
        'key' => 'l3_practitioners',
        'label' => 'L3 Practitioners',
        'description' => '',
        'value' => 0,
        'goal' => 0,
        'trend' => 0,
        'category' => 'l3',
        'type' => 'number',
        'public' => true,
    ];

    return $stats;
}, 70, 2 );
