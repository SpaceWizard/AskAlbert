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
    $uid = $db -> get_id($_SESSION['username']);
    $recommend = $db->recommended_for_you($uid);
    $gettags;
    $tag_num;
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
    $params["tags"] = array();
    for($i=0;$i<count($replies);$i++){
        $params["replies"][$i] = array();
        //$params["replies"][i]["answerVotes"] = array();
        //$params["replies"][i]["replyID"] = $replies[i]["USER_ID"];
        $params["replies"][$i]["Question"] = $replies[$i]["QUESTION"];
        $params["replies"][$i]["Title"] = $replies[$i]["TITLE"];
           $gettags = $db->get_tag_by_question($replies[$i]["QUESTION"]);
        $tag_num = count($gettags);
       // $params["tags"][$i]=array();
        $params["replies"][$i]["tags"] = array();
        for($j = 0;$j<$tag_num;$j++){
            //$params["tags"][$j]["Desc"] = array();
            $params["replies"][$i]["tags"][$j]=$gettags[$j]["DESCRIPTION"];
            //$params["tags"][$j]["Desc"] = $gettags[$j]["DESCRIPTION"];
            

        }
        if($i>20){
        	break;
        }
      
     //   $params["replies"][$i]["Vote"] = $replies[$i]["VOTE"];
    }
    //print_r($gettags);
    
    $params["leader"] = array();
    for($i=0;$i<count($leader);$i++){
        $params["leader"][$i] = array();
        $params["leader"][$i]["User"] = $leader[$i]["ID"];
        $params["leader"][$i]["Name"] = $leader[$i]["USER_NAME"];
        $params["leader"][$i]["Score"] = $leader[$i]["SCORE"];
        if($i>20){
        	break;
        }
    }
    $params["recommend"] = array();
    for($i=0;$i<count($recommend);$i++){
        $params["recommend"][$i] = array();
        $params["recommend"][$i]["Question"] = $recommend[$i]["QUESTION"];
        $gettags = $db->get_tag_by_question($recommend[$i]["QUESTION"]);
        $tag_num = count($gettags);
  		$params["recommend"][$i]["tags"] = array();
        for($j = 0;$j<$tag_num;$j++){
            //$params["tags"][$j]["Desc"] = array();
            $params["recommend"][$i]["tags"][$j]=$gettags[$j]["DESCRIPTION"];
            //$params["tags"][$j]["Desc"] = $gettags[$j]["DESCRIPTION"];
            

        }
      
        $params["recommend"][$i]["Title"] = $recommend[$i]["TITLE"];
        if($i>20){
        	break;
        }
     //   $params["recommend"][$i]["Vote"] = $leader[$i]["VOTE"];
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
        showHome();
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