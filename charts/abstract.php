<?php


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
    /**
     * Disciple_Tools_Counter constructor.
     */
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
//        $line = '<li><a href="'. site_url( '/zume-path/'.$this->base_slug.'/' . $this->slug ) . '">' . $this->title . '</a></li>';
//
//        $ref = '<ul class="menu vertical nested" id="' . $this->base_slug . '-menu">';
//        $pos = strpos( $content, $ref );
//        if ( $pos === false ){
//            $content .= '
//            <li><a href="'. site_url( '/zume-path/'. $this->base_slug .'/'. $this->slug ) .'">'.$this->base_title.'</a>
//                <ul class="menu vertical nested" id="' . $this->base_slug . '-menu">'
//                . $line . '
//            </ul></li>';
//        } else {
//            $content = substr_replace( $content, $ref . $line, $pos, strlen( $ref ) );
//        }

        $content .= '<li class=""><a href="'.site_url('/zume-path/'.$this->base_slug).'" id="'.$this->base_slug.'-menu">' .  $this->base_title . '</a></li>';

        return $content;
    }


    /**
     *  This hook add a page for the metric charts
     *
     * @param $template_for_url
     *
     * @return mixed
     */
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

    public function _empty_geojson() {
        return array(
            'type' => 'FeatureCollection',
            'features' => []
        );
    }

    public function _no_results() {
        return '<p>'. esc_attr__( 'No Results', 'disciple_tools' ) .'</p>';
    }
    public function _circular_structure_error( $wp_error ) {
        $link = false;
        $data = $wp_error->get_error_data();

        if ( isset( $data['record'] ) ){
            $link = "<a target='_blank' href=" . get_permalink( $data['record'] ) . '>Open record</a>';
        }
        return '<p>' . esc_html( $wp_error->get_error_message() ) . ' ' . $link . '</p>';
    }
}
