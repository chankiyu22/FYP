<html>
<head>
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
  <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>

  <title><tmv-term></title>
</head>
<body>
<div class="page-header"><h1 align="center">
<?php
$db = new SQLite3("yelp_data.db");
$stmt = $db->prepare("SELECT title FROM terms WHERE id=:id");
$stmt->bindValue(':id', htmlspecialchars($_GET['termid']), SQLITE3_INTEGER);
$result = $stmt->execute();
echo $result->fetchArray()['title'];
$db->close();
?>
</h1>
</div>
<div class="row">
  <div class="col-md-5 col-md-offset-2">
    <table class="table table-condensed"><tr><td align="center"><b>Votes</b></td><td align="center"><b>Views</b></td><td><b>Review Preview</b></td></tr>
<?php
$num_doc = 15;
$db = new SQLite3("yelp_data.db");
$stmt = $db->prepare("SELECT doc FROM doc_term WHERE term=:id ORDER BY score DESC");
$stmt->bindValue(':id', htmlspecialchars($_GET['termid']) - 1, SQLITE3_INTEGER);
$docs = $stmt->execute();
$page = htmlspecialchars($_GET["page"]);
for ($i = 1; $i < $page; $i++) {
  for ($j = 0; $j < $num_doc; $j++) {
    $row = $docs->fetchArray();
  }
}

for ($i = 0; $i < $num_doc; $i++) {
  $row = $docs->fetchArray();
  $stmt = $db->prepare("SELECT id, title FROM docs WHERE id=:id");
  $stmt->bindValue(':id', $row['doc'] + 1, SQLITE3_INTEGER);
  $res = $stmt->execute();
  $doc = $res->fetchArray();

  $useragent = $_SERVER['HTTP_USER_AGENT'];

  if (strstr($useragent, "Windows Phone") || strstr($useragent, "iPhone") || strstr($useragent, "iPad") || strstr($useragent, "Android")) {
    echo '<tr onclick="window.location.href=\'doc.php?docid=' . $doc['id'] . '\'"><td align="center"><span class="badge">';
  } else {
    echo '<tr class="document" id=' . $doc['id'] . '><td align="center"><span class="badge">';
  }
  echo 0;
  echo '</span></td><td align="center"><span class="badge">';
  echo 0;
  echo '</span></td><td><a href="#">' . $doc['title'] . '</a></td></tr>';
}
$db->close();

$page = htmlspecialchars($_GET["page"]);
if (!$page)
  $page = 1;
$nextpage = $page+1;
$prevpage = $page-1;
echo '<tr><td colspan=3 align="center"><div class="btn-group"><button type="button" class="btn btn-default" ';
if ($page == 1) {
  echo ' disabled="disabled"';
}
else {
  echo ' onclick="window.location.href=\'term.php?termid=' . htmlspecialchars($_GET["termid"]) . '&page=' . $prevpage . '\'"';
}
echo '><span class="glyphicon glyphicon-arrow-left"></span> Prev Page';
echo '</button><button type="button" class="btn btn-default" onclick="window.location.href=\'term.php?termid=' . htmlspecialchars($_GET["termid"]) . '&page=' .  $nextpage . '\'">Next Page <span class="glyphicon glyphicon-arrow-right"></span></a></button></div></td></tr>';
?>
    </table>
  </div>
  <div class="col-md-3">
    <table class="table table-condensed"><tr><td align="center"><b>Related Topics</b></td>
<?php
$db = new SQLite3("yelp_data.db");
$stmt = $db->prepare("select topics.id, topics.title from topic_term left join terms, topics where topic_term.topic=topics.id-1 and topic_term.term=terms.id-1 and terms.id=:id order by score desc");
$stmt->bindValue(':id', htmlspecialchars($_GET['termid']), SQLITE3_INTEGER);
$topics = $stmt->execute();
while ($row = $topics->fetchArray()) {
  echo '<tr><td align="center" onclick="window.location.href=\'topic.php?topicid=' . $row[0] . '\'"><a href="#">' . $row[1] . '</a></td></tr>';
}
$db->close();
?>
    </table>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <div class="modal-title" id="myModalLabel" align="center"><span class="label label-success"><span class="glyphicon glyphicon-arrow-up"></span> Up 0</span> Review # ? <span class="label label-danger"><span class="glyphicon glyphicon-arrow-down"></span> Down 0</span></div>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- End -->
    <script>
      $(".document").click(function (event) {
        did = this.id;
        $("#myModal").modal();
        $.ajax({url: "document.php", type: "POST", data: {docid: did}})
         .done(function (data) { $(".modal-body").html(data); });
      });
    </script>
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <div id="comment">
          <div class="input-group">
            <input type="text" class="form-control" placeholder="Comments" id="input-comment">
            <span class="input-group-btn">
              <button class="btn btn-default" type="button" id="submit-comment">Submit</button>
            </span>
            <script>
              $("#submit-comment").click(function (event) {
                $.ajax( {url: "post_comment.php",
                        type: "POST",
                        data: {page: "term",
                              value: $("#input-comment").val(),
                              termid: <?php echo htmlspecialchars($_GET["termid"]) . "}"; ?>
                        })
                 .done( function () {
                    location.reload();
                  });
              });
            </script>
          </div>
          <div id="show-comment">
<?php
$db = new SQLite3("yelp_data.db");
$termid = htmlspecialchars($_GET["termid"]);
$stmt = $db->prepare("SELECT * FROM term_comment where term=:id order by id desc");
$stmt->bindValue(":id", $termid, SQLITE3_INTEGER);
$comments = $stmt->execute();

// id | term id | comment | time
echo "<br />";
while($row = $comments->fetchArray()) {
  echo $row[2];
  echo '<div align="right">' . $row[3] . '</div><hr />';
}
?>
          </div>
        </div>
      </div>
    </div>
  </body>
</html>
