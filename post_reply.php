<?php

session_start();


require_once 'vendor/autoload.php';
require_once 'db.php';
$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader, array());

$db = new db;
$db->connect();

if(isset($_POST['submit']))
{   
   post_reply();
   
} 

function post_reply(){
	global $db;
    
   //$replyID = $db->post_question($_SESSION['id'],$_REQUEST['QuestionTitle'],$_REQUEST['QuestionBody']);
    //echo $twig->render('ask.html',$params);        
   // sleep(10);
    $replyID = $db->post_answer($_REQUEST['questionID'],$_REQUEST['answer'],$_SESSION['id']);
    //sleep (10);
    //show_question($_REQUEST['questionID']);
    show_question($replyID);
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


?>