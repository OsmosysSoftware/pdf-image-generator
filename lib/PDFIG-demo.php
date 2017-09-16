<?php

class PDFIG_Demo {

    public function __construct() {
        // Actions
        add_action('admin_post_PDFIG_download', [$this, 'PDFIG_download']);
        add_action('admin_post_nopriv_PDFIG_download', [$this, 'PDFIG_download']);

        // Shortcodes
        add_shortcode('PDFIG_generator_admin_demo', [$this, 'PDFIG_generator_admin_demo']);
        add_shortcode('PDFIG_generator_demo', [$this, 'PDFIG_generator_demo']);
    }

    /*
     * Generates demo short code for admin settings page
     */

    public function PDFIG_generator_admin_demo() {
        return $this->PDFIG_parseTemplate(__DIR__ . '/../templates/admin/PDFIG-admin-demo-template.php');
    }

    /*
     * Generates demo short code
     */

    public function PDFIG_generator_demo() {
        return $this->PDFIG_parseTemplate(__DIR__ . '/../templates/PDFIG-demo-template.php');
    }

    /*
     * It is used to parse the template
     */

    private function PDFIG_parseTemplate($file) {
        ob_start();
        include($file);
        return ob_get_clean();
    }

    /*
     * This is admin post call back on click on submit button in demo pages
     */

    public function PDFIG_download() {
        $PDFIG_input_data = filter_input_array(INPUT_POST);
        $redirect_URL_raw = filter_input(INPUT_SERVER, 'HTTP_REFERER');
        $redirection_URL = $redirect_URL_raw ? $redirect_URL_raw : site_url();
        // Verify nonce
        if (
                !isset($PDFIG_input_data['PDFIG_download_nonce']) ||
                !wp_verify_nonce($PDFIG_input_data['PDFIG_download_nonce'], 'PDFIG_download')
        ) {
            $this->PDFIG_set_error(__('Sorry! some problem has occurred while processing your request. Please try again later.', 'PDFIG'), $redirection_URL, $PDFIG_input_data);
        }

        // User should enter either path ro HTML.
        if ((isset($PDFIG_input_data['path']) && !empty($PDFIG_input_data['path'])) ||
                (isset($PDFIG_input_data['HTMLString']) && !empty($PDFIG_input_data['HTMLString']))) {

            // If a user has entered path then generate parameters for URL to PDF conversion.
            if (isset($PDFIG_input_data['path']) && !empty($PDFIG_input_data['path'])) {
                $src = $PDFIG_input_data['path'];
                $type = filter_var($src, FILTER_VALIDATE_URL) ? 'URL' : 'TEMPLATE_PATH';
            } else {
                $type = 'HTML_STRING';
                $src = $PDFIG_input_data['HTMLString'];
            }
        } else {
            $this->PDFIG_set_error(__('There is some problem with your submission. Please enter the correct details.', 'PDFIG'), $redirection_URL, $PDFIG_input_data);
        }

        if (!(isset($PDFIG_input_data['GeneratePDF']) || isset($PDFIG_input_data['GenerateImg']))) {
            $this->PDFIG_set_error(__('There is some problem with your submission. Please enter the correct details.', 'PDFIG'), $redirection_URL, $PDFIG_input_data);
        }

        // Check which button user has clicked and generate correspondingly.
        $conversion = isset($PDFIG_input_data['GeneratePDF']) ? 'PDF' : 'IMG';

        $file_name = (isset($PDFIG_input_data['FileName']) && !empty($PDFIG_input_data['FileName'])) ? $PDFIG_input_data['FileName'] : '';

        $file_path = PDFIG_generate($src, $type, $file_name, $conversion);

        if ($file_path) {
            set_transient('PDFIG_path', $file_path);
        } else {
            $this->PDFIG_set_error(__('Sorry! some problem has occurred while processing your request. Please try again later.', 'PDFIG'), $redirection_URL, $PDFIG_input_data);
        }
        wp_redirect($redirection_URL);
        exit;
    }

    /*
     * It returns tmp folder URL.
     */

    public function PDFIG_get_tmp_folder_url() {
        $uploads_pth_arr = wp_upload_dir();
        return $uploads_pth_arr['baseurl'] . "/PDFIG";
    }

    /*
     * It sets the error and redirect to demo page.
     */

    private function PDFIG_set_error($error_message, $redirection_URL, $PDFIG_input_data = '') {
        set_transient('PDFIG_input_data', $PDFIG_input_data);
        set_transient('PDFIG_error_msg', $error_message);
        wp_redirect($redirection_URL);
        exit;
    }

}
