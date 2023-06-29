<?php

class PluginTest extends TestCase
{
    public function test_plugin_installed() {
        activate_plugin( 'zume-funnels/zume-funnels.php' );

        $this->assertContains(
            'zume-funnels/zume-funnels.php',
            get_option( 'active_plugins' )
        );
    }
}
