<?php

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
//        add_action('rest_api_init', [$this, 'add_api_routes']);

        add_filter( 'desktop_navbar_menu_options', [ $this, 'add_navigation_links' ], 35 );
        add_filter( 'off_canvas_menu_options', [ $this, 'add_navigation_links' ], 35);

        // ss url
        $url_path = dt_get_url_path(true);

        // top
        if ($this->base_slug === substr($url_path, 0, 9  ) ) {
            add_filter('dt_templates_for_urls', [$this, 'add_url']);
            add_filter('dt_metrics_menu', [$this, 'top_menu'], 99);
//            add_action( 'wp_enqueue_scripts', [ $this, 'scripts' ], 99 );
        }
    }

    public function top_menu( $content ) {
        $content .= '<li class=""><a href="'.site_url().'/zume-path" >' .  esc_html( 'Critical Path' ) . '</a></li>';
        $content .= '<li class=""><a href="'.site_url().'/zume-path/candidate" >' .  esc_html( 'Candidate' ) . '</a></li>';
        $content .= '<li class=""><a href="'.site_url().'/zume-path/pre" >' .  esc_html( 'Pre-Training' ) . '</a></li>';
        $content .= '<li class=""><a href="'.site_url().'/zume-path/active" >' .  esc_html( 'Active Training' ) . '</a></li>';
        $content .= '<li class=""><a href="'.site_url().'/zume-path/post" >' .  esc_html( 'Post-Training' ) . '</a></li>';
        $content .= '<li class=""><a href="'.site_url().'/zume-path/l1-practitioner" >' .  esc_html( 'L1 Practitioner' ) . '</a></li>';
        $content .= '<li class=""><a href="'.site_url().'/zume-path/l2-practitioner" >' .  esc_html( 'L2 Practitioner' ) . '</a></li>';
        $content .= '<li class=""><a href="'.site_url().'/zume-path/l3-practitioner" >' .  esc_html( 'L3 Practitioner' ) . '</a></li>';
        return $content;
        // jQuery('#metrics-sidemenu li').removeClass('side-menu-item-highlight');
    }

    public function add_url( $template_for_url ) {
        $template_for_url['zume-path'] = 'template-metrics.php';
        return $template_for_url;
    }

    public function add_navigation_links( $tabs ) {
        //check user permissions
        if ( current_user_can( 'access_contacts' ) ) {

            $tabs[] = [
                "link" => site_url( "/zume-path/" ), // the link where the user will be directed when they click
                "label" => __( "Critical Path", "disciple_tools" )  // the label the user will see
            ];

        }
        return $tabs;
    }

}
Zume_Metrics_Base::instance();
