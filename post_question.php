<?php

session_start();


require_once 'vendor/autoload.php';
require_once 'db.php';
$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader, array());

$db = new db;
$db->connect();
$tags = $_REQUEST['QuestionTags'];

if(isset($_POST['submit']))
{
   post_question();
} 

function post_question(){
	global $db;
    $params = array();
    $params["userName"] = $_SESSION['username'];
    $tag = $_REQUEST['tags'];
 //   echo($tag);
	$replyID = $db->post_question($_SESSION['id'],$_REQUEST['QuestionTitle'],$_REQUEST['QuestionBody'],$tag);
    //echo $twig->render('ask.html',$params);        
   // sleep(10);
    
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
    $params["tags"] = array();
    $params["userName"] = $_SESSION['username'];
    $params["questionID"] = $qid;
     $gettags = $db->get_tag_by_question($qid);
     $tag_num = count($gettags);
     $params["tags"] = array();
        for($j = 0;$j<$tag_num;$j++){
            //$params["tags"][$j]["Desc"] = array();
            $params["tags"][$j]=$gettags[$j]["DESCRIPTION"];
            //$params["tags"][$j]["Desc"] = $gettags[$j]["DESCRIPTION"];
            

        }
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