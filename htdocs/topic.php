<html>
  <head>
<?php
  echo '<title>Topic ' . htmlspecialchars($_GET["topicid"]) . '</title>';
  $db = new SQLite3("yelp_data.db");
  $stmt = $db->prepare("UPDATE topics SET views=views+1 WHERE id=:id");
  $stmt->bindValue(':id', htmlspecialchars($_GET["topicid"]), SQLITE3_INTEGER);
  $stmt->execute();
  $db->close();
?>
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
  <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
  </head>

  <body>
    <div class="page-header"><h1 align="center">Topic 
<?php
  echo htmlspecialchars($_GET["topicid"]);
?>
      </h1>
      <div align="center">
<?php
// Global Defination
  $word_show = 15;

// Body
  $db = new SQLite3("yelp_data.db");
  $stmt = $db->prepare("select * from topic_term left join terms where topic_term.term = terms.id-1 and topic_term.topic=:id");
  $stmt->bindValue(':id', htmlspecialchars($_GET["topicid"]) - 1, SQLITE3_INTEGER);
  $result = $stmt->execute();

  for ($i = 0; $i < $word_show; $i++) {
    $row = $result->fetchArray();
    $termid = $row["term"] + 1;
    echo '<span  class="label label-info" onclick="window.location.href=\'term.php?termid=' . $termid . '\'" href="#">' . $row['title'] . '</span> ';
  }
  $db->close();
?>
    </div></div>

    <div class="col-md-5 col-md-offset-2">
      <table class="table table-condensed table-hover"><tr><th class="col-md-1">Votes</th><th class="col-md-1">Views</th><th>Review Preview</th></tr>
<?php
// Global Defination
$num_doc = 14;

// Body
$db = new SQLite3("yelp_data.db");
$stmt = $db->prepare("select doc from doc_topic where topic=:id order by score desc");
$stmt->bindValue(':id', htmlspecialchars($_GET["topicid"]) - 1, SQLITE3_INTEGER);
$result = $stmt->execute();
$docs = array();
$page = htmlspecialchars($_GET["page"]);

// Ignore Results Until To the desire Page
for ($i = 1; $i < $page; $i++) {
  for ($j = 0; $j < $num_doc; $j++) {
    $row = $result->fetchArray();
  }
}

// Fetch Result
for ($i = 0; $i < $num_doc; $i++) {
  $row = $result->fetchArray();

  $stmt = $db->prepare("SELECT id, views, title, (up-down) FROM docs WHERE id=:id");
  $stmt->bindValue(':id', $row['doc'] + 1, SQLITE3_INTEGER);
  $res = $stmt->execute(); 
  $doc = $res->fetchArray();
  echo '<tr>';
  echo '<td align="center"><span class="badge">';
  // return thr number of votes
  echo $doc[3];
  echo '</span></td><td align="center"><span class="badge">';
  // return the number of views
  echo $doc['views'];

  $useragent = $_SERVER['HTTP_USER_AGENT'];

  if (strstr($useragent, "Windows Phone") || strstr($useragent, "iPhone") || strstr($useragent, "iPad") || strstr($useragent, "Android")) {
    echo '</span></td><td><a href="doc.php?docid=' . $doc['id'] . '">' . $doc['title'] . '</a></td></tr>';
  } else {
    echo '</span></td><td><a href="#" class="document" id=' . $doc['id'] . '>' . $doc['title'] . '</a></td></tr>';
  }
}
$db->close();

// Page Controller
$page = htmlspecialchars($_GET["page"]);
if (!$page)
  $page = 1;
$nextpage = $page+1;
$prevpage = $page-1;
echo '<tr><td colspan=3 align="center"><div class="btn-group"><button type="button" class="btn btn-default" ';
if ($page == 1) {
  echo 'disabled="disabled"';
} else {
  echo 'onclick="window.location.href=\'topic.php?topicid=' . htmlspecialchars($_GET["topicid"]) . '&page=' . $prevpage . '\'"';
}
echo '><span class="glyphicon glyphicon-arrow-left"></span> Prev Page';
echo '</button><button type="button" class="btn btn-default" onclick="window.location.href=\'topic.php?topicid=' . htmlspecialchars($_GET["topicid"]) . '&page=' .  $nextpage . '\'">Next Page <span class="glyphicon glyphicon-arrow-right"></span></button></div></td></tr>';
?>
      </table>
    </div>

    <div class="col-md-3">
      <table class="table table-condensed"><tr><td align="center"><b>Related Topics</b></td></tr>

<?php
$db = new SQLite3("yelp_data.db");
$stmt = $db->prepare("select topics.id, topics.title, score from topic_topic, topics where topic_topic.topic_b=topics.id-1 and topic_a=:id UNION select topics.id, topics.title, score from topic_topic, topics where topic_topic.topic_a=topics.id-1 and topic_b=:id order by score asc");

$stmt->bindValue(':id', htmlspecialchars($_GET["topicid"]) - 1, SQLITE3_INTEGER);
$result = $stmt->execute();
$result->fetchArray();

while ($row = $result->fetchArray()) {
  echo '<tr><td align="center"><a href=\'topic.php?topicid=' . $row['topics.id'] . '\'">' . $row['topics.title'] . '</a></td></tr>';
}
$db->close();
?>

      </table>
    </div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <div class="modal-title" id="myModalLabel" align="center"><span class="label label-success" id="up-vote" data=""><span class="glyphicon glyphicon-arrow-up"></span> Up <upvote>0</upvote></span> Review <doc-no>?</doc-nop>
 <span class="label label-danger" id="down-vote" data=""><span class="glyphicon glyphicon-arrow-down"></span> Down <downvote>0</downvote></span></div>
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
        $.ajax({url: "document.php", type: "POST", data: {docid: did}, dataType: "json"})
         .done(function (data) { 
           $(".modal-body").html(data["text"]); 
           $("upvote").html(data["up"]);
           $("downvote").html(data["down"]);
         });
        $("doc-no").html(did);
        $("#up-vote").attr("data", did);
        $("#down-vote").attr("data", did);
      });

      $("#up-vote").click(function (event) {
        did = $(this).attr("data");
        $.ajax({url: "vote.php", type: "POST", data: {target: "doc", id: did, vote: 1}})
         .done(function (data) { location.reload(); });
      });

      $("#down-vote").click(function (event) {
        did = $(this).attr("data");
        $.ajax({url: "vote.php", type: "POST", data: {target: "doc", id: did, vote: -1}})
         .done(function (data) { location.reload(); });
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
                        data: {page: "topic", 
                              value: $("#input-comment").val(),
                              topicid: <?php echo htmlspecialchars($_GET["topicid"]) . "}"; ?>
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
$topicid = htmlspecialchars($_GET["topicid"]);
$stmt = $db->prepare("SELECT * FROM topic_comment where topic=:id order by id desc");
$stmt->bindValue(":id", $topicid, SQLITE3_INTEGER);
$comments = $stmt->execute();

// id | topic id | comment | time
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
