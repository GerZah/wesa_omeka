<?php

/**
 * @package     omeka
 * @subpackage  ItemNetwork
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

?>

<?php echo head(array(
  'title' => in_getExhibitField('title'),
  'bodyclass' => 'itemnetwork show'
)); ?>

<!-- Exhibit title: -->
<h1><?php echo in_getExhibitField('title'); ?></h1>


<?php echo foot(); ?>
