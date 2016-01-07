<?php
    system_check();
    if(isset($_POST['generalpost'])){
        system_setsetting("websitename", $_POST['websitename']);
        system_setsetting("websiteslogan", $_POST['slogan']);
        if(isset($_POST['allowregistration'])){
            system_setsetting("allow_registration", 1);
        }
        else{
            system_setsetting("allow_registration", 0);
        }
        system_setsetting("default_role", $_POST['defaultrole']);
        system_setsetting("timeformat", $_POST['timeformat']);
        system_setsetting("dateformat", $_POST['dateformat']);
    }
?>

<form method="post">
<h4>Website info</h4>
    <input class="form-control" name="websitename" placeholder="Websitename" style="margin-bottom: 5px;" value="<?php echo system_getsetting("websitename");?>">
    <input class="form-control" name="slogan" placeholder="Slogan" style="margin-bottom: 5px;" value="<?php echo system_getsetting("websiteslogan");?>">
    <hr>
<h4>User settings</h4>
    <?php
    $check = "";
    if(system_getsetting("allow_registration") == 1){
        $check = "checked";
    }
    else{
        $check = "";   
    }
    ?>
    <input name="allowregistration" type="checkbox" <?php echo $check;?>> <label for="allowregistration">Allow registration</label><br>
    Default userrole:
    <select name="defaultrole" class="form-control" style="width: 200px; display: inline;">
        <option>Admin</option>
        <option>Contributor</option>
        <option>Writer</option>
        <option>Subscriber</option>
    </select>
    <hr>
<h4>Time</h4>
    <table>
        <tr>
            <td width="100px">Timeformat</td><td><input type="text" class="form-control" name="timeformat" value="<?php echo system_getsetting("timeformat");?>"></td>
        </tr>
        <tr>
            <td>Dateformat</td><td><input type="text" class="form-control" name="dateformat" value="<?php echo system_getsetting("dateformat");?>"></td>
        </tr>
    </table>
<input class="btn" name="generalpost" type="submit" value="Send">
</form>