<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly.


class Zume_Path_Candidate extends Zume_Chart_Base
{
    //slug and title of the top menu folder
    public $base_slug = 'candidates'; // lowercase
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
        $this->base_title = __( 'Candidate', 'disciple_tools' );

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
                                        <select id="range">
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
                            <div class="grid-x">
                                <div class="cell medium-6">
                                    <div class="grid-x zume-critical-path"></div>
                                </div>
                                <div class="cell medium-6" style="padding:1em;">
                                    <h3><strong>What is the Candidate stage?</strong></h3>
                                    <p>
                                        A Candidate is a person in this stage that moves from an Anonymous Visitor coming from the web
                                        to a person who has registered.
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <h2>Goals</h2>
                            <div class="grid-x zume-goals"></div>
                            <hr>
                            <h2>Trends</h2>
                            <div class="grid-x zume-trends"></div>
                        </div>
                    `)

                let days = 30
                let range_select = jQuery('#range')
                let site_url = '<?php echo site_url() ?>' + '/wp-json/zume_stats/v1/stats/candidates'
                window.phase_data = []
                function get_range_stats() {
                    jQuery.get( site_url+'?days='+days+'&filter=candidate&range=true', function(data){
                        window.phase_data = data
                        jQuery('.loading-spinner').removeClass('active')
                        console.log(data)
                    })
                }
                range_select.on('change', function(){
                    days = jQuery(this).val()
                    jQuery('.loading-spinner').addClass('active')
                    get_range_stats()
                })


                let valence = ['valence-grey', 'valence-grey', 'valence-darkred', 'valence-red', 'valence-grey', 'valence-green', 'valence-darkgreen']
                function get_valence( valence ) {
                    return valence[Math.floor(Math.random()*valence.length)]
                }

                let path = [
                    {
                        "title": "Candidates",
                        "link": "candidate",
                        "value": '45,034',
                        "goal": get_valence( valence ),
                        "trend": get_valence( valence )
                    },
                ]

                jQuery('.zume-critical-path').empty()
                jQuery.each( path, function( key, value ) {
                    jQuery('.zume-critical-path').append(`
                            <div class="cell zume-trio-card">
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

                let goals = [
                    {
                        "title": "Visitors",
                        "value": '100',
                        "description": "Unique visitors to the website.",
                        "goal": get_valence( valence ),
                        "trend": get_valence( valence )
                    },
                    {
                        "title": "Registrations",
                        "value": '100',
                        "description": "Registrations have crossed the line from visitor to trainee.",
                        "goal": get_valence( valence ),
                        "trend": get_valence( valence )
                    },
                ]

                jQuery.each( goals, function( key, value ) {
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

                let trends = [
                    {
                        "title": "Visitors",
                        "value": 100,
                        "description": "Unique visitors to the website.",
                        "goal": get_valence( valence ),
                        "trend": get_valence( valence )
                    },
                    {
                        "title": "Registrations",
                        "value": '100',
                        "description": "Registrations have crossed the line from visitor to trainee.",
                        "goal": get_valence( valence ),
                        "trend": get_valence( valence )
                    },
                ]

                jQuery.each( trends, function( key, value ) {
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

                jQuery('.loading-spinner').removeClass('active')
                get_range_stats()
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
new Zume_Path_Candidate();
