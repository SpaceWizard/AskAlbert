<?php

session_start();
require_once 'post_reply.php';

require_once 'vendor/autoload.php';
require_once 'db.php';
$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader, array());

$db = new db;
$db->connect();

post_question_vote();

function post_question_vote(){
	global $db;

	$check = $db->vote_question($_SESSION['id'],$_REQUEST['qID'],$_REQUEST['vote']);
    //echo($_REQUEST['vote']);
    show_question($_REQUEST['qID']);
}

?>    