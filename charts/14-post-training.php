<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Path_Post extends Zume_Chart_Base
{
    //slug and title of the top menu folder
    public $base_slug = 'post'; // lowercase
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
        $this->base_title = __( 'Post-Training', 'disciple_tools' );

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

        wp_enqueue_script( 'zume_api', plugin_dir_url(__FILE__) . 'charts.js', [ 'jquery' ], filemtime( plugin_dir_path(__FILE__) . 'charts.js' ), true );
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

                            <div class="grid-x">
                                <div class="cell medium-6">
                                    <div class="grid-x zume-critical-path"></div>
                                </div>
                                <div class="cell medium-6" style="padding:1em;">
                                    <h3><strong>What is the Post-Training stage?</strong></h3>
                                    <p>
                                         The Post-Training stage is the time after a person has completed the training. This stage is
                                            primarily focused on the 3-month plan and beginning to practice Zúme skills.
                                    </p>
                                    <p>
                                       The top goal is get a coach and to start growing the disciple making lifestyle and finish the 3-month plan.
                                    </p>
                                </div>
                            </div>
                            <hr>
                            <h2>Goals</h2>
                            <div class="grid-x zume-goals"  data-equalizer data-equalize-by-row></div>
                            <hr>
                            <h2>Trends</h2>
                            <div class="grid-x zume-trends"  data-equalizer data-equalize-by-row></div>
                        </div>
                    `)

                let valence = ['valence-grey', 'valence-grey', 'valence-darkred', 'valence-red', 'valence-grey', 'valence-green', 'valence-darkgreen']

                let data = [
                    {
                        "title": "People",
                        "value": 0,
                        "description": "Number of People in the Post-Training Phase",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "3-Month Plans",
                        "value": 0,
                        "description": "Active 3-Month Plans",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "Check-ins",
                        "value": 0,
                        "description": "Check-ins",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "Has a Coach",
                        "value": 0,
                        "description": "Has a Coach",
                        "goal": 'valence-grey',
                        "trend": 'valence-grey'
                    },
                    {
                        "title": "Actively being Coached",
                        "value": 0,
                        "description": "Actively being Coached",
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
                        "title": "Post-Training Trainee",
                        "link": "post",
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
new Zume_Path_Post();
