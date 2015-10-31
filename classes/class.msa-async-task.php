<?php
/* ===================================================================
 *
 * My Site Audit https://mysiteaudit.com
 *
 * Created: 10/30/15
 * Package: Classes/Aync Task
 * File: class.msa-async-task.php
 * Author: Kyle Benk
 *
 *
 * Copyright 2015
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * ================================================================= */

// Exit if accessed directly

if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists('MSA_Create_Audit') ) :

class MSA_Create_Audit extends WP_Async_Task {

    /**
     * action
     *
     * (default value: 'msa_create_audit')
     *
     * @var string
     * @access protected
     */
    protected $action = 'msa_create_audit';

    /**
     * Prepare data for the asynchronous request
     *
     * @throws Exception If for any reason the request should not happen
     *
     * @param array $data An array of data sent to the hook
     *
     * @return array
     */
    protected function prepare_data( $data ) {
	    return array('audit' => $data[0]);
    }

    /**
     * Audit the post
     *
     * @access protected
     * @param mixed $data
     * @return void
     */
    protected function run_action() {

	   	// Make sure our values are set

	    if ( isset($_POST['audit']) ) {
	        do_action( "wp_async_$this->action", $_POST['audit'] );
	    }
    }

}
$msa_create_audit_task = new MSA_Create_Audit();

endif;