<?php
queue_js_file('cytoscape.min');
queue_js_file('jquery-2.0.3.min');
queue_js_file('network');
queue_css_file('network');
/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

?>

<?php echo head(array(
  'title' => in_getExhibitField('title'),
  'bodyclass' => 'network show'
)); ?>

<!-- Exhibit title: -->
<h1><?php echo in_getExhibitField('title'); ?></h1>
<hr>
<?php
$db = get_db();
$selectItems = "SELECT item_id FROM `$db->NetworkRecord` where exhibit_id == $exhibit_id";
$selectRelations = "SELECT selected_relations FROM `$db->NetworkExhibit` where exhibit_id == $exhibit_id";
$select = " SELECT * FROM {$db->ItemRelationsRelations} where subject_item_id in $selectItems and property_id in $selectRelations
ORDER BY id";
echo "<pre>" . print_r($select) . "</pre>"; die();
$elements = $db->fetchAll($select);
foreach ($elements as $element) {
}

?>
<div id="cy"></div>

<?php echo foot(); ?>

<script
