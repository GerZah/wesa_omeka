<?php
$pageTitle = __('Re-add deleted item to Exhibit');
echo head(array('title'=>$pageTitle));
echo flash();
if (isset($_POST['id'])) { $id = !empty($_POST['id']) ? intval($_POST['id']) : "NULL";}
if (isset($_POST['owner_id'])) { $owner_id = !empty($_POST['owner_id']) ? intval($_POST['owner_id']) : "NULL";}
if (isset($_POST['item_id'])) { $item_id = !empty($_POST['item_id']) ? intval($_POST['item_id']) : "NULL";}
if (isset($_POST['exhibit_id'])) { $exhibit_id = !empty($_POST['exhibit_id']) ? intval($_POST['exhibit_id']) : "NULL";}
if (isset($_POST['added'])) { $added = !empty($_POST['added']) ? $_POST['added'] : "NULL";}
if (isset($_POST['modified'])) { $modified = !empty($_POST['modified']) ? $_POST['modified'] : "NULL";}
if (isset($_POST['title'])) { $title = !empty($_POST['title']) ? $_POST['title'] : "NULL";}
if (isset($_POST['item_title'])) { $item_title = !empty($_POST['item_title']) ? $_POST['item_title'] : "NULL";}
if (isset($_POST['body'])) { $body = !empty($_POST['body']) ? $_POST['body'] : "NULL";}
if (isset($_POST['start_date'])) { $start_date = !empty($_POST['start_date']) ? $_POST['start_date'] : "NULL";}
if (isset($_POST['end_date'])) { $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : "NULL";}
if (isset($_POST['after_date'])) { $after_date = !empty($_POST['after_date']) ? $_POST['after_date'] : "NULL";}
if (isset($_POST['before_date'])) { $before_date = !empty($_POST['before_date']) ? $_POST['before_date'] : "NULL";}
?>
  <section class="seven columns alpha">
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
    <?php
    $db = get_db();
      $db->query("INSERT INTO `$db->NetworkRecord` (`id`, `owner_id`, `item_id`, `exhibit_id`, `added`, `modified`,`title`,`item_title`,`body`,`start_date`,`end_date`,`after_date`,`before_date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
      array($id, $owner_id, $item_id,$exhibit_id,$added,$modified,$title,$item_title,$body,$start_date,$end_date,$after_date,$before_date));
    ?>
    <div class="field">
        <h2><?php echo __("You have successfully readded the item to exhibit."); ?></h2>
    </div>
</form>
    </fieldset>
  </section>
  <section class="three columns omega">
    <div id="save" class="panel">
      <a href="<?php echo html_escape(url('network/view/'.$exhibit_id)); ?>" class="add big green button"><?php echo __('Back'); ?></a>
    </div>
  </section>
<?php echo foot(); ?>
