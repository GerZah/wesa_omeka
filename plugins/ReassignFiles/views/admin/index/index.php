<?php
    queue_js_file('items');
    queue_js_file('tabs');
    queue_css_file('reassignfiles');
    echo head(array('title' => __('reassignfiles'), 'bodyclass' => 'reassignfiles'));
  ?>

<?php echo flash(); ?>
<p>
<?php
echo __("View the files that are currently assigned to items");
?>
</p>
<?php echo foot();
