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
    $replies = $db->get_recent_question();
    //$replies = $db->get_reply_by_question($qid);
    /*echo "<pre>";
    print_r($replies);
    die();*/
    $params = array();
    $params["userName"] = $_SESSION['username'];
    $params["numberAnswers"] = count($replies);
    $params["replies"] = array();
    for($i=0;$i<$params["numberAnswers"];$i++){
        $params["replies"][$i] = array();
        //$params["replies"][i]["answerVotes"] = array();
        //$params["replies"][i]["replyID"] = $replies[i]["USER_ID"];
        $params["replies"][$i]["Question"] = $replies[$i]["ID"];
        $params["replies"][$i]["Title"] = $replies[$i]["TITLE"];
        $params["replies"][$i]["Date"] = $replies[$i]["DATE_TIME"];
    }

 //   echo "<pre>";
 //   print_r($params);
 //   echo "</pre>";
    
    echo $twig->render('discussion.html',$params);
}
function discussion2(){
    global $twig;
    global $db;
    $replies = $db->get_top_question();
    //$replies = $db->get_reply_by_question($qid);
    /*echo "<pre>";
    print_r($replies);
    die();*/
    $params = array();
    $params["userName"] = $_SESSION['username'];
    $params["numberAnswers"] = count($replies);
    $params["replies"] = array();
    for($i=0;$i<$params["numberAnswers"];$i++){
        $params["replies"][$i] = array();
        //$params["replies"][i]["answerVotes"] = array();
        //$params["replies"][i]["replyID"] = $replies[i]["USER_ID"];
        $params["replies"][$i]["Question"] = $replies[$i]["QUESTION"];
        $params["replies"][$i]["Title"] = $replies[$i]["TITLE"];
        $params["replies"][$i]["Vote"] = $replies[$i]["VOTE"];
    }

 //   echo "<pre>";
 //   print_r($params);
 //   echo "</pre>";
    
    echo $twig->render('discussion.html',$params);
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