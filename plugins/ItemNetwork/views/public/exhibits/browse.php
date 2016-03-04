<?php

/**
 * @package     omeka
 * @subpackage  ItemNetwork
 * @copyright   2014 Rector and Board of Visitors, University of Virginia
 * @license     http://www.apache.org/licenses/LICENSE-2.0.html
 */

?>

<?php echo head(array(
  'title' => __('Item Network | Browse Exhibits'),
  'content_class' => 'itemnetwork'
)); ?>

<div id="primary">

  <?php echo flash(); ?>
  <h1><?php echo __('Item Network | Browse Exhibits'); ?></h1>

  <?php if (in_exhibitsHaveBeenCreated()): ?>

    <div class="pagination"><?php echo pagination_links(); ?></div>

      <?php foreach (loop('ItemNetworkExhibit') as $e): ?>
        <h2>
          <?php echo in_getExhibitLink(
            $e, 'show', in_getExhibitField('title'),
            array('class' => 'itemnetwork'), true
          );?>
        </h2>
      <?php endforeach; ?>

    <div class="pagination"><?php echo pagination_links(); ?></div>

  <?php endif; ?>

</div>

<?php echo foot(); ?>
