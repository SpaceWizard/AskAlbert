<?php

session_start();


require_once 'vendor/autoload.php';
require_once 'db.php';
$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader, array());

$db = new db;
$db->connect();
$uid = $_GET["User"] ;

if(!isset($_SESSION['username'])){
    if (isset($_REQUEST["login"])) {
        login();
    }else{
        echo $twig->render('login.html',array());
    }
}else{
    profile($uid);
}

function profile($uid){
    global $twig;
    global $db;
    $profile = $db->get_profile($uid);
    $params = array();
    $params["userName"] = $_SESSION['username'];
    $params["userScore"] = $profile['SCORE'];
    $params["joinDate"] = $profile['JOIN_DATE'];
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