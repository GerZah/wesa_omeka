<?php

/**
 * WebGLViewer plugin.
 *
 * @package Omeka\Plugins\WebGLViewer
 */
class VideoEmbedPlugin extends Omeka_Plugin_AbstractPlugin {

  protected $_options = array(
  	// 'webgl_viewer_height' => 500,
  );

  # Pseudo code to embed video: {{#xx}} or {{#xx;yy-zz}}
  protected static $_videoEmbedRegEx = "{{#(\d+)(?:;(\d+)-(\d+))?}}";
  private static $_foundEmbeds;

  protected $_hooks = array(
    'initialize',
    'install',
    'uninstall',
    // 'config_form',
    // 'config',
    'admin_items_show',
    'public_items_show',
  );

  /**
   * Install the plugin.
   */
  public function hookInstall() {
		SELF::_installOptions();
  }

  /**
   * Uninstall the plugin.
   */
  public function hookUninstall() {
		SELF::_uninstallOptions();
	}

  /**
   * Add the translations and connect display module
   */
  public function hookInitialize() {
    add_translation_source(dirname(__FILE__) . '/languages');
    $db = get_db();

    $sql = "
      SELECT es.name AS el_set, el.name AS el_name
      FROM `$db->Elements` el
      LEFT JOIN `$db->ElementSets` es ON el.element_set_id = es.id
    ";

    $elements = $db->fetchAll($sql);
    foreach($elements as $element) {
        add_filter(
            array("Display", 'Item', $element["el_set"], $element["el_name"]),
            array($this, "filterDisplay")
        );
    }

    SELF::$_foundEmbeds = array(); # Start with an empty array -- will be filled if we find embeddings

  }

  /**
  * Non-interfering content filter, checking for and collecting video embed pseudocode via RegEx
  */
  public function filterDisplay($text, $args) {
    $result = $text;

    $regEx = SELF::$_videoEmbedRegEx;
    $matchCount = preg_match_all("/$regEx/", $text, $matches);

    for($i=0; ($i<$matchCount); $i++) {
      $data = array();
      foreach($matches as $match) { $data[] = $match[$i]; }
      SELF::$_foundEmbeds[$data[0]] = $data;
    }

    if (true) { # +#+#+# Configuration switch: remove {{#xx;yy-zz}} pseudocode embeddings
      $result = preg_replace("/$regEx/", "", $result);
    }

    // $result .= "\n<pre>$matchCount: " . print_r($matches,true) . "</pre>\n";
    // $result .= "\n<pre>" . print_r(SELF::$_foundEmbeds,true) . "</pre>\n";

    return $result;
  }

  /**
  * Additional item display: display referenced video players
  */
  public function hookAdminItemsShow($args) {
    $fileIds = array();
    foreach(SELF::$_foundEmbeds as $embed) {
      $fileIds[$embed[1]] = $embed[1];
    }

    if ($fileIds) {
      $fileIdsInfix = implode(",", $fileIds);

      $db = get_db();

      $sql = "SELECT * FROM `$db->Files` WHERE id IN ($fileIdsInfix)";
      $embeddedFiles = $db->fetchAll($sql);

      $fileArray = array();

      foreach($embeddedFiles as $embeddedFile) {
        $metadata = json_decode($embeddedFile["metadata"]);
        if (substr($metadata->mime_type,0,6) == "video/") {
          $id = $embeddedFile["id"];
          $fileArray[$id] = $embeddedFile;
        }
      }

      foreach(SELF::$_foundEmbeds as $embed) {

        if ($fileArray[$embed[1]]) {
          // echo "<pre>" . print_r($embed,true) . "</pre>";
          // echo "<pre>" . print_r($fileArray[$embed[1]], true) . "</pre>";

          $url = public_url("files/original/".$fileArray[$embed[1]]["filename"]);
          $title = $fileArray[$embed[1]]["original_filename"];
          $escapedUrl = html_escape($url);
          $attrs = array(
              'src' => $url,
              'class' => 'omeka-media',
              'width' => "320", // $options['width'],
              'height' => "200", // $options['height'],
              'controls' => true, // (bool) $options['controller'],
              // 'autoplay' => (bool) $options['autoplay'],
              // 'loop'     => (bool) $options['loop'],
          );

          $html = '<video ' . tag_attributes($attrs) . '>' .
                  '<a href="' . $escapedUrl . '">$title</a>' .
                  '</video>'
          ;
          echo "<h4>$title</h4>\n";
          echo "$html\n";

        }
      }
    }
  }

  /**
  * Same as hookAdminItemsShow, but in public context
  */
  public function hookPublicItemsShow($args) { SELF::hookAdminItemsShow($args); }

}

?>
