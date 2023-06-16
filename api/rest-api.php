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
            $namespace, '/total', [
                'methods'  => [ 'GET', 'POST' ],
                'callback' => [ $this, 'total' ],
                'permission_callback' => '__return_true'
            ]
        );
        register_rest_route(
            $namespace, '/location', [
                'methods'  => [ 'GET', 'POST' ],
                'callback' => [ $this, 'location' ],
                'permission_callback' => '__return_true'
            ]
        );

        register_rest_route(
            $namespace, '/list', [
                'methods'  => [ 'GET', 'POST' ],
                'callback' => [ $this, 'list' ],
                'permission_callback' => '__return_true'
            ]
        );
        register_rest_route(
            $namespace, '/map', [
                'methods'  => [ 'GET', 'POST' ],
                'callback' => [ $this, 'map' ],
                'permission_callback' => '__return_true'
            ]
        );
        register_rest_route(
            $namespace, '/training_elements', [
                'methods'  => [ 'GET', 'POST' ],
                'callback' => [ $this, 'training_elements' ],
                'permission_callback' => '__return_true'
            ]
        );

        register_rest_route(
            $namespace, '/log', [
                'methods'  => [ 'GET', 'POST' ],
                'callback' => [ $this, 'log' ],
                'permission_callback' => '__return_true'
            ]
        );

        // dev
        register_rest_route(
            $namespace, '/sample', [
                'methods'  => [ 'GET', 'POST' ],
                'callback' => [ $this, 'sample' ],
                'permission_callback' => '__return_true'
            ]
        );
    }

    /**
     * @param WP_REST_Request $request
     * @return array|WP_Error
     */
    public function total( WP_REST_Request $request ) {
        $params =  dt_recursive_sanitize_array( $request->get_params() );
        if ( ! isset( $params['stage'] ) ) {
            return new WP_Error( 'no_stage', __( 'No stage key provided.', 'zume' ), array( 'status' => 400 ) );
        }

        if ( ! isset( $params['negative_stat'] ) ) {
            $params['negative_stat'] = false;
        }

        if ( ! isset( $params['range'] ) ) {
            $params['range'] = false;
        }

        switch( $params['stage'] ) {
            case 'anonymous':
                return $this->total_anonymous( $params );
            case 'registrants':
                return $this->total_registrants( $params );
            case 'att':
                return $this->total_att( $params );
            case 'ptt':
                return $this->total_ptt( $params );
            case 's1':
                return $this->total_s1( $params );
            case 's2':
                return $this->total_s2( $params );
            case 's3':
                return $this->total_s3( $params );
            case 'facilitator':
                return $this->total_facilitator( $params );
            case 'early':
                return $this->total_early( $params );
            case 'advanced':
                return $this->total_advanced( $params );
            default:
                return $this->general( $params );
        }
    }
    public function total_anonymous( $params ) {
        $negative_stat = $params['negative_stat'];
        $value = rand(100, 1000);
        $goal = rand(500, 700);
        $trend = rand(500, 700);

        switch( $params['key'] ) {

            case 'total_registrations':
                return [
                    'key' => 'total_registrations',
                    'label' => 'Total Registrations',
                    'description' => 'Total registrations over the entire history of the project',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'visitors':
                return [
                    'key' => 'visitors',
                    'label' => 'Visitors',
                    'description' => 'Visitors to some content on the website (not including bounces).',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'registrations':
                return [
                    'key' => 'registrations',
                    'label' => 'Registrations',
                    'description' => 'Total registrations to the system',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'coach_requests':
                return [
                    'key' => 'coach_requests',
                    'label' => 'Coach Requests',
                    'description' => 'Responses to the "Request a Coach" CTA',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'joined_online_training':
                return [
                    'key' => 'joined_online_training',
                    'label' => 'Joined Online Training',
                    'description' => 'People who have responded the online training CTA',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'total_anonymous':
            default:
                return [
                    'key' => 'total_anonymous',
                    'label' => 'Anonymous',
                    'description' => 'Sample description.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
        }

    }
    public function total_registrants( $params ) {
        $negative_stat = $params['negative_stat'];
        $value = rand(100, 1000);
        $goal = rand(500, 700);
        $trend = rand(500, 700);

        switch( $params['key'] ) {


            case 'locations':
                return [
                    'key' => 'locations',
                    'label' => 'Locations',
                    'description' => 'Cumulative number of locations in this stage.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => 0,
                    'goal_valence' => 'valence-grey',
                    'goal_percent' => 0,
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];

            case 'countries':
                return [
                    'key' => 'countries',
                    'label' => 'Countries',
                    'description' => 'Cumulative number of countries in this stage.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => 0,
                    'goal_valence' => 'valence-grey',
                    'goal_percent' => 0,
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];


            case 'new_registrations':
                return [
                    'key' => 'new_registrations',
                    'label' => 'New Registrations',
                    'description' => 'Total number of registrants in this stage.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'has_plan':
                return [
                    'key' => 'has_plan',
                    'label' => 'Has Plan',
                    'description' => 'Total number of registrants who have a plan.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'no_plan':
                return [
                    'key' => 'no_plan',
                    'label' => 'Has No Plan',
                    'description' => 'Total number of registrants who have no plan.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'no_friends':
                return [
                    'key' => 'no_friends',
                    'label' => 'Has No Friends',
                    'description' => 'Total number of registrants who have not invited any friends.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'no_coach':
                return [
                    'key' => 'no_coach',
                    'label' => 'Has Not Requested a Coach',
                    'description' => 'Total number of registrants who have not requested a coach.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'no_updated_profile':
                return [
                    'key' => 'no_updated_profile',
                    'label' => 'Has Not Updated Profile',
                    'description' => 'Total number of registrants who have not updated their profile.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];

            case 'total_registrants':
            default:
                return [
                    'key' => 'total_registrants',
                    'label' => 'Registrants',
                    'description' => 'People who have registered but have not progressed into training.',
                    'link' => 'registrants',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];

        }

    }
    public function total_att( $params ) {
        $negative_stat = $params['negative_stat'];
        $value = rand(100, 1000);
        $goal = rand(500, 700);
        $trend = rand(500, 700);

        switch( $params['key'] ) {
            case 'has_coach':
                return [
                    'key' => 'has_coach',
                    'label' => 'Has Coach',
                    'description' => 'Active trainees who have a coach.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'inactive_trainees':
                return [
                    'key' => 'inactive_trainees',
                    'label' => 'Inactive Trainees',
                    'description' => 'People who have been inactive more than 6 months.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_active_trainees':
                return [
                    'key' => 'new_active_trainees',
                    'label' => 'New Active Trainees',
                    'description' => 'New people who entered stage during time period.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'total_checkins':
                return [
                    'key' => 'total_checkins',
                    'label' => 'Total Checkins',
                    'description' => 'Total number of checkins registered for training.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'has_no_coach':
                return [
                    'key' => 'has_no_coach',
                    'label' => 'Has No Coach',
                    'description' => 'People who have no coach.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'has_no_updated_profile':
                return [
                    'key' => 'has_no_updated_profile',
                    'label' => 'No Updated Profile',
                    'description' => 'People who have not updated their profile.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'session_1':
                return [
                    'key' => 'session_1',
                    'label' => 'Session 1',
                    'description' => 'Session 1 of the training.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'session_2':
                return [
                    'key' => 'session_2',
                    'label' => 'Session 2',
                    'description' => 'Session 2 of the training.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'session_3':
                return [
                    'key' => 'session_3',
                    'label' => 'Session 3',
                    'description' => 'Session 3 of the training.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'session_4':
                return [
                    'key' => 'session_4',
                    'label' => 'Session 4',
                    'description' => 'Session 4 of the training.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'session_5':
                return [
                    'key' => 'session_5',
                    'label' => 'Session 5',
                    'description' => 'Session 5 of the training.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'session_6':
                return [
                    'key' => 'session_6',
                    'label' => 'Session 6',
                    'description' => 'Session 6 of the training.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'session_7':
                return [
                    'key' => 'session_7',
                    'label' => 'Session 7',
                    'description' => 'Session 7 of the training.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'session_8':
                return [
                    'key' => 'session_8',
                    'label' => 'Session 8',
                    'description' => 'Session 8 of the training.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'session_9':
                return [
                    'key' => 'session_9',
                    'label' => 'Session 9',
                    'description' => 'Session 9 of the training.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'session_10':
                return [
                    'key' => 'session_10',
                    'label' => 'Session 10',
                    'description' => 'Session 10 of the training.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'total_att':
            default:
                return [
                    'key' => 'total_att',
                    'label' => 'Active Training Trainees',
                    'description' => 'People who are actively working a training plan or have only partially completed the training.',
                    'link' => 'active',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
        }

    }
    public function total_ptt( $params ) {
        $negative_stat = $params['negative_stat'];
        $value = rand(100, 1000);
        $goal = rand(500, 700);
        $trend = rand(500, 700);

        switch( $params['key'] ) {

            case 'needs_3_month_plan':
                return [
                    'key' => 'needs_3_month_plan',
                    'label' => 'Needs 3 Month Plan',
                    'description' => 'Needs a 3 month plan',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'needs_coach':
                return [
                    'key' => 'needs_coach',
                    'label' => 'Needs Coach',
                    'description' => 'Needs a coach',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_trainees':
                return [
                    'key' => 'new_trainees',
                    'label' => 'New Trainees',
                    'description' => 'New trainees entering stage in time period.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_3_month_plans':
                return [
                    'key' => 'new_3_month_plans',
                    'label' => 'New 3-Month Plans',
                    'description' => 'New 3-Month Plans',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_coaching_requests':
                return [
                    'key' => 'new_coaching_requests',
                    'label' => 'New Coaching Requests',
                    'description' => 'New coaching requests during the time period.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'total_ptt':
            default:
                return [
                    'key' => 'total_ptt',
                    'label' => 'Post-Training Trainees',
                    'description' => 'People who have completed the training and are working on a 3-month plan.',
                    'link' => 'post',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
        }

    }
    public function total_s1( $params ) {
        $negative_stat = $params['negative_stat'];
        $value = rand(100, 1000);
        $goal = rand(500, 700);
        $trend = rand(500, 700);

        switch( $params['key'] ) {

            case 'total_churches';
                return [
                    'key' => 'total_churches',
                    'label' => 'Churches',
                    'description' => 'Total number of churches reported by S1 Practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'total_locations';
                return [
                    'key' => 'total_locations',
                    'label' => 'Locations',
                    'description' => 'Total number of locations reported by S1 Practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'total_active_reporters';
                return [
                    'key' => 'total_active_reporters',
                    'label' => 'Reporting',
                    'description' => 'Total number of active reporters.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_practitioners';
                return [
                    'key' => 'new_practitioners',
                    'label' => 'New Practitioners',
                    'description' => 'Total number of new practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_reporters';
                return [
                    'key' => 'new_reporters',
                    'label' => 'New Reporters',
                    'description' => 'Total number of new reporters.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_churches';
                return [
                    'key' => 'new_churches',
                    'label' => 'New Churches',
                    'description' => ' Total number of new churches reported by S1 Practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_locations';
                return [
                    'key' => 'new_locations',
                    'label' => 'New Locations',
                    'description' => 'Total number of new locations reported by S1 Practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'has_no_coach';
                return [
                    'key' => 'has_no_coach',
                    'label' => 'Has No Coach',
                    'description' => 'Total number of S1 Practitioners who have not yet been assigned a coach.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'has_not_reported';
                return [
                    'key' => 'has_not_reported',
                    'label' => 'Has Not Reported',
                    'description' => 'Total number of S1 Practitioners who have not yet reported.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];

            case 'total_s1':
            default:
                return [
                    'key' => 'total_s1',
                    'label' => '(S1) Partial Practitioners',
                    'description' => 'Learning through doing. Implementing partial checklist / 4-fields',
                    'link' => 's1_practitioners',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
        }

    }
    public function total_s2( $params ) {
        $negative_stat = $params['negative_stat'];
        $value = rand(100, 1000);
        $goal = rand(500, 700);
        $trend = rand(500, 700);

        switch( $params['key'] ) {

            case 'total_churches';
                return [
                    'key' => 'total_churches',
                    'label' => 'Churches',
                    'description' => 'Total number of churches reported by S2 Practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'total_locations';
                return [
                    'key' => 'total_locations',
                    'label' => 'Locations',
                    'description' => 'Total number of locations reported by S2 Practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'total_active_reporters';
                return [
                    'key' => 'total_active_reporters',
                    'label' => 'Active Reporters',
                    'description' => 'Total number of active reporters.',
                    'link' => 's1_practitioners',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_practitioners';
                return [
                    'key' => 'new_practitioners',
                    'label' => 'New Practitioners',
                    'description' => 'Total number of new practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_reporters';
                return [
                    'key' => 'new_reporters',
                    'label' => 'New Reporters',
                    'description' => 'Total number of new reporters.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_churches';
                return [
                    'key' => 'new_churches',
                    'label' => 'New Churches',
                    'description' => ' Total number of new churches reported by S2 Practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_locations';
                return [
                    'key' => 'new_locations',
                    'label' => 'New Locations',
                    'description' => 'Total number of new locations reported by S2 Practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'has_no_coach';
                return [
                    'key' => 'has_no_coach',
                    'label' => 'Has No Coach',
                    'description' => 'Total number of S2 Practitioners who have not yet been assigned a coach.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'has_not_reported';
                return [
                    'key' => 'has_not_reported',
                    'label' => 'Has Not Reported',
                    'description' => 'Total number of S2 Practitioners who have not yet reported.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'total_s2':
            default:
                return [
                    'key' => 'total_s2',
                    'label' => '(S2) Completed Practitioners',
                    'description' => 'People who are seeking multiplicative movement and are completely skilled with the coaching checklist.',
                    'link' => 's2_practitioners',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
        }

    }
    public function total_s3( $params ) {
        $negative_stat = $params['negative_stat'];
        $value = rand(100, 1000);
        $goal = rand(500, 700);
        $trend = rand(500, 700);

        switch( $params['key'] ) {
            case 'total_churches';
                return [
                    'key' => 'total_churches',
                    'label' => 'Churches',
                    'description' => 'Total number of churches reported by S2 Practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'total_locations';
                return [
                    'key' => 'total_locations',
                    'label' => 'Locations',
                    'description' => 'Total number of locations reported by S2 Practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'total_active_reporters';
                return [
                    'key' => 'total_active_reporters',
                    'label' => 'Active Reporters',
                    'description' => 'Total number of active reporters.',
                    'link' => 's1_practitioners',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_practitioners';
                return [
                    'key' => 'new_practitioners',
                    'label' => 'New Practitioners',
                    'description' => 'Total number of new practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_reporters';
                return [
                    'key' => 'new_reporters',
                    'label' => 'New Reporters',
                    'description' => 'Total number of new reporters.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_churches';
                return [
                    'key' => 'new_churches',
                    'label' => 'New Churches',
                    'description' => ' Total number of new churches reported by S2 Practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_locations';
                return [
                    'key' => 'new_locations',
                    'label' => 'New Locations',
                    'description' => 'Total number of new locations reported by S2 Practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'has_no_coach';
                return [
                    'key' => 'has_no_coach',
                    'label' => 'Has No Coach',
                    'description' => 'Total number of S2 Practitioners who have not yet been assigned a coach.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'has_not_reported';
                return [
                    'key' => 'has_not_reported',
                    'label' => 'Has Not Reported',
                    'description' => 'Total number of S2 Practitioners who have not yet reported.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'total_s3':
            default:
                return [
                    'key' => 'total_s3',
                    'label' => '(S3) Multiplying Practitioners',
                    'description' => 'People who are seeking multiplicative movement and are stewarding generational fruit.',
                    'link' => 's3_practitioners',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
        }

    }
    public function total_facilitator( $params ) {
        $negative_stat = $params['negative_stat'];
        $value = rand(100, 1000);
        $goal = rand(500, 700);
        $trend = rand(500, 700);

        switch( $params['key'] ) {

            case 'new_coaching_requests';
                return [
                    'key' => 'new_coaching_requests',
                    'label' => 'New Coaching Requests',
                    'description' => 'Total number of new coaching requests submitted to Facilitator Coaches.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'languages';
                return [
                    'key' => 'languages',
                    'label' => 'Languages',
                    'description' => 'Number of languages from requests',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'locations';
                return [
                    'key' => 'locations',
                    'label' => 'Locations',
                    'description' => 'Locations from requests.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            default:
                return [
                    'key' => 'general',
                    'label' => 'General',
                    'description' => 'General',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
        }

    }
    public function total_early( $params ) {
        $negative_stat = $params['negative_stat'];
        $value = rand(100, 1000);
        $goal = rand(500, 700);
        $trend = rand(500, 700);

        switch( $params['key'] ) {
            case 'new_coaching_requests';
                return [
                    'key' => 'new_coaching_requests',
                    'label' => 'New Coaching Requests',
                    'description' => 'Total number of new coaching requests submitted to Facilitator Coaches.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'languages';
                return [
                    'key' => 'languages',
                    'label' => 'Languages',
                    'description' => 'Number of languages from requests',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'locations';
                return [
                    'key' => 'locations',
                    'label' => 'Locations',
                    'description' => 'Locations from requests.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            default:
                return [
                    'key' => 'total_s3',
                    'label' => '(S3) Multiplying Practitioners',
                    'description' => 'People who are seeking multiplicative movement and are stewarding generational fruit.',
                    'link' => 's3_practitioners',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
        }

    }
    public function total_advanced( $params ) {
        $negative_stat = $params['negative_stat'];
        $value = rand(100, 1000);
        $goal = rand(500, 700);
        $trend = rand(500, 700);

        switch( $params['key'] ) {
            case 'total_churches';
                return [
                    'key' => 'total_churches',
                    'label' => 'Total Churches',
                    'description' => 'Total number of churches reported by S2 Practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'total_locations';
                return [
                    'key' => 'total_locations',
                    'label' => 'Total Locations',
                    'description' => 'Total number of locations reported by S2 Practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'total_active_reporters';
                return [
                    'key' => 'total_active_reporters',
                    'label' => 'Total Active Reporters',
                    'description' => 'Total number of active reporters.',
                    'link' => 's1_practitioners',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_practitioners';
                return [
                    'key' => 'new_practitioners',
                    'label' => 'New Practitioners',
                    'description' => 'Total number of new practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_reporters';
                return [
                    'key' => 'new_reporters',
                    'label' => 'New Reporters',
                    'description' => 'Total number of new reporters.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_churches';
                return [
                    'key' => 'new_churches',
                    'label' => 'New Churches',
                    'description' => ' Total number of new churches reported by S2 Practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'new_locations';
                return [
                    'key' => 'new_locations',
                    'label' => 'New Locations',
                    'description' => 'Total number of new locations reported by S2 Practitioners.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'has_no_coach';
                return [
                    'key' => 'has_no_coach',
                    'label' => 'Has No Coach',
                    'description' => 'Total number of S2 Practitioners who have not yet been assigned a coach.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'has_not_reported';
                return [
                    'key' => 'has_not_reported',
                    'label' => 'Has Not Reported',
                    'description' => 'Total number of S2 Practitioners who have not yet reported.',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'total_s3':
            default:
                return [
                    'key' => 'total_s3',
                    'label' => '(S3) Multiplying Practitioners',
                    'description' => 'People who are seeking multiplicative movement and are stewarding generational fruit.',
                    'link' => 's3_practitioners',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
        }

    }
    public function general( $params ) {
        $negative_stat = $params['negative_stat'];
        $value = rand(100, 1000);
        $goal = rand(500, 700);
        $trend = rand(500, 700);

        switch( $params['key'] ) {

            case 'active_coaches';
                return [
                    'key' => 'active_coaches',
                    'label' => 'Active Coaches',
                    'description' => 'Number of active coaches',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'total_people_in_coaching';
                return [
                    'key' => 'total_people_in_coaching',
                    'label' => 'People in Coaching',
                    'description' => 'Number of people in coaching',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'people_in_coaching';
                return [
                    'key' => 'people_in_coaching',
                    'label' => 'People in Coaching',
                    'description' => 'Number of people in coaching',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'coaching_engagements';
                return [
                    'key' => 'coaching_engagements',
                    'label' => 'Coaching Engagements',
                    'description' => 'Number of coaching engagements',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => 'valence-grey',
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
            case 'general':
            default:
                return [
                    'key' => 'general',
                    'label' => 'General',
                    'description' => 'Key not found',
                    'link' => '',
                    'value' => Zume_Query::format_int( $value ),
                    'valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal' => $goal,
                    'goal_valence' => Zume_Query::get_valence( $value, $goal, $negative_stat ),
                    'goal_percent' => Zume_Query::get_percent( $value, $goal ),
                    'trend' => $trend,
                    'trend_valence' => Zume_Query::get_valence( $value, $trend, $negative_stat ),
                    'trend_percent' => Zume_Query::get_percent( $value, $trend ),
                    'negative_stat' => $negative_stat,
                ];
        }

    }


    public function list( WP_REST_Request $request ) {
        return Zume_Query::trainees_list( dt_recursive_sanitize_array( $request->get_params() ) );
    }
    public function log( WP_REST_Request $request ) {
        return Zume_Query::log( dt_recursive_sanitize_array( $request->get_params() ) );
    }
    public function location( WP_REST_Request $request ) {
        return DT_Ipstack_API::get_location_grid_meta_from_current_visitor();
    }
    public function map( WP_REST_Request $request ) {
        $params =  dt_recursive_sanitize_array( $request->get_params() );
        return [ "link" => '<iframe class="map-iframe" width="100%" height="2500" src="https://zume5.training/coaching/zume_app/heatmap_trainees" frameborder="0" style="border:0" allowfullscreen></iframe>' ];
    }
    public function training_elements( WP_REST_Request $request ) {
        return Zume_Query::training_elements( dt_recursive_sanitize_array( $request->get_params() ) );
    }

    // dev
    public function sample( WP_REST_Request $request ) {
        return Zume_Query::sample( dt_recursive_sanitize_array( $request->get_params() ) );
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
