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
                            <div class="grid-x critical-path">
                                <div class="cell"><div class="trainees_full zume-critical-path"><span class="loading-spinner active"></span></div></div>
                                <div class="cell"><div class="practitioners zume-critical-path"><span class="loading-spinner active"></span></div></div>
                                <div class="cell"><div class="churches zume-critical-path"><span class="loading-spinner active"></span></div></div>
                            </div>
                        </div>
                    `)

                window.load = ( filter ) => {

                    window.spin_add()
                    window.API_post( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Fully Trained Trainees'
                        data.key = 'full_trained_trainees'
                        data.link = window.site_info.site_url + '/zume_app/heatmap_trainees'
                        data.description = 'Trainees who have completed the full Zúme training course and have recorded their progress.'
                        jQuery('.trainees_full').html(window.template_map_list(data))
                        window.click_listener(data)
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_post( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Practitioners'
                        data.key = 'practitioners'
                        data.link = window.site_info.site_url + '/zume_app/heatmap_practitioners'
                        data.description = 'Disciple making movement practitioners of all stages (Partial, Completed, Multiplying). These are those who have indicated that they are seeking movement with multiplicative methods and want to participate in the Zúme Community.'
                        jQuery('.practitioners').html(window.template_map_list(data))
                        window.click_listener(data)
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_post( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Churches'
                        data.key = 'churches'
                        data.link = window.site_info.site_url + '/zume_app/heatmap_churches'
                        data.description = 'These are the total number of churches reported by all the practitioners of all stages in the Zúme Community.'
                        jQuery('.churches').html(window.template_map_list(data))
                        window.click_listener(data)
                        window.spin_remove()
                    })
                }
                window.setup_filter()


                window.click_listener = (data ) => {
                    jQuery('.zume-list.'+data.key).click(function(){
                        jQuery('#modal-large').foundation('open')
                        jQuery('#modal-large-title').empty().html(`${data.label}<hr>`)
                        jQuery('#modal-large-content').empty().html('<span class="loading-spinner active"></span>')

                        window.API_get( window.site_url+'trainees/list', ( data_list ) => {
                            jQuery('#modal-large-content').empty().html('<table class="hover"><tbody id="zume-list-modal"></tbody></table>')
                            jQuery('#zume-list-modal').append( `<tr><td><strong>Name</strong></td><td><strong>Registered</strong></td></tr>`)
                            jQuery.each(data_list, function(i,v)  {
                                jQuery('#zume-list-modal').append( `<tr><td><a href="#">${ v.display_name }</a></td><td>${v.user_registered}</td></tr>`)
                            })
                            jQuery('.loading-spinner').removeClass('active')
                        })
                    })
                    jQuery('.zume-map.'+data.key).click(function(){
                        jQuery('#modal-full').foundation('open')
                        jQuery('#modal-full-title').empty().html(`${data.label}<hr>`)
                        jQuery('#modal-full-content').empty().html(`<iframe class="map-iframe" width="100%" height="2500" src="${data.link}" frameborder="0" style="border:0" allowfullscreen></iframe>`)
                        jQuery('.map-iframe').prop('src', jQuery(this).data('link')).prop('height', window.innerHeight - 150)

                    })
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
