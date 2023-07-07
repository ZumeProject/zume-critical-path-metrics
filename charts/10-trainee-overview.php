<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Funnel_Trainee extends Zume_Funnel_Chart_Base
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
        $this->base_title = __( 'Overview', 'zume_funnels' );

        $url_path = dt_get_url_path( true );
        if ( "zume-funnel" === $url_path ) {
            add_action( 'wp_enqueue_scripts', [ $this, 'base_scripts' ], 99 );
            add_action( 'wp_head',[ $this, 'wp_head' ], 1000);
        }
    }

    public function base_menu( $content ) {
        $content .= '<li class=""><a href="'.site_url('/zume-funnel/'.$this->base_slug).'" id="'.$this->base_slug.'-menu">' .  $this->base_title . '</a></li>';
        return $content;
    }
    public function wp_head() {
        $this->styles();
            $this->js_api();
            ?>
            <script>
                jQuery(document).ready(function(){
                    "use strict";
                    let chart = jQuery('#chart')
                    chart.empty().html(`
                        <div id="zume-funnel">
                            <div class="grid-x">
                                <div class="cell small-6"><h1>Practitioner Funnel</h1></div>
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
                                    <span class="loading-spinner active right" style="margin:.5em 1em;"></span>
                                </div>
                            </div>
                            <hr>
                            <div class="grid-x grid-padding-x">

                                <div class="cell medium-9 center">
                                     STEPS
                                </div>
                                <div class="cell medium-3"></div>
                                <!-- element-->
                                <div class="cell medium-9 zume-funnel">
                                     <div class="registrants"><span class="loading-spinner active"></span></div>
                                </div>
                                <div class="cell medium-3 padding-top">
                                    <h3>Step Identity</h3>
                                    <ul>
                                        <li>Has registered with an email</li>
                                    </ul>
                                    <h3>Next Steps</h3>
                                    <ul>
                                        <li>Make a training plan</li>
                                        <li>Invite friends</li>
                                    </ul>
                                </div>

                                <!-- element-->
                                <div class="cell medium-9 zume-funnel">
                                     <div class="active_training_trainees"><span class="loading-spinner active"></span></div>
                                </div>
                                <div class="cell medium-3 padding-top">
                                    <h3>Step Identity</h3>
                                    <ul>
                                        <li>Has a plan</li>
                                    </ul>
                                    <h3>Next Steps</h3>
                                    <ul>
                                        <li>Complete training content</li>
                                        <li>Create 3-month plan</li>
                                    </ul>
                                </div>

                                <!-- element-->
                                <div class="cell medium-9 zume-funnel">
                                     <div class="post_training_trainees"><span class="loading-spinner active"></span></div>
                                </div>
                                <div class="cell medium-3 padding-top">
                                    <h3>Step Identity</h3>
                                    <ul>
                                        <li>Has completed training</li>
                                        <li>Working 3-month plan (maybe)</li>
                                    </ul>
                                    <h3>Next Steps</h3>
                                    <ul>
                                        <li>Make first practitioner report</li>
                                        <li>Establish ongoing coaching relationship</li>
                                    </ul>
                                </div>

                                <!-- element-->
                                <div class="cell medium-9 zume-funnel">
                                     <div class="s1_practitioners"><span class="loading-spinner active"></span></div>
                                </div>
                                <div class="cell medium-3 padding-top">
                                    <h3>Step Identity</h3>
                                    <ul>
                                        <li>Implementing partial checklist</li>
                                        <li>Some fruit, inconsistent</li>
                                        <li>Getting coached</li>
                                    </ul>
                                    <h3>Next Steps</h3>
                                    <ul>
                                        <li>Full MAWL competence with coaching checklist</li>
                                        <li>Continued reporting</li>
                                        <li>Connect with S2 practitioner hubs</li>
                                    </ul>
                                </div>

                                <!-- element-->
                                <div class="cell medium-9 zume-funnel">
                                     <div class="s2_practitioners"><span class="loading-spinner active"></span></div>
                                </div>
                                <div class="cell medium-3 padding-top">
                                    <h3>Step Identity</h3>
                                    <ul>
                                        <li>Coaching checklist competence</li>
                                        <li>Consistent effort, consistent fruit</li>
                                    </ul>
                                    <h3>Next Steps</h3>
                                    <ul>
                                        <li>focus on 2,3,4 generations of disciples</li>
                                        <li>focus on 2,3,4 generations of churches</li>
                                        <li>Connect with S3 practitioner hubs</li>
                                    </ul>
                                </div>

                                <!-- element-->
                                <div class="cell medium-9 zume-funnel">
                                    <div class="s3_practitioners"><span class="loading-spinner active"></span></div>
                                </div>
                                <div class="cell medium-3 padding-top">
                                    <h3>Step Identity</h3>
                                    <ul>
                                        <li>2,3,4 generations of disciples</li>
                                        <li>2,3,4 generations of churches</li>
                                    </ul>
                                    <h3>Next Steps</h3>
                                    <ul>
                                        <li>downstream coaching for consistent generations</li>
                                    </ul>
                                </div>
                            </div>


                        </div>
                    `)

                    window.path_load = ( range ) => {
                        window.spin_add()
                        makeRequest('GET', 'total', { stage: "registrants", key: "total_registrants", range: range }, window.site_info.rest_root ).done( function( data ) {
                            data.label = "Registrant"
                            jQuery('.registrants').html(window.template_map_list(data))
                            window.click_listener(data)
                            window.spin_remove()
                        })
                        window.spin_add()
                        makeRequest('GET', 'total', { stage: "att", key: "total_att", range: range }, window.site_info.rest_root ).done( function( data ) {
                            data.label = "Active Training Trainee"
                            jQuery('.active_training_trainees').html(window.template_map_list(data))
                            window.click_listener(data)
                            window.spin_remove()
                        })
                        window.spin_add()
                        makeRequest('GET', 'total', { stage: "ptt", key: "total_ptt", range: range }, window.site_info.rest_root ).done( function( data ) {
                            data.label = "Post Training Trainee"
                            jQuery('.post_training_trainees').html(window.template_map_list(data))
                            window.click_listener(data)
                            window.spin_remove()
                        })
                        window.spin_add()
                        makeRequest('GET', 'total', { stage: "s1", key: "total_s1", range: range }, window.site_info.rest_root ).done( function( data ) {
                            data.label = "(S1) Partial Practitioner"
                            jQuery('.s1_practitioners').html(window.template_map_list(data))
                            window.click_listener(data)
                            window.spin_remove()
                        })
                        window.spin_add()
                        makeRequest('GET', 'total', { stage: "s2", key: "total_s2", range: range }, window.site_info.rest_root ).done( function( data ) {
                            data.label = "(S2) Completed Practitioner"
                            jQuery('.s2_practitioners').html(window.template_map_list(data))
                            window.click_listener(data)
                            window.spin_remove()
                        })
                        window.spin_add()
                        makeRequest('GET', 'total', { stage: "s3", key: "total_s3", range: range }, window.site_info.rest_root ).done( function( data ) {
                            data.label = "(S3) Multiplying Practitioner"
                            jQuery('.s3_practitioners').html(window.template_map_list(data))
                            window.click_listener(data)
                            window.spin_remove()
                        })
                    }
                    window.setup_filter()

                    window.click_listener = ( data ) => {
                        window.load_list(data)
                        window.load_map(data)
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
new Zume_Funnel_Trainee();
