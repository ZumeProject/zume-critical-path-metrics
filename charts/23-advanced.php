<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Coaching_Advanced extends Zume_Chart_Base
{
    //slug and title of the top menu folder
    public $base_slug = 'coaching_advanced'; // lowercase
    public $slug = '';
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
        $this->base_title = __( 'Advanced Practitioner', 'disciple_tools' );

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
                                    <span class="loading-spinner active" style="float: right; margin:0 10px;"></span>
                                </div>
                            </div>
                            <hr>
                            <h2>Key Progress Indicators</h2>
                            <div class="grid-x grid-margin-x grid-margin-y">
                                 <div class="cell medium-6 first"></div>
                                 <div class="cell medium-6 second"></div>
                                 <div class="cell medium-6 third"></div>
                            </div>
                            <hr>
                            <h2>All Time</h2>
                            <div class="grid-x grid-margin-x grid-margin-y">
                                 <div class="cell medium-3 all1"></div>
                                 <div class="cell medium-3 all2"></div>
                                 <div class="cell medium-3 all3"></div>
                                 <div class="cell medium-3 all4"></div>
                            </div>
                        </div>
                    `)

                window.load = ( filter ) => {
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'New Requests'
                        jQuery('.first').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Coaching Engagements'
                        jQuery('.second').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Reports'
                        jQuery('.third').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })


                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'People'
                        jQuery('.all1').html( window.template_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Locations'
                        jQuery('.all2').html( window.template_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = ''
                        jQuery('.all3').html( window.template_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                }
                window.setup_filter()

                window.click_listener = (key) => {
                    jQuery('.zume-list.'+key).click(function(){
                        jQuery('#modal-large').foundation('open')
                        jQuery('#modal-large-title').empty().html('Fact Label<hr>')
                        jQuery('#modal-large-content').empty().html('<span class="loading-spinner active"></span>')

                        window.API_get( window.site_url+'trainees/list', ( data ) => {
                            jQuery('#modal-large-content').empty().html('<table class="hover"><tbody id="zume-list-modal"></tbody></table>')
                            jQuery.each(data, function(i,v)  {
                                jQuery('#zume-list-modal').append( '<tr><td><a href="#">' + v.display_name + '</a></td></tr>')
                            })
                            jQuery('.loading-spinner').removeClass('active')
                        })
                    })
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
new Zume_Coaching_Advanced();
