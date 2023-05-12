<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Stats_Endpoints
{
    public $namespace = 'zume_stats/v1';
    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    public function __construct() {
        if ( $this->dt_is_rest() ) {
            add_action( 'rest_api_init', [ $this, 'add_api_routes' ] );
            add_filter( 'dt_allow_rest_access', [ $this, 'authorize_url' ], 10, 1 );
        }
    }
    public function add_api_routes() {
        $namespace = $this->namespace;

        register_rest_route(
            $namespace, '/candidates', [
                'methods'  => [ 'GET', 'POST' ],
                'callback' => [ $this, 'candidates' ],
                'permission_callback' => '__return_true'
            ]
        );
        register_rest_route(
            $namespace, '/pre_training_trainees', [
                'methods'  => [ 'GET', 'POST' ],
                'callback' => [ $this, 'pre_training_trainees' ],
                'permission_callback' => '__return_true'
            ]
        );
        register_rest_route(
            $namespace, '/active_training_trainees', [
                'methods'  => [ 'GET', 'POST' ],
                'callback' => [ $this, 'active_training_trainees' ],
                'permission_callback' => '__return_true'
            ]
        );
        register_rest_route(
            $namespace, '/post_training_trainees', [
                'methods'  => [ 'GET', 'POST' ],
                'callback' => [ $this, 'post_training_trainees' ],
                'permission_callback' => '__return_true'
            ]
        );
        register_rest_route(
            $namespace, '/l1_practitioners', [
                'methods'  => [ 'GET', 'POST' ],
                'callback' => [ $this, 'l1_practitioners' ],
                'permission_callback' => '__return_true'
            ]
        );
        register_rest_route(
            $namespace, '/l2_practitioners', [
                'methods'  => [ 'GET', 'POST' ],
                'callback' => [ $this, 'l2_practitioners' ],
                'permission_callback' => '__return_true'
            ]
        );
        register_rest_route(
            $namespace, '/l3_practitioners', [
                'methods'  => [ 'GET', 'POST' ],
                'callback' => [ $this, 'l3_practitioners' ],
                'permission_callback' => '__return_true'
            ]
        );





        register_rest_route(
            $namespace, '/candidates/hero', [
                'methods'  => [ 'GET', 'POST' ],
                'callback' => [ $this, 'candidates_hero' ],
                'permission_callback' => '__return_true'
            ]
        );

    }
    public function candidates( WP_REST_Request $request ) {
        return Zume_Query::candidates( dt_recursive_sanitize_array( $request->get_params() ) );
    }
    public function pre_training_trainees( WP_REST_Request $request ) {
        return Zume_Query::pre_training_trainees( dt_recursive_sanitize_array( $request->get_params() ) );
    }
    public function active_training_trainees( WP_REST_Request $request ) {
        return Zume_Query::active_training_trainees( dt_recursive_sanitize_array( $request->get_params() ) );
    }
    public function post_training_trainees( WP_REST_Request $request ) {
        return Zume_Query::post_training_trainees( dt_recursive_sanitize_array( $request->get_params() ) );
    }
    public function l1_practitioners( WP_REST_Request $request ) {
        return Zume_Query::l1_practitioners( dt_recursive_sanitize_array( $request->get_params() ) );
    }
    public function l2_practitioners( WP_REST_Request $request ) {
        return Zume_Query::l2_practitioners( dt_recursive_sanitize_array( $request->get_params() ) );
    }
    public function l3_practitioners( WP_REST_Request $request ) {
        return Zume_Query::l3_practitioners( dt_recursive_sanitize_array( $request->get_params() ) );
    }








    public function candidates_hero( WP_REST_Request $request ) {
        $params = dt_recursive_sanitize_array( $request->get_params() );
        $requested_range = $this->requested_range( $params );
        return Zume_Query::candidate_hero( $requested_range );
    }

    public function endpoint_candidates( WP_REST_Request $request ) {
        $params = $request->get_params();

        $value = 100;
        $goal = 90;
        $trend = 110;

        $requested_range = $this->requested_range( $params );
        $stats = [
            'current_timestamp' => time(),
        ];
        $stats['requested_range'] = $requested_range;
        $stats['hero'] = [
            'key' => 'candidate',
            'label' => 'Candidates',
            'description' => 'Candidates are visitors',
            'value' => $value, // current value
            'goal' => $goal, // value set as goal
            'goal_color' => $this->get_valence( $value, $goal ),
            'trend' => $trend, // value from previous block of time
            'trend_percent' => $this->get_valence( $value, $trend ),
            'category' => 'candidate',
        ];

        $stats['facts'][] = [
            'key' => 'visitors',
            'label' => 'Visitors',
            'description' => 'Visitors to all Zume properties.',
            'value' => 0,
            'goal' => 0,
            'trend' => 0,
            'category' => 'candidate',
        ];
        $stats['facts'][] = [
            'key' => 'registrations',
            'label' => 'Registrations',
            'description' => 'Registrations to all Zume properties.',
            'value' => 0,
            'goal' => 0,
            'trend' => 0,
            'category' => 'candidate',
        ];

        return $stats;

    }
    public function get_valence( $value, $compare ) {
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

    public function endpoint_list( WP_REST_Request $request ) {

        $params = $request->get_params();

        global $wpdb;
        $list = $wpdb->get_results( "SELECT post_title FROM $wpdb->posts WHERE post_type = 'contacts' LIMIT 100" );

        return $list;
    }
    public function requested_range( $params ) {
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

    public function authorize_url( $authorized ){
        if ( isset( $_SERVER['REQUEST_URI'] ) && strpos( sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ), $this->namespace  ) !== false ) {
            $authorized = true;
        }
        return $authorized;
    }
    public function dt_is_rest( $namespace = null ) {
        // https://github.com/DiscipleTools/disciple-tools-theme/blob/a6024383e954cec2ac4e7a1a31fb4601c940f485/dt-core/global-functions.php#L60
        // Added here so that in non-dt sites there is no dependency.
        $prefix = rest_get_url_prefix();
        if ( defined( 'REST_REQUEST' ) && REST_REQUEST
            || isset( $_GET['rest_route'] )
            && strpos( trim( sanitize_text_field( wp_unslash( $_GET['rest_route'] ) ), '\\/' ), $prefix, 0 ) === 0 ) {
            return true;
        }
        $rest_url    = wp_parse_url( site_url( $prefix ) );
        $current_url = wp_parse_url( add_query_arg( array() ) );
        $is_rest = strpos( $current_url['path'], $rest_url['path'], 0 ) === 0;
        if ( $namespace ){
            return $is_rest && strpos( $current_url['path'], $namespace ) != false;
        } else {
            return $is_rest;
        }
    }
}
Zume_Stats_Endpoints::instance();
