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

<div id="cy"></div>
<?php
echo "<pre>" . print_r($_POST) . "</pre>"; die();
$db = get_db();
$selectItems = "SELECT item_id FROM `$db->NetworkRecord` WHERE exhibit_id == $exhibit_id";
$selectRelations = "SELECT selected_relations FROM `$db->NetworkExhibit` WHERE exhibit_id == $exhibit_id";
$select = "SELECT * FROM `$db->ItemRelationsRelations` WHERE subject_item_id IN ($selectItems) AND property_id IN($selectRelations) ORDER BY subject_item_id";
$elements = $db->fetchAll($selectItems);

foreach ($elements as $element) {
}

?>
<?php echo foot(); ?>

<script
