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


    public static function candidates( $params ) {
        global $wpdb;

        $requested_range = self::requested_range( $params );
        $value = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );

        $goals = Zume_Goals::get();
        $goal = $goals['post_training_trainees'];

        return [
            'key' => 'candidates',
            'label' => 'Candidates',
            'link' => 'candidates',
            'description' => 'Number of users who are marked as completed training.',
            'value' => self::format_int( $value ),
            'valence' => self::get_valence( $value, $goal ),
            'goal' => $goal,
            'goal_valence' => self::get_valence( $value, $goal ),
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];
    }
    public static function pre_training_trainees( $params ) {
        global $wpdb;

        $requested_range = self::requested_range( $params );
        $value = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );

        $goals = Zume_Goals::get();
        $goal = $goals['post_training_trainees'];

        return [
            'key' => 'pre_training_trainees',
            'label' => 'Pre-Training Trainees',
            'link' => 'pre_training_trainees',
            'description' => 'Number of users who are marked as completed training.',
            'value' => self::format_int( $value ),
            'valence' => self::get_valence( $value, $goal ),
            'goal' => $goal,
            'goal_valence' => self::get_valence( $value, $goal ),
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];
    }
    public static function active_training_trainees( $params ) {
        global $wpdb;

        $requested_range = self::requested_range( $params );
        $value = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );

        $goals = Zume_Goals::get();
        $goal = $goals['post_training_trainees'];

        return [
            'key' => 'active_training_trainees',
            'label' => 'Active Training Trainees',
            'link' => 'active_training_trainees',
            'description' => 'Number of users who are marked as completed training.',
            'value' => self::format_int( $value ),
            'valence' => self::get_valence( $value, $goal ),
            'goal' => $goal,
            'goal_valence' => self::get_valence( $value, $goal ),
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];
    }
    public static function post_training_trainees( $params ) {
        global $wpdb;

        $requested_range = self::requested_range( $params );
        $value = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );

        $goals = Zume_Goals::get();
        $goal = $goals['post_training_trainees'];

        return [
            'key' => 'post_training_trainees',
            'label' => 'Post-Training Trainees',
            'link' => 'trainees',
            'description' => 'Number of users who are marked as completed training.',
            'value' => self::format_int( $value ),
            'valence' => self::get_valence( $value, $goal ),
            'goal' => $goal,
            'goal_valence' => self::get_valence( $value, $goal ),
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];
    }
    public static function l1_practitioners( $params ) {
        global $wpdb;

        $requested_range = self::requested_range( $params );
        $value = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );

        $goals = Zume_Goals::get();
        $goal = $goals['post_training_trainees'];

        return [
            'key' => 'l1_practitioners',
            'label' => 'L1 Practitioners',
            'link' => 'l1_practitioners',
            'description' => 'Number of users who are marked as completed training.',
            'value' => self::format_int( $value ),
            'valence' => self::get_valence( $value, $goal ),
            'goal' => $goal,
            'goal_valence' => self::get_valence( $value, $goal ),
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];
    }
    public static function l2_practitioners( $params ) {
        global $wpdb;

        $requested_range = self::requested_range( $params );
        $value = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );

        $goals = Zume_Goals::get();
        $goal = $goals['post_training_trainees'];

        return [
            'key' => 'l2_practitioners',
            'label' => 'L2 Practitioners',
            'link' => 'l2_practitioners',
            'description' => 'Number of users who are marked as completed training.',
            'value' => self::format_int( $value ),
            'valence' => self::get_valence( $value, $goal ),
            'goal' => $goal,
            'goal_valence' => self::get_valence( $value, $goal ),
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];
    }
    public static function l3_practitioners( $params ) {
        global $wpdb;

        $requested_range = self::requested_range( $params );
        $value = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );

        $goals = Zume_Goals::get();
        $goal = $goals['post_training_trainees'];

        return [
            'key' => 'l3_practitioners',
            'label' => 'L3 Practitioners',
            'link' => 'l3_practitioners',
            'description' => 'Number of users who are marked as completed training.',
            'value' => self::format_int( $value ),
            'valence' => self::get_valence( $value, $goal ),
            'goal' => $goal,
            'goal_valence' => self::get_valence( $value, $goal ),
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];
    }










    public static function goals( $requested_range ) {
        global $wpdb;
        $goals = Zume_Goals::get();

        $stats = [
            'current_timestamp' => time(),
            'requested_range' => $requested_range,
            'list' => [],
        ];

        $trainees = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );
//        $trainees = $wpdb->get_var( $wpdb->prepare(
//            "SELECT COUNT(DISTINCT(user_id)) as value
//                    FROM wp_dt_reports
//                    WHERE type = 'zume_session'
//                      AND ( subtype = '9' || subtype = '10' )
//                        AND time_end >= %s AND time_end <= %s
//                    ", $requested_range['start_time'], $requested_range['end_time'] ) );

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

    public static function requested_range( $params ) {
        $requested_days = 30;
        if ( isset( $params['days'] ) && ! empty( $params['days'] )  ) {
            $requested_days = absint( $params['days'] );
        }
        $days = $requested_days + 1;
        $compare_days = $requested_days * 2 + 1;
        $filter = 'none';
        if ( isset( $params['filter'] ) && ! empty( $params['filter'] )  ) {
            $filter = sanitize_text_field( $params['filter'] );
        }
        $range = [
            'filter' => $filter,
            'days' => $requested_days,
            'end' => date( 'Y-m-d', strtotime( 'yesterday' ) ),
            'end_time' => strtotime( 'yesterday' ),
            'start' => date( 'Y-m-d', strtotime( '-' . $days . ' days' ) ),
            'start_time' => strtotime( '-' . $days . ' days' ),
            'compare_end' => date( 'Y-m-d', strtotime( '-' . $days  . ' days' ) ),
            'compare_end_time' => strtotime( '-' . $days  . ' days' ),
            'compare_start' => date( 'Y-m-d', strtotime( '-' . $compare_days . ' days' ) ),
            'compare_start_time' => strtotime( '-' . $compare_days . ' days' ),
        ];
        return $range;
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
//(
//[filter] => none
//[days] => 30
//    [end] => 2023-05-10
//    [end_time] => 1683676800
//    [start] => 2023-04-10
//    [start_time] => 1681128596
//    [compare_end] => 2023-04-10
//    [compare_end_time] => 1681128596
//    [compare_start] => 2023-03-11
//    [compare_start_time] => 1678536596
//)
