<?php
/**
 * This file is responsible for the entire audit creation workflow.  It uses WP
 * Cron to handle auditing many posts at a time.
 *
 * @package Functions / Create Audit
 */

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Run an audit
 *
 * @access public
 * @return void
 */
function msa_run_audit()
{

    // Create an audit if we have one in the queue.
    $next_audit = msa_get_next_audit_to_run();

    if ( isset( $next_audit ) && is_array( $next_audit ) ) {
        wp_schedule_single_event( time(), 'msa_run_audit_background', array( $next_audit ) );
        set_transient( 'msa_schedule_audit', true );
        msa_clear_audit_queue();
    }

}

add_action( 'init', 'msa_run_audit' );

/**
 * Set the current create audit data
 *
 * @access public
 * @param array $data The audit data.
 * @return bool true|false Was the audit added to the queue?
 */
function msa_add_audit_to_queue( $data )
{
    // Sanitize create audit post data
    $sanitized_data = array();
    foreach ( $data as $key => $item ) {
        $key                    = sanitize_text_field( $key );
        if($key != "post-types") {
            $item                   = sanitize_text_field( wp_unslash( $item ) );
        } else {
            foreach($item as $key_p => $item_p) {
                $item[$key_p]                   = sanitize_text_field(  $item_p );
            }
        }
        $sanitized_data[ $key ] = $item;
    }

    // Check if we are already performing an audit.
    if ( false !== ($current_audit = get_transient( 'msa_run_audit' )) || false !== ($can_run = get_transient( 'msa_running_audit' )) ) {
        return null;
    }

    // Add this audit to the queue.
    set_transient( 'msa_run_audit', $sanitized_data );

    return true;
}

/**
 * Get the next audit to run
 *
 * @access public
 * @return bool $result Was the audit queue cleared?
 */
function msa_clear_audit_queue()
{
    $result = delete_transient( 'msa_run_audit' );
    return $result;
}

/**
 * Get the next audit to run
 *
 * @access public
 * @return array $current_audit The next audit to run.
 */
function msa_get_next_audit_to_run()
{

    // Check if we are already performing an audit.
    if ( false !== ($current_audit = get_transient( 'msa_run_audit' )) && false === ($can_run = get_transient( 'msa_running_audit' )) ) {
        return $current_audit;
    }

    return null;
}
