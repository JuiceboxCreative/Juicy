<?php

namespace Juicy\Core;

use Timber;

class Newsletter
{
    protected $newsletter_instance = 0;

    /**
     * List of fields to exclude from validation/sending service
     * @var [type]
     */
    protected $exclude = [
        'nonce',
        'action'
    ];

    public function __construct()
    {
        $this->register();
    }

    public function register()
    {
        add_shortcode('newsletter_field', [$this, 'newsletter_field']);

        add_shortcode('newsletter_signup', [$this, 'newsletter_signup']);

        // Handle newsletter signups
        add_action( 'wp_ajax_newsletter_signup', array( $this, 'add_to_list' ) );
        add_action( 'wp_ajax_nopriv_newsletter_signup', array( $this, 'add_to_list' ) );
    }

    public function newsletter_signup($atts, $content)
    {
        // Increment instance so we can have multiple on one page.
        $this->newsletter_instance++;

        // Init twig data array
        $data = [];

        // We will use this for build form and field ID's
        $data['instance'] = $this->newsletter_instance;

        // Add data from js script
        wp_localize_script(
            'jb_newsletter',
            'newsletterData',
            [
                'nonce'             => wp_create_nonce( 'newsletter_signup' ),
                'ajaxUrl'           => admin_url( 'admin-ajax.php' )
            ]
        );

        wp_enqueue_script('jb_newsletter');

        // Parse our atts
        $atts = shortcode_atts([
        ], $atts);

        // Get all fields
        $matches = [];
        preg_match_all("/\[\/?\w.*?\]|[^\[]+/", $content, $matches );

        $data['fields'] = [];

        foreach ( $matches[0] as $match ) {
            $data['fields'][] = do_shortcode($match);
        }

        return Timber::compile('newsletter/base.twig', $data);
    }

    public function newsletter_field($atts, $content)
    {
        $atts = shortcode_atts([
            'type'          => 'text',
            'name'          => 'name',
            'placeholder'   => '',
            'validate'      => ''
        ], $atts);

        $atts['instance'] = $this->newsletter_instance;

        return Timber::compile('newsletter/field.twig', $atts);
    }

    public function add_to_list()
    {
        // Handle nonce/action check first
        if ( !wp_verify_nonce( $_POST['nonce'], 'newsletter_signup' ) || $_POST['action'] !== 'newsletter_signup' ) {
            header('Content-Type: application/json; charset=utf-8', true, 403);
            echo json_encode(array(
                'message' => 'Sorry there has been an error, please reload the page and try again.'
            ));
            exit;
        }

        $data = array_filter($_POST, function ($val, $key) {
            if ( !in_array($key, $this->exclude) ) {
                return true;
            }
        }, ARRAY_FILTER_USE_BOTH);

        if ( !$this->validate_fields( $data ) ) {
            header('Content-Type: application/json; charset=utf-8', true, 403);
            echo json_encode(array(
                'message' => 'Some of the data entered is incorrect, please reload the page and try again.'
            ));
            exit;
        }

        $service = env('NEWSLETTER_SERVICE', false);

        if ( $service ) {
            $this->{"handle_$service"}($data);
        }

        header('Content-Type: application/json; charset=utf-8', true, 403);
        echo json_encode(array(
            'message' => 'A service has not been set, please contact an administrator.'
        ));
        exit;
    }

    /**
     * Handle signups to Campaign Monitor
     * @param  array $data Data from the form
     * @return [type]       [description]
     */
    private function handle_CM($data)
    {
        $cm = new \CS_REST_Subscribers(
            env('NEWSLETTER_LIST'),
            array(
                'api_key' => env('NEWSLETTER_CLIENT')
            )
        );

        $data = [
            'Name'          => sanitize_text_field($data['name']),
            'EmailAddress'  => sanitize_email($data['email']),
            'Resubscribe'   => 1 // Force users to be resubscribed
        ];

        $response = $cm->add( $data );

        $response_code = $response->was_successful() ? 200 : 406;

        $message = $response->was_successful() ? 'Thank you for subscribing to our newsletter.' : 'Sorry, there was an error proccessing your request, please try again.';

        // normally, the script expects a json response
        header('Content-Type: application/json; charset=utf-8', true, $response_code);
        echo json_encode(array(
            'message'       => $message
        ));

        exit; // important
    }

    /**
     * Handle signups to mailchimp
     * @param  array $data Data from the form
     * @return [type]       [description]
     */
    private function handle_MC($data)
    {
        $mc = new \Mailchimp(env('NEWSLETTER_CLIENT'));

        $result = $mc->lists->subscribe(
            env('NEWSLETTER_LIST'),
            [
                'email' => $data['email']
            ],
            [
                'FNAME'  => $data['name']
            ],
            'html',
            false, // Require email confirmation from user
            true // Update existing
        );

        // normally, the script expects a json response
        header('Content-Type: application/json; charset=utf-8', true, 200);
        echo json_encode(array(
            'message'       => 'Thank you for subscribing to our newsletter.'
        ));

        exit; // important
    }

    /**
     * Extermely basic validation.
     * @return boolean
     */
    private function validate_fields( $data )
    {
        foreach ( $data as $key => $value ) {
            if ( trim($value) === '' ) {
                return false;
            }
        }

        return true;
    }
}
