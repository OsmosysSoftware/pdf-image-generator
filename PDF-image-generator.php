<?php

/**
 * Plugin Name: PDF or image generator
 * Description: It helps you in converting page content, html string or file with html to image or PDF.
 * Version: 1.0
 * Author: Osmosys
 * Text Domain: PDFIG
 * Domain Path: /i18n/languages/
 * Author URI: http://osmosys.asia/
 */
require_once __DIR__ . '/lib/PDF-image-generator-class.php';
require_once __DIR__ . '/lib/PDFIG-demo.php';

define('PDFIG_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PDFIG_PLUGIN_PATH', __FILE__);
define('PDFIG_WKHTML_TO_PDF_PATH', __DIR__ . '/includes/wkhtmltox/bin/wkhtmltopdf');
define('PDFIG_WKHTML_TO_IMG_PATH', __DIR__ . '/includes/wkhtmltox/bin/wkhtmltoimage');

/*
 * It calls class PDIG generate function.
 */

function PDFIG_generate($src, $type = PDFIG_TYPE_TEMPLATE, $dest_file_name = '', $conversion_type = PDFIG_PDF_TYPE) {
    global $PDFIG_obj;
    $file_path = $PDFIG_obj->PDFIG_generate($src, $type, $dest_file_name, $conversion_type);
    return $file_path;
}

function PDFIG_init() {
    global $PDFIG_obj;
    $PDFIG_obj = new PDF_Image_Generator();
    new PDFIG_Demo();
}

PDFIG_init();
