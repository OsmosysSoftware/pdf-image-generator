<?php

define('PDFIG_SETTINGS', 'PDFIG_options');
define('PDFIG_TYPE_TEMPLATE', 'TEMPLATE_PATH');
define('PDFIG_PDF_TYPE', 'PDF');

class PDF_Image_Generator {

    const PDFIG_PDF_DIMENSIONS = '1024x920';
    const PDFIG_TYPE_HTML_STRING = 'HTML_STRING';
    const PDFIG_TYPE_URL = 'URL';
    const PDFIG_IMG_TYPE = 'IMG';

    private static $folders = [
        'PDF' => 'pdf',
        'IMG' => 'img'
    ];
    private static $extensions = ['PDF' => 'pdf',
        'IMG' => 'jpg'
    ];

    public function __construct() {
        // Variables
        $PDFIG_plugin_name = plugin_basename(PDFIG_PLUGIN_PATH);
        $_SESSION[PDFIG_SETTINGS] = get_option(PDFIG_SETTINGS);

        // Actions
        add_action('plugins_loaded', [$this, 'PDFIG_load_text_domain']);
        add_action('admin_menu', [$this, 'PDFIG_add_settings_page']);
        add_action('admin_init', [$this, 'PDFIG_register_settings']);

        // Filters
        add_filter("plugin_action_links_$PDFIG_plugin_name", [$this, 'PDFIG_settings_link']);
    }

    /**
     * Registering the settings pages for this plugin
     */
    public function PDFIG_register_settings() {
        register_setting(PDFIG_SETTINGS, PDFIG_SETTINGS, [$this, 'PDFIG_validate_settings']);
    }

    /**
     * This method is used to validate the settings options
     * @param type $PDFIG_options
     * @return type
     */
    public function PDFIG_validate_settings($PDFIG_options) {
        $wkhtmltox_slugs_labels = [
            'wkhtmltox-image' => 'wkhtmltox image',
            'wkhtmltox-PDF' => 'wkhtmltox PDF'
        ];
        foreach ($wkhtmltox_slugs_labels as $slug => $label) {
            $file_path = isset($PDFIG_options[$slug]) ? trim($PDFIG_options[$slug]) : '';
            if ((!$file_path ||
                    empty($PDFIG_options[$slug])) && PHP_OS !== 'Linux') {
                add_settings_error($slug, $slug, "$label is required.");
            } elseif (!empty($PDFIG_options[$slug]) && !file_exists($file_path)) {
                add_settings_error($slug, $slug, "$label path should be valid");
            }
        }
        return $PDFIG_options;
    }

    /**
     * To add PDF image settings page
     */
    public function PDFIG_add_settings_page() {
        add_options_page('PDF or image generator settings', 'PDF or image generator settings', 'manage_options', 'PDF-image-settings', [$this, 'PDFIG_settings_page']);
    }

    /**
     * Getting the content for PDFIG_settings_page template
     */
    public function PDFIG_settings_page() {
        $options = get_option(PDFIG_SETTINGS);
        echo $this->PDFIG_parse_template(__DIR__ . '/../templates/admin/PDF-image-settings.php', $options);
    }

    /**
     * Add settings link on plugin page
     */
    public function PDFIG_settings_link($links) {
        $settingsLink = '<a href="options-general.php?page=PDF-image-settings">Settings</a>';
        array_unshift($links, $settingsLink);
        return $links;
    }

    /*
     * This function returns tmp folder path.
     */

    private function PDFIG_get_tmp_folder_path() {
        $uploads_pth_arr = wp_upload_dir();
        return $uploads_pth_arr['basedir'] . "/PDFIG";
    }

    /*
     * It is used to convert source in to pdf or image based on the parameters.
     * @param $src, it can be a URL, HTML string or template path.
     * @param $type, it specifies what is the source type.
     * @param $dest_file_name is the name with which PDF/img needs to be created.
     */

    public function PDFIG_generate($src, $type, $dest_file_name, $conversion_type) {
        try {
            $HTML = $src;
            $WKHTML_files = $this->PDFIG_check_WKHTML_files();
            // If source type is template then check whether template exists or not
            // If it exists then parse that template else return false.
            if ($type === PDFIG_TYPE_TEMPLATE) {
                if (file_exists($src)) {
                    $HTML = $this->PDFIG_parse_template($src);
                } else {
                    return false;
                }
            } elseif ($type === self::PDFIG_TYPE_URL) { // if source type is URL then get HTML from that URL.
                $file_headers = @get_headers($src);
                if (!$file_headers || strpos($file_headers[0], "404")) {
                    return false;
                }
                $HTML = file_get_contents($src);
            }
            $dest_path = $this->PDFIG_get_file_path($conversion_type, $dest_file_name);
            if (!$dest_path) {
                return false;
            }
            if (file_put_contents("$dest_path.html", $HTML) === FALSE) {
                return false;
            }
            $dest_full_path = $dest_path . '.' . self::$extensions[$conversion_type];
            $WKHTML_command = '"' . $WKHTML_files['PDF'] . '"' . ' ' . '--viewport-size' . ' ' . self::PDFIG_PDF_DIMENSIONS;
            if ($conversion_type === self::PDFIG_IMG_TYPE) {
                $WKHTML_command = '"' . $WKHTML_files['image'] . '"';
            }
            $html_path = $dest_path . '.html';
            exec($WKHTML_command . ' ' . $html_path . ' ' . $dest_full_path);
             unlink("$dest_path.html");
            if (file_exists($dest_full_path)) {
                return $dest_full_path;
            }
        } catch (Exception $ex) {
            $response = ['success' => false, 'message' => $ex->getMessage()];
            $this->PDFIG_log_response($ex, 'PDFIG_generate', [$src, $type, $dest_file_name, $conversion_type], $response);
        }
        return false;
    }

