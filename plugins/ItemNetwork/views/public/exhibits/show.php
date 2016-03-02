<?php

/**
 * @package     omeka
 * @subpackage  ItemNetwork
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

?>

<?php echo head(array(
  'title' => nl_getExhibitField('title'),
  'bodyclass' => 'itemnetwork show'
)); ?>

<!-- Exhibit title: -->
<h1><?php echo nl_getExhibitField('title'); ?></h1>


<?php echo foot(); ?>
