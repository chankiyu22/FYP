<html>
  <head>
    <script language="JavaScript" src="http://code.jquery.com/jquery-1.4.4.js"></script>
    <script language="JavaScript" src="../js/browser_adjust.js"></script>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>

    <script type="text/JavaScript">
    <!--

    var array = <tmv-topic-pie-array>;
    var elements = generate_pie_elements(array);
    var piec = null;
    function init() {
        browser_adjust();
        
        // create pie chart
        piec = new PieChart(elements);
        piec.initialize();

    }

    function highlight(i) {
        piec.highlight(i);
    }
    
    function unhighlight() {
        piec.unhighlight();
    }
    //-->
    </script>

  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    
  <title>
<?php
$db = new SQLite3("yelp_data.db");
$stmt = $db->prepare("select title from docs where id=:id");
$stmt->bindValue(':id', htmlspecialchars($_GET['docid']), SQLITE3_INTEGER);
$result = $stmt->execute();
echo $result->fetchArray()[0];
?>
  </title>
</head>

<body>
  <!--
  <div class="page-header" align="center">
    <h1>
<?php
$db = new SQLite3("yelp_data.db");
$stmt = $db->prepare("select title from docs where id=:id");
$stmt->bindValue(':id', htmlspecialchars($_GET['docid']), SQLITE3_INTEGER);
$result = $stmt->execute();
echo $result->fetchArray()[0];
?>
    </h1>
  </div>
  //-->
    <div id="row">
      <div class="col-md-6 col-md-offset-1">
        <pre id="document">
<?php
$handle = @fopen("yelp_academic_dataset_review.json", "r");
if ($handle) {
  for ($i = 1; $i < htmlspecialchars($_GET["docid"]); $i++) {
    fgets($handle);
  }
  $line = fgets($handle);
  $replaced = str_replace("\\n", "<br />", $line);
  $json = json_decode($replaced);
  $text = $json->{'text'};
  echo $text;
  fclose($handle);
}
?>
        </pre>
      </div>
      <div class="col-md-4">
        <div id="releated-doc">
          <table class="table table-condensed table-hover">
            <tr class="active">
              <th>Related Reviews</th>
                </tr>
<?php
$db = new SQLite3("yelp_data.db");
$stmt = $db->prepare("select doc_b, score from doc_doc where doc_a=:id union select doc_a, score from doc_doc where doc_b=:id order by score asc");
$stmt->bindValue(':id', htmlspecialchars($_GET["docid"]) - 1, SQLITE3_INTEGER);
$result = $stmt->execute();
while ($row = $result->fetchArray()) {
  $doc = $row[0] + 1;
  $stmt = $db->prepare("select id, title from docs where id=:id");
  $stmt->bindValue(':id', $doc, SQLITE3_INTEGER);
  $res = $stmt->execute()->fetchArray();
  echo '<tr><td><a href="doc.php?docid=' . $res[0] . '">' . $res[1] . '</a></td></tr>';
}
$db->close();
?>
          </table>
        </div>
        <div id="vote">
          <div class="btn-group-vertical">
            <button type="button" class="btn btn-default">
              <span class="glyphicon glyphicon-chevron-up"></span> Up
            </button>
            <button type="button" class="btn btn-primary" disabled="disabled"> 0 </button>
            <button type="button" class="btn btn-default">
              <span class="glyphicon glyphicon-chevron-down"></span> Down
            </button>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
