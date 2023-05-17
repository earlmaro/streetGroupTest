<?php

use App\Parser;

require_once __DIR__ . '\app\Parser.php';
$Parser = new Parser();
if (isset($_POST["importCSV"])) {
  $response = $Parser->readCsv();
}
?>
<?php include './header.php'; ?>

<div class="row border p-4">
    <form action="" method="post" name="frmCSVImport" id="frmCSVImport" enctype="multipart/form-data" onsubmit="return validateFile()" class="mt-4 w-75">
      <div class="form-group">
        <label for="exampleInputEmail1"><a href="./csv/example.csv" download> Download sample csv document for upload</a></label>
        <input class="mt-5" type="file" name="file" id="file" class="file" accept=".csv,.xls,.xlsx">
      </div>
      <div class="mt-5">
        <button type="submit" id="submit" name="importCSV" class="btn btn-primary"> Upload File</button>
      </div>
    </form>
</div>