<?php

class PluginTest extends TestCase
{
    public function test_plugin_installed() {
        activate_plugin( 'zume-critical-path/zume-critical-path.php' );

        $this->assertContains(
            'zume-critical-path/zume-critical-path.php',
            get_option( 'active_plugins' )
        );
    }
}
