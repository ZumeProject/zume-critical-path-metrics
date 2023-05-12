<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Trainee_Critical_Path extends Zume_Chart_Base
{
    //slug and title of the top menu folder
    public $base_slug = 'trainee_journey'; // lowercase
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
        $this->base_title = __( 'Trainee Journey', 'disciple_tools' );

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

//        wp_enqueue_script( 'zume_api', plugin_dir_url(__FILE__) . 'charts.js', [ 'jquery' ], filemtime( plugin_dir_path(__FILE__) . 'charts.js' ), true );
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
                                        <select>
                                            <option value="30">Last 30 days</option>
                                            <option value="7">Last 7 days</option>
                                            <option value="90">Last 90 days</option>
                                            <option value="365">Last 1 year</option>
                                            <option value="all">All Time</option>
                                        </select>
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <span class="loading-spinner active"></span>

                            <div class="grid-y zume-cards critical-path" id="zume-cards">
                                <div class="candidates"></div>
                                <div class="pre_training_trainees"></div>
                                <div class="active_training_trainees"></div>
                                <div class="post_training_trainees"></div>
                                <div class="l1_practitioners"></div>
                                <div class="l2_practitioners"></div>
                                <div class="l3_practitioners"></div>
                            </div>

                        </div>
                    `)

                    window.API_post( window.site_url+'candidates', ( data ) => {
                        jQuery('.candidates').html(window.template_trio(data))
                    })
                    window.API_post( window.site_url+'pre_training_trainees', ( data ) => {
                        jQuery('.pre_training_trainees').html(window.template_trio(data))
                    })
                    window.API_post( window.site_url+'active_training_trainees', ( data ) => {
                        jQuery('.active_training_trainees').html(window.template_trio(data))
                    })
                    window.API_post( window.site_url+'post_training_trainees', ( data ) => {
                        jQuery('.post_training_trainees').html(window.template_trio(data))
                    })
                    window.API_post( window.site_url+'l1_practitioners', ( data ) => {
                        jQuery('.l1_practitioners').html(window.template_trio(data))
                    })
                    window.API_post( window.site_url+'l2_practitioners', ( data ) => {
                        jQuery('.l2_practitioners').html(window.template_trio(data))
                    })
                    window.API_post( window.site_url+'l3_practitioners', ( data ) => {
                        jQuery('.l3_practitioners').html(window.template_trio(data))
                    })



                    // window.API_post( window.site_url+'l1', ( data ) => {
                    // critical_path.append(window.template_trio(data))
                    // console.log(data)
                    // })

                    // window.API_post( window.site_url+'churches', ( data ) => {
                    // critical_path.append(window.template_trio(data))
                    // console.log(data)
                    // })

                    // let data = [
                    //     {
                    //         "title": "Candidates",
                    //         "link": "candidates",
                    //         "value": '0',
                    //         "goal": 'valence-grey',
                    //         "trend": 'valence-grey',
                    //     },
                    //     {
                    //         "title": "Pre-Training",
                    //         "link": "pre",
                    //         "value": '0',
                    //         "goal": 'valence-grey',
                    //         "trend": 'valence-grey',
                    //     },
                    //     {
                    //         "title": "Active Training",
                    //         "link": "active",
                    //         "value": '0',
                    //         "goal": 'valence-grey',
                    //         "trend": 'valence-grey',
                    //     },
                    //     {
                    //         "title": "Post-Training",
                    //         "link": "post",
                    //         "value": '0',
                    //         "goal": 'valence-grey',
                    //         "trend": 'valence-grey',
                    //     },
                    //     {
                    //         "title": "L1 Practitioners",
                    //         "link": "l1_practitioners",
                    //         "value": '0',
                    //         "goal": 'valence-grey',
                    //         "trend": 'valence-grey',
                    //     },
                    //     {
                    //         "title": "L2 Practitioners",
                    //         "link": "l2_practitioners",
                    //         "value": '0',
                    //         "goal": 'valence-grey',
                    //         "trend": 'valence-grey',
                    //     },
                    //     {
                    //         "title": "L3 Practitioners",
                    //         "link": "l3_practitioners",
                    //         "value": '0',
                    //         "goal": 'valence-grey',
                    //         "trend": 'valence-grey',
                    //     }
                    // ]

                    // jQuery.each( data, function( key, value ) {
                    //     jQuery('#zume-cards').append(`
                    //         <div class="cell zume-trio-card" >
                    //             <div class="zume-trio-card-content" data-link="${value.link}">
                    //                 <div class="zume-trio-card-title">
                    //                     ${value.title}
                    //                 </div>
                    //                 <div class="zume-trio-card-value">
                    //                     ${value.value}
                    //                 </div>
                    //             </div>
                    //             <div class="zume-trio-card-footer">
                    //                 <div class="grid-x">
                    //                     <div class="cell small-6 zume-goal ${value.goal}">
                    //                         GOAL
                    //                     </div>
                    //                     <div class="cell small-6 zume-trend ${value.trend}">
                    //                         TREND
                    //                     </div>
                    //                 </div>
                    //             </div>
                    //         </div>
                    //     `)
                    // })

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
            .zume-cards {
                max-width: 700px;
            }
        </style>
        <?php
    }

}
new Zume_Trainee_Critical_Path();
