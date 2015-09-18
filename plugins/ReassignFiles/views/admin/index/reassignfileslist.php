<script type="text/javascript">
            function reassignFilesSelectAllCheckboxes(checked) {
                jQuery('#reassignFiles-file-checkboxes tr:visible input').each(function() {
                    this.checked = checked;
                });
                jQuery('#reassignFiles-file-checkboxes').trigger('reassignFiles-all-toggled');
            }

            function reassignFilesFilterFiles() {
                var filter = jQuery.trim(jQuery('#reassignFiles-file-filter').val().toLowerCase());
                var someHidden = false;
                jQuery('#reassignFiles-file-checkboxes input').each(function() {
                    var v = jQuery(this);
                    if (filter != '') {
                        if (v.val().toLowerCase().indexOf(filter) != -1) {
                            v.parent().parent().show();
                        } else {
                            v.parent().parent().hide();
                            someHidden = true;
                        }
                    } else {
                        v.parent().parent().show();
                    }
                });
                jQuery('#reassignFiles-show-all').toggle(someHidden);
            }

            function reassignFilesNoEnter(e) {
                var e  = (e) ? e : ((event) ? event : null);
                var node = (e.target) ? e.target : ((e.srcElement) ? e.srcElement : null);
                if ((e.keyCode == 13) && (node.type=="text")) {return false;}
            }

            jQuery(document).ready(function () {
                jQuery('#reassignFiles-select-all').click(function () {
                    reassignFilesSelectAllCheckboxes(this.checked);
                });

                jQuery('#reassignFiles-show-all').click(function (event) {
                    event.preventDefault();
                    jQuery('#reassignFiles-file-filter').val('');
                    reassignFilesFilterFiles();
                });

                jQuery('#reassignFiles-file-filter').keyup(function () {
                    reassignFilesFilterFiles();
                }).keypress(reassignFilesNoEnter);

                jQuery('.reassignFiles-js').show();
                jQuery('#reassignFiles-show-all').hide();
            });
        </script>
      <div class="add-new"><?php echo __('Add from Existing Files'); ?></div>
                <p class="reassignFiles-js" style="display:none;">
                    <?php echo __('Filter files by name:'); ?>
                    <input type="text" id="reassignFiles-file-filter">
                    <button type="button" id="reassignFiles-show-all" class="blue"><?php echo __('Show All'); ?></button>
                </p>
                <div class="drawer-contents">
                <table>
                    <colgroup>
                        <col style="width: 2em">
                        <col>
                    </colgroup>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="reassignFiles-select-all" class="reassignFiles-js" style="display:none"></th>
                            <th><?php echo __('File Name'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="reassignFiles-file-checkboxes">
                      <?php
                       $itemId = metadata('item', 'id');
                       $fileNames = array();
                       $db = get_db();
                       $select = "SELECT original_filename from $db->File where item_id !=$itemId order by original_filename";
                       $files = $db->fetchAll($select);
                       foreach ($files as $file) {
                        $fileNames[$file['original_filename']] = $file['original_filename'];
                       }
                      ?>
                    <?php foreach ($fileNames as $fileName): ?>
                        <tr><td><input type="checkbox" name="reassignFiles-files[]" value="<?php echo html_escape($fileName); ?>"/></td><td><?php echo html_escape($fileName); ?></td></tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
        <a class="add green button"><?php echo __('Assign'); ?></a> </div>
