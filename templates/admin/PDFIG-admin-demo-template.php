<style>
    #PDFImgDmoFrm {
        margin-top: 30px;
    }
    #PDFImgDmoFrm h3 {
        margin-top: 0px;
    }
    #PDFImgDmoFrm .seperator {
        padding-left: 165px;
    }
    .PDF-img-error {
        color: #ff0000;
        margin-top: 30px;
    }
    .download-PDF-img-link {
        display: none;
    }
    #PDFImgDmoFrm .form-control{
        width: 60%;
    }
    .PDF-img-usage{
        margin-top: 40px;
    }

</style>
<h3><?php _e('Test PDF/image generator', 'PDFIG'); ?></h3>
<?php
$data = [];

// If there is an error message, show that message and unset the session value.
$error_message = get_transient('PDFIG_error_msg');
$PDFIG_input_data = get_transient('PDFIG_input_data');
if (isset($error_message) && $error_message) {
    delete_transient('PDFIG_error_msg');
    echo "<p class=PDF-img-error>$error_message</p>";
    $data = isset($PDFIG_input_data) ? $PDFIG_input_data : [];
    delete_transient('PDFIG_input_data');
}
?>

<form method="POST" id="PDFImgDmoFrm" action="<?php echo get_admin_url() ?>admin-post.php">

    <table class="optiontable form-table">
        <tbody>
            <!--URL/Path-->
            <tr valign="top">
                <th scope="row">
                    <label for="txtPath" class="control-label col-sm-2"> <?php _e('URL/Path', 'PDFIG'); ?></label>
                </th>
                <td>
                    <input type="text" size="40" id="txtPath" class="form-control" name="path" value="<?= PDFIG_set_value($data, 'path') ?>">
                    <p class="description"><?php _e('Enter URL or path of the file that you want to convert to PDF/image.', 'PDFIG'); ?></p>
                </td>
            </tr>
            <!--File name-->
            <tr valign="top">
                <th scope="row">
                    <label for="fileName" class="control-label col-sm-2"><?php _e('File name', 'PDFIG') ?></label>
                </th>
                <td>
                    <input type="text" size="40" id="fileName" name="FileName" class="form-control" value="<?= PDFIG_set_value($data, 'FileName') ?>">
                    <p class="description"><?php _e('File will get downloaded with the above entered value.', 'PDFIG'); ?></p>
                </td>
            </tr>
        </tbody>
    </table>
    <?php wp_nonce_field('PDFIG_download', 'PDFIG_download_nonce'); ?>
    <input type="submit" name="GeneratePDF" id="generatePDF" class="button-primary" value="<?php  _e('Generate PDF', 'PDFIG') ?>">
    <input type="submit" name="GenerateImg" id="generateImg" class="button-primary" value="<?php _e('Generate Image', 'PDFIG') ?>">
    <input type="hidden" name="action" value="PDFIG_download">
</form>
<a class="download-PDF-img-link"  download></a>

<!--usage-->
<h3 class="PDF-img-usage"><?php _e('Usage', 'PDFIG'); ?></h3>
<p>
    <?php _e('We have to call the below function with specified paramters and it will return the generated PDF/image path.', 'PDFIG'); ?>
</p>
<p>
    <code>
        PDFIG_generate($source, $source_type, $dest_file_name, $conversion_type);
    </code>
</p>
<ol>
    <li><?php _e('$source is required, expected string. Ex: \'http://osmosys.asia\', \'home/osmosys/test.php\' etc.'); ?></li>
    <li><?php _e('$source_type is required, expected string. It specifies what is the source type, it can be one of the 3 possible values. Possible values: \'TEMPLATE_PATH\', \'HTML_STRING\', \'URL\'.', 'PDFIG'); ?></li>
    <li><?php _e('$dest_file_name is optional, expected string. It is the file with which file will get downloaded. Ex: \'test\' etc.', 'PDFIG'); ?></li>
    <li><?php _e('$conversion_type is optional, expected string. It specifies to which format we have to convert the given source, it can be one of the 2 possible values. Possible values: \'PDF\', \'IMG\'.', 'PDFIG'); ?></li>
</ol>

<?php
// If file is generated then add the file path to anchor href and download that file.
$PDFIG_path = get_transient('PDFIG_path');
if (isset($PDFIG_path) && $PDFIG_path) {
    $tmpURL = $this->PDFIG_get_tmp_folder_url();
    $distPath = explode('PDFIG', $PDFIG_path);
    $path_from_PDF = str_replace('\\', '/', $distPath[1]);
    $dist_URL = $tmpURL . $path_from_PDF;
    delete_transient('PDFIG_path');
    echo "<script>"
    . "jQuery('.download-PDF-img-link').attr('href', '$dist_URL');"
    . "jQuery('.download-PDF-img-link')[0].click();
    </script>";
}

/*
 * It sets the input fields value when there is an error.
 */

function PDFIG_set_value($data, $key) {
    $value = isset($data[$key]) ? $data[$key] : '';
    return $value;
}
