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
 queue_js_string("
   var networkDataUrl = ".json_encode(url('')).";
 ");
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
$db = get_db();
$selectItems = "SELECT item_id FROM `$db->NetworkRecord` WHERE exhibit_id = $exhibit_id";
$selectRelations = "SELECT selected_relations FROM `$db->NetworkExhibit` WHERE id = $exhibit_id";
$select = "SELECT subject_item_id, property_id FROM `$db->ItemRelationsRelations` WHERE subject_item_id IN ($selectItems) AND property_id IN ($selectRelations) order by subject_item_id";
$elements = $db->fetchAll($select);
$networkData = array();
$item = array();
$itemTitle = array();
foreach ($elements as $element) {
$networkData['subject_item_id'] =  metadata(get_record_by_id('Item', $element['subject_item_id']), array('Dublin Core', 'Title'));
$networkData['property_id'] =  $element['property_id'];
}
echo "<pre>" . print_r($networkData) . "</pre>"; die();
?>
<?php echo foot(); ?>
