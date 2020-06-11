<?php

namespace Barn2\WPT_Lib\Plugin\License;

use Barn2\WPT_Lib\Scheduled_Task,
    Barn2\WPT_Lib\Schedulable;

/**
 * A scheduled task to periodically check the status of the plugin license.
 *
 * @package   Barn2/barn2-lib
 * @author    Barn2 Plugins <info@barn2.co.uk>
 * @license   GPL-3.0
 * @copyright Barn2 Media Ltd
 */
class License_Checker extends Scheduled_Task implements Schedulable {

    /**
     * The plugin license.
     *
     * @var License
     */
    private $license;

    public function __construct( $plugin_file, License $license ) {
        parent::__construct( $plugin_file );
        $this->license = $license;
    }

    public function run() {
        if ( $this->license->is_active() ) {
            $this->license->refresh();
        }
    }

    protected function get_cron_hook() {
        return 'barn2_license_check_' . $this->license->get_item_id();
    }

    protected function get_interval() {
        return 'daily';
    }

}
