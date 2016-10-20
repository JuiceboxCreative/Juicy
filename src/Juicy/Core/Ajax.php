<?php
/**
 * Handles all custom ajax calls for the site.
 * Keeps Site class cleaner this way
 */

namespace Juicy\Core;

class Ajax
{
    public function __construct()
    {

    }

    private function sendResponse( $response, $error = false )
    {
        return $error ? wp_send_json_error( $response ) : wp_send_json_success( $response );
    }

    private function validateCall( $action )
    {
        if ( !isset( $_POST['action'] ) || $_POST['action'] !== $action ) {
            return false;
        }

        if ( !wp_verify_nonce( $_POST['nonce'], $action ) ) {
            $response = array(
                'message'   => 'Sorry there has been an authorising your request.'
            );

            $this->sendResponse( $response, true );
        }

        return true;
    }

    /**
     * Function to add action for ajax call.
     * Calls function named "jb_ajax_{action name}"
     * @param string  $action Action name.
     * @param boolean $nopriv Can a user that is not logged in run this action.
     */
    private function addCall( $action, $nopriv = true )
    {
        add_action( "wp_ajax_$action", array( $this, "jb_ajax_$action" ) );

        if ( $nopriv ) {
            add_action( "wp_ajax_nopriv_$action", array( $this, "jb_ajax_$action" ) );
        }
    }
}
