<?php

namespace Juicy\Core;

class GravityForms
{
    public $grid_system = 'flex';

    public $breakpoint = 'sm';

    public $btn_class = 'btn btn-default';

    public $include_styles = false;

    public function __construct()
    {
        add_filter( 'gform_enable_credit_card_field', '__return_true', 11 );

        add_action('admin_head', function(){
            echo '<style>.size_setting.field_setting { display: none !important; } .ginput_container input { width: 96% !important; }</style>';
        });

        add_action( 'gform_field_appearance_settings', array($this, 'add_bootstrap_cols'), 10, 2 );

        add_action( 'gform_editor_js', array($this, 'field_settings_js') );

        add_action('gform_enqueue_scripts', array($this, 'remove_gravityforms_style'));

        add_filter( 'gform_field_content', array($this, 'edit_markup_input'), 99, 5 );

        add_filter( 'gform_field_container', array($this, 'edit_markup_container'), 99, 6 );

        add_filter( 'gform_get_form_filter', array($this, 'edit_form_markup'), 99, 2);

        add_filter( 'gform_submit_button', array($this, 'form_create_btns_submit'), 10, 2 );
    }

    /**
     * Removes gravity forms styles
     */
    public function remove_gravityforms_style() {
        if (!$this->include_styles) {
            wp_deregister_style("gforms_formsmain_css");    
            wp_deregister_style("gforms_reset_css");
            wp_deregister_style("gforms_ready_class_css");
            wp_deregister_style("gforms_browsers_css");
        }
    }

    public function form_create_btns_submit( $button, $form )
    {
        return $this->form_create_btns( $button, $form, 'submit' );
    }

    /**
     * Turns all inputs[type="button|submit"] into buttons
     */
    public function form_create_btns( $button, $form, $dir = null )
    {
        $dom = new \DOMDocument();
        $dom->loadHTML( $button );
        $input = $dom->getElementsByTagName( 'input' )->item(0);

        $text = $input->getAttribute( 'value' );
        $input->removeAttribute( 'value' );

        $attrs = array();

        foreach( $input->attributes as $attribute ) {
            $attrs[$attribute->name] = $attribute->value;
        }

        $attrs['class'] .= ' '. $this->btn_class;

        foreach( $attrs as $key => $val ) {
            $attrs[$key] = "{$key}='{$val}'";
        }

        return "<button ". implode(' ', $attrs) .">{$text}</button>";
    }

    /**
     * Remove wrapping <ul>, add row to form body
     */
    public function edit_form_markup( $form_string, $form )
    {
        if ( is_admin() ) {
            return $form_string;
        }

        $form_string = preg_replace("/<div class=(\"|')gform_body(\"|')>(?:\n\s*)?<ul .*?>/i", "<div class=\"gform_body row\">", $form_string);
        $form_string = preg_replace("/<div class=(\"|')gform_page_fields(\"|')>(?:\n\s*)?<ul .*?>/i", "<div class=\"gform_page_fields row\">", $form_string);
        $form_string = preg_replace("/<\/ul>(?:\n\s*)?<\/div>/i", "</div>", $form_string);

        return $form_string;
    }

    /**
     * Add `form-control` class to all fields
     */
    public function edit_markup_input( $content, $field, $value, $lead_id, $form_id )
    {
        if ( is_admin() ) {
            return $content;
        }

        $function = 'handle'. ucfirst($field->type);

        if ( method_exists( $this, $function ) ) {
            return $this->{$function}($content, $field, $value, $lead_id, $form_id);
        }

        $content = preg_replace("/(<(input|textarea|select)[^>]+) class=('|\").*?('|\")/i", "$1 class=\"form-control\"", $content);

        $replace = array(
            'validation_message'    => 'help-block small bold text-uppercase',
            'rows=\'10\''            => ''
        );

        $content = str_replace(array_keys($replace), array_values($replace), $content);


        return $content;
    }

    /**
     * Add bootstrap columsn to the inputs
     */
    public function edit_markup_container( $field_container, $field, $form, $css_class, $style, $field_content )
    {
        if ( is_admin() ) {
            return $field_container;
        }
        $classes = array();

        if( !empty($field['cssClass']) ) {
            $classes[] = $field['cssClass'];
        }

        $classes[] = 'input-wrapper';
        $classes[] = $field['type'];
        $classes[] = ( $this->grid_system == 'flex' ? "col {$this->breakpoint}-" : "col-{$this->breakpoint}-" ) . ($field->columns == '' ? '12' : $field->columns);
        $classes[] = 'form-group';

        if ( $field->isRequired ) {
            $classes[] = 'required';
        }

        if ( $field->failed_validation !== '' ) {
            $classes[] = 'validated';
            $classes[] = $field->failed_validation === true ? 'has-error' : 'has-success';
        }

        $field_container = "<div id=\"field_{$form['id']}_{$field->id}\" class=\"". implode(' ', $classes) ."\">{FIELD_CONTENT}</div>";

        if ( $field->type == 'hidden' ) {
            return '{FIELD_CONTENT}';
        }

        return $field_container;
    }

    /**
     * Add bootstrap columns as an option...
     * @param [type] $form_id
     */
    public function add_bootstrap_cols( $placement, $form_id )
    {
        if ( $placement != 300 ) {
            return;
        }

        global $__gf_tooltips;

        $__gf_tooltips['form_field_columns_size'] = '<h6>' . __( 'Bootstrap Columns', 'gravityforms' ) . '</h6>' . __( 'How wide would you like this field to display.', 'gravityforms' )

        ?>
        <li class="column_setting field_setting">
            <label for="field_columns_size">
                <?php esc_html_e( 'Bootstrap Columns', 'gravityforms' ); ?>
                <?php gform_tooltip( 'form_field_columns_size' ) ?>
            </label>
            <select id="field_columns_size" onchange="SetFieldProperty('columns', jQuery(this).val());">
                <option value="3"><?php esc_html_e( '25%', 'gravityforms' ); ?></option>
                <option value="4"><?php esc_html_e( '33%', 'gravityforms' ); ?></option>
                <option value="6"><?php esc_html_e( '50%', 'gravityforms' ); ?></option>
                <option value="8"><?php esc_html_e( '66%', 'gravityforms' ); ?></option>
                <option value="9"><?php esc_html_e( '75%', 'gravityforms' ); ?></option>
                <option value="12"><?php esc_html_e( '100%', 'gravityforms' ); ?></option>
            </select>
        </li>
        <?php
    }

    public function field_settings_js()
    {
        ?>

        <script type="text/javascript">
            (function($) {
                $(document).ready(function(){
                    for( i in fieldSettings ) {
                        fieldSettings[i] += ', .column_setting';
                    }
                });

                 $(document).bind( 'gform_load_field_settings', function( event, field, form ) {
                    $('#field_columns_size').val(field.columns);
                } );
            })(jQuery);
        </script>

        <?php
    }
}
