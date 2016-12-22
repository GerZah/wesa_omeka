<?php
  echo head(array(
    'title' => __('Network') . " | " . __('Remove Items from Network'),
  ));
  echo flash();
  $record_id = null;
  if (isset($_GET['record_id'])) { $record_id = intval($_GET['record_id']); }
?>
  <section class="seven columns alpha">
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <div class="field">
          <form method="post" action="<?php echo url('network/undo'); ?>">
            <?php
              $db = get_db();
              if ($record_id) {
            			#echo "<div class='panel'><h4>".__("Item Record Undo Operation")."</h4>\n";
          				$db = get_db();
          			  $select = "SELECT * FROM `$db->NetworkRecord` where id = '$record_id'";
                  $itemFields = $db->fetchAll($select);
                  $fields = array();
              if ($itemFields) {
                  foreach($itemFields as $itemField) {
                    $fields['id'] =  $itemField['id'];
                    $fields['owner_id'] =  $itemField['owner_id'];
                    $fields['item_id'] =  $itemField['item_id'];
                    $fields['item_type_id'] =  $itemField['item_type_id'];
                    $fields['exhibit_id'] =  $itemField['exhibit_id'];
                    $fields['added'] =  $itemField['added'];
                    $fields['modified'] =  $itemField['modified'];
                    $fields['title'] =  $itemField['title'];
                    $fields['item_title'] =  $itemField['item_title'];
                    $fields['body'] =  $itemField['body'];
                    $fields['start_date'] =  $itemField['start_date'];
                    $fields['end_date'] =  $itemField['end_date'];
                    $fields['after_date'] =  $itemField['after_date'];
                    $fields['before_date'] =  $itemField['before_date'];
                    $exhibitId = $itemField['exhibit_id'];
                  }
                   echo $this->formHidden('id', $fields['id']);
                   echo $this->formHidden('owner_id', $fields['owner_id']);
                   echo $this->formHidden('item_id', $fields['item_id'] );
                   echo $this->formHidden('item_type_id', $fields['item_type_id'] );
                   echo $this->formHidden('exhibit_id', $fields['exhibit_id']);
                   echo $this->formHidden('added', $fields['added']);
                   echo $this->formHidden('modified', $fields['modified']);
                   echo $this->formHidden('title', $fields['title']);
                   echo $this->formHidden('item_title', $fields['item_title']);
                   echo $this->formHidden('body', $fields['body']);
                   echo $this->formHidden('start_date', $fields['start_date']);
                   echo $this->formHidden('end_date', $fields['end_date']);
                   echo $this->formHidden('after_date', $fields['after_date']);
                   echo $this->formHidden('before_date', $fields['before_date']);
          			}
              if ($record_id) {
                $delete = "DELETE FROM `$db->NetworkRecord` where id = '$record_id'";
                 $db->query($delete);
                }
            }
        ?>
        <h2><?php echo __("You have successfully removed the item from network."); ?></h2>
        </div>
    </fieldset>
  </section>
  <section class="three columns omega">
    <div id="save" class="panel">
      <input type="submit" class="add big green button" name="submit" value="<?php echo __('Undo'); ?>">
     <a href="<?php echo html_escape(url('network/view/'.$exhibitId)); ?>" class="add big green button"><?php echo __('Back'); ?></a>
   </div>
  </section>
</form>
<?php echo foot(); ?>
