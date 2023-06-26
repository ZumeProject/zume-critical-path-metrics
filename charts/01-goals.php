<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.


class Zume_Path_Goals extends Zume_Chart_Base
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
        $this->base_title = __( 'Goals', 'disciple_tools' );

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
                'data' => $this->data(),
            ]
        );
    }

    public function base_menu( $content ) {
        $content .= '<li class="">ZÚME</li>';
        $content .= '<li class=""><a href="'.site_url('/zume-path/'.$this->base_slug).'" id="'.$this->base_slug.'-menu">' .  $this->base_title . '</a></li>';
        return $content;
    }

    public function wp_head() {
        $this->styles();
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
                                <div class="cell small-6"><h1>Zúme ${title}</h1></div>
                                <div class="cell small-6">
                                     <span style="float: right;">
                                        <select id="range-filter">
                                            <option value="-1">All Time</option>
                                            <option value="30">Last 30 days</option>
                                            <option value="7">Last 7 days</option>
                                            <option value="90">Last 90 days</option>
                                            <option value="365">Last 1 Year</option>
                                        </select>
                                        <span class="loading-spinner active"></span>
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <div class="grid-x grid-padding-x">
                                <div class="cell medium-3">
                                    <h2>Vision</h2>
                                    <p><strong>Zúme's vision</strong> is to saturation the world with multiplying disciples in our generation. As a measurement, our goal is to catalyze 1 trained practitioner and 2 multiplying simple churches for every 5,000 people in the USA and 50,000 people globally</p>
                                    <p><strong>Zúme Training</strong> is an on-line and in-life learning experience designed for small groups who follow Jesus to learn how to obey His Great Commission and make disciples who multiply.</p>
                                    <p><strong>Zúme Community</strong> is a community of practice for those who what to see disciple making movements.</p>
                                    <h2>Top Metrics</h2>
                                    <p>These metrics (fully trained trainees, practitioners, and churches) represent the highest level milestones for accomplishing Zúme's vision. </p>
                                </div>
                                <div class="cell medium-9">
                                     <div class="grid-x critical-path">
                                        <div class="cell"><div class="trainees_full zume-critical-path"><span class="loading-spinner active"></span></div></div>
                                        <div class="cell"><div class="practitioners zume-critical-path"><span class="loading-spinner active"></span></div></div>
                                        <div class="cell"><div class="churches zume-critical-path"><span class="loading-spinner active"></span></div></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `)

                window.path_load = ( filter ) => {

                    window.spin_add()
                    window.API_post( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Fully Trained Trainees'
                        data.key = 'full_trained_trainees'
                        data.link = ''
                        data.description = 'Fully Trained Trainees are those who have completed the full Zúme training course and have recorded their progress.'
                        jQuery('.trainees_full').html(window.template_map_list(data))
                        window.click_listener(data)
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_post( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Practitioners'
                        data.key = 'practitioners'
                        data.link = ''
                        data.description = 'Practitioners are those who have identified as movement practitioners (of all stages: Partial, Completed, Multiplying). They are seeking movement with multiplicative methods and want to participate in the Zúme Community.'
                        jQuery('.practitioners').html(window.template_map_list(data))
                        window.click_listener(data)
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_post( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Churches'
                        data.key = 'churches'
                        data.link = ''
                        data.description = 'These are the total number of churches reported by all the practitioners of all stages in the Zúme Community.'
                        jQuery('.churches').html(window.template_map_list(data))
                        window.click_listener(data)
                        window.spin_remove()
                    })
                }
                window.setup_filter()


                window.click_listener = ( data ) => {
                    window.load_list(data)
                    window.load_map(data)
                }

                jQuery('.loading-spinner').removeClass('active')
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
    public function styles() {
        ?>
        <style>
            .side-menu-item-highlight {
                font-weight: 300;
            }
            #-menu {
                font-weight: 700;
            }
            .zume-cards {
                max-width: 700px;
            }
        </style>
        <?php
    }
}
new Zume_Path_Goals();
