<?php

/**
 * @package     omeka
 * @subpackage  network
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

?>

<?php echo head(array(
  'title' => __('Network') . " | " . __('Browse Networks'),
  'content_class' => 'network'
)); ?>

<div id="primary">

  <?php echo flash(); ?>
  <h1><?php echo __('Network') . " | " . __('Browse Networks'); ?></h1>

  <?php if (in_exhibitsHaveBeenCreated()): ?>


    <div class="pagination"><?php echo pagination_links(); ?></div>

      <?php foreach (loop('NetworkExhibit') as $e): ?>
        <h2>
          <?php echo in_getExhibitLink($e, 'show', in_getExhibitField('title'), array('class' => 'network'), true);?>

        </h2>
      <?php endforeach; ?>

    <div class="pagination"><?php echo pagination_links(); ?></div>

  <?php endif; ?>

</div>

<?php echo foot(); ?>
