<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Path_Active extends Zume_Chart_Base
{
    //slug and title of the top menu folder
    public $base_slug = 'active'; // lowercase
    public $slug = ''; // lowercase
    public $title;
    public $base_title;
    public $js_object_name = 'wp_js_object'; // This object will be loaded into the metrics.js file by the wp_localize_script.
    public $js_file_name = '/dt-metrics/groups/overview.js'; // should be full file name plus extension
    public $permissions = [ 'dt_all_access_contacts', 'view_project_metrics' ];

    public function __construct() {
        parent::__construct();
        if ( !$this->has_permission() ){
            return;
        }
        $this->base_title = __( 'Active Training', 'disciple_tools' );

        $url_path = dt_get_url_path( true );
        if ( "zume-path/$this->base_slug" === $url_path ) {
            add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ], 99 );
            add_action( 'wp_head',[ $this, 'wp_head' ], 1000);
        }
    }

    public function scripts() {
        wp_register_script( 'amcharts-core', 'https://www.amcharts.com/lib/4/core.js', false, '4' );
        wp_register_script( 'amcharts-charts', 'https://www.amcharts.com/lib/4/charts.js', false, '4' );
        wp_register_script( 'amcharts-animated', 'https://www.amcharts.com/lib/4/themes/animated.js', [ 'amcharts-core' ], '4' );

        wp_enqueue_style( 'zume_charts', plugin_dir_url(__FILE__) . 'charts.css', [], filemtime( plugin_dir_path(__FILE__) . 'charts.css' ) );

        wp_enqueue_script( 'dt_metrics_project_script', get_template_directory_uri() . $this->js_file_name, [
            'jquery',
            'jquery-ui-core',
            'amcharts-core',
            'amcharts-charts',
            'amcharts-animated',
            'lodash'
        ], filemtime( get_theme_file_path() . $this->js_file_name ), true );

        wp_localize_script(
            'dt_metrics_project_script', 'dtMetricsProject', [
                'root' => esc_url_raw( rest_url() ),
                'theme_uri' => get_template_directory_uri(),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'current_user_login' => wp_get_current_user()->user_login,
                'current_user_id' => get_current_user_id(),
                'data' => $this->data(),
            ]
        );
    }

    public function wp_head() {
        $this->js_api();
        ?>
        <script>
            window.site_url = '<?php echo site_url() ?>' + '/wp-json/zume_stats/v1/'
            jQuery(document).ready(function(){
                "use strict";
                let chart = jQuery('#chart')
                let title = '<?php echo $this->base_title ?>'
                chart.empty().html(`
                        <div id="zume-path">
                            <div class="grid-x">
                                <div class="cell small-6"><h1>${title}</h1></div>
                                <div class="cell small-6">
                                    <span style="float: right;">
                                         <select id="range-filter">
                                            <option value="30">Last 30 days</option>
                                            <option value="7">Last 7 days</option>
                                            <option value="90">Last 90 days</option>
                                            <option value="365">Last 1 Year</option>
                                            <option value="-1">All Time</option>
                                        </select>
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <span class="loading-spinner active"></span>
                            <div class="grid-x">
                                <div class="cell medium-6">
                                    <div class="active_training_trainees"></div>
                                </div>
                                <div class="cell medium-6" style="padding:1em;">
                                    <h3><strong>What is the Active Training stage?</strong></h3>
                                    <p>
                                        The Active Training stage is the stage where the user is actively going through the training.
                                        The goal of this stage is to get the user to complete the training.
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <h2>Goals</h2>
                            <div class="grid-x zume-goals"></div>
                            <hr>
                            <h2>Trends</h2>
                            <div class="grid-x zume-trends"></div>
                            <hr>
                            <h2>Session Goals</h2>
                            <div class="grid-x zume-session-goals"></div>
                            <hr>
                            <h2>Session Trends</h2>
                            <div class="grid-x zume-session-trends"></div>
                        </div>
                    `)

                window.load = ( filter ) => {
                    window.API_post( window.site_url+'active_training_trainees?filter='+filter, ( data ) => {
                        jQuery('.active_training_trainees').html(window.template_trio(data))
                    })
                }
                window.setup_filter()

                let data = [
                    {
                        "title": "People",
                        "value": '0',
                        "description": "Description.",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "Check-ins",
                        "value": '0',
                        "description": "Description.",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "Has a Coach",
                        "value": '0',
                        "description": "Description.",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "Has a Profile",
                        "value": '0',
                        "description": "Description.",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "Session Progress",
                        "value": '0',
                        "description": "Description.",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    }
                ]

                jQuery.each( data, function( key, value ) {
                    jQuery('.zume-goals').append(`
                            <!-- Zume Card-->
                            <div class="cell medium-4 large-3" data-equalizer-watch>
                                <div class="zume-card ${value.goal}">
                                    <div class="zume-card-title">
                                        ${value.title}
                                    </div>
                                    <div class="zume-card-content">
                                        ${value.value}
                                    </div>
                                    <div class="zume-card-footer">
                                        ${value.description}
                                    </div>
                                </div>
                            </div><!-- card -->
                        `)
                })

                jQuery.each( data, function( key, value ) {
                    jQuery('.zume-trends').append(`
                            <!-- Zume Card-->
                            <div class="cell medium-4 large-3" data-equalizer-watch>
                                <div class="zume-card ${value.trend}">
                                    <div class="zume-card-title">
                                        ${value.title}
                                    </div>
                                    <div class="zume-card-content">
                                        ${value.value}
                                    </div>
                                    <div class="zume-card-footer">
                                        ${value.description}
                                    </div>
                                </div>
                            </div><!-- card -->
                        `)
                })

                let path = [
                    {
                        "title": "Active Training Trainees",
                        "link": "active",

                        "value": '0',
                        "goal": 'valence-grey',
                        "trend": 'valence-grey',
                    },
                ]

                jQuery('.zume-critical-path').empty()
                jQuery.each( path, function( key, value ) {
                    jQuery('.zume-critical-path').append(`
                            <div class="cell zume-trio-card" style="margin:5px;">
                                <div class="zume-trio-card-content" data-link="${value.link}">
                                    <div class="zume-trio-card-title">
                                        ${value.title}
                                    </div>
                                    <div class="zume-trio-card-value">
                                        ${value.value}
                                    </div>
                                </div>
                                <div class="zume-trio-card-footer">
                                    <div class="grid-x">
                                        <div class="cell small-6 zume-goal ${value.goal}">
                                            GOAL
                                        </div>
                                        <div class="cell small-6 zume-trend ${value.trend}">
                                            TREND
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `)
                })

                let sessions = [
                    {
                        "title": "Session 1",
                        "value": 0,
                        "description": "Session 1.",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "Session 2",
                        "value": 0,
                        "description": "Session 2.",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "Session 3",
                        "value": 0,
                        "description": "Session 3.",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "Session 4",
                        "value": 0,
                        "description": "Session 4.",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "Session 5",
                        "value": 0,
                        "description": "Session 5.",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "Session 6",
                        "value": 0,
                        "description": "Session 6.",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "Session 7",
                        "value": 0,
                        "description": "Session 7.",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "Session 8",
                        "value": 0,
                        "description": "Session 8.",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "Session 9",
                        "value": 0,
                        "description": "Session 9.",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "Session 10",
                        "value": 0,
                        "description": "Session 10.",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    }
                ]

                jQuery.each( sessions, function( key, value ) {
                    jQuery('.zume-session-goals').append(`
                            <div class="cell medium-4 large-3">
                                <div class="zume-card ${value.goal}">
                                    <div class="zume-card-title">
                                        ${value.title}
                                    </div>
                                    <div class="zume-card-content">
                                        ${value.value}
                                    </div>
                                    <div class="zume-card-footer">
                                        ${value.description}
                                    </div>
                                </div>
                            </div><!-- card -->
                        `)
                })

                jQuery.each( sessions, function( key, value ) {
                    jQuery('.zume-session-trends').append(`
                            <div class="cell medium-4 large-3">
                                <div class="zume-card ${value.trend}">
                                    <div class="zume-card-title">
                                        ${value.title}
                                    </div>
                                    <div class="zume-card-content">
                                        ${value.value}
                                    </div>
                                    <div class="zume-card-footer">
                                        ${value.description}
                                    </div>
                                </div>
                            </div><!-- card -->
                        `)
                })

                jQuery('.zume-card').click(function(){
                    jQuery('#modal-large').foundation('open')

                    jQuery('#modal-large-title').empty().html('Fact Label<hr>')

                    jQuery('#modal-large-content').empty().html('<span class="loading-spinner active"></span>')
                    jQuery.get('https://zume5.training/coaching/wp-json/zume_stats/v1/stats_list?days=365&range=true&all_time=true', function(data){
                        jQuery('#modal-large-content').empty().html('<table class="hover"><tbody id="zume-list-modal"></tbody></table>')
                        jQuery.each(data, function(i,v)  {
                            jQuery('#zume-list-modal').append( '<tr><td><a href="">' + v.post_title + '</a></td></tr>')
                        })
                        jQuery('.loading-spinner').removeClass('active')
                    })
                })

                jQuery('.loading-spinner').delay(3000).removeClass('active')
            })

        </script>
        <?php
    }

    public function data() {
        return [
            'translations' => [
                'title_overview' => __( 'Project Overview', 'disciple_tools' ),
            ],
        ];
    }

}
new Zume_Path_Active();
