<?php
/**
 * EDET Theme functions file
 *
 * @package WordPress
 * @subpackage EDET Theme
 * @since EDET Theme 1.0
 */


add_theme_support( 'post-formats', array( 'image', 'quote', 'status', 'link' ) );
add_theme_support( 'post-thumbnails' );
add_theme_support( 'automatic-feed-links' );
add_theme_support( 'menus' );

load_theme_textdomain( 'edettheme', get_template_directory().'/languages' );

/**
 * Enqueue newtheme scripts
 * @return void
 */
function edettheme_enqueue_scripts() {

	//Stylesheets
	wp_enqueue_style('edettheme_stylesheet', get_template_directory_uri() . '/assets/css/min/style.min.css', false, null);
	wp_enqueue_style('slick_stylesheet', get_template_directory_uri() . '/assets/css/min/slick.min.css', false, null);
	wp_enqueue_style('slicktheme_stylesheet', get_template_directory_uri() . '/assets/css/min/slick-theme.min.css', false, null);

    //Fonts
    wp_enqueue_style('playfair_display','https://fonts.googleapis.com/css?family=Playfair+Display', false, null);
    wp_enqueue_style('open_sans','https://fonts.googleapis.com/css?family=Open+Sans', false, null);


	//JS Scripts
	wp_deregister_script('jquery');
  	wp_deregister_script('jquery-migrate');
	wp_register_script('jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js', false, null, true);
	wp_enqueue_script('jquery');

    wp_register_script('jqueryUI', 'https://code.jquery.com/ui/1.12.0/jquery-ui.min.js', false, null, true);
    wp_enqueue_script('jqueryUI');

	wp_register_script('slick', get_template_directory_uri() . '/assets/js/min/slick.min.js', false, null, true);
	wp_enqueue_script('slick');

	wp_register_script('masonry', get_template_directory_uri() . '/assets/js/min/masonry.min.js', false, null, true);
	wp_enqueue_script('masonry');

	wp_register_script('images-loaded', get_template_directory_uri() . '/assets/js/min/imagesLoaded.min.js', false, null, true);
	wp_enqueue_script('images-loaded');


	wp_register_script('newtheme_main', get_template_directory_uri() . '/assets/js/min/main.min.js', false, null, true);
	wp_enqueue_script('newtheme_main');

	wp_register_script('googleapis', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCnTl1p4VKGvyp9EXnZHi2kYY1pcbp5shk', false, null, true);
	//wp_register_script('googleapis', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyCnTl1p4VKGvyp9EXnZHi2kYY1pcbp5shk&callback=initMap', false, null, true);
	wp_enqueue_script('googleapis');


    //Ajax Variable
    wp_enqueue_script('ajax-script', get_template_directory_uri() . '/assets/js/variables.js', false, null, true);
    wp_localize_script( 'ajax-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );



}
add_action( 'wp_enqueue_scripts', 'edettheme_enqueue_scripts' );


//Remove wpemoji support in HEAD
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

//Register Google Maps API Key
function my_acf_init() {

	acf_update_setting('google_api_key', 'AIzaSyCnTl1p4VKGvyp9EXnZHi2kYY1pcbp5shk');
}

add_action('acf/init', 'my_acf_init');


//Send mail function

add_action('wp_ajax_send_request_email', array('Request_Email', 'send_request_email') );
add_action('wp_ajax_nopriv_send_request_email', array('Request_Email', 'send_request_email') );
add_filter('wp_mail_content_type', array('Request_Email', 'mail_content_type') );

class Request_Email {

    public static function send_request_email() {

        $target_dir = 'tmp/';
        $attachments = array();
        $uploadOk = 1;
        $errors_array = array();
        $successMessage = __('Sent', 'edettheme');

        //Create a stdClass instance (o/p object) to hold important information
        $return = new stdClass();
        $return->success = true;
        $return->data = array();
        $return->error = '';
        $return->successMessage = $successMessage;

        // Check files array for files $_FILES[]
        if (count($_FILES) > 0) {
            //for each file input
            foreach ($_FILES as $file) {

                //test to see if the file input is a multiple or a single
                if (is_array($file['name'])) {
                    //to handle multiple files in each file input
                    for ($fileCount = 0; $fileCount < count($file['name']); $fileCount++) {
                        if ($file['error'][$fileCount] == 0) {
                            $target_file = $target_dir . basename($file['name'][$fileCount]);

                            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                            // Check MIME types
                            if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif') {
                                $errorText = $file['name'][$fileCount] . __(': Sorry, only JPG, JPEG, PNG & GIF files are allowed.', 'edettheme');
                                array_push($errors_array, $errorText);
                                $uploadOk = 0;
                            }
                            //Check Size
                            if ($file['size'][$fileCount] > 2000000) {
                                $errorText = $file['name'][$fileCount] . __(': Sorry, your file is too large. The size limit is 2MB.', 'edettheme');
                                array_push($errors_array, $errorText);
                                $uploadOk = 0;
                            }
                            // Move uploaded files to server & create array of file paths [$attachments]
                            if (move_uploaded_file($file['tmp_name'][$fileCount], $target_file)) {
                                array_push($attachments, $target_file);
                            } else {
                                $errorText = $file['name'][$fileCount] . __(': Could not upload your file.', 'edettheme');
                                array_push($errors_array, $errorText);
                                $uploadOk = 0;
                            }
                        } else {
                            if ($file['error'][$fileCount] != 4) {
                                $errorText = $file['name'][$fileCount] . __(': There was an error uploading your file (error code: ', 'edettheme') . $file['error'][$fileCount] . '.';
                                array_push($errors_array, $errorText);
                                $uploadOk = 0;
                            }
                        }
                    }

                } else {
                    if ($file['error'] == 0) {
                        $target_file = $target_dir . basename($file['name']);

                        $imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
                        // Check MIME types
                        if ($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif') {
                            $errorText = $file['name'] . __(': Sorry, only JPG, JPEG, PNG & GIF files are allowed.', 'edettheme');
                            array_push($errors_array, $errorText);
                            $uploadOk = 0;
                        }
                        //Check Size
                        if ($file['size'] > 2000000) {
                            $errorText = $file['name'] . __(': Sorry, your file is too large. The size limit is 2MB.', 'edettheme');
                            array_push($errors_array, $errorText);
                            $uploadOk = 0;
                        }
                        // Move uploaded files to server & create array of file paths [$attachments]
                        if (move_uploaded_file($file['tmp_name'], $target_file)) {
                            array_push($attachments, $target_file);
                        } else {
                            $errorText = $file['name'] . __(': Could not upload your file.', 'edettheme');
                            array_push($errors_array, $errorText);
                            $uploadOk = 0;
                        }
                    } else {
                        if ($file['error'] != 4) {
                            $errorText = $file['name'] . __(': There was an error uploading your file (error code: ', 'edettheme') . $file['error'] . '.';
                            array_push($errors_array, $errorText);
                            $uploadOk = 0;
                        }
                    }
                }



            }
        }

        if ($uploadOk) {
            // remove the action as is no longer needed
            unset($_POST['action']);

            // Compose email, attach files and send
            //Populate mail fields
            $to = get_option('quote_recipient_email_address');
            $headers = 'From: ' . $_POST['quote-form-name'] . ' <"' . $_POST['quote-form-email-address'] . '">';
            $subject = "EDET Quote Request from " . $_POST['quote-form-name'];

            //Start to construct the message body
            $tableCellStart = '<tr><td style="background:#d4d4d4;padding:10px;border-bottom:1px solid #fff;border-right:1px solid #fff">';
            $tableCellMid = ' :</td><td style="background:#e7e7e7;padding:10px;border-bottom:1px solid #fff">';
            $tableCellEnd = '</td></tr>';

            //Create the main message body
            $message = "EDET website quote form submission: \n\n";
            $message.= '<table cellpadding="0" cellspacing="0" style="margin:20px">';

            foreach ($_POST as $key => $value) {

                //Sanitise the field for the email
                $newKey = str_replace('quote-form-', '', $key);
                //$newKey = preg_replace('/-\d/', '', $newKey);
                $newKey = ucwords(str_replace('-', ' ', $newKey));

                $message.= $tableCellStart . $newKey . $tableCellMid . $value . $tableCellEnd;
            }

            //Complete the message body and add technical information from the header
            $message.= "</table>";
            $message.= '<p style="color:#999">Some technical information in case this message is spam:<br><i>';
            $message .= 'IP: '.$_SERVER['REMOTE_ADDR']."\r\n";
            $message .= 'Browser: '.$_SERVER['HTTP_USER_AGENT']."\r\n";
            $message.= "</i></p>";

            //Send the mail using WP Mail
            $mail = wp_mail($to, $subject, $message, $headers, $attachments);
            //$mail = true;

            if(!$mail){
                $return->success = false;
                unset($return->data['errors']);
                $return->error = __('Sorry, there was a problem sending your request. Please try again soon.', 'edettheme');
            }

        } else {

            $return->success = false;
            if (count($errors_array) == 1) {
                $return->error = __('There was an error uploading images.', 'edettheme');
            }
            if (count($errors_array) > 1) {
                $return->error = __('There were some errors uploading images', 'edettheme');
            }
        }

        //Assign the altered data to a property of $return
        $return->data['errors'] = $errors_array;
        //Encode the stdClass object containing information and return data as a json string
        $json = json_encode($return);
        //Return the object to the client
        echo $json;

        // Delete files (unlink)
        foreach ($_FILES as $file) {
            if (is_array($file['name'])) {
                for ($fileCount = 0; $fileCount <= count($file['name']); $fileCount++) {
                    unlink($file['tmp_name'][$fileCount]);
                }
            } else {
                unlink($file['tmp_name']);
            }
        }

        exit();

    }

    public static function mail_content_type() {
        return "text/html";
    }
}

//Add global options to store Quote form recipient email address
add_action('admin_init', 'add_edet_options');
function add_edet_options()
{
	register_setting('edet_options_group', 'quote_recipient_email_address');
	add_settings_section('edet_options_section', 'EDET Custom Options', 'edet_setting_section_title_callback' , 'edet_options');
    add_settings_field('quote_recipient_email_address', 'Email Address', 'edet_setting_callback', 'edet_options', 'edet_options_section');

}

function edet_setting_section_title_callback() {
	echo 'Enter the email address that the quote forms should be send to:';
}

function edet_setting_callback(){
	$setting = esc_attr( get_option('quote_recipient_email_address'));
	echo "<input type='email' name='quote_recipient_email_address' size='100' value='" . $setting . "' />";
}

function edet_setting_callback_function()
{
?>
    <div class="wrap">
        <form method="POST" action="options.php">
            <?php settings_fields( 'edet_options_group' ); ?>
            <?php do_settings_sections( 'edet_options' ); ?>
            <?php submit_button(); ?>
       	</form>
    </div>
<?php
}


add_action( 'admin_menu', 'edet_custom_admin_menu' );
function edet_custom_admin_menu() {
    add_options_page(
        'EDET Options Page',
        'EDET Options Page',
        'manage_options',
        'edet_options',
        'edet_setting_callback_function'
    );
}


?>