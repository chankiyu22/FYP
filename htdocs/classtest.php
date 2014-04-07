<html>
  <head>
    <title>Class Test</title>
    <script src="http://code.jquery.com/jquery-1.10.1.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
  </head>
  <body>
    <div class="document" id="1">Document 1</div>
    <div class="document" id="2">Document 2</div>
    <div class="document" id="10">Document 10</div>
    <div id="display"></div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title" id="myModalLabel" align="center">Votes</h4>
      </div>
      <div class="modal-body">
        ...
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
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
  </body>
</html> 
