<?php
if ( !defined( 'ABSPATH' ) ) { exit; } // Exit if accessed directly.

class Zume_Metrics_Base {

    public $base_slug = 'zume-path';
    private static $_instance = null;
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct()
    {
        add_filter( 'desktop_navbar_menu_options', [ $this, 'add_navigation_links' ], 35 );
        add_filter( 'off_canvas_menu_options', [ $this, 'add_navigation_links' ], 35);

        $url_path = dt_get_url_path(true);

        // top
        if ( str_contains( $url_path, $this->base_slug ) !== false ) {
            add_filter('dt_templates_for_urls', [$this, 'dt_templates_for_urls']);
            add_filter('dt_metrics_menu', [$this, 'dt_metrics_menu'], 99);

            require_once ('abstract.php');

            require_once ('01-goals.php');
            require_once ('02-concepts.php');

            require_once ('10-trainee-journey.php');
            require_once ('11-candidate.php');
            require_once ('12-pre-training.php');
            require_once ('13-active-training.php');
            require_once ('14-post-training.php');
            require_once ('15-l1-practitioner.php');
            require_once ('16-l2-practitioner.php');
            require_once ('17-l3-practitioner.php');

            require_once ('20-overview.php');
            require_once ('21-facilitator.php');
            require_once ('22-early.php');
            require_once ('23-advanced.php');
        }
    }

    public function add_navigation_links( $tabs ) {
        if ( current_user_can( 'access_disciple_tools' ) ) {
            $tabs[] = [
                "link" => site_url( "/zume-path/" ), // the link where the user will be directed when they click
                "label" => __( "Critical Path", "disciple_tools" )  // the label the user will see
            ];
        }
        return $tabs;
    }

    public function dt_metrics_menu( $content ) {
        return $content;
    }

    public function dt_templates_for_urls( $template_for_url ) {
        $template_for_url['zume-path'] = 'template-metrics.php';
        return $template_for_url;
    }
}
Zume_Metrics_Base::instance();
