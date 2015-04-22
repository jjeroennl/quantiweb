<?php
    system_check();
?>

<div role="tabpanel">

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" id="nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#general" aria-controls="home" role="tab" data-toggle="tab">General</a></li>
    <li role="presentation"><a href="#users" aria-controls="users" role="tab" data-toggle="tab">Users</a></li>
    <li role="presentation"><a href="#plugin" aria-controls="plugin" role="tab" data-toggle="tab">Plugin</a></li>

    <li role="presentation"><a href="#content" aria-controls="content" role="tab" data-toggle="tab">Content</a></li>
  </ul>

  <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="general">
        <br>
        <div class="left-align">
            <?php include 'general.php';?>
        </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="users">
        <br>
        <div class="left-align">
            <?php include 'users.php';?>
        </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="plugin">
        <br>
        <div class="left-align">
            <?php include 'plugin.php';?>
        </div>
    </div>
    <div role="tabpanel" class="tab-pane" id="content">
        <br>
        <div class="left-align">
            <?php include 'content.php';?>
        </div>
    </div>
  </div>

</div>
<script>
$( document ).ready(function() {
    var url = document.location.toString();
    var spliturl = url.split("#")[1];
    var newdiv = '#nav-tabs a[href="' + spliturl + '"]';
    $('#nav-tabs a[href="profile"]').tab('show');

});
</script>