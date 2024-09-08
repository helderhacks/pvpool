<?php

$file_name = 'cp1500_all_overall_rankings.csv';
$row_limit = $_POST['row_limit'] ?? 10;

function parse_csv_data($file_name, $row_limit, $filter) {
  $counter = 0;
  $rows = [];

  if (($open = fopen($file_name, 'r')) !== false) {
    while (($data = fgetcsv($open, 1000)) !== false && $counter <= $row_limit) {
      $rows[] = $data;
      $counter++;
    }
    array_shift($rows);
    fclose($open);
  }

  $pokedex_entry = array_column($rows, 2);
  $pokemon_types = array_unique(array_map(function($type1, $type2) {
    return $type1 . ($type2 !== 'none' ? "&$type2" : "");
  }, array_column($rows, 3), array_column($rows, 4)));


  switch ($filter) {
    case 'dex':
      return implode(",", $pokedex_entry);
      break;
    case 'typing':
      return implode(",", array_unique($pokemon_types));
      break;
    default:
      return implode(",", array_merge($pokedex_entry, $pokemon_types));
      break;

  }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>PVPool</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <form method="post" onsubmit="this.submit(); return false;">
    <input type="number" name="row_limit" min="1" max="1000" value="<?php echo $_POST['row_limit'] ?>" id="">
    <input type="submit" value="Generate Search String">
  </form>
  <fieldset>
    <legend>Search String By Pokedex Entry</legend>
    <textarea name="search_string" id="">
      <?php echo parse_csv_data($file_name, $row_limit, 'dex'); ?>
    </textarea>
  </fieldset>
  <fieldset>
    <legend>Search String By Typing</legend>
    <textarea name="search_string" id="">
      <?php echo parse_csv_data($file_name, $row_limit, 'typing'); ?>
    </textarea>
  </fieldset>
  <fieldset>
    <legend>Search String Combined</legend>
    <textarea name="search_string" id="">
      <?php echo parse_csv_data($file_name, $row_limit, ''); ?>
    </textarea>
  </fieldset>
  <script>
    document.querySelector('form').addEventListener('submit', function(e) {
      e.preventDefault();
      this.submit();
    });
  </script>
</body>
</html>
