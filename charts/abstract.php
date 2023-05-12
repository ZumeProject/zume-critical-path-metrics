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
                window.template_trio = (value) => {
                    return `
                      <div class="cell zume-trio-card ${value.key}">
                          <div class="zume-trio-card-content ${value.key}" data-link="${value.link}">
                              <div class="zume-trio-card-title ${value.key}">
                                  ${value.label}
                              </div>
                              <div class="zume-trio-card-value ${value.key}">
                                  ${value.value}
                              </div>
                          </div>
                          <div class="zume-trio-card-footer ${value.key}">
                              <div class="grid-x">
                                  <div class="cell small-6 zume-goal ${value.key} ${value.goal_valence}">
                                      GOAL
                                  </div>
                                  <div class="cell small-6 zume-trend ${value.key} ${value.trend_valence}">
                                      TREND
                                  </div>
                              </div>
                          </div>
                      </div>
                    `;
                }
                window.template_single = (value) => {
                    return `
                        <div class="cell medium-4 large-3" data-equalizer-watch>
                            <div class="zume-card ${value.key} ${value.goal_valence}">
                                <div class="zume-card-title ${value.key}">
                                    ${value.title}
                                </div>
                                <div class="zume-card-content ${value.key}">
                                    ${value.value}
                                </div>
                                <div class="zume-card-footer ${value.key}">
                                    ${value.description}
                                </div>
                            </div>
                        </div>
                    `;
                }
            })
        </script>
        <?php
    }


}
