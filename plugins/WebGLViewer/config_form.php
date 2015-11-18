<?php $view = get_view(); ?>
<div class="field">
    <div class="two columns alpha">
        <?php echo $view->formLabel('webgl_viewer_height', __('Height')); ?>
    </div>
    <div class="inputs five columns omega">
        <p class="explanation">
            <?php
            echo __(
                'Height of the embedded WebGL viewer, in pixels. Width will'.
                ' automatically adjust to fit the surrounding content.'
            );
            ?>
        </p>
        <?php echo $view->formText('webgl_viewer_height', $webGlHeight); ?>
    </div>
</div>
