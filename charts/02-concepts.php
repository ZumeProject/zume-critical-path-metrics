<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Path_Concept extends Zume_Chart_Base
{
    //slug and title of the top menu folder
    public $base_slug = 'concepts'; // lowercase
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
        $this->base_title = __( 'Concepts', 'disciple_tools' );

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
                                <div class="cell small-6"></div>
                            </div>
                            <hr>
                            <span class="loading-spinner active"></span>
                            <div class="grid-x">
                                <div class="cell small-12">
                                    <h1 style="background-color: lightgrey; padding: 1em;"><strong>FUNNEL RELATIONSHIPS</strong></h1>
                                    <img src="<?php  echo plugin_dir_url(__DIR__) . 'images/funnel-relationships.png' ?>" />
                                </div>
                                <div class="cell"><hr></div>
                                <div class="cell small-12">
                                    <h1 style="background-color: lightgrey; padding: 1em;"><strong>STAGE SUMMARIES</strong></h1>
                                    <div class="grid-x grid-padding-x grid-margin-x grid-margin-y">
                                        <div class="cell small-4 center tile">
                                            <img src="<?php  echo plugin_dir_url(__DIR__) . 'images/stage-anonymous.png' ?>" />
                                        </div>
                                        <div class="cell small-4 center tile">
                                            <img src="<?php  echo plugin_dir_url(__DIR__) . 'images/stage-registrant.png' ?>" />
                                        </div>
                                        <div class="cell small-4 center tile">
                                            <img src="<?php  echo plugin_dir_url(__DIR__) . 'images/stage-active.png' ?>" />
                                        </div>
                                        <div class="cell small-4 center tile">
                                            <img src="<?php  echo plugin_dir_url(__DIR__) . 'images/stage-post.png' ?>" />
                                        </div>
                                        <div class="cell small-4 center tile">
                                            <img src="<?php  echo plugin_dir_url(__DIR__) . 'images/stage-s1.png' ?>" />
                                        </div>
                                        <div class="cell small-4 center tile">
                                            <img src="<?php  echo plugin_dir_url(__DIR__) . 'images/stage-s2.png' ?>" />
                                        </div>
                                        <div class="cell small-4 center tile">
                                            <img src="<?php  echo plugin_dir_url(__DIR__) . 'images/stage-s3.png' ?>" />
                                        </div>
                                    </div>
                                    <style>.cell.tile { border: 1px solid grey; border-radius: 10px; padding: 1em; }</style>
                                </div>
                                <div class="cell"><hr></div>
                                <div class="cell small-12">
                                    <h1 style="background-color: lightgrey; padding: 1em;"><strong>DIFFICULTY OVER TIME</strong></h1>
                                    <img src="<?php  echo plugin_dir_url(__DIR__) . 'images/difficulty-vs-time.png' ?>" />
                                </div>
                                <div class="cell"><hr></div>
                                <div class="cell">
                                    <h1 style="background-color: lightgrey; padding: 1em;"><strong>VALENCE</strong></h1>
                                    <div class="grid-x" style="color:white;text-align:center;font-size:2.5em;">
                                        <div class="cell small-2 valence-darkred">-20%</div>
                                        <div class="cell small-2 valence-red">-10%</div>
                                        <div class="cell small-4 valence-grey">On Track</div>
                                        <div class="cell small-2 valence-green">+10%</div>
                                        <div class="cell small-2 valence-darkgreen">+20%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `)

                jQuery('.loading-spinner').delay(3000).removeClass('active')
            })

        </script>
        <?php
    }

    public function styles() {

    }

}
new Zume_Path_Concept();
