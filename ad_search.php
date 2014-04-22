<?php

session_start();


require_once 'vendor/autoload.php';
require_once 'db.php';
$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader, array());

$db = new db;
$db->connect();

if(!isset($_SESSION['username'])){
    if (isset($_REQUEST["login"])) {
        login();
    }else{
        echo $twig->render('login.html',array());
    }
}else{
    discussion();
}

function discussion(){
    global $twig;
    global $db;
   // $replies = $db->get_recent_question();
    /*echo "<pre>";
    print_r($replies);
    die();*/
    $params = array();
    $params["userName"] = $_SESSION['username'];
    $startDate = $_REQUEST['startDate'];
    $endDate = $_REQUEST['endDate'];
    $name1 = $_REQUEST['userName'];
    $sentence = $_REQUEST['sentence'];
    $tags = $_REQUEST['tags'];
    //echo($tags);
    $u_id = $db -> get_id($name1);
    //echo($u_id);
    $replies = $db-> search($sentence,$tags,$u_id,$startDate,$endDate);
    $params["numberAnswers"] = count($replies);
    //echo($params["numberAnswers"]);
    $params["replies"] = array();
    $params["tags"] = array();
    for($i=0;$i<$params["numberAnswers"];$i++){
        $params["replies"][$i] = array();
        //$params["replies"][i]["answerVotes"] = array();
        //$params["replies"][i]["replyID"] = $replies[i]["USER_ID"];
        $params["replies"][$i]["Question"] = $replies[$i]["ID"];
        $params["replies"][$i]["Title"] = $replies[$i]["TITLE"];
        $params["replies"][$i]["Date"] = $replies[$i]["DATE_TIME"];
        $gettags = $db->get_tag_by_question($replies[$i]["ID"]);
        $tag_num = count($gettags);
        $params["replies"][$i]["tags"] = array();
        for($j = 0;$j<$tag_num;$j++){
            //$params["tags"][$j]["Desc"] = array();
            $params["replies"][$i]["tags"][$j]=$gettags[$j]["DESCRIPTION"];
            //$params["tags"][$j]["Desc"] = $gettags[$j]["DESCRIPTION"];
            

        }
    }

 //   echo "<pre>";
 //   print_r($params);
 //   echo "</pre>";
    
    echo $twig->render('search.html',$params);
}


function login(){
    global $db;
    global $twig;
    $id = $db->authenticate($_REQUEST["username"],$_REQUEST["password"]);
    if ($id == null) {
        $msg = "access denied";
        echo "denied";
    } else {
        //session_start();
     //   $_SESSION['check'] = new foo();
        $_SESSION['id'] = $id["ID"];
        $_SESSION['username'] = $_REQUEST["username"];
        //Hack! change to show_home
        ask_question();
    }
}
/*$template = $twig->loadTemplate('template2.phtml');
$params = array(
    'name' => 'Krzysztof',
    'friends' => array(
        array(
            'firstname' => 'John',
            'lastname' => 'Smith'
        ),
        array(
            'firstname' => 'Britney',
            'lastname' => 'Spears'
        ),
        array(
            'firstname' => 'Brad',
            'lastname' => 'Pitt'
        )
    )
);
$template->display($params);*/
?>