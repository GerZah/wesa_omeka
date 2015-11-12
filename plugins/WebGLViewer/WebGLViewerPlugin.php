<?php

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
    'before_save_file',
    'after_delete_file',
	);

  protected $_pdfMimeTypes = array(
      'application/pdf',
      'application/x-pdf',
      'application/acrobat',
      'text/x-pdf',
      'text/pdf',
      'applications/vnd.pdf',
  );
  /**
   * Install the plugin.
   */
  public function hookInstall() { SELF::_installOptions(); }

  /**
   * Uninstall the plugin.
   */
  public function hookUninstall() { SELF::_uninstallOptions(); }

  /**
   * Add the translations.
   */
  public function hookInitialize() {
    add_translation_source(dirname(__FILE__) . '/languages');
  }

  /**
   * Unzip ZIP files upon file upload.
   */
  public function hookBeforeSaveFile($args) {
    // Only on file insert.
    if (!$args['insert']) { return; }
    $file = $args['record'];
    # +#+#+#
    # - Unzip ZIP file.
    # - Make sure that it's a displayable WebGL file
    # - Store some piece of information about it
    # ... check PDF Text plugin for further reference
  }

  public function hookAfterDeleteFile($args) {
    # +#+#+#
    # - If WebGL ZIP folder exists, remove it.
  }

  # +#+#+#
  # - Tap into object display and create Iframe and link to external window
  # ... check PDF Embed plugin for further reference

}
