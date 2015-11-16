<?php

define('WEBGL_DIR', FILES_DIR . "/" . "webgl");
define('WEBGL_WEBDIR', WEB_FILES . "/" . "webgl");
define('WEBGL_REGEX', "^WebGL_(.*).zip"); // only files named WebGL_*.zip, deliver the "*"

/**
 * WebGLViewer plugin.
 *
 * @package Omeka\Plugins\WebGLViewer
 */
class WebGLViewerPlugin extends Omeka_Plugin_AbstractPlugin {

  /**
	 * @var array This plugin's hooks.
	 */
	protected $_hooks = array(
		'initialize',
		'install',
		'uninstall',
    'after_save_file',
    'after_delete_file',
	);

  private static $_zipMimeTypes = array('application/zip');
	private static $_zipFileExtensions = array('zip');

  /**
   * Install the plugin.
   */
  public function hookInstall() {
		SELF::_installOptions();
		mkdir(WEBGL_DIR);
		$indexfile = WEBGL_DIR."/index.html";
		touch($indexfile);
		copy(FILES_DIR."/original/index.html", $indexfile);
	}

  /**
   * Uninstall the plugin.
   */
  public function hookUninstall() {
		SELF::_uninstallOptions();
		SELF::_rmdir(WEBGL_DIR);
	}

  /**
   * Add the translations and connect display module
   */
  public function hookInitialize() {
    add_translation_source(dirname(__FILE__) . '/languages');
		/* */
		add_file_display_callback(
				array(
						'mimeTypes' => self::$_zipMimeTypes,
						'fileExtensions' => self::$_zipFileExtensions
				),
				'WebGLViewerPlugin::displayWebGL',
				array() # self::_getSettings()
		);
		/* */
  }

  /**
   * Unzip ZIP files upon file upload.
   */
  public function hookAfterSaveFile($args) {
    // Only on file insert.
    if (!$args['insert']) { return; }
    $file = $args['record'];

		// Ignore non-ZIP files.
		if ( !in_array($file->mime_type, $this->_zipMimeTypes) ) { return; }

		$regEx = WEBGL_REGEX;
		$matches = null;
		if ( !preg_match("/$regEx/", $file->original_filename, $matches) ) { return; }

		if ($matches) {
			$glName = $matches[1];
			$requiredFileName = "$glName/WebGL/$glName.html";

			$zip = new ZipArchive;
			$zipFilename = FILES_DIR . "/" . $file->getStoragePath();

			if (TRUE === $res = $zip->open($zipFilename)) {
				$foundRequiredFileName = false;
				for( $i = 0; $i < $zip->numFiles; $i++ ){
					$stat = $zip->statIndex( $i );
					$curName = $stat['name'];
					if ( $curName == $requiredFileName ) {
						$foundRequiredFileName = true;
						break;
					}
				}

				if ($foundRequiredFileName) {
					$pathParts = pathinfo($file->filename);
					$zipPath = WEBGL_DIR . "/" . $pathParts["filename"];

					mkdir($zipPath);
					$zip -> extractTo($zipPath);
				}

				$zip -> close();

			} # if (TRUE === $res = $zip->open
			# else { die("res: $res"); }

		} # if ($matches)

  }

	/**
   * Delete ZIP folders upon file deletion
   */
  public function hookAfterDeleteFile($args) {
		$file = $args['record'];

		$pathParts = pathinfo($file->filename);
		$zipPath = WEBGL_DIR . "/" . $pathParts["filename"];
		SELF::_rmdir($zipPath);
  }

	/**
	 * Display WebGL - if present
	 */
	public function displayWebGL($file, $options) {

		$zipFilename = WEB_FILES . "/" . $file->getStoragePath();
		echo '<div class="item-file application-zip">'.
					'<a href="'.$zipFilename.'">'.
					__("Download file ").
					$file->original_filename.
					'</a>'.
					"</div>\n";

		$regEx = WEBGL_REGEX;
		$matches = null;
		if ( !preg_match("/$regEx/", $file->original_filename, $matches) ) { return; }

		if ($matches) {
			$glName = $matches[1];
			$requiredFileName = "$glName/WebGL/$glName.html";

			$pathParts = pathinfo($file->filename);
			$zipPath = WEBGL_DIR . "/" . $pathParts["filename"];
			$indexPath = $zipPath . "/" . $requiredFileName;

			if (file_exists($indexPath)) {
				$url = WEBGL_WEBDIR . "/" . $pathParts["filename"] . "/" . $requiredFileName;
				echo "<iframe src='".$url."' style='width:100%; height:500px;'></iframe>";
				echo '<div class="item-file">'.
							"<a href='$url' target='_blank'>".
							__("Open model in new window").
							'</a>'.
							'</div>';
			}
		}

		# echo "<pre>" . print_r($file,1) . "</pre>";
	}


	/**
	 * Recursively remove directory ("rm -r")
	 * taken from http://stackoverflow.com/a/3338133
	 */
	protected function _rmdir($dir) {
	  if (is_dir($dir)) {
	  	$objects = scandir($dir);
	    foreach ($objects as $object) {
	    	if ($object != "." && $object != "..") {
	      	if (is_dir($dir."/".$object)) SELF::_rmdir($dir."/".$object);
	        else unlink($dir."/".$object);
	    	}
	  	}
	  	rmdir($dir);
		}
	}

}
