<?php
  $target = htmlspecialchars($_POST["target"]);
  $id = htmlspecialchars($_POST["id"]);
  $vote = htmlspecialchars($_POST["vote"]);

  $db = new SQLite3("yelp_data.db");
  $stmt;
  if ($vote == 1) {
    if ($target == "topic")
      $stmt = $db->prepare("update topics set up = up+1 where id=:id");
    else if ($target == "doc")
      $stmt = $db->prepare("update docs set up = up+1 where id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();
  } else if ($vote == -1) {
    if ($target == "topic")
      $stmt = $db->prepare("update topics set down = down+1 where id=:id");
    else if ($target == "doc")
      $stmt = $db->prepare("update docs set down = down+1 where id = :id");
    $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
    $stmt->execute();
  }
  $db->close();
?>
