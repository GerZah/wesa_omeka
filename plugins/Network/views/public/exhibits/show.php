<?php
queue_js_file('cytoscape.min');
queue_js_file('jquery-2.0.3.min');
queue_js_file('network');
queue_css_file('network'); //works
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

<div id="cy"></div>

<!-- Exhibit and description : -->
<?php echo in_getExhibitMarkup(); ?>



<?php echo foot(); ?>
