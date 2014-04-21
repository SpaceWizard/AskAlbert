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
    checkOtherRequests();
}

function checkOtherRequests(){
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
}


function show_question($qid){
    global $twig;
    global $db;
    $question = $db->get_question_by_id($qid);
    $replies = $db->get_reply_by_question($qid);
    /*echo "<pre>";
    print_r($replies);
    die();*/
    $params = array();
    $params["userName"] = $_SESSION['username'];
    $params["questionID"] = $qid;
    $params["questionTitle"] = $question['TITLE'];
    $params["questionContent"] = $question['CONTENT'];
    $params["dateTime"] = $question['DATE_TIME'];
    //$params["askerName"] = $question['ASKER_NAME'];
    $params["askerID"] = $question['ASKER'];
    $params["votes"] = $question['VOTEUP'] - $question['VOTEDOWN'];
    $params["numberAnswers"] = count($replies);
    $params["replies"] = array();
    for($i=0;$i<$params["numberAnswers"];$i++){
        $params["replies"][$i] = array();
        //$params["replies"][i]["answerVotes"] = array();
        //$params["replies"][i]["replyID"] = $replies[i]["USER_ID"];
        $params["replies"][$i]["replyName"] = $replies[$i]["USER_NAME"];
        $params["replies"][$i]["timeStamp"] = $replies[$i]["DATE_TIME"];
        $params["replies"][$i]["answerContent"] = $replies[$i]["TEXT"];
    }

 //   echo "<pre>";
 //   print_r($params);
 //   echo "</pre>";
    
    echo $twig->render('question.html',$params);
}

function show_home(){

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