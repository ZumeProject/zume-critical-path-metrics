<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

abstract class Zume_Chart_Base
{

    public $core = 'zume-path';
    public $base_slug = 'example'; //lowercase
    public $base_title = 'Example';

    //child
    public $title = '';
    public $slug = '';
    public $js_object_name = ''; // This object will be loaded into the metrics.js file by the wp_localize_script.
    public $js_file_name = ''; // should be full file name plus extension
    public $permissions = [];

    public function __construct() {
        $this->base_slug = str_replace( ' ', '', trim( strtolower( $this->base_slug ) ) );
        $url_path = dt_get_url_path( true );

        if ( strpos( $url_path, 'zume-path' ) === 0 ) {
            if ( !$this->has_permission() ){
                return;
            }
            add_filter( 'dt_metrics_menu', [ $this, 'base_menu' ], 20 ); //load menu links

            if ( strpos( $url_path, "zume-path/$this->base_slug" ) === 0 ) {
                add_filter( 'dt_templates_for_urls', [ $this, 'base_add_url' ] ); // add custom URLs
                add_action( 'wp_enqueue_scripts', [ $this, 'base_scripts' ], 99 );
            }
        }

    }

    public function base_menu( $content ) {
        $content .= '<li class=""><a href="'.site_url('/zume-path/'.$this->base_slug).'" id="'.$this->base_slug.'-menu">' .  $this->base_title . '</a></li>';
        return $content;
    }

    public function base_add_url( $template_for_url ) {
        if ( empty ( $this->base_slug ) ) {
            $template_for_url["zume-path"] = 'template-metrics.php';
        } else {
            $template_for_url["zume-path/$this->base_slug"] = 'template-metrics.php';
        }
        return $template_for_url;
    }

