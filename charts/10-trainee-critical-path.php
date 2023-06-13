<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Trainee_Critical_Path extends Zume_Chart_Base
{
    //slug and title of the top menu folder
    public $base_slug = 'trainee_critical_path'; // lowercase
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
                'data' =>[
                    'translations' => [
                        'title_overview' => __( 'Project Overview', 'disciple_tools' ),
                    ],
                ],
            ]
        );
    }
    public function base_menu( $content ) {
        $content .= '<li class=""><hr></li>';
        $content .= '<li class="">TRAINEES</li>';
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
                                <div class="cell small-6"><h1>${title}</h1></div>
                                <div class="cell small-6">
                                    <span style="float: right;">
                                        <select id="range-filter">
                                            <option value="-1">All Time</option>
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

                            <div class="grid-y critical-path" id="zume-cards">
                                <div class="critical-path-element registrants"><span class="loading-spinner active"></span></div>
                                <div class="critical-path-element active_training_trainees"><span class="loading-spinner active"></span></div>
                                <div class="critical-path-element post_training_trainees"><span class="loading-spinner active"></span></div>
                                <div class="critical-path-element s1_practitioners"><span class="loading-spinner active"></span></div>
                                <div class="critical-path-element s2_practitioners"><span class="loading-spinner active"></span></div>
                                <div class="critical-path-element s3_practitioners"><span class="loading-spinner active"></span></div>
                            </div>

                        </div>
                    `)

                    window.load = ( range ) => {
                        window.spin_add()
                        window.API_get( window.site_info.total_url, { stage: "registrants", key: "total_registrants", range: range }, ( data ) => {
                            jQuery('.registrants').html(window.template_map_list(data))
                            window.click_listener(data)
                            window.spin_remove()
                        })
                        window.spin_add()
                        window.API_get( window.site_info.total_url, { stage: "att", key: "total_att", range: range }, ( data ) => {
                            jQuery('.active_training_trainees').html(window.template_map_list(data))
                            window.click_listener(data)
                            window.spin_remove()
                        })
                        window.spin_add()
                        window.API_get( window.site_info.total_url, { stage: "ptt", key: "total_ptt", range: range }, ( data ) => {
                            jQuery('.post_training_trainees').html(window.template_map_list(data))
                            window.click_listener(data)
                            window.spin_remove()
                        })
                        window.spin_add()
                        window.API_get( window.site_info.total_url, { stage: "s1", key: "total_s1", range: range }, ( data ) => {
                            jQuery('.s1_practitioners').html(window.template_map_list(data))
                            window.click_listener(data)
                            window.spin_remove()
                        })
                        window.spin_add()
                        window.API_get( window.site_info.total_url, { stage: "s2", key: "total_s2", range: range }, ( data ) => {
                            jQuery('.s2_practitioners').html(window.template_map_list(data))
                            window.click_listener(data)
                            window.spin_remove()
                        })
                        window.spin_add()
                        window.API_get( window.site_info.total_url, { stage: "s3", key: "total_s3", range: range }, ( data ) => {
                            jQuery('.s3_practitioners').html(window.template_map_list(data))
                            window.click_listener(data)
                            window.spin_remove()
                        })
                    }
                    window.setup_filter()

                    window.click_listener = ( data ) => {
                        window.load_list(data)
                        window.load_map(data)
                        window.load_redirect(data)
                        jQuery('.z-card-main.hover.'+data.key).click(function(){
                            window.location.href = data.link
                        })
                    }
                })

            </script>
            <?php
    }

    public function styles() {
        ?>
        <style>
            .zume-cards {
                max-width: 700px;
            }
        </style>
        <?php
    }

}
new Zume_Trainee_Critical_Path();
