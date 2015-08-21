<?php
$class = get_class($record);
$pageTitle = __('Delete %s', Inflector::titleize($class));
echo head(array('title' => $pageTitle));
?>
<div title="<?php echo $pageTitle; ?>">
    <h2><?php echo __('Are you sure?'); ?></h2>
</div>
<?php echo foot(); ?>
