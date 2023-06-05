<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Query {

    public static function sample( $params ) {
        $negative_stat = false;
        if ( isset( $params['negative_stat'] ) && $params['negative_stat'] ) {
            $negative_stat = $params['negative_stat'];
        }

        $value = rand(100, 1000);
        $goal = rand(500, 700);
        $trend = rand(500, 700);
        return [
            'key' => 'sample',
            'label' => 'Sample',
            'link' => 'sample',
            'description' => 'Sample description.',
            'value' => self::format_int( $value ),
            'valence' => self::get_valence( $value, $goal, $negative_stat ),
            'goal' => $goal,
            'goal_valence' => self::get_valence( $value, $goal, $negative_stat ),
            'goal_percent' => self::get_percent( $value, $goal ),
            'trend' => $trend,
            'trend_valence' => self::get_valence( $value, $trend, $negative_stat ),
            'trend_percent' => self::get_percent( $value, $trend ),
            'negative_stat' => $negative_stat,
        ];
    }

    public static function anonymous( $params ) {
        global $wpdb;

        $requested_range = self::requested_range( $params );
        if ( 'all' === $requested_range['days'] ) {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );
        } else {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                      AND time_end > %s AND time_end < %s
                    ", $requested_range['start_time'], $requested_range['end_time'] ) );
        }

        $goals = Zume_Goals::get();
        $goal = $goals['post_training_trainees'];

        return [
            'key' => 'anonymous',
            'label' => 'Anonymouss',
            'link' => 'anonymous',
            'description' => 'Number of users who are marked as completed training.',
            'value' => self::format_int( $value ),
            'valence' => self::get_valence( $value, $goal ),
            'goal' => $goal,
            'goal_valence' => self::get_valence( $value, $goal ),
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];
    }


    public static function registrants( $params ) {
        global $wpdb;

        $data = [
            'key' => 'registrants',
            'label' => 'Registrants',
            'link' => 'registrants',
            'description' => 'Number of who have registered in the time frame.',
            'trend' => 0,
            'trend_valence' => 'valence-grey'
        ];
        $goals = Zume_Goals::get();
        $goal = $goals['registrants'];

        $requested_range = self::requested_range( $params );
        if ( 'all' === $requested_range['days'] ) {
            $value = $wpdb->get_var( $wpdb->prepare(
                "
                    SELECT COUNT( DISTINCT(user_id)) as count
                    FROM $wpdb->dt_reports
                    WHERE type = 'zume'
                    AND subtype = 'registration'
                    AND value = 0
                    GROUP BY subtype, value;
                    " ) );

            $data['value'] = self::format_int( $value );
            $data['valence'] = self::get_valence( $value, $goal );
            $data['goal'] = $goal;
            $data['goal_valence'] = self::get_valence( $value, $goal );

        } else {
            $value = $wpdb->get_var( $wpdb->prepare(
                "
                    SELECT COUNT( DISTINCT(user_id)) as count
                    FROM $wpdb->dt_reports
                    WHERE type = 'zume'
                    AND subtype = 'registration'
                    AND value = 0
                    AND time_end >= %s AND time_end <= %s
                    GROUP BY subtype, value;
                    ", $requested_range['start_time'], $requested_range['end_time'] ) );

            $trend = $wpdb->get_var( $wpdb->prepare(
                "
                    SELECT COUNT( DISTINCT(user_id)) as count
                    FROM $wpdb->dt_reports
                    WHERE type = 'zume'
                    AND subtype = 'registration'
                    AND value = 0
                    AND time_end >= %s AND time_end <= %s
                    GROUP BY subtype, value;
                    ", $requested_range['compare_start_time'], $requested_range['compare_end_time'] ) );

            $data['value'] = self::format_int( $value );
            $data['valence'] = self::get_valence( $value, $goal );
            $data['goal'] = $goal;
            $data['goal_valence'] = self::get_valence( $value, $goal );

            if ( $trend ) {
                $data['trend'] = $trend;
                $data['trend_valence'] = self::get_valence( $value, $trend );
            }

        }

        return $data;

    }
    public static function active_training_trainees( $params ) {
        global $wpdb;

        $requested_range = self::requested_range( $params );
        if ( 'all' === $requested_range['days'] ) {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );
        } else {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                      AND time_end > %s AND time_end < %s
                    ", $requested_range['start_time'], $requested_range['end_time'] ) );
        }

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
        if ( 'all' === $requested_range['days'] ) {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );
        } else {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                      AND time_end > %s AND time_end < %s
                    ", $requested_range['start_time'], $requested_range['end_time'] ) );
        }


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
    public static function s1_practitioners( $params ) {
        global $wpdb;

        $requested_range = self::requested_range( $params );
        if ( 'all' === $requested_range['days'] ) {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );
        } else {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                      AND time_end > %s AND time_end < %s
                    ", $requested_range['start_time'], $requested_range['end_time'] ) );
        }

        $goals = Zume_Goals::get();
        $goal = $goals['post_training_trainees'];

        return [
            'key' => 's1_practitioners',
            'label' => 'S1 (Partial)',
            'link' => 's1_practitioners',
            'description' => 'Number of users who are marked as completed training.',
            'value' => self::format_int( $value ),
            'valence' => self::get_valence( $value, $goal ),
            'goal' => $goal,
            'goal_valence' => self::get_valence( $value, $goal ),
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];
    }
    public static function s2_practitioners( $params ) {
        global $wpdb;

        $requested_range = self::requested_range( $params );
        if ( 'all' === $requested_range['days'] ) {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );
        } else {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                      AND time_end > %s AND time_end < %s
                    ", $requested_range['start_time'], $requested_range['end_time'] ) );
        }

        $goals = Zume_Goals::get();
        $goal = $goals['post_training_trainees'];

        return [
            'key' => 's2_practitioners',
            'label' => 'S2 (Completed)',
            'link' => 's2_practitioners',
            'description' => 'Number of users who are marked as completed training.',
            'value' => self::format_int( $value ),
            'valence' => self::get_valence( $value, $goal ),
            'goal' => $goal,
            'goal_valence' => self::get_valence( $value, $goal ),
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];
    }
    public static function s3_practitioners( $params ) {
        global $wpdb;

        $requested_range = self::requested_range( $params );
        if ( 'all' === $requested_range['days'] ) {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );
        } else {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                      AND time_end > %s AND time_end < %s
                    ", $requested_range['start_time'], $requested_range['end_time'] ) );
        }

        $goals = Zume_Goals::get();
        $goal = $goals['post_training_trainees'];

        return [
            'key' => 's3_practitioners',
            'label' => 'S3 (Multiplying)',
            'link' => 's3_practitioners',
            'description' => 'Number of users who are marked as completed training.',
            'value' => self::format_int( $value ),
            'valence' => self::get_valence( $value, $goal ),
            'goal' => $goal,
            'goal_valence' => self::get_valence( $value, $goal ),
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];
    }
    public static function training_elements( $params ) {
        global $wpdb;
        $items = zume_elements();

        $list = $wpdb->get_results( $wpdb->prepare(
            "
                    SELECT subtype, COUNT(*) as value
                    FROM $wpdb->dt_reports
                    WHERE subtype LIKE 'training_%'
                    GROUP BY subtype
                    " ), ARRAY_A );

        foreach( $list as $index => $value ) {
            $list[$index]['value'] = (int) $value['value'];
            $list[$index]['label'] = $items[$value['subtype']]['label'];
        }

        return $list;
    }



    public static function trainees_some( $params ) {
        global $wpdb;

        $requested_range = self::requested_range( $params );
        if ( 'all' === $requested_range['days'] ) {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );
        } else {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                      AND time_end > %s AND time_end < %s
                    ", $requested_range['start_time'], $requested_range['end_time'] ) );
        }

        $goals = Zume_Goals::get();
        $goal = $goals['post_training_trainees'];

        return [
            'key' => 'trainees_some',
            'label' => 'Some Training',
            'link' => 'trainees_some',
            'description' => 'Number of people who have received some training. This is an attempt to de-duplicate users who have moved through at least one training element.',
            'value' => self::format_int( $value ),
            'valence' => self::get_valence( $value, $goal ),
            'goal' => $goal,
            'goal_valence' => self::get_valence( $value, $goal ),
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];
    }
    public static function trainees_full( $params ) {
        global $wpdb;

        $requested_range = self::requested_range( $params );
        if ( 'all' === $requested_range['days'] ) {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );
        } else {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                      AND time_end > %s AND time_end < %s
                    ", $requested_range['start_time'], $requested_range['end_time'] ) );
        }

        $goals = Zume_Goals::get();
        $goal = $goals['post_training_trainees'];

        return [
            'key' => 'trainees_full',
            'label' => 'Fully Trained',
            'link' => 'trainees_full',
            'description' => 'Number of users who are marked as completed training.',
            'value' => self::format_int( $value ),
            'valence' => self::get_valence( $value, $goal ),
            'goal' => $goal,
            'goal_valence' => self::get_valence( $value, $goal ),
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];
    }
    public static function trainees_list( $params ) {
        global $wpdb;

        $list = $wpdb->get_results( $wpdb->prepare(
            "
                    SELECT ID, display_name, user_registered
                    FROM $wpdb->users
                    ORDER BY user_registered DESC
                    LIMIT 100
                    " ) );

        return $list;
    }
    public static function practitioners( $params ) {
        global $wpdb;

        $requested_range = self::requested_range( $params );
        if ( 'all' === $requested_range['days'] ) {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );
        } else {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                      AND time_end > %s AND time_end < %s
                    ", $requested_range['start_time'], $requested_range['end_time'] ) );
        }

        $goals = Zume_Goals::get();
        $goal = $goals['post_training_trainees'];

        return [
            'key' => 's3_practitioners',
            'label' => 'S3 (Multiplying)s',
            'link' => 's3_practitioners',
            'description' => 'Number of users who are marked as completed training.',
            'value' => self::format_int( $value ),
            'valence' => self::get_valence( $value, $goal ),
            'goal' => $goal,
            'goal_valence' => self::get_valence( $value, $goal ),
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];
    }
    public static function churches( $params ) {
        global $wpdb;

        $requested_range = self::requested_range( $params );
        if ( 'all' === $requested_range['days'] ) {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                    " ) );
        } else {
            $value = $wpdb->get_var( $wpdb->prepare(
                "SELECT COUNT(DISTINCT(user_id)) as value
                    FROM wp_dt_reports
                    WHERE type = 'zume_session'
                      AND ( subtype = '9' || subtype = '10' )
                      AND time_end > %s AND time_end < %s
                    ", $requested_range['start_time'], $requested_range['end_time'] ) );
        }

        $goals = Zume_Goals::get();
        $goal = $goals['post_training_trainees'];

        return [
            'key' => 's3_practitioners',
            'label' => 'S3 (Multiplying)s',
            'link' => 's3_practitioners',
            'description' => 'Number of users who are marked as completed training.',
            'value' => self::format_int( $value ),
            'valence' => self::get_valence( $value, $goal ),
            'goal' => $goal,
            'goal_valence' => self::get_valence( $value, $goal ),
            'trend' => 0,
            'trend_valence' => 'valence-grey',
        ];
    }


    public static function format_int( $int ) {
        return number_format( $int, 0, '.', ',' );
    }
    public static function get_valence( $value, $compare, $negative_stat = false ) {
        $percent = self::get_percent( $value, $compare );

        if ( $negative_stat ) {
            if ( $percent > 20 ) {
                $valence = 'valence-darkred';
            } else if ( $percent > 10 ) {
                $valence = 'valence-red';
            } else if ( $percent < -10 ) {
                $valence = 'valence-green';
            } else if ( $percent < -20 ) {
                $valence = 'valence-darkgreen';
            } else {
                $valence = 'valence-grey';
            }
        } else {
            if ( $percent > 20 ) {
                $valence = 'valence-darkgreen';
            } else if ( $percent > 10 ) {
                $valence = 'valence-green';
            } else if ( $percent < -10 ) {
                $valence = 'valence-red';
            } else if ( $percent < -20 ) {
                $valence = 'valence-darkred';
            } else {
                $valence = 'valence-grey';
            }
        }


        return $valence;
    }
    public static function get_percent( $value, $compare ) {
        $percent =  ( $value / $compare ) * 100;
        if ( $percent > 100 ) {
            $percent = round( $percent - 100, 1 );
        } else if ( $percent < 100 ) {
            $percent = round( (100 - $percent), 1) * -1;
        } else {
            $percent = 0;
        }
        return $percent;
    }
    public static function requested_range( $params ) {

        $requested_days = 'all';
        $days = 10000;
        $compare_days = 10000;
        if ( isset( $params['filter'] ) ) {
            if ( $params['filter'] < 1 ) {
                $requested_days = 'all';
                $days = 10000;
                $compare_days = 10000;
            } else {
                $requested_days = absint( $params['filter'] );
                $days = $requested_days + 1;
                $compare_days = $requested_days * 2 + 1;
            }
        }

        $range = [
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

    public static function log( $params ) {

        $time = strtotime( 'Today -'.$params['days_ago'].' days' );

        $contact_id = Disciple_Tools_Users::get_contact_for_user($params['user_id']);

        return dt_report_insert( [
            'type' => 'zume',
            'subtype' => $params['subtype'],
            'post_id' => $contact_id,
            'value' => $params['value'],
            'grid_id' => $params['grid_id'],
            'label' => $params['label'],
            'lat' => $params['lat'],
            'lng' => $params['lng'],
            'level' => $params['level'],
            'user_id' => $params['user_id'],
            'time_end' => $time,
            'hash' => hash('sha256', maybe_serialize($params)  . time() ),
        ] );
    }

}