    /*
     * It checks whether WKHTML files exists in given path
     */

    private function PDFIG_check_WKHTML_files() {
        $WKHTML_files = [];
        $WKHTML_files['image'] = $this->PDFIG_get_settings('wkhtmltox-image');
        $WKHTML_files['PDF'] = $this->PDFIG_get_settings('wkhtmltox-PDF');

        // If OS is linux and user didn't submit files paths then consider default WKHTML files.
        if (PHP_OS === 'Linux' && (!file_exists($WKHTML_files['image']) || !file_exists($WKHTML_files['PDF']))) {
            $WKHTML_files['image'] = PDFIG_WKHTML_TO_IMG_PATH;
            $WKHTML_files['PDF'] = PDFIG_WKHTML_TO_PDF_PATH;
        } else {
            if (!file_exists($WKHTML_files['image']) || !file_exists($WKHTML_files['PDF'])) {
                throw new Exception('WKHTML files don\'t exist.');
            }
        }
        return $WKHTML_files;
    }

    /**
     * It gets the PDFIG settings from session variable else it will get it from get_option
     * @param type $option
     * @return string
     */
    private function PDFIG_get_settings($option) {
        $PDF_img_settings = isset($_SESSION[PDFIG_SETTINGS]) ? $_SESSION[PDFIG_SETTINGS] : get_option(PDFIG_SETTINGS);
        $PDFIG_option_val = isset($PDF_img_settings[$option]) ? $PDF_img_settings[$option] : false;
        return $PDFIG_option_val;
    }

    /*
     * It logs the erros
     */

    private function PDFIG_log_response($message, $method_name = '', $parameters_sent = '', $response = '') {
        $upload_dir = wp_upload_dir();
        $base_dir = $upload_dir['basedir'];
        $PDFIG_logs_path = $base_dir . '/PDFIG/logs';
        $txt_to_show = "Method failed : " . $method_name . "\n";
        $txt_to_show .= "Time stamp : " . date("Y-m-d h:i:s") . "\n";
        $txt_to_show .= "Message : " . $message . "\n";
        $txt_to_show .= "Parameters sent : " . json_encode($parameters_sent) . "\n";
        $txt_to_show .= "-------------------------------------------------------------------------------------------------------------------------------------------- \n";
        $txt_to_show .= "Response : \n" . json_encode($response) . "\n";
        $txt_to_show .= "-------------------------------------------------------------------------------------------------------------------------------------------- \n";
        $txt_to_show .= "-------------------------------------------------------------------------------------------------------------------------------------------- \n";
        $txt_to_show .= "\n \n";
        // If log folder doesn't exist, create and then give permissions.
        if (!file_exists($PDFIG_logs_path)) {
            mkdir($PDFIG_logs_path, 0777, true);
            chmod($PDFIG_logs_path, 0777);
        }
        file_put_contents($PDFIG_logs_path . '/' . date("Y-m-d") . '.txt', $txt_to_show, FILE_APPEND);
    }

    /*
     * This function clears the tmp folder.
     */

    public function PDFIG_empty_tmp_folder() {
        $tmp_folder_path = $this->PDFIG_get_tmp_folder_path();
        $tmp_PDF_folder_path = $tmp_folder_path . DIRECTORY_SEPARATOR . 'pdf';
        $tmp_img_folder_path = $tmp_folder_path . DIRECTORY_SEPARATOR . 'img';
        array_map('unlink', glob("$tmp_PDF_folder_path/*"));
        array_map('unlink', glob("$tmp_img_folder_path/*"));
    }

    /*
     * It is used to parse the template 
     */

    private function PDFIG_parse_template($file, $data = null) {
        ob_start();
        include($file);
        return ob_get_clean();
    }

    /*
     * If file name is there then it generates path with that name or it will
     * generates with unique id.
     */

    private function PDFIG_get_file_path($conversion_type, $file_name) {
        try {
            if ($file_name) {
                $nrmlsd_file_name = str_replace(' ', '-', $file_name);
            } else {
                $nrmlsd_file_name = date('YmdHis') . '-' . uniqid('pdf');
            }

            $tmp_folder_path = $this->PDFIG_get_tmp_folder_path();
            $dest_folder_path = $tmp_folder_path . DIRECTORY_SEPARATOR . self::$folders[$conversion_type];

            // Check whethere pdf/img folder exists or not
            // If not then create a folder with all permissions.
            if (!file_exists($dest_folder_path)) {
                mkdir($dest_folder_path, 0777, true);
                chmod($dest_folder_path, 0777);
            }
            return $dest_folder_path . DIRECTORY_SEPARATOR . $nrmlsd_file_name;
        } catch (Exception $ex) {
            $response = ['success' => false, 'message' => $ex->getMessage()];
            $this->PDFIG_log_response($ex, 'PDFIG_get_file_path', [$conversion_type, $file_name], $response);
        }
        return false;
    }

    /**
     * This is to load the text domain
     */
    public function PDFIG_load_text_domain() {
        load_plugin_textdomain('PDFIG', false, 'pdf-img-generator/i18n/languages');
    }

}
