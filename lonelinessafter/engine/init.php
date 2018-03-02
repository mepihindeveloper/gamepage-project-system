<?php
/**
* 
*/
class Init 
{
	public $siteinfo = null;
	public $prefix = "/";
	public $version = null;

	function __construct()
	{
		$this->siteinfo = parse_ini_file( 'siteconfig.ini', true );
		if ( $this->get_from_config("configuration", "hosttype") === "local" )
			$this->prefix = "";
		else {
			$url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$url = array_filter(explode("/", $url));
			$this->prefix .= $url[count($url) - 1]."/";
		}
		$this->version = "?v=".$this->get_from_config("template","version");

	}

	function get_init_variable ($var) {
		return $this->$var;
	}

	function construct_charset()
	{
		return "<meta charset='{$this->get_from_config("metatags", "charset")}'>";
	}

	function construct_metatags ()
	{
		$metatags_html = null;
	  	foreach ($this->get_from_config("metatags") as $key => $value) {
	  		$metatags_html .= "<meta name='{$key}' content='{$value}'>";
	  	}
	  	return $metatags_html;
	}

	function construct_bootstrap_css() {
		$bootstrapfolder = $this->get_from_config("configuration","bootstrapfolder");
		if ( !empty($bootstrapfolder) ) $bootstrapfolder .= "/";

		$css_files = glob("../{$bootstrapfolder}css/bootstrap*.min.css");
		$css_files = $this->make_keyvalue_array_forbootstrap_files($css_files);

		$bootstrap_html .= "<link rel='stylesheet' href='{$css_files['bootstrap.min.css']}{$this->version}'>";
		
		return $bootstrap_html;
	}

	function construct_bootstrap_js() {
		$bootstrapfolder = $this->get_from_config("configuration","bootstrapfolder");
		$bootstrap_html = "<script src='{$bootstrapfolder}"/"{$this->get_from_config("configuration","jquerylink")}'></script>";
		if ( !empty($bootstrapfolder) ) $bootstrapfolder .= "/";

		$js_files = glob("../{$bootstrapfolder}js/bootstrap*.min.js");
		$js_files = $this->make_keyvalue_array_forbootstrap_files($js_files);

		$bootstrap_html .= "<script src='{$js_files['bootstrap.min.js']}{$this->version}'></script>";
		
		return $bootstrap_html;
	}

	function construct_favicon()
	{
		return "<link rel='icon' href='{$this->prefix}{$this->get_from_config("configuration","favicon")}' type='image/x-icon'>
				<link rel='shortcut icon' href='{$this->prefix}{$this->get_from_config("configuration","favicon")}' type='image/x-icon'>";
	}

	function get_from_config($section, $key=null) {
		if ( is_null($key) )
			return $this->siteinfo[$section];
		else
			return $this->siteinfo[$section][$key];
	}

	function construct_tempalte($section, $ext="php") {
		$template_folder = "./template/";
		$section_content = file_get_contents($template_folder.$section.".".$ext, FILE_USE_INCLUDE_PATH);
		$template_folder = $this->prefix."template/";
		$section_content = str_replace("{template_path}", $template_folder, $section_content);

		$template_folder = "./template/";

		$screenshots_html = $this->get_screenshots_html($template_folder);
		$section_content = str_replace("{screenshots_slides}", $screenshots_html, $section_content);

		return $section_content;
	}

	function get_screenshots_html($template_folder) {
		$slide_template = file_get_contents($template_folder."slide.php", FILE_USE_INCLUDE_PATH);
		$template_folder .= "images/screenshots/";
		$images = glob($template_folder."*.{jpg,png,gif}", GLOB_BRACE);
		
		$screenshots_html = null;

		foreach ($images as $key => $image_path) {
			$screenshots_html .= str_replace("{slide_image_src}", $image_path, $slide_template);
		}

		return $screenshots_html;
	}

	function generate_css_js_html($css_array=null, $js_array=null) {
		$html = null;
		
		if ( isset($css_array) )
		{
			foreach ($css_array as $key => $value) {
				$html .= "<link rel='stylesheet' href='{$value}{$this->version}'>";
			}
		}

		if ( isset($js_array) )
		{
			foreach ($js_array as $key => $value) {
				$html .= "<script src='{$value}{$this->version}'></script>";
			}
		}

		return $html;
	}

	function get_licensetext() {
		$license_file = file_get_contents("./template/license.txt", FILE_USE_INCLUDE_PATH);

		return nl2br(htmlspecialchars($license_file));
	}

	function make_keyvalue_array_forbootstrap_files($array) {
		$keys = array();
		$values = array();

		foreach ($array as $key => $value) {
			$key = explode("/", $value);
			$value = substr($value, 2);
			array_push($keys, end($key));
			array_push($values, $value);
		}

		$array = array();

		for ($i = 0; $i < count($keys); $i++)
        {
            $array = array_merge([$keys[$i] => $values[$i]], $array);
        }

        return $array;
	}
}

$init_class = new Init();