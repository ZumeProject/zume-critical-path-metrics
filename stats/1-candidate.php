<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

add_filter( 'zume_range_stats', function( $request_range, $stats ) {


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
