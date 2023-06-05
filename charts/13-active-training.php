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
                                </div>
                            </div>
                            <hr>
                            <div id="hero"><span class="loading-spinner active"></span></div>
                            <div class="grid-x grid-margin-x grid-margin-y">
                                 <div class="cell medium-3 needs_coach"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 inactive"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 "><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 "><span class="loading-spinner active"></span></div>
                            </div>
                            <hr>
                            <div class="grid-x">
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
                                 <div class="cell medium-6 first"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-6 second"><span class="loading-spinner active"></span></div>
                            </div>
                            <div class="grid-x grid-margin-x grid-margin-y">
                                 <div class="cell"><h2>Remaining Progress</h2></div>
                            </div>
                            <div class="grid-x grid-margin-x grid-margin-y">
                                 <div class="cell medium-6 third"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-6 fourth"><span class="loading-spinner active"></span></div>
                            </div>
                            <hr>
                            <h2>Sessions</h2>
                            <div class="grid-x grid-margin-x grid-margin-y">
                                 <div class="cell medium-3 session1"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session2"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session3"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session4"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session5"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session6"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session7"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session8"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session9"><span class="loading-spinner active"></span></div>
                                 <div class="cell medium-3 session10"><span class="loading-spinner active"></span></div>
                            </div>
                            <hr>
                            <h2>Engagements Per Training Element</h2>
                            <div id="chartdiv"></div>
                        </div>
                    `)
                window.load = ( filter ) => {
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Active Training Trainees'
                        jQuery('#hero').html( window.template_map_list( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })

                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Has Coach'
                        jQuery('.needs_coach').html( window.template_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Inactive Trainees'
                        jQuery('.inactive').html( window.template_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })

                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'New Active Trainees'
                        jQuery('.first').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Total Check-ins'
                        jQuery('.second').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter+'&negative_stat=true', ( data ) => {
                        data.label = 'Has No Coach'
                        jQuery('.third').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter+'&negative_stat=true', ( data ) => {
                        data.label = 'Has No Updated Profile'
                        jQuery('.fourth').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })

                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Session 1'
                        jQuery('.session1').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Session 2'
                        jQuery('.session2').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Session 3'
                        jQuery('.session3').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Session 4'
                        jQuery('.session4').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Session 5'
                        jQuery('.session5').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Session 6'
                        jQuery('.session6').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Session 7'
                        jQuery('.session7').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Session 8'
                        jQuery('.session8').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Session 9'
                        jQuery('.session9').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })
                    window.spin_add()
                    window.API_get( window.site_url+'sample?filter='+filter, ( data ) => {
                        data.label = 'Session 10'
                        jQuery('.session10').html( window.template_trio_single( data ) )
                        window.click_listener( data.key )
                        window.spin_remove()
                    })






                    window.spin_add()
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
new Zume_Path_Active();
