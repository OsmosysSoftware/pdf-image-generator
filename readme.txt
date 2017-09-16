=== PDF or image generator ===
Contributors: wposmosys, sampathkumar0019, prasanna03, siddhartha-osmosys
Donate link: http://osmosys.asia/
Tags: PDF converter, image converter, html to pdf, html to image, converter
Requires at least: 4.0
Requires PHP: 5.5
Tested up to: 4.8
Stable tag: 0.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

It is for converting web page content, HTML string or files with HTML or text into an image or PDF.

== Description ==

It provides the following functionalities

- Converting webpage content, HTML string or file into image. 
- Converting webpage content, HTML string or file into PDF.


== Installation ==

1. Upload the 'pdf-img-generator' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress

or

1. Go to the 'Plugins' menu in WordPress and click 'Add New'
2. Search for 'PDF or image generator' and select 'Install Now'
3. Activate the plugin when prompted

= Getting Started =

1. If it is Linux machine you can ignore the below steps.
2. Go to plugin settings page through settings menu or settings link under plugin name in the plugins page.
3. Download Wkhtmltox from <a href="https://wkhtmltopdf.org/downloads.html" target="_blank">here</a>.
4. Run that executable file then it will create wkhtmltox folder. Enter the absolute path of the wkhtmltox->bin files.
5. `[PDFIG_generator_demo]` Simply insert the above shortcode for a demo page.
6. We have to call the below function with specified paramters and it will return the generated PDF/image path.

`PDFIG_generate($src, $src_type, $dest_file_name, $conversion_type);`

1. $src is required, expected string. Ex: 'http://osmosys.asia', 'home/osmosys/test.php' etc.
2. $src_type is required, expected string. It specifies what is the source type, It can be one of the 3 values which are specified in the example. Ex: 'TEMPLATE_PATH', 'HTML_STRING', 'URL'.
3. $dest_file_name is optional, expected string. It is the file with which file will get downloaded. Ex: 'test' etc.
4. $conversion_type is optional, expected string. It specifies to which format we have to convert the given source, it can be one among the 2 values which are specified in the example Ex: 'PDF', 'IMG'.

== Screenshots ==
1. PDF or image generator settings screen. 
`/assets/pdf-image-generator-settings.png`


== Changelog ==

= 0.0.1 =
* Initial release.

== Upgrade Notice ==

= 0.1 =
* Initial release.

