<?php
  $docid = htmlspecialchars($_POST["docid"]);
  $useragent = $_SERVER['HTTP_USER_AGENT'];
  
  if (strstr($useragent, "Windows Phone")) {
    header("Location: http://wwwfyp.cse.ust.hk:7122/doc.php?id=" + $docid); 
    exit(0);
  }

  $handle = @fopen("yelp_academic_dataset_review.json", "r");
  if ($handle) {
    for ($i = 1; $i < htmlspecialchars($_POST["docid"]); $i++) {
      fgets($handle);
    }
    $line = fgets($handle);
    $replaced = str_replace("\\n", "<br />", $line);
    $json = json_decode($replaced);
    $return["text"] = $json->{'text'};
    fclose($handle);

    $db = new SQLite3("yelp_data.db");
    $stmt = $db->prepare("update docs set views = views + 1 where id=:id");
    $stmt->bindValue(':id', htmlspecialchars($_POST["docid"]), SQLITE3_INTEGER);
    $stmt->execute();
    $db->close();

    $db = new SQLite3("yelp_data.db");
    $stmt = $db->prepare("select up, down from docs where id = :id");
    $stmt->bindValue(':id', htmlspecialchars($_POST{"docid"}), SQLITE3_INTEGER);
    $result = $stmt->execute();
    $row = $result->fetchArray();
    $return["up"] = $row[0]; $return["down"] = $row[1];
    $db->close();

    echo json_encode($return);
  }
?>
