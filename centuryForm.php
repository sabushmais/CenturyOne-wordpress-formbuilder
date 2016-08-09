<?php
/**
 * Plugin Name: CenturyOne Form Builder
 * Description: Create forms flexibly using centuryForm. Build your own form manually then copy the generated shortcode in any of your pages or posts. frequent updates and features are to be expected
 * Version: 1.0
 * Author: centuryOne
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

//======================================================================Primary form builder zone========================================================
/**
 * Function containing the code to echo in order to view and use the admin form builder
 */

global $wpdb;
define("CENTURY_FORMS_TABLE", $wpdb->prefix . 'century_flexible_forms');

function century_client_form ()
{
    echo '
        <style>
        #century_form_builder {
        margin-top:50px;
        }
        
        </style>
    ';
    echo '
<div class="row">
<div class="col-sm-12">
    <h3>Create your own handmade form</h3>
    <br />
    <form id="century_form_builder" class="form-horizontal" action="' . $_SERVER['REQUEST_URI'] . '" method="post">
        <div id="century_mail_admin" class="form-group form-inline">
        <label for="mail_admin">What email should the form data be sent to? </label>
            <input type="email" class="form-control" id="mail_admin" name="mail_admin" placeholder="Send to mail">
        </div>
        ';
    for ($i = 0; $i < 6; $i++):
        echo '
        <div class="form-group form-inline">
        <input type="text" class="form-control" id="customField_' . $i . '" name="fieldname_' . $i . '" placeholder="Fieldname">

        <select name="fieldtype_' . $i . '" class="form-control">
          <option value="null">Field\'s Type</option>
          <option value="text">Text</option>
          <option value="email">Email</option>
          <option value="password">Password</option>
          <option value="date">Date</option>
          <option value="number">Number</option>
          <option value="textarea">Textarea</option>
        </select>
       </div>
      ';
    endfor;
    echo '
        <input type="submit" name="submit" class="btn btn-default" name="Valider" style="margin-top=5px">
    </form>
    </div>
    </div>
    ';
}

/**
 * Function to handle the post data sent from the admin form creator. concatenates every element, then saves the constructed form and its useful data to the database
 * Generates the shortcode for the newly built form and prints it to the screen
 */
function century_retrieve_post_data ()
{
    if (isset($_POST['submit'])) {
        $err_args = century_validate_builder_form_data($_POST);

        if (!count($err_args->get_error_messages())) {
            $century_built_form = century_scratch_form();
            century_save_form_todb($century_built_form['form_data'], $century_built_form['shortcode'], $century_built_form['types_names'], $century_built_form['mail_admin']);
        } else {
            if (is_wp_error($err_args)) {
                foreach ($err_args->get_error_messages() as $error) {
                    echo '
                <div>
                    <strong>ERROR: </strong>
                    ' . $error . '
                </div>
            ';
                }
            }
        }
    }
}

function century_scratch_form ()
{
    $mail_admin = sanitize_email($_POST['mail_admin']);
    $theres_date = $century_form = $century_types_names = '';

    for ($i = 0; $i < 6; $i++) {
        $century_types_names[sanitize_text_field($_POST['fieldname_' . $i])] = $_POST['fieldtype_' . $i];
        $century_form .= century_form_element($_POST['fieldtype_' . $i],
            sanitize_text_field($_POST['fieldname_' . $i]));
        ($_POST['fieldtype_' . $i] == 'date') ? $theres_date = true : $theres_date = false;
    }

    global $wpdb;
    $suffix = $wpdb->get_var('SELECT id FROM ' . CENTURY_FORMS_TABLE . ' ORDER BY id Desc') + 1;

    $century_final_form = 'echo \'
            <form name="form_' . $suffix . '" action="\' . $_SERVER[\'REQUEST_URI\'] . \'" method="post">'
            . $century_form .
            '<p><input type="submit" name="submit_' . $suffix . '" value="Submit"/></p>
            </form>\'; ';

    // echo $century_final_form;
    if ($theres_date) {
        $header = 'echo \'
                <script type="text/javascript" src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
                <link rel="stylesheet" href="https://formden.com/static/cdn/bootstrap-iso.css" />
                <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js"></script>
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css"/>
            \';';

        $century_final_form = $header . $century_final_form;
    }

    echo '<h1>your short code is:</h1> century_form_' . $suffix;
    return $century_built_form = ['form_data' => $century_final_form, 'shortcode' => 'century_form_' . $suffix, 'types_names' => $century_types_names, 'mail_admin' => $mail_admin];
}

