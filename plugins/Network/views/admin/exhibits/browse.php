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
    'title' => __('Network | Browse Exhibits')
  ));
  echo flash();
?>

<div id="primary">

  <?php if(in_exhibitsHaveBeenCreated()): ?>

  <a class="add small green button"
    href="<?php echo url('network/add'); ?>">
    <?php echo __('Create an Exhibit'); ?>
  </a>

  <table class="network">

    <thead>
      <tr>
        <?php echo browse_sort_links(array(
          __('Exhibit') => 'title',
          __('Created') => 'added',
          __('# Items') => null,
          __('Public')  => null
        ), array('link_tag' => 'th scope="col"')); ?>
      </tr>
    </thead>

  <!-- Top pagination. -->
  <div class="pagination"><?php echo pagination_links(); ?></div>

    <tbody>

      <?php foreach (loop('NetworkExhibit') as $e): ?>
        <tr>

          <td class="title">

            <!-- Title. -->
            <?php if (is_allowed($e, 'editor')) {
                echo in_getExhibitLink(
                  $e, 'editor', null,
                  array('class' => 'editor'), false
                );
              } else {
                echo in_getExhibitField('title');
              }
            ?>

            <ul class="action-links group">

              <!-- Public View. -->
              <li>
                <?php echo in_getExhibitLink(
                  $e, 'show', __('Public View'),
                  array('class' => 'public'), true
                ); ?>
              </li>


              <!-- Exhibit Settings. -->
              <?php if (is_allowed($e, 'edit')): ?>
                <li>
                  <?php echo in_getExhibitLink(
                    $e, 'edit', __('Exhibit Settings'),
                    array('class' => 'edit'), false
                  ); ?>
                </li>
              <?php endif; ?>

              <!-- View. -->
              <?php if (is_allowed($e, 'view')): ?>
                <li>
                  <?php echo in_getExhibitLink(
                    $e, 'view-items', __('View Items'),
                    array('class' => 'view-items'), false
                  );?>
                </li>
              <?php endif; ?>

              <!-- Import Omeka Items. -->
              <?php if (is_allowed($e, 'import')): ?>
                <li>
                  <?php echo in_getExhibitLink(
                    $e, 'import', __('Import Items'),
                    array('class' => 'import'), false
                  ); ?>
                </li>
              <?php endif; ?>

              <!-- Delete. -->
              <?php if (is_allowed($e, 'delete')): ?>
                <li>
                  <?php echo in_getExhibitLink(
                    $e, 'delete-confirm', __('Delete'),
                    array('class' => 'delete-confirm'), false
                  );?>
                </li>
              <?php endif; ?>

            </ul>
          </td>

          <!-- Created. -->
          <td>
            <?php echo format_date(in_getExhibitField('added')); ?>
          </td>

          <!-- # Items. -->
          <td>
            <?php echo in_getExhibitRecordCount(); ?>
          </td>

          <!-- Public. -->
          <td>
            <?php echo in_getExhibitField('public') ?
              __('Yes') : __('No'); ?>
          </td>

        </tr>
      <?php endforeach; ?>

    </tbody>

  </table>

  <!-- Bottom pagination. -->
  <div class="pagination"><?php echo pagination_links(); ?></div>

  <?php else: ?>

    <h2><?php echo __('You have no exhibits.'); ?></h2>
    <p><?php echo __('Get started by creating a new one!'); ?></p>

    <a class="add big green button"
      href="<?php echo url('network/add'); ?>">
      <?php echo __('Create an Exhibit'); ?>
    </a>

  <?php endif; ?>

</div>

<?php echo foot(); ?>
