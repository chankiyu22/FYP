<?php
$db = new SQLite3("yelp_data.db");
$page = $_POST["page"];
$value = $_POST["value"];

// comment to index page
if ($page == "index") {
  $stmt = $db->prepare("insert into index_comment(comment) values (:cm)");
  $stmt->bindValue(":cm", $value, SQLITE3_TEXT);
  $stmt->execute();
  $db->close();
} else if ($page == "topic") {
  $topicid = $_POST["topicid"];
  $stmt = $db->prepare("insert into topic_comment(topic, comment) values (:id, :cm)");
  $stmt->bindValue(":id", $topicid, SQLITE3_INTEGER);
  $stmt->bindValue(":cm", $value, SQLITE3_TEXT);
  $stmt->execute();
  $db->close();
} else if ($page == "term") {
  $termid = $_POST["termid"];
  $stmt = $db->prepare("insert into term_comment(term, comment) values (:id, :cm)");
  $stmt->bindValue(":id", $termid, SQLITE3_INTEGER);
  $stmt->bindValue(":cm", $value, SQLITE3_TEXT);
  $stmt->execute();
  $db->close();
} else if ($page == "doc") {
  $docid = $_POST["docid"];
  $stmt = $db->prepare("insert into doc_comment(doc, comment) values (:id, :cm)");
  $stmt->bindValue(":id", $docid, SQLITE3_INTEGER);
  $stmt->bindValue(":cm", $value, SQLITE3_TEXT);
  $stmt->execute();
  $db->close();
}
?>
