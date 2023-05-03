<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly.


class Zume_Path_Overview extends Zume_Chart_Base
{
    //slug and title of the top menu folder
    public $base_slug = ''; // lowercase
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
        $this->base_title = __( 'Critical Path', 'disciple_tools' );

        $url_path = dt_get_url_path( true );
        if ( "zume-path" === $url_path ) {
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
                'data' =>[
                    'translations' => [
                        'title_overview' => __( 'Project Overview', 'disciple_tools' ),
                    ],
                ],
            ]
        );
    }

    public function wp_head() {
        $this->styles();
            ?>
            <script>
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
                                        <select>
                                            <option value="30">Last 30 days</option>
                                            <option value="7">Last 7 days</option>
                                            <option value="90">Last 90 days</option>
                                            <option value="365">Last 1 Year</option>
                                        </select>
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <span class="loading-spinner active"></span>
                            <div class="grid-x valence-legend">
                                <div class="cell small-2 valence-darkred"></div>
                                <div class="cell small-2 valence-red"></div>
                                <div class="cell small-4 valence-grey"></div>
                                <div class="cell small-2 valence-green"></div>
                                <div class="cell small-2 valence-darkgreen"></div>
                            </div>
                            <div class="grid-y zume-cards critical-path" id="zume-cards"></div>

                        </div>
                    `)

                    let valence = ['valence-grey', 'valence-grey', 'valence-darkred', 'valence-red', 'valence-grey', 'valence-green', 'valence-darkgreen']

                    let data = [
                        {
                            "title": "Candidates",
                            "link": "candidates",
                            "value": '45,034',
                            "goal": valence[Math.floor(Math.random()*valence.length)],
                            "trend": valence[Math.floor(Math.random()*valence.length)],
                        },
                        {
                            "title": "Pre-Training Trainee",
                            "link": "pre",
                            "value": '467',
                            "goal": valence[Math.floor(Math.random()*valence.length)],
                            "trend": valence[Math.floor(Math.random()*valence.length)],
                        },
                        {
                            "title": "Active Training Trainees",
                            "link": "active",
                            "value": '150',
                            "goal": valence[Math.floor(Math.random()*valence.length)],
                            "trend": valence[Math.floor(Math.random()*valence.length)],
                        },
                        {
                            "title": "Post-Training Trainees",
                            "link": "post",
                            "value": '570',
                            "goal": valence[Math.floor(Math.random()*valence.length)],
                            "trend": valence[Math.floor(Math.random()*valence.length)],
                        },
                        {
                            "title": "L1 Practitioners",
                            "link": "l1_practitioners",
                            "value": '122',
                            "goal": valence[Math.floor(Math.random()*valence.length)],
                            "trend": valence[Math.floor(Math.random()*valence.length)],
                        },
                        {
                            "title": "L2 Practitioners",
                            "link": "l2_practitioners",
                            "value": '20',
                            "goal": valence[Math.floor(Math.random()*valence.length)],
                            "trend": valence[Math.floor(Math.random()*valence.length)],
                        },
                        {
                            "title": "L3 Practitioners",
                            "link": "l3_practitioners",
                            "value": '10',
                            "goal": valence[Math.floor(Math.random()*valence.length)],
                            "trend": valence[Math.floor(Math.random()*valence.length)],
                        }
                    ]

                    jQuery.each( data, function( key, value ) {
                        jQuery('#zume-cards').append(`
                            <div class="cell zume-trio-card" >
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

                    jQuery('.zume-trio-card-content').click(function(){
                        let link = jQuery(this).data('link')
                        window.location.href = '/coaching/zume-path/' + link
                    })

                    jQuery('.loading-spinner').delay(3000).removeClass('active')
                })

            </script>
            <?php
    }

    public function styles() {
        ?>
        <style>
            .side-menu-item-highlight {
                font-weight: 300;
            }
            #-menu {
                font-weight: 700;
            }
        </style>
        <?php
    }

}
new Zume_Path_Overview();
