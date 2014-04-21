<?php

session_start();


require_once 'vendor/autoload.php';
require_once 'db.php';
$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader, array());

$db = new db;
$db->connect();

class foo extends ArrayObject{
    function __destruct(){
        echo 'dying:';
        debug_print_backtrace();
    }
}

if(!isset($_SESSION['username'])){
    if (isset($_REQUEST["login"])) {
        login();
    }else{
        echo $twig->render('login.html',array());
    }
}else{
    showHome();
}

/*function checkOtherRequests(){
//    echo "<pre>";
  //  print_r($_REQUEST);
    //echo "</pre>";

    if(!isset($_REQUEST["function"]));
        show_question(1);

    switch($_REQUEST["function"]){
        case "home":
            show_home();
            break;
        case "question":
            show_question();
            break;
        case "post_answer":
            post_answer();
            break;
    }
}*/


function showHome(){
    global $twig;
    global $db;
    $replies = $db->get_top_question();
    $leader = $db->leaderboard2();
    $recommend = $db->recommended_for_you($_SESSION['username']);
    //$replies = $db->get_reply_by_question($qid);
    /*echo "<pre>";
    print_r($replies);
    die();*/
    $params = array();
    $params["userName"] = $_SESSION['username'];
    $params["numberAnswers"] = count($replies);
    $params["numberLeader"] = count($leader);
    $params["numberRecommend"] = count($recommend);
    $params["replies"] = array();
    for($i=0;$i<$params["numberAnswers"];$i++){
        $params["replies"][$i] = array();
        //$params["replies"][i]["answerVotes"] = array();
        //$params["replies"][i]["replyID"] = $replies[i]["USER_ID"];
        $params["replies"][$i]["Question"] = $replies[$i]["QUESTION"];
        $params["replies"][$i]["Title"] = $replies[$i]["TITLE"];
        $params["replies"][$i]["Vote"] = $replies[$i]["VOTE"];
    }
    for($i=0;$i<$params["numberLeader"];$i++){
        $params["leader"][$i] = array();
        $params["leader"][$i]["Name"] = $leader[$i]["USER_NAME"];
        $params["leader"][$i]["Score"] = $leader[$i]["SCORE"];
    }
 //   echo "<pre>";
 //   print_r($params);
 //   echo "</pre>";
    
    echo $twig->render('home.html',$params);
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
        show_question(1);
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