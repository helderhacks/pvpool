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
  $pokemon_types = array_unique(array_map(function($fast_move, $chr1, $chr2) {
    return "@". $fast_move . "&" . "@" . $chr1 . "&" . "@" . $chr2;
  }, array_column($rows, 11), array_column($rows, 12), array_column($rows, 13)));


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
  <header>
    <img src="pokesift-logo.png" alt="PokeSift Logo" class="logo">
  </header>
  <form method="post" onsubmit="this.submit(); return false;">
    <input type="number" name="row_limit" min="1" max="1000" value="<?php echo $_POST['row_limit'] ?>" id="">
    <input type="submit" value="Generate Search String">
  </form>
  <fieldset>
    <legend>Search String By Pokedex Entry</legend>
    <textarea name="search_string" id="dexSearch">
      <?php echo parse_csv_data($file_name, $row_limit, 'dex'); ?>
    </textarea>
    <button  onclick="copyClipboard('dexSearch')">Copy text</button>
  </fieldset>
  <fieldset>
    <legend>Search String By Typing</legend>
    <textarea name="search_string" id="typingSearch">
      <?php echo parse_csv_data($file_name, $row_limit, 'typing'); ?>
    </textarea>
    <button  onclick="copyClipboard('typingSearch')">Copy text</button>
  </fieldset>
  <fieldset>
    <legend>Search String Combined</legend>
    <textarea name="search_string" id="combinedSearch">
      <?php echo parse_csv_data($file_name, $row_limit, ''); ?>
    </textarea>
    <button  onclick="copyClipboard('combinedSearch')">Copy text</button>
  </fieldset>
  <script>
    document.querySelector('form').addEventListener('submit', function(e) {
      e.preventDefault();
      this.submit();
    });

    function copyClipboard(textAreaId) {
      let copyText = document.getElementById(textAreaId);

      copyText.select();
      copyText.setSelectionRange(0, 99999);

      navigator.clipboard.writeText(copyText.value);

      alert("Copied the text: " + copyText.value);
    }
  </script>
</body>
</html>
