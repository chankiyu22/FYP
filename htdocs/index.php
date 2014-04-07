<html>
  <head>
  <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
  <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
  <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>

    <title>FYP - LZ2: Topic Browser (Yelp)</title>
  </head>
  <body>
    <div id="user-agent">
    <?php
      $useragent = $_SERVER['HTTP_USER_AGENT'];
      if (strstr($useragent, "Windows Phone"))
        echo "Windows Phone!";
    ?>
    </div>
    <div class="page-header" align="center">
      <h1>Yelp Topics</h1>
    </div>
    <div class="row">
      <div class="col-md-6 col-md-offset-3">
        <table class='table table-hover table-condensed'>
<?php
  $word_per_topic = 10;
  echo '<tr><td align="center"><b>Votes</b></td><td align="center"><b>Views</b></td><td><b>Topic</b></td></tr>';

  $db = new SQLite3("yelp_data.db");
  $topics = $db->query("SELECT * FROM topics");

  while ($topic = $topics->fetchArray()) {
    $stmt = $db->prepare('SELECT term+1, term_title FROM topic_term WHERE topic=:id-1');
    $stmt->bindValue(':id', $topic['id'], SQLITE3_INTEGER);
    $terms = $stmt->execute();

    echo '<tr><td align="center"><a href="#"><span data-toggle="tooltip" data-placement="left" title="';
    echo '<span class=\'label label-success\' onclick=\'vote(1, ' . $topic['id'] . ')\'><span class=\'glyphicon glyphicon-arrow-up\'></span> ' . $topic['up'] . '</span> <span class=\'label label-danger downvote\' onclick=\'vote(-1, ' . $topic['id'] . ')\'><span class=\'glyphicon glyphicon-arrow-down\'></span> ' . $topic['down'] . '</span>';
    echo '" class="badge vote_number">';
    echo $topic['up'] - $topic['down'];
    echo '</span>';

    echo '</a>';
    echo '</td><td align="center"><a href="#"><span class="badge">';
    echo $topic['views'];
    echo '</span></a></td><td><span class="label label-primary" href="#" onclick="window.location.href=\'topic.php?topicid=' . $topic['id'] . '\'">Topic ' . $topic['id'] . '</span><br />';
    for ($i = 0; $i < $word_per_topic; $i++) {
      $term = $terms->fetchArray();
      echo '<span class="label label-info" href="#" onclick="window.location.href=\'term.php?termid=' . $term[0] . '\'">' . $term[1] . '</span> ';
    }
    echo '</td></tr>';
  }
?>
        </table>
      </div>
    </div>
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
                        data: {page: "index", value: $("#input-comment").val()}
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
$comments = $db->query("SELECT * FROM index_comment order by id desc");

echo "<br />";
while($row = $comments->fetchArray()) {
  echo $row[1];
  echo '<div align="right">' . $row[2] . '</div><hr />';
}
?>
          </div>
        </div>
      </div>
    </div>
    <div align="center">Some footer message</div>
    <br />

    <script>
      $(".vote_number").tooltip({delay: {show: 500, hide:2000}, html: true});

      function vote(type, topicid) {
        console.log(type + " " + topicid);
        $.ajax({
          url: "vote.php",
          data: { target: "topic", vote: type, id: topicid },
          type: "POST"}
        )
        .done(function () {
          location.reload();
        });
      }
    </script>
  </body>
</html>
