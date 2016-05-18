<?php
$pageTitle = __('Re-add deleted item to Exhibit');
echo head(array('title'=>$pageTitle));
echo flash();
if (isset($_POST['id'])) { $id = intval($_POST['id']); }
if (isset($_POST['owner_id'])) { $owner_id = intval($_POST['owner_id']); }
if (isset($_POST['item_id'])) { $item_id = intval($_POST['item_id']); }
if (isset($_POST['exhibit_id'])) { $exhibit_id = intval($_POST['exhibit_id']); }
if (isset($_POST['added'])) { $added = $_POST['added']; }
if (isset($_POST['modified'])) { $modified = $_POST['modified']; }
if (isset($_POST['slug'])) { $slug = $_POST['slug']; }
if (isset($_POST['title'])) { $title = $_POST['title']; }
if (isset($_POST['item_title'])) { $item_title = $_POST['item_title']; }
if (isset($_POST['body'])) { $body = $_POST['body']; }
if (isset($_POST['start_date'])) { $start_date = $_POST['start_date']; }
if (isset($_POST['end_date'])) { $end_date = $_POST['end_date']; }
if (isset($_POST['after_date'])) { $after_date = $_POST['after_date']; }
if (isset($_POST['before_date'])) { $before_date = $_POST['before_date']; }
?>
  <section class="seven columns alpha">
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
    <?php
    $db = get_db();

    if ($item_id) {
      $db->query("INSERT INTO `$db->NetworkRecord` (`id`, `owner_id`, `item_id`, `exhibit_id`, `added`, `modified`,`slug`,`title`,`item_title`,`body`,`start_date`,`end_date`,`after_date`,`before_date`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
      array($id, $owner_id, $item_id,$exhibit_id,$added,$modified,$slug,$title,$item_title,$body,$start_date,$end_date,$after_date,$before_date));
     #echo "<pre>" . print_r($insert) . "</pre>"; die();
      }
    ?>
    <div class="field">
        <h2><?php echo __("You have successfully readded the item to exhibit."); ?></h2>
    </div>
</form>
    </fieldset>
  </section>
  <section class="three columns omega">
    <div id="save" class="panel">
     <a href="<?php echo html_escape(url('network/view')); ?>" class="add big green button"><?php echo __('Back'); ?></a>
   </div>
  </section>
<?php echo foot(); ?>
