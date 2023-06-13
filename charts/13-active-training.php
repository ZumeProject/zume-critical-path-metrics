<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Path_Active extends Zume_Chart_Base
{
    //slug and title of the top menu folder
    public $base_slug = 'active'; // lowercase
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
        $this->base_title = __( 'Active Training', 'disciple_tools' );

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
        <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
        <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
        <style>
            #chartdiv {
                width: 100%;
                height: 800px;
            }
        </style>
        <script>
            jQuery(document).ready(function(){
                "use strict";
                let chart = jQuery('#chart')
                chart.empty().html(`
                        <div id="zume-path">
                            <div class="grid-x">
                                <div class="cell small-6"><h1>Active Training</h1></div>
                                <div class="cell small-6">
                                </div>
                            </div>
                            <hr>
                            <div class="grid-x">
                                <div class="cell hero"><span class="loading-spinner active"></span></div>
                            </div>
                            <div class="grid-x grid-margin-x grid-margin-y">
                                 <div class="cell medium-3 has_coach"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 inactive_trainees"><span class="loading-spinner active"></span></div>
                            </div>
                            <hr>
                            <div class="grid-x">
                                <div class="cell center"><h1 id="range-title">Last 30 Days</h1></div>
                                <div class="cell small-6">
                                    <h2>Progress Indicators</h2>
                                </div>
                                <div class="cell small-6" style="float: right;">
                                     <span>
                                        <select id="range-filter" class="z-range-filter">
                                            <option value="30">Last 30 days</option>
                                            <option value="7">Last 7 days</option>
                                            <option value="90">Last 90 days</option>
                                            <option value="365">Last 1 Year</option>
                                        </select>
                                    </span>
                                    <span class="loading-spinner active float-spinner"></span>
                                </div>
                            </div>
                            <div class="grid-x grid-margin-x grid-margin-y">
                                 <div class="cell medium-6 new_active_trainees"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-6 total_checkins"><span class="loading-spinner active"></span></div>
                            </div>
                            <div class="grid-x grid-margin-x grid-margin-y">
                                 <div class="cell"><h2>Remaining Progress</h2></div>
                            </div>
                            <div class="grid-x grid-margin-x grid-margin-y">
                                 <div class="cell medium-6 has_no_coach"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-6 has_no_updated_profile"><span class="loading-spinner active"></span></div>
                            </div>
                            <hr>
                            <h2>Sessions</h2>
                            <div class="grid-x grid-margin-x grid-margin-y">
                                 <div class="cell medium-3 session_1"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session_2"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session_3"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session_4"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session_5"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session_6"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session_7"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session_8"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session_9"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session_10"><span class="loading-spinner active"></span></div>
                            </div>
                            <hr>
                            <h2>Engagements Per Training Element</h2>
                            <div id="chartdiv"></div>
                        </div>
                    `)
                // totals
                window.spin_add()
                window.API_get( window.site_info.total_url, { stage: "att", key: "total_att" }, ( data ) => {
                    data.link = ''
                    jQuery('.hero').html(window.template_map_list(data))
                    window.click_listener( data )
                    window.spin_remove()
                })
                window.spin_add()
                window.API_get( window.site_info.total_url, { stage: "att", key: "has_coach" }, ( data ) => {
                    jQuery('.has_coach').html(window.template_single_list(data))
                    window.click_listener( data )
                    window.spin_remove()
                })
                window.spin_add()
                window.API_get( window.site_info.total_url, { stage: "att", key: "inactive_trainees" }, ( data ) => {
                    jQuery('.inactive_trainees').html(window.template_single_list(data))
                    window.click_listener( data )
                    window.spin_remove()
                })

                // range
                window.load = ( range ) => {
                    // positive stats
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "att", key: "new_active_trainees", range: range }, ( data ) => {
                        jQuery('.new_active_trainees').html(window.template_trio(data))
                        window.click_listener( data )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "att", key: "total_checkins", range: range }, ( data ) => {
                        jQuery('.total_checkins').html(window.template_trio(data))
                        window.click_listener( data )
                        window.spin_remove()
                    })

                    // negative stats
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "att", key: "has_no_coach", range: range, negative_stat: true }, ( data ) => {
                        jQuery('.has_no_coach').html(window.template_trio(data))
                        window.click_listener( data )
                        window.spin_remove()
                    })

                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "att", key: "has_no_updated_profile", range: range, negative_stat: true }, ( data ) => {
                        jQuery('.has_no_updated_profile').html(window.template_trio(data))
                        window.click_listener( data )
                        window.spin_remove()
                    })



                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "att", key: "session_1", range: range }, ( data ) => {
                        jQuery('.session_1').html(window.template_trio(data))
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "att", key: "session_2", range: range }, ( data ) => {
                        jQuery('.session_2').html(window.template_trio(data))
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "att", key: "session_3", range: range }, ( data ) => {
                        jQuery('.session_3').html(window.template_trio(data))
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "att", key: "session_4", range: range }, ( data ) => {
                        jQuery('.session_4').html(window.template_trio(data))
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "att", key: "session_5", range: range }, ( data ) => {
                        jQuery('.session_5').html(window.template_trio(data))
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "att", key: "session_6", range: range }, ( data ) => {
                        jQuery('.session_6').html(window.template_trio(data))
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "att", key: "session_7", range: range }, ( data ) => {
                        jQuery('.session_7').html(window.template_trio(data))
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "att", key: "session_8", range: range }, ( data ) => {
                        jQuery('.session_8').html(window.template_trio(data))
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "att", key: "session_9", range: range }, ( data ) => {
                        jQuery('.session_9').html(window.template_trio(data))
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_info.total_url, { stage: "att", key: "session_10", range: range }, ( data ) => {
                        jQuery('.session_10').html(window.template_trio(data))
                        window.spin_remove()
                    })


                    window.API_get( window.site_url+'training_elements?filter='+filter, ( data ) => {
                        am5.ready(function() {
                            console.log(data)

                            am5.array.each(am5.registry.rootElements, function(root) {
                                if (root.dom.id == "chartdiv") {
                                    root.dispose();
                                }
                            });

                            if ( typeof root === 'undefined' ) {
                                var root = am5.Root.new("chartdiv");
                                root.setThemes([
                                    am5themes_Animated.new(root)
                                ]);

                                var chart = root.container.children.push(am5xy.XYChart.new(root, {
                                    panX: true,
                                    panY: true,
                                    wheelX: "panX",
                                    wheelY: "zoomX",
                                    pinchZoomX: true
                                }));

                                var cursor = chart.set("cursor", am5xy.XYCursor.new(root, {}));
                                cursor.lineY.set("visible", false);
                                var xRenderer = am5xy.AxisRendererX.new(root, { minGridDistance: 30 });
                                xRenderer.labels.template.setAll({
                                    rotation: -90,
                                    centerY: am5.p50,
                                    centerX: am5.p100,
                                    paddingRight: 15
                                });

                                xRenderer.grid.template.setAll({
                                    location: 1
                                })

                                var xAxis = chart.xAxes.push(am5xy.CategoryAxis.new(root, {
                                    maxDeviation: 0.3,
                                    categoryField: "label",
                                    renderer: xRenderer,
                                    tooltip: am5.Tooltip.new(root, {})
                                }));

                                var yAxis = chart.yAxes.push(am5xy.ValueAxis.new(root, {
                                    maxDeviation: 0.3,
                                    renderer: am5xy.AxisRendererY.new(root, {
                                        strokeOpacity: 0.1
                                    })
                                }));

                                var series = chart.series.push(am5xy.ColumnSeries.new(root, {
                                    name: "Series 1",
                                    xAxis: xAxis,
                                    yAxis: yAxis,
                                    valueYField: "value",
                                    sequencedInterpolation: true,
                                    categoryXField: "label",
                                    tooltip: am5.Tooltip.new(root, {
                                        labelText: "{valueY}"
                                    })
                                }));

                                series.columns.template.setAll({ cornerRadiusTL: 5, cornerRadiusTR: 5, strokeOpacity: 0 });

                                xAxis.data.setAll(data);
                                series.data.setAll(data);

                                series.appear(1000);
                                chart.appear(1000, 100);
                            }
                        })
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

    public function data() {
        return [
            'translations' => [
                'title_overview' => __( 'Project Overview', 'disciple_tools' ),
            ],
        ];
    }

}
new Zume_Path_Active();
