# PDF or image generator

Contributors: Osmosys

Tags: PDF, image

Requires at least: 4.0

Requires PHP: 5.5

Tested up to: 4.8

Stable tag: 0.0.1

License: GPLv2 or later

License URI: http://www.gnu.org/licenses/gpl-2.0.html


##### It is for converting webpage content, HTML string or file into image and PDF.


## Functionalities

- Converting webpage content, HTML string or file into image. 
- Converting webpage content, HTML string or file into PDF.

## Functions

#### PDFIG_generate
- This method is used to convert web page content, HTML string or file into image or PDF.
- The following are the parameters to this method 

Parameters                | Type           | Required     | Example
-------------             | -------------  | ----------   | -----------
$source                   | string         | Required     | 'http://osmosys.asia/', '/home/osmosys/test.php'
$type                     | string         | Optional     | 'TEMPLATE_PATH', 'HTML_STRING', 'URL'
$dest_file_name           | string         | Optional     | 'test'
$conversion_type          | string         | Optional     | 'PDF', 'IMG'

- It returns the PDF/img path.


## Installation 

1. Upload `pdf-img-generator` to the `/wp-content/plugins/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.

## Getting Started
1. If it is Linux machine you can ignore the below steps.
2. Go to plugin settings page through settings menu or settings link under plugin name in the plugins page.
3. Download Wkhtmltox from <a href="https://wkhtmltopdf.org/downloads.html" target="_blank">here</a>.
4. Run that executable file then it will create wkhtmltox folder. Enter the absolute path of the wkhtmltox->bin files.
5. `[PDFIG_generator_demo]`, simply insert the shortcode for a demo page.


## Screenshots 

PDF or image generator settings screen 
![PDF or image generator settings screen](/plugins/pdf-img-generator/img/pdf-image-generator-settings.png)
