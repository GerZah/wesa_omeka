<?php
    queue_js_file('items');
    queue_js_file('tabs');
    queue_css_file('reassignfiles');
    echo head(array('title' => __('reassignfiles'), 'bodyclass' => 'reassignfiles'));
    $tagDelimiter = get_option('tag_delimiter');
  ?>

<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function () {
    Omeka.Items.tagDelimiter = <?php echo js_escape($tagDelimiter); ?>;
    Omeka.Items.tagChoices('#reassignfiles-tags', <?php echo js_escape(url(array('controller' => 'tags', 'action' => 'autocomplete'), 'default', array(), true)); ?>);
});
//]]>
</script>
<?php echo flash(); ?>
<p>
<?php
echo __("To make files available in the reassignfiles, upload them to the reassignfiles plugin's "
    . "files/ folder on the server.  reassignfiles files can be added in bulk to your site "
    . "from this page, or added to individual items through an item's admin interface.");
?>
</p>
<form action="<?php echo html_escape(url(array('action'=>'add'))); ?>" method="post" accept-charset="utf-8">
    <section class="seven columns alpha">
        <h2><?php echo __('Batch Add Items'); ?></h2>
        <p>
        <?php
        echo __('For each file selected, a new item will be created. '
            . 'The properties set to the right will be applied to each new item.');
        ?>
        </p>
        <?php reassignfiles_list(); ?>
    </section>
    <section class="three columns omega">
        <div id="save" class="panel">
            <input type="submit" class="submit big green button" name="submit" id="reassignfiles-upload-files" value="<?php echo __('Upload Files as Items'); ?>" />
            <div id="public-featured">
                <div class="public">
                    <label for="reassignfiles-public"><?php echo __('Public'); ?></label>
                    <?php echo $this->formCheckbox('reassignfiles-public', null, array('checked' => true)); ?>
                </div>
                <div class="featured">
                    <label for="reassignfiles-featured"><?php echo __('Featured'); ?></label>
                    <?php echo $this->formCheckbox('reassignfiles-featured'); ?>
                </div>
            </div>
            <div id="collection-form" class="field">
                <label for="reassignfiles-collection-id"><?php echo __('Collection'); ?></label>
                <div class="inputs">
                    <?php
                    echo $this->formSelect(
                        'reassignfiles-collection-id',
                        null,
                        array(),
                        get_table_options('Collection')
                    );
                    ?>
                </div>
            </div>
            <div id="tags-form" class="field">
                <label for="reassignfiles-tags"><?php echo __('Tags'); ?></label>
                <div class="inputs">
                    <?php echo $this->formText('reassignfiles-tags'); ?>
                    <p class="explanation"><?php echo __('Separate tags with %s', option('tag_delimiter')); ?></p>
                </div>
            </div>
        </div>
    </section>
</form>
<script type="text/javascript">
jQuery('document').ready(function () {
    function toggleUploadButton() {
        jQuery('#reassignfiles-upload-files').prop('disabled',
            !jQuery('input[name="reassignfiles-files[]"]:checked').length);
    }

    toggleUploadButton();
    jQuery('input[name="reassignfiles-files[]"]').change(toggleUploadButton);
    jQuery('#reassignfiles-file-checkboxes').on('reassignfiles-all-toggled', toggleUploadButton);
});
</script>
<?php echo foot();
