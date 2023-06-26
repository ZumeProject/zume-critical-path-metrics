<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.


class Zume_Partner extends Zume_Chart_Base
{
    //slug and title of the top menu folder
    public $base_slug = 'partner_general'; // lowercase
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
        $this->base_title = __( 'General', 'disciple_tools' );

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
        $content .= '<li class="">PARTNERS</li>';
        $content .= '<li class=""><a href="'.site_url('/zume-path/'.$this->base_slug).'" id="'.$this->base_slug.'-menu">' .  $this->base_title . '</a></li>';
        return $content;
    }
    public function wp_head() {
        $this->js_api();
        ?>
        <script>
            window.site_url = '<?php echo site_url() ?>' + '/wp-json/zume_stats/v1/'
            jQuery(document).ready(function(){
                "use strict";

                let chart = jQuery('#chart')
                chart.empty().html(`
                        <div id="zume-path">
                            <div class="grid-x">
                                <div class="cell small-6"><h1>Partner Statistics</h1></div>
                                <div class="cell small-6 right">General statistics that are valuable for partners and Zume supporters</div>
                            </div>
                            <hr>
                            <div class="grid-x grid-margin-x grid-margin-y" id="hero"></div>
                            <div class="grid-x grid-margin-x grid-margin-y" id="top">
                                 <div class="cell medium-6 registered_people"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-6 completed_trainees"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-6 total_practitioners"><span class="loading-spinner active"></span></div>
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
                                            <option value="90">Last 90 days</option>
                                            <option value="365">Last 1 Year</option>
                                        </select>
                                    </span>
                                    <span class="loading-spinner active" style="float: right; margin:0 10px;"></span>
                                </div>
                            </div>
                            <div class="grid-x grid-margin-x grid-margin-y" id="range">
                                 <div class="cell medium-6 languages"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-6 trainees"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-6 coaching_engagements"><span class="loading-spinner active"></span></div>
                            </div>
                        </div>
                    `)

                // totals
                window.spin_add()
                window.API_get( window.site_info.total_url, { stage: "general", key: "registered_people" }, ( data ) => {
                    data.label = 'Registered Peoples'
                    data.description = 'Description'
                    jQuery('.'+data.key ).html(window.template_single_map(data))
                    window.click_listener( data )
                    window.spin_remove()
                })

                window.spin_add()
                window.API_get( window.site_info.total_url, { stage: "general", key: "completed_trainees" }, ( data ) => {
                    data.label = 'Completed Trainees'
                    data.description = 'Description'
                    jQuery('.'+data.key).html(window.template_single_map(data))
                    window.click_listener( data )
                    window.spin_remove()
                })

                window.spin_add()
                window.API_get( window.site_info.total_url, { stage: "general", key: "total_practitioners" }, ( data ) => {
                    data.label = 'Total Practitioners'
                    data.description = 'Description'
                    jQuery('.'+data.key).html(window.template_single_map(data))
                    window.click_listener( data )
                    window.spin_remove()
                })

                window.path_load = ( range ) => {

                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "general", key: "languages", range: range }, ( data ) => {
                        data.label = 'Languages'
                        data.description = 'Languages used by Zume training and community.'
                        jQuery('.'+data.key).html(window.template_single(data))
                        window.click_listener(data)
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "general", key: "trainees", range: range }, ( data ) => {
                        data.label = 'Trainees'
                        data.description = ''
                        jQuery('.'+data.key).html(window.template_single(data))
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "general", key: "coaching_engagements", range: range }, ( data ) => {
                        data.label = 'Coaching Engagements'
                        data.description = ''
                        jQuery('.'+data.key).html(window.template_single_map(data))
                        window.click_listener( data )
                        window.spin_remove()
                    })

                }
                window.setup_filter()

                window.click_listener = ( data ) => {
                    window.load_list(data)
                    window.load_map(data)
                }
            })
        </script>
        <?php
    }

}
new Zume_Partner();
