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
   var networkDataUrl = ".json_encode(url('network/exhibits/')).";
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
$nodes = array();
$edges= array();
foreach ($elements as $element) {
$nodes['id'] =  $element['subject_item_id'];
$nodes['name'] = metadata(get_record_by_id('Item', $element['subject_item_id']), array('Dublin Core', 'Title'));
$edges['source'] =  $element['subject_item_id'];
$edges['target'] =  $element['property_id'];
}
$json = array(
   'nodes' => $nodes,
   'edges' => $edges
);
$jsonstring = json_encode($json);
#echo "<pre>" . print_r($jsonstring) . "</pre>"; die();
?>
<?php echo foot(); ?>
