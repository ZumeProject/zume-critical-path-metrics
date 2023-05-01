<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

add_filter( 'zume_all_time_stats', function( $stats, $filter  ) {
    if ( ! ( 'none' === $filter || 'l1' === $filter ) ) {
        return $stats;
    }

    $stats[] = [
        'key' => 'zume_visitors',
        'label' => 'Visitors',
        'description' => '',
        'value' => 0,
        'category' => 'l1',
        'type' => 'number',
        'public' => true,
    ];


    return $stats;
}, 20, 2 );
