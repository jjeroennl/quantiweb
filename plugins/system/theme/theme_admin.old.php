<?php
system_check();

if(isset($_GET['settheme'])){
    system_setsetting("theme", $_GET['settheme']);
}

$files = scandir("themes");
$files = array_diff($files, array('.', '..', 'admin'));
foreach($files as $file){
    $url = 'themes/' . $file . '/info.txt';
    if($fh = fopen($url,"r")){
	   while (!feof($fh)){
            $F1[] = fgets($fh,9999);
	   }
        fclose($fh);
        $description =  str_replace("DESC: ", "", $F1['0']);
        $author =  str_replace("AUTHOR: ", "", $F1['1']);
        $website =  str_replace("WEBSITE: ", "", $F1['2']);
        $version = str_replace("VERSION: ", "", $F1['3']);
       
        echo theme($file, $description, $author, $website, $version);
        unset($description);
        unset($author);
        unset($website);
        unset($version);
        unset($F1);
    }
    else{
        
    }
}

echo '<div class="clearfix"></div>';

function theme($themename, $description, $author, $website, $version){
    $returnvar = '
            <div class="col-sm-6">
                <div class="portfolio-item">
                    <div class="portfolio-image">
                        <div class="theme-picture" style="background-image: url(themes/' . $themename . '/image.png);"></div>
                    </div>
                    <div class="portfolio-info">
                        <ul>
                            <li class="portfolio-project-name">' . $themename . '</li>
                            <li>' . $description . '</li>
                            <li>Author: <a href="' . $website . '">' . $author . '</a></li>
                            ';
    if(system_getsetting("theme") == $themename){
        $returnvar = $returnvar . '<li><a class="btn btn-grey nonactive">Already is default</a></li>';
    }
    else{
        $returnvar = $returnvar . '<li><a class="btn btn-blue" href="?p=4&settheme=' . $themename . '">Set as default theme</a></li>';
    }
    $returnvar = $returnvar . '</ul>
                            </div>
                        </div>
                    </div>';
    return $returnvar;
}


?>
<style>
    .theme-picture{
        height: 200px;
        background-repeat: no-repeat;
        background-size:cover;
        background-position: top center;
    }
    .nonactive{
        cursor:not-allowed;
    }
    .nonactive:hover{
        background-color: #E7E7E7 !important;
    }
</style>
