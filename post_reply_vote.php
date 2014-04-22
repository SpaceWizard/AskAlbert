<?php

session_start();
require_once 'post_reply.php';

require_once 'vendor/autoload.php';
require_once 'db.php';
$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader, array());

$db = new db;
$db->connect();

post_reply_vote();

function post_reply_vote(){
	global $db;
	echo($_REQUEST['replyID']);
	$check = $db->vote_question($_SESSION['id'],$_REQUEST['replyID'],$_REQUEST['vote']);
    show_question($_REQUEST['qID']);
}

?>    