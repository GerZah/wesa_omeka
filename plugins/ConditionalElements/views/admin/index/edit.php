<?php
$type_name = strip_formatting($dependent);
if ($type_name != '') {
  $type_name = ': &quot;' . html_escape($type_name) . '&quot; ';
} else {
  $type_name = '';
}
$title = __('Edit dependent #%s', $dependent_id) . $type_name;

echo head(array('title'=> $title));
echo flash(); ?>

<form method="post" action="<?php echo url('conditional-elements/index/save'); ?>">
  <div>
    <?php
    echo $this->formHidden('element_id', $element['id']);
    $name = 'type';
    echo $this->formLabel($name, __('Dependee'));
    echo ' ';
    echo $this->formSelect(
    $name,
    $element_type['element_type'],
    null,
    $element_types_info_options
  );
  $name = 'type';
  echo $this->formLabel($name, __('Term'));
  echo ' ';
  echo $this->formSelect(
  $name,
  $element_type['element_type'],
  null,
  $element_types_info_options
);
?>
</div>
<?php
echo $this->formSubmit('save', __('Save'));
?>
</form>

<?php echo foot(); ?>