function century_validate_builder_form_data ($posted_data)
{
    $err_args = new WP_Error();

    $field = false;
    if (! (is_email($posted_data['mail_admin']) && isset($posted_data['mail_admin'])) ) {
        $err_args->add('Invalid email', 'The email you entered is invalid');
    }

    for ($i = 0; $i < 6; $i++) {
        if ( !empty($_POST['fieldname_' . $i]) && $_POST['fieldtype_' . $i] == 'null' ) {
            $err_args->add('field', 'Please set the field names and field types correctly');
        }
        if (!empty($_POST['fieldname_' . $i])) {
            $field = true;
        }
    }

    if (! $field) {
        $err_args->add('Field', 'At least one field should be filled to define a form');
    }

   return $err_args;
}

/**
 *Function to create each element of the being built form
 * @param $type, of every field element of the customized form
 * @param $fieldname, user-given name of every field element of the customized form
 * @return string, html code of the created form form element of the future custom form
 */
function century_form_element ($type, $fieldname)
{
    if( in_array($type, ['text', 'email', 'password', 'number'])):
        return '
         <div class="form-group">
         <label for="' . $fieldname . '">' . $fieldname . '</label><br>
         <input type="' . $type . '" name="' . $fieldname . '" class="form-control">
         </div>
        ';
    elseif ($type == 'date'):
        return '
         <div class="form-group">
         <label for="' . $fieldname . '">' . $fieldname . '</label><br>
         <input type="' . $type . '" name="' . $fieldname . '" class="form-control">
         </div>
        ';
    elseif ($type == 'textarea'):
        return '
        <div class="form-group">
        <label for="' . $fieldname . '">' . $fieldname . '</label><br>
        <textarea name="' . $fieldname . '" class="form-control"></textarea>
        </div>
        ';
    endif;
}

/**
 *Function to save the data concerning the newly created form to the database
 * @param $form, string of the newly created form
 * @param $shortcode, shortcode of the newly created form
 * @param $types_names, array of $fieldname=>$fieldtype for every element of the newly created form
 */
function century_save_form_todb ($form, $shortcode, $types_names, $email)//and create shortcode
{
    global $wpdb;
    $wpdb->insert(CENTURY_FORMS_TABLE, ['form_data' => $form, 'shortcode' => $shortcode, 'century_types_names' => json_encode($types_names), 'mail_admin' => $email]);
}

// Primary admin function---------------------function to handle the form making process
/**
 * Function to handle the form making process
 * @return string
 */
function century_create_form ()
{
    ob_start();

    century_client_form();
    century_retrieve_post_data();

    return ob_get_clean();
}

/**
 * Visualizing the admin_form for custom form creation
 */
add_shortcode('century_flexible_form', 'century_create_form');

//==================================================end of Primary form builder zone===============================================================

//==================================================Handling newly custom built forms==============================================================

//------------ Automatically adding shortcodes
/**
 * Automation of add_shortcode for every created form available in the databse
 *
 */
