<?php
$pageTitle = __('Add dependency');
echo head(array('title'=>$pageTitle));
echo flash();
?>
<?php
/* // useless: This is executed _after_ IndexController.php has run saveAction()
if(!isset($_SESSION))
{
  session_start();
}
$_SESSION['conditional_elements_term'] = $_POST['term'];
*/
?>
<form method="post" action="<?php echo url('conditional-elements/index'); ?>">
  <section class="seven columns alpha">
    <fieldset class="bulk-metadata-editor-fieldset" id='bulk-metadata-editor-items-set' style="border: 1px solid black; padding:15px; margin:10px;">
      <div class="field">
        <h2>You have successfully saved the dependency</h2>
      </div>
    </fieldset>
  </section>
  <section class="three columns omega">
    <div id="save" class="panel">
      <input type="submit" class="big green button" name="submit" value="<?php echo __('Back'); ?>">
    </div>
  </section>
</form>
<?php echo foot(); ?>
