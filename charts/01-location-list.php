<?php
if ( !defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly.


class Zume_Metrics_Locations_Goals extends Zume_Chart_Base
{

    //slug and title of the top menu folder
    public $base_slug = 'locations_goals'; // lowercase
    public $base_title;
    public $title;
    public $slug = 'locations_goals'; // lowercase
    public $js_object_name = 'wp_js_object'; // This object will be loaded into the metrics.js file by the wp_localize_script.
    public $js_file_name = '/dt-metrics/combined/locations-list.js'; // should be full file name plus extension
    public $permissions = [ 'dt_all_access_contacts', 'view_project_metrics' ];
    public $namespace = 'zume_stats/v1';

    public function __construct() {
        parent::__construct();
        if ( !$this->has_permission() ){
            return;
        }
        $this->title = __( 'Locations', 'disciple_tools' );
        $this->base_title = __( 'Location Goals', 'disciple_tools' );

        $url_path = dt_get_url_path( true );
        if ( "zume-path/$this->base_slug" === $url_path ) {
            add_action( 'wp_enqueue_scripts', [ $this, 'list_scripts' ], 99 );
        }
    }

    public function list_scripts() {
        DT_Mapping_Module::instance()->drilldown_script();

        // Datatable
        wp_register_style( 'datatable-css', '//cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css', [], '1.10.19' );
        wp_enqueue_style( 'datatable-css' );
        wp_register_script( 'datatable', '//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js', [], '1.10.19' );

        // Map starter Script
        wp_enqueue_script( 'dt_'.$this->slug.'_script',
            get_template_directory_uri() . $this->js_file_name,
            [
                'jquery',
                'datatable',
                'lodash'
            ],
            filemtime( get_theme_file_path() .  $this->js_file_name ),
            true
        );
        wp_localize_script(
            'dt_'.$this->slug.'_script', $this->js_object_name, [
                'rest_endpoints_base' => esc_url_raw( rest_url() ) . $this->namespace,
                'rest_endpoint' => esc_url_raw( rest_url() ) . $this->namespace . '/location_goals',
                'load_url' =>  "zume-path/$this->base_slug",
                'base_slug' => $this->base_slug,
                'root' => esc_url_raw( rest_url() ),
                'nonce' => wp_create_nonce( 'wp_rest' ),
                'current_user_login' => wp_get_current_user()->user_login,
                'current_user_id' => get_current_user_id(),
                'translations' => $this->translations(),
                'mapping_module' => DT_Mapping_Module::instance()->localize_script(),
            ]
        );
    }

    public function translations() {
        $translations = [];
        return $translations;
    }
}
new Zume_Metrics_Locations_Goals();
