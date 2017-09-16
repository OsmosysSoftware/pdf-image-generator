<style>
    #PDFImgDmoFrm {
        margin-top: 30px;
    }
    #PDFImgDmoFrm h3 {
        margin-top: 0px;
    }
    #PDFImgDmoFrm .seperator {
        text-align: center;
    }
    .PDF-img-error {
        color: #ff0000;
        margin-top: 30px;
    }
    .download-PDF-img-link {
        display: none;
    }
</style>
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

<form method="POST" class="form-horizontal" id="PDFImgDmoFrm" action="<?php echo get_admin_url() ?>admin-post.php">

    <div class="form-group">
        <div>
            <label for="txtPath" class="control-label col-sm-2"><?php _e('URL/Path', 'PDFIG'); ?></label>
            <div class="col-sm-10">    
                <input type="text" id="txtPath" class="form-control" name="path" value="<?= PDFIG_set_value($data, 'path') ?>">
            </div>
        </div>
    </div>

    <h3 class="group seperator"><strong><?php _e('Or', 'PDFIG') ?></strong></h3>

    <div class="form-group">
        <label for="txtHTMLString" class="control-label col-sm-2"><?php _e('HTML/Text', 'PDFIG') ?></label>
        <div class="col-sm-10">    
            <textarea id="txtHTMLString" name="HTMLString"><?= PDFIG_set_value($data, 'HTMLString') ?></textarea>
        </div>
    </div>

    <div class="form-group">
        <label for="fileName" class="control-label col-sm-2"><?php _e('File name', 'PDFIG') ?></label>
        <div class="col-sm-10"> 
            <input type="text" id="fileName" name="FileName" class="form-control" value="<?= PDFIG_set_value($data, 'FileName') ?>">
            <p><?php _e('File will get downloaded with the above entered value.', 'PDFIG'); ?></p>
        </div>
    </div>
    <?php wp_nonce_field('PDFIG_download', 'PDFIG_download_nonce'); ?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <div class="pull-right">
                <input type="hidden" name="action" value="PDFIG_download">
                <input type="submit" name="GeneratePDF" id="generatePDF" class="btn" value="<?php  _e('Generate PDF', 'PDFIG') ?>">
                <input type="submit" name="GenerateImg" id="generateImg" class="btn" value="<?php _e('Generate Image', 'PDFIG') ?>">
            </div>
        </div>
    </div>
</form>


<a class="download-PDF-img-link"  download></a>
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
