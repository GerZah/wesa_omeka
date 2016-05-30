<?php

/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

?>

<?php
  echo head(array(
    'title' => __('Network | Confirm Items to Import')
  ));
?>

<div id="primary">

<?php echo flash();
if (isset($_POST['range'])) { $range = $_POST['range']; }
if (isset($_POST['collection'])) { $collection = $_POST['collection']; }
if (isset($_POST['type'])) { $type = intval($_POST['type']); }
if (isset($_POST['tags'])) { $tags = $_POST['tags']; }
?>

<table>
<?php


?>
</table>
<div>
<input
  type="submit"
  id="submit_search_advanced"
  class="submit big green button"
  name="submit_search"
  value="<?php echo __('Import Items'); ?>" />
</div>
