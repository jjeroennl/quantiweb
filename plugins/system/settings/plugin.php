<?php
    system_check();
    if(isset($_POST['generalpost'])){
         if(isset($_POST['indexpage'])){
             _system_setindex($_POST['indexpage']);
         }
    }
?>
<form method="post">
<h4>Active plugins</h4>
<ul>
<?php
    $plugins = system_getplugins();
    foreach($plugins as $plugin){
        if(substr($plugin, 0, 1) === '!')
        {
            $plugin = substr($plugin, 1);
        }
        echo "<li>" . ucfirst($plugin) . "</li>";
    }
?>
</ul>

<?php
    if (strpos(system_getsetting("wantindex"),',') !== false) {
?>
<h4>Homepage settings</h4>
<p>If multiple plugins want to be your homepage, then you can choose which one you want to be it.</p>
<select class="form-control" name="indexpage">
<?php
    $index_array = explode(",", system_getsetting("wantindex"));
    foreach($index_array as $indexes){
        echo "<option>" . $indexes . "</option>";
    }
?>
</select><br><br>
<input class="btn" type="submit" name="pluginpost" value="Send">
<?php
    }
?>

</form>