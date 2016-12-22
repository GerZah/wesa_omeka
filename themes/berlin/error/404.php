<?php
$pageTitle = __('404 Page Not Found');
echo head(array('title'=>$pageTitle));
?>
<h1><?php echo $pageTitle; ?></h1>
<p><?php echo __('%s is not a valid URL.', html_escape($badUri)); ?></p>
<?php
  if (!(current_user())) {
    $baseUrl = url("/");
    $loginUrl = str_replace_first($baseUrl, $baseUrl."admin/", $_SERVER["REQUEST_URI"]);
?>
  <hr>
  <h3>Sie sind nicht angemeldet</h3>
  <p>Möglicherweise haben Sie nicht genügend Zugriffsrechte, um diese Seite anzusehen.</p>
  <ul>
    <li>
      <strong><a href="<?php echo $loginUrl; ?>">[Anmelden]</a></strong><br>
      Suchen Sie die gewünschte Informationen nach erfolgter Anmeldung im Datenbank-Backend.
    </li>
    <li>
      <strong><a href="<?php echo $baseUrl; ?>">[Informationen]</a></strong><br>
      Lesen Sie mehr, wie Sie einen eigenen Forscher-Account beantragen können.
    </li>
  </ul>
<?php
  }
  # --- http://stackoverflow.com/a/1252710
  function str_replace_first($needle, $replace, $haystack) {
    $pos = strpos($haystack, $needle);
    if ($pos !== false) {
        $newstring = substr_replace($haystack, $replace, $pos, strlen($needle));
    }
    else { $newstring = $haystack; }
    return $newstring;
  }
?>
<?php echo foot(); ?>