foreach (century_retrieve_shortcodes_fromdb() as $data) {
    add_shortcode($data->shortcode, function () use ($data) {
        eval($data->form_data);
        echo '
        <script>
            $(document).ready(function(){
              var date_input=$(\'input[id="date"]\'); //our date input has the name "date"
              var container=$(\'.bootstrap-iso form\').length>0 ? $(\'.bootstrap-iso form\').parent() : "body";
              var options={
                format: \'mm/dd/yyyy\',
                container: container,
                todayHighlight: true,
                autoclose: true,
              };
              date_input.datepicker(options);
            })
        </script>
            ';
        $suffix = substr($data->shortcode, 13, strlen($data->shortcode));
        century_handle_custom_form($suffix, $data->century_types_names, $data->mail_admin);
    });
}

/**
 * Function to retrieve list of available shortcodes of forms from the database
 * @return array|null|object
 */
function century_retrieve_shortcodes_fromdb ()
{
    global $wpdb;
    return $wpdb->get_results('SELECT * FROM ' . CENTURY_FORMS_TABLE);
}

/**
 * Function to handle the operation that will be executed using the data sent from the custom form. By default the executed function is sending email to an email address given by the admin
 * @param $suffix, identifier of the concerned form
 * @param $types_names, array that contains $fieldname=>$fieldtype of every element of the specified form
 */
function century_handle_custom_form ($suffix, $types_names, $mail_admin)//function to handle the posted data from the form
{
    if (isset($_POST['submit_' . $suffix])) {
        $types = json_decode($types_names);

        //Gestion des erreurs
        $err_args = century_form_validation($types, $_POST);
        if (! count($err_args->get_error_messages())) {
            //sanitizing and escaping posted data
            $ready_to_use_data = century_sanitize_custom_form_data($types, $_POST);
            century_send_email($ready_to_use_data, $mail_admin);
        } else {
            //printing the errors
            if (is_wp_error($err_args)) {
                foreach ($err_args->get_error_messages() as $error) {
                    echo '
                <div>
                    <strong>ERROR: </strong>
                    ' . $error . '
                </div>
            ';
                }
            }
        }

    }
}

/**
 * Function to send email to the email given when creating the custom form
 * @param $century_custom_form_data
 */
function century_send_email ($century_custom_form_data, $mail_to)
{
    $subject = 'New Century Form was Submitted';
    $message = 'New Century Form was Submitted. Here\'s the data that was sent: '
        . '<br />===============<br />'
        . $century_custom_form_data
        . '<br />=======================<br />'
    ;

    if (mail ( $mail_to , $subject , $message )){
        echo 'Your mail was sent succesfully';
    } else {
        echo 'Please configure your server to send emails. An error occured, please try submitting your data again';
    }
}

/**
 * Function to verify the validity of entered fields by the user. minimum length and validity of email
 * @param $types, array containg each forms element name, and type $fieldname=>$fieldtype
 * @param $posted_data, user data sent via the form using the post method
 */
function century_form_validation ($types, $posted_data)//validation of the data sent from a custom form
{
    $err_args = new WP_Error();

    foreach ( $types as $fieldname => $fieldtype) {
        if (! (isset($posted_data[$fieldname]) )) {
            if ($fieldname != '_empty_') {
                $err_args->add($fieldname, 'Certain fields are void. ' . $posted_data[$fieldname]);
            }
        }
        if ($fieldtype == 'text') {
            if ( 4 > strlen($posted_data[$fieldname])) {
                $err_args->add('Short field', 'Text fields should be at least 4 characters');
            }

        } elseif ($fieldtype == 'password') {
            if ( 7 > strlen($posted_data[$fieldname])) {
                $err_args->add('Invalid password', 'Password fields should be at least 7 characters');
            }
        } elseif ($fieldtype == 'email') {
            if ( 5 > strlen($posted_data[$fieldname])) {
                $err_args->add('Short email', 'Email fields should be at least 5 characters');
            }

            if (! is_email($posted_data[$fieldname])) {
                $err_args->add('Invalid email', 'This email is invalid');
            }

        } elseif ($fieldtype == 'date') {
            if (! is_date($posted_data[$fieldname])) {
                $err_args->add('Invalid date', 'The date field you entered is invalid');
            }

        } elseif ($fieldtype == 'number') {
            //testing numbers?

        } elseif ($fieldtype == 'textarea') {
            //what about text areas
        }
    }
     return $err_args;
}

/**
 * Function to sanitize the data sent by the user using the post method in order to further secure the form
 * @param $types array that contains $fieldname=>$fieldtype of each element of the form as it was defined while building the form
 * @param $posted_data data that is sent fron the form using the post method
 * @return array of $fieldname=>$fieldvalue representing the data sanitazed and ready to be used
 */
function century_sanitize_custom_form_data ($types, $posted_data)
{
    $array_of_data = [];
    foreach ( $types as $fieldname => $fieldtype) {
        if ($fieldtype == 'text') {
            array_push($array_of_data, [$fieldname => sanitize_text_field($posted_data[$fieldname])]);
        } elseif ($fieldtype == 'password') {
            array_push($array_of_data, [$fieldname => esc_attr($posted_data[$fieldname])]);
        } elseif ($fieldtype == 'email') {
            array_push($array_of_data, [$fieldname => sanitize_email($posted_data[$fieldname])]);
        } elseif ($fieldtype == 'date') {
            array_push($array_of_data, [$fieldname => $posted_data[$fieldname]]);

        } elseif ($fieldtype == 'number') {
            //testing numbers?
            array_push($array_of_data, [$fieldname => esc_attr($posted_data[$fieldname])]);
        } elseif ($fieldtype == 'textarea') {
            //what about text areas
            array_push($array_of_data, [$fieldname => sanitize_text_field($posted_data[$fieldname])]);
        }
    }
    return $array_of_data;//clean ready to be used form data
}

//============================Handling the menu
add_action( 'admin_menu', 'century_add_in_admin_menu');
function century_add_in_admin_menu ()
{
    add_menu_page (
        'Century Form Builder',
        'Century Form Builder',
        'manage_options',
        plugin_dir_path(__FILE__) . '/admin_form_builder.php',
        '',
        plugin_dir_url( __FILE__ ).'form-builder.png',
        '23.50'
    );
}

//===============Create a table in the database when the plugin is activated
function century_create_flexible_forms_table ()
{
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE " . CENTURY_FORMS_TABLE . " (
              id mediumint(9) NOT NULL AUTO_INCREMENT,
              form_data text NOT NULL,
              shortcode VARCHAR(35),
              century_types_names text NOT NULL,
              mail_admin VARCHAR(30),
              UNIQUE KEY id(id)
              )  $charset_collate;
            "
    ;
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

register_activation_hook(__FILE__, 'century_create_flexible_forms_table');
