<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Query {
    public static function format_int( $int ) {
        return number_format( $int, 0, '.', ',' );
    }
    public static function get_valence( $value, $compare ) {
        $percent = round( ( $value / $compare ) * 100, 0 );

        $valence = 'valence-grey';
        if ( $percent > 120 ) {
            $valence = 'valence-darkgreen';
        } else if ( $percent > 110 ) {
            $valence = 'valence-green';
        } else if ( $percent < 80 ) {
            $valence = 'valence-red';
        } else if ( $percent < 90 ) {
            $valence = 'valence-darkred';
        }

        return $valence;
    }

    public static function goals( $requested_range ) {
        global $wpdb;
        $goals = Zume_Goals::get();

        $stats = [
            'current_timestamp' => time(),
            'requested_range' => $requested_range,
            'list' => [],
        ];


        $trainees = $wpdb->get_var(
            "SELECT COUNT(*)
                    FROM $wpdb->usermeta
                    WHERE meta_key = 'zume_training_complete'

                    " );

        $stats['list'][] = [
            'key' => 'trainees',
            'label' => 'Trainees',
            'link' => 'trainees',
            'description' => 'Number of users who are marked as completed training.',
            'value' => self::format_int( $trainees ),
            'goal' => $goals['post_training_trainees'],
            'goal_valence' => self::get_valence( $trainees, $goals['post_training_trainees'] ),
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];

        $stats['list'][] = [
            'key' => 'practitioners',
            'label' => 'L1 Practitioners',
            'link' => 'l1_practitioners',
            'description' => 'Description',
            'value' => 0,
            'goal' => 0,
            'goal_valence' => 'valence-grey',
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];

        $stats['list'][] = [
            'key' => 'churches',
            'label' => 'Churches',
            'link' => 'churches',
            'description' => 'Description',
            'value' => 0,
            'goal' => 0,
            'goal_valence' => 'valence-grey',
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];
        return $stats;
    }
    public static function candidate_hero( $requested_range ) {
        $stats = [
            'current_timestamp' => time(),
            'requested_range' => $requested_range,
        ];
        $stats[] = [
            'key' => 'registrations',
            'label' => 'Registrations',
            'description' => 'Registrations to all Zume properties.',
            'value' => 0,
            'goal' => 0,
            'trend' => 0,
            'category' => 'candidate',
            'type' => 'number',
            'public' => true,
        ];
        return $stats;
    }


}
