<?php

  // -----------------------------------------------
	header('Content-type: text/plain; charset=utf-8');
	// -----------------------------------------------
  ini_set('include_path', '.' . DIRECTORY_SEPARATOR . '..');
  require_once 'bootstrap.php';
  // Configure and initialize the application.
	$application = new Omeka_Application(APPLICATION_ENV);
	$application->initialize();
  // -----------------------------------------------

  $targetUrl = "../admin/items";

  if (isset($_GET["id"])) {
    $db = get_db(); // Database connection
    $sql = "
      SELECT id FROM `$db->Elements`
      WHERE name = 'Anmerkungen'
    ";
    $anmerkungen = $db->fetchOne($sql);
    // echo "$sql\n-> $anmerkungen\n";

    if ($anmerkungen) {
      $id = mres($_GET["id"]);
      $sql = "
        SELECT record_id
        FROM `$db->ElementTexts`
        WHERE element_id = $anmerkungen
        AND text LIKE '%$id%'
      ";
      $item = $db->fetchOne($sql);
      // echo "$sql\n-> $item\n";

      if ($item) { $targetUrl .= "/show/$item"; }

    }
  }

  header('Location: '.$targetUrl);

  // -----------------------------------------------

  function mres($value) {
    // http://stackoverflow.com/a/1162502
    $search = array("\\",  "\x00", "\n",  "\r",  "'",  '"', "\x1a");
    $replace = array("\\\\","\\0","\\n", "\\r", "\'", '\"', "\\Z");
    return str_replace($search, $replace, $value);
  }

  // -----------------------------------------------

?>