    public function base_scripts() {
        wp_localize_script(
            'dt_'.$this->base_slug.'_script', 'wpMetricsBase', [
                'slug' => $this->base_slug,
                'root' => esc_url_raw( rest_url() ),
                'plugin_uri' => plugin_dir_url( __DIR__ ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'current_user_login' => wp_get_current_user()->user_login,
                'current_user_id' => get_current_user_id()
            ]
        );
    }

    public function has_permission(){
        $permissions = $this->permissions;
        $pass = count( $permissions ) === 0;
        foreach ( $this->permissions as $permission ){
            if ( current_user_can( $permission ) ){
                $pass = true;
            }
        }
        return $pass;
    }

    public function js_api() {
        ?>
        <script>
            jQuery(document).ready(function($) {

                window.API_get = (url, callback ) => {
                    return $.get(url, callback);
                }
                window.API_post = (url, callback ) => {
                    return $.post(url, callback);
                }
                window.setup_filter = () => {
                    let range_filter = jQuery('#range-filter')
                    window.filter = range_filter.val()
                    range_filter.on('change', function(){
                        window.filter = range_filter.val()
                        window.load( window.filter )
                    })
                    window.load( window.filter )
                }
                window.template_trio = ({key, link, label, goal_valence, trend_valence, value, description}) => {
                    return `
                    <div class="grid-x">
                        <div class="cell zume-trio-card ${key}">
                            <div class="zume-trio-card-content ${key}" data-link="${link}">
                                <div class="zume-trio-card-title ${key}">
                                  ${label}
                                </div>
                                <div class="zume-trio-card-value ${key}">
                                  ${value}
                                </div>
                                <div>
                                  <div class="${key} description">
                                      ${description}
                                  </div>
                                </div>
                            </div>
                            <div class="zume-trio-card-footer ${key}">
                                <div class="grid-x">
                                    <div class="cell small-6 zume-goal ${key} ${goal_valence}">
                                      GOALS
                                    </div>
                                    <div class="cell small-6 zume-trend ${key} ${trend_valence}">
                                      TRENDS
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    `;
                }
                window.template_trio_single = ({key, link, label, goal, goal_valence, goal_percent, trend, trend_valence, trend_percent, value, description}) => {
                    return `
                      <div class="grid-x"><div class="cell zume-trio-single-card" >
                            <div class="zume-trio-single-top zume-list ${key}" data-link="${link}">
                                <div class="zume-trio-single-top-title ${key}">
                                    ${label}
                                </div>
                                <div class="zume-trio-single-top-value ${key}">
                                    ${value}
                                </div>
                                <div>
                                  <div class="zume-trio-single-top-description ${key}">
                                      ${description}
                                  </div>
                                </div>
                            </div>
                            <div class="zume-trio-single-left ${goal_valence} ${key}">
                                <div class="zume-trio-single-top-title ${key}">
                                    GOAL
                                </div>
                                <div class="zume-trio-single-sub-value ${key}">
                                    ${goal_percent}%
                                </div>
                                <div class="zume-trio-single-top-description ${key}">
                                     goal for this period ( ${goal} )
                                 </div>
                            </div>
                            <div class="zume-trio-single-right ${trend_valence} ${key}">
                                <div class="zume-trio-single-top-title ${key}">
                                    TREND
                                </div>
                                <div class="zume-trio-single-sub-value ${key}">
                                     ${trend_percent}%
                                </div>
                                <div class="zume-trio-single-top-description ${key}">
                                      previous period ( ${trend} )
                                  </div>
                            </div>
                      </div></div>
                    `;
                }
                window.template_trio_hero = ({key, link, label, goal, goal_valence, goal_percent, trend, trend_valence, trend_percent, value, description}) => {
                    return `
                      <div class="grid-x"><div class="cell zume-trio-single-card" >
                            <div class="zume-trio-single-top zume-list ${key}" data-key="${key}" data-link="${link}">
                                <div class="zume-trio-hero-title ${key}">
                                    ${label}
                                </div>
                                <div class="zume-trio-single-top-value ${key}">
                                    ${value}
                                </div>
                                <div>
                                  <div class="zume-trio-single-top-description ${key}">
                                      ${description}
                                  </div>
                                </div>
                            </div>
                            <div class="zume-trio-single-left ${goal_valence} ${key}">
                                <div class="zume-trio-single-top-title ${key}">
                                    GOAL
                                </div>
                                <div class="zume-trio-single-sub-value ${key}">
                                    ${goal_percent}%
                                </div>
                                <div class="zume-trio-single-top-description ${key}">
                                     goal for this period ( ${goal} )
                                 </div>
                            </div>
                            <div class="zume-trio-single-right ${trend_valence} ${key}">
                                <div class="zume-trio-single-top-title ${key}">
                                    TREND
                                </div>
                                <div class="zume-trio-single-sub-value ${key}">
                                     ${trend_percent}%
                                </div>
                                <div class="zume-trio-single-top-description ${key}">
                                      previous period ( ${trend} )
                                  </div>
                            </div>
                      </div></div>
                    `;
                }
                window.template_map_list = ({key, link, label, value, description}) => {
                    return `
                      <div class="cell zume-trio-card ${key} medium-4 large-3" data-equalizer-watch>
                          <div class="zume-card-content ${key}" >
                              <div class="zume-trio-card-title ${key}">
                                  ${label}
                              </div>
                              <div class="zume-trio-card-value ${key}">
                                  ${value}
                              </div>
                              <div class="${key} description">
                                  ${description}
                               </div>
                          </div>
                          <div class="zume-trio-card-footer ${key}">
                              <div class="grid-x">
                                  <div class="cell small-6 zume-card-sub-left zume-list ${key}" data-link="${link}">
                                      LIST
                                  </div>
                                  <div class="cell small-6 zume-card-sub-right zume-map ${key}">
                                      MAP
                                  </div>
                              </div>
                          </div>
                      </div>
                    `;
                }
                window.template_single = ({key, valence, label, value, description}) => {
                    return `
                        <div class="grid-x">
                        <div class="cell" data-equalizer-watch>
                            <div class="zume-card zume-list ${key} ${valence}">
                                <div class="zume-card-title ${key}">
                                    ${label}
                                </div>
                                <div class="zume-card-value ${key}">
                                    ${value}
                                </div>
                                <div class="zume-card-footer ${key}">
                                    ${description}
                                </div>
                            </div>
                        </div></div>
                    `;
                }
                window.spin_add = () => {
                    if ( typeof window.spin_count === 'undefined' ){
                        window.spin_count = 0
                    }
                    window.spin_count++
                    jQuery('.loading-spinner').addClass('active')
                }
                window.spin_remove = () => {
                    if ( typeof window.spin_count === 'undefined' ){
                        window.spin_count = 0
                    }
                    window.spin_count--
                    if ( window.spin_count === 0 ) {
                        jQuery('.loading-spinner').removeClass('active')
                    }
                }
            })
        </script>
        <?php
    }


}
