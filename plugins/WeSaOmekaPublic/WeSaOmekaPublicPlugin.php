<?php

/**
 * WeSa Omeka Public plugin.
 *
 * @package Omeka\Plugins\WeSaOmekaPublic
 */
class WeSaOmekaPublicPlugin extends Omeka_Plugin_AbstractPlugin {

  protected $_hooks = array(
    // 'initialize',
    'public_header',
  );

  // public function hookInitialize() {
  // }

  // <div style="float:left; border: thin dotted black; width:20px;">Foo</div>


    public function hookPublicHeader($args) {
      $rootUrl = url();
      $dirName = url("plugins/WeSaOmekaPublic");
      $imgUrl = $dirName."/WeSa_Logo_64x64_BB_opt.png";
      echo <<<EOT
      <div style="float:left;
                  margin-right: 1em;
                  width: 64px;
                  height: auto;">
        <a href="$rootUrl"><img src="$imgUrl"></a>
      </div>

EOT;
    }

}
