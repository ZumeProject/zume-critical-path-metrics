<?php

class PluginTest extends TestCase
{
    public function test_plugin_installed() {
        activate_plugin( 'zume-funnel-metrics/zume-funnel-metrics.php' );

        $this->assertContains(
            'zume-funnel-metrics/zume-funnel-metrics.php',
            get_option( 'active_plugins' )
        );
    }
}
