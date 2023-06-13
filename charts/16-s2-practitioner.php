<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Path_L2 extends Zume_Chart_Base
{
    //slug and title of the top menu folder
    public $base_slug = 's2_practitioners'; // lowercase
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
        $this->base_title = __( 'S2 (Completed)', 'disciple_tools' );

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
            jQuery(document).ready(function(){
                "use strict";

                let chart = jQuery('#chart')
                chart.empty().html(`
                        <div id="zume-path">
                            <div class="grid-x">
                                <div class="cell small-6"><h1>Stage 2 - Completed Practitioner</h1></div>
                                <div class="cell small-6 right">Achieving competence and consistent fruit. Implementing full checklist / 4-fields.</div>
                            </div>
                            <hr>
                            <div class="grid-x">
                                <div class="cell hero"><span class="loading-spinner active"></span></div>
                            </div>
                            <div class="grid-x grid-margin-x grid-margin-y">
                                 <div class="cell medium-3 total_churches"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 total_locations"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 total_active_reporters"><span class="loading-spinner active"></span></div>
                            </div>
                            <hr>
                            <div class="grid-x">
                                <div class="cell center"><h1 id="range-title">Last 30 Days</h1></div>
                                <div class="cell small-6">
                                    <h2>Progress Indicators</h2>
                                </div>
                                <div class="cell small-6">
                                    <span style="float: right;">
                                        <select id="range-filter">
                                            <option value="30">Last 30 days</option>
                                            <option value="7">Last 7 days</option>
                                            <option value="90">Last 90 days</option>
                                            <option value="365">Last 1 Year</option>
                                        </select>
                                    </span>
                                    <span class="loading-spinner active" style="float: right; margin:0 10px;"></span>
                                </div>
                            </div>
                            <div class="grid-x grid-margin-x grid-margin-y">
                                 <div class="cell medium-6 new_practitioners"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-6 new_reporters"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-6 new_churches"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-6 new_locations"><span class="loading-spinner active"></span></div>
                            </div>
                            <div class="grid-x grid-margin-x grid-margin-y">
                                 <div class="cell"><h2>Remaining Progress</h2></div>
                            </div>
                            <div class="grid-x grid-margin-x grid-margin-y">
                                 <div class="cell medium-6 has_no_coach"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-6 has_not_reported"><span class="loading-spinner active"></span></div>
                            </div>
                        </div>
                    `)

                // totals
                window.spin_add()
                window.API_get( window.site_info.total_url, { stage: "s2", key: "total_s2" }, ( data ) => {
                    jQuery('.hero').html(window.template_map_list(data))
                    window.click_listener( data )
                    window.spin_remove()
                })
                window.spin_add()
                window.API_get( window.site_info.total_url, { stage: "s2", key: "total_churches" }, ( data ) => {
                    jQuery('.total_churches').html(window.template_single(data))
                    window.click_listener( data )
                    window.spin_remove()
                })
                window.spin_add()
                window.API_get( window.site_info.total_url, { stage: "s2", key: "total_locations" }, ( data ) => {
                    jQuery('.total_locations').html(window.template_single(data))
                    window.click_listener( data )
                    window.spin_remove()
                })
                window.spin_add()
                window.API_get( window.site_info.total_url, { stage: "s2", key: "total_active_reporters" }, ( data ) => {
                    jQuery('.total_active_reporters').html(window.template_single(data))
                    window.click_listener( data )
                    window.spin_remove()
                })

                window.load = ( range ) => {

                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "s2", key: "new_practitioners", range: range }, ( data ) => {
                        jQuery('.new_practitioners').html( window.template_trio( data ) )
                        window.click_listener( data )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "s2", key: "new_reporters", range: range }, ( data ) => {
                        jQuery('.new_reporters').html( window.template_trio( data ) )
                        window.click_listener( data )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "s2", key: "new_churches", range: range }, ( data ) => {
                        jQuery('.new_churches').html( window.template_trio( data ) )
                        window.click_listener( data )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "s2", key: "new_locations", range: range }, ( data ) => {
                        jQuery('.new_locations').html( window.template_trio( data ) )
                        window.click_listener( data )
                        window.spin_remove()
                    })

                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "s2", key: "has_no_coach", range: range, negative_stat: true }, ( data ) => {
                        jQuery('.has_no_coach').html( window.template_trio( data ) )
                        window.click_listener( data )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "s2", key: "has_not_reported", range: range, negative_stat: true }, ( data ) => {
                        jQuery('.has_not_reported').html( window.template_trio( data ) )
                        window.click_listener( data )
                        window.spin_remove()
                    })

                }
                window.setup_filter()

                window.click_listener = ( data ) => {
                    window.load_list(data)
                    window.load_map(data)
                    window.load_redirect(data)
                }
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
new Zume_Path_L2();
