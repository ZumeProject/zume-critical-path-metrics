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
                                        <select>
                                            <option value="30">Last 30 days</option>
                                            <option value="7">Last 7 days</option>
                                            <option value="90">Last 90 days</option>
                                            <option value="365">Last 1 Year</option>
                                            <option value="all">All Time</option>
                                        </select>
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <span class="loading-spinner active"></span>
                            <div class="grid-x">
                                <div class="cell medium-6">
                                    <div class="post_training_trainees zume-critical-path"></div>

                                </div>
                                <div class="cell medium-6" style="padding:1em;">
                                    <h3><strong>What are Practitioners?</strong></h3>
                                    <p>
                                        A Candidate is a person in this stage that moves from an Anonymous Visitor coming from the web
                                        to a person who has registered.
                                    </p>
                                    <h3><strong>What are Churches?</strong></h3>
                                    <p>
                                        A Candidate is a person in this stage that moves from an Anonymous Visitor coming from the web
                                        to a person who has registered.
                                    </p>
                                </div>
                            </div>
                        </div>
                    `)

                window.API_post( window.site_url+'post_training_trainees', ( data ) => {
                    jQuery('.post_training_trainees').html(window.template_trio(data))
                })



                // let critical_path = jQuery('.zume-critical-path')
                //     critical_path.empty()
                //
                // window.API_post( window.site_url+'post_training_trainees', ( data ) => {
                //     critical_path.append(window.template_trio(data))
                //     console.log(data)
                // })



                // window.API_post( window.site_url+'l1', ( data ) => {
                    // critical_path.append(window.template_trio(data))
                    // console.log(data)
                // })

                // window.API_post( window.site_url+'churches', ( data ) => {
                    // critical_path.append(window.template_trio(data))
                    // console.log(data)
                // })

                // window.API_post( window.site_url, ( data ) => {
                //     critical_path.empty()
                //     jQuery.each( data.list, function( key, value ) {
                //         let content = window.template_trio(value)
                //         critical_path.append(content)
                //     })
                //     console.log(data)
                // })

                // window.load_stats( window.site_url, 'zume-critical-path')


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
        </style>
        <?php
    }
}
new Zume_Path_Goals();
