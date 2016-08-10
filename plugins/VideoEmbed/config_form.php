<?php $view = get_view(); ?>

<div class="field">
  <div class="two columns alpha">
      <?php echo get_view()->formLabel('videoembed_remove_pseudocode', __('Remove Pseudo Code')); ?>
  </div>
  <div class="inputs five columns omega">
    <p class="explanation">
      <?php
        echo __('Remove "{{#xx}}" / "{{#xx;yy-zz}}" pseudo code tags when displaying content (see below).');
      ?>
    </p>
    <div>
      <?php
        echo $view->formCheckbox('videoembed_remove_pseudocode', true, array('checked' => SELF::$_curOptions["videoembed_remove_pseudocode"]));
      ?>
    </div>
  </div>
</div>

<div class="field">
  <div class="two columns alpha">
      <?php echo get_view()->formLabel('videoembed_show_related_items', __('Display Related items')); ?>
  </div>
  <div class="inputs five columns omega">
    <p class="explanation">
      <?php
        echo __('Display related items that also embed the same video, sorted after their respective timecodes.');
      ?>
    </p>
    <div>
      <?php
        echo $view->formCheckbox('videoembed_show_related_items', true, array('checked' => SELF::$_curOptions["videoembed_show_related_items"]));
      ?>
    </div>
  </div>
</div>

<div class="field">
  <div class="two columns alpha">
      <?php echo get_view()->formLabel('videoembed_adminwidth', __('Video Width in Admin View')); ?>
  </div>
  <div class="inputs five columns omega">
    <p class="explanation">
      <?php
        echo __('Width of the embedded video viewer in admin backend, in pixels. – Enter "0" for 100% width.');
      ?>
    </p>
    <div><?php echo $view->formText('videoembed_adminwidth', SELF::$_curOptions["videoembed_adminwidth"]); ?></div>
  </div>
</div>

<div class="field">
  <div class="two columns alpha">
      <?php echo get_view()->formLabel('videoembed_publicwidth', __('Video Width in Public View')); ?>
  </div>
  <div class="inputs five columns omega">
    <p class="explanation">
      <?php
        echo __(
          'Width of the embedded video viewer in public frontend, in pixels. – Enter "0" for 100% width.'
        );
      ?>
    </p>
    <div><?php echo $view->formText('videoembed_publicwidth', SELF::$_curOptions["videoembed_publicwidth"]); ?></div>
  </div>
</div>

<hr>

<div class="field">
  <div class="two columns alpha">
    <strong><?php echo __("Documentation"); ?></strong>
  </div>
  <div class="inputs five columns omega" style="font-size:80%;">
    <p class="explanation">
      <?php
        echo __("
          If you upload a video file to Omeka (or any other kind of file for that matter), it will be part of that particular
          item that you added it to. Sometimes, however, it might be desirable to reference a video file from different other
          items. A good example for this is when you upload a video file that contains references to multiple icons; files
          can not be attached to multiple items at once.
        ");
      ?>
    </p>
    <p class="explanation">
      <?php
        echo __("
          With this plugin, you may refence videos by adding a “pseudo code tag” anywhere in your item's content. It doesn't
          matter where you add it, it could be in some comment field or anywhere else. This way, the video will be displayed
          below your regular item's content. Even better: You may specify a specific timecode, so the video reference will
          give you the ability to play only one particular segment from that video. This frees you from the need to
          upload videos multiple times and/or to split it into multiple pieces and upload those.
        ");
      ?>
    </p>
    <p class="explanation">
      <?php
        echo __("
          To reference a video file, first find out its numerical ID. The easiest way is to check the content that it is part of
          and click on its video placeholder icon. <em>Please note:</em> The files use a separate numbering than the items:
          Item ID and file ID are not the same thing. – For example, let us say that you found the video ID to be #42.
        ");
      ?>
    </p>
    <p class="explanation"><?php echo __("To reference video #42, enter: <strong>{{#42}}</strong>"); ?></p>
    <p class="explanation"><?php echo __("To play video #42 from 0:50 to 1:10, enter: <strong>{{#42;50-70}}</strong>"); ?></p>
  </div>
</div>
