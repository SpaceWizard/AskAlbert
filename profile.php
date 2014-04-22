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
    profile();
}

function profile(){
    global $twig;
    global $db;
    $uid = $db->get_id($_SESSION['username']);
    $profile = $db->get_profile($_SESSION['username']);
    $question = $db->get_question_by_user($uid);
   // echo($uid);
    $userAnswer = $db -> get_ques_user_ans($uid);
    $active = $db->act_log($uid);
    $params = array();
    $params["userName"] = $_SESSION['username'];
    $params["userScore"] = $profile['SCORE'];
    $params["joinDate"] = $profile['JOIN_DATE'];
    $params["numberAnswers"] = count($active);
    //echo($params["numberAnswers"]);
    $params["activity"] = array();
    for($i=0;$i<7;$i++){
        $params["activity"][$i] = array();
        //$params["replies"][i]["answerVotes"] = array();
        //$params["replies"][i]["replyID"] = $replies[i]["USER_ID"];
        $params["activity"][$i]["Act"] = $active[$i]["ACT"];
        $params["activity"][$i]["Date"] = substr(($active[$i]["DATE_TIME"]),0,10);
    }
    //echo(count($question));
    $params["questionsAsked"]=array();
    for($i=0;$i<8;$i++){
        $params["questionsAsked"][$i] = array();
        //$params["replies"][i]["answerVotes"] = array();
        //$params["replies"][i]["replyID"] = $replies[i]["USER_ID"];
        $params["questionsAsked"][$i]["Title"] = $question[$i]["TITLE"];
        $params["questionsAsked"][$i]["Id"]= $question[$i]["ID"];
    }
    $params["answered"]=array();
    for($i=0;$i<8;$i++){
        $params["answered"][$i] = array();
        //$params["replies"][i]["answerVotes"] = array();
        //$params["replies"][i]["replyID"] = $replies[i]["USER_ID"];
        $params["answered"][$i]["Title"] = $question[$i]["TITLE"];
        $params["answered"][$i]["Id"]= $question[$i]["ID"];
    }
    echo $twig->render('profile.html',$params);
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