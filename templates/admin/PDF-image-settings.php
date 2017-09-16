<style>
    .form-table th {
        width: 265px;
    }
    #PDFImageSettings .form-control{
        width: 60%;
    }
</style>
<div class="wrap PDF-image-settings">
    <div class="icon32" id="icon-tools"> <br /> </div>
    <h2><?php _e('PDF or image generator Settings', 'PDFIG'); ?></h2>
    <p><?php echo __('Download Wkhtmltox from', 'PDFIG') . ' ' . '<a href="https://wkhtmltopdf.org/downloads.html" target="_blank">' . __('here', 'PDFIG') . '</a>' . ' ' . __('and enter the absolute path of the downloaded files in the below fields. Ignore this if it is Linux machine.', 'PDFIG'); ?></p>
    <h3><?php _e('Path to the binary/executable files', 'PDFIG'); ?></h3>
    <form action="options.php" method="post" id="PDFImageSettings">
        <?php
        settings_fields(PDFIG_SETTINGS);
        do_settings_sections(__FILE__);
        $options = $data;
        if (PHP_OS === 'Linux' && (!$options['wkhtmltox-image'] || !$options['wkhtmltox-PDF'])) {
            $options['wkhtmltox-image'] = PDFIG_WKHTML_TO_IMG_PATH;
            $options['wkhtmltox-PDF'] = PDFIG_WKHTML_TO_PDF_PATH;
        }
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="wkhtmltox-image"><?php _e('Image :', 'PDFIG'); ?></label></th>
                <td>
                    <input type="text"  size = "70" class="form-control" id="wkhtmltox-image" name="<?php echo PDFIG_SETTINGS ?>[wkhtmltox-image]" value="<?php PDFIG_check_and_echo($options, 'wkhtmltox-image'); ?>" >
                    <p class="description"><?php _e('Enter image conversion binary/executable file absolute path', 'PDFIG'); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="wkhtmltox-PDF"><?php _e('PDF :', 'PDFIG'); ?></label></th>
                <td>
                    <input type="text"  size = "70" class="form-control" id="wkhtmltox-PDF" name="<?php echo PDFIG_SETTINGS ?>[wkhtmltox-PDF]" value="<?php PDFIG_check_and_echo($options, 'wkhtmltox-PDF'); ?>" >
                    <p class="description"><?php _e('Enter PDF conversion binary/executable file absolute path', 'PDFIG'); ?></p>
                </td>
            </tr>
        </table>
        <p class="submit">
            <b><input type="submit" name="save" id="btnSave" class="button button-primary" value="<?php _e('Save Changes', 'PDFIG'); ?>"></b>
        </p>
    </form>
    <?php
    echo do_shortcode('[PDFIG_generator_admin_demo]');
    ?>
</div>

<?php

function PDFIG_check_and_echo($options, $prop, $default = '') {
    echo (!empty($options[$prop])) ? $options[$prop] : $default;
}
