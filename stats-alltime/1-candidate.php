<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

add_filter( 'zume_all_time_stats', function( $stats ) {


    $stats[] = [
        'key' => 'zume_visitors',
        'label' => 'Visitors',
        'description' => 'Visitors to all Zume properties.',
        'value' => 0,
        'category' => 'candidate',
        'type' => 'minutes',
        'public' => true,
    ];


    return $stats;
}, 20, 2 );
