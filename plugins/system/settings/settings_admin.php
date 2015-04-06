<?php
    system_check();
?>
<div role="tabpanel">

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#general" aria-controls="home" role="tab" data-toggle="tab">General</a></li>
    <li role="presentation"><a href="#users" aria-controls="profile" role="tab" data-toggle="tab">Users</a></li>
    <li role="presentation"><a href="#plugin" aria-controls="messages" role="tab" data-toggle="tab">Plugin</a></li>
    <li role="presentation"><a href="#content" aria-controls="settings" role="tab" data-toggle="tab">Content</a></li>
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