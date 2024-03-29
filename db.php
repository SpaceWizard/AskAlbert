<?php
class db {
	private $host = "oracle.cise.ufl.edu";
	private $port = "1521";
	private $sid  = "orcl";
	private $user = "jjiaqi";
	private $password = "oraclepass";
	private $connection;
	
	public function connect() {
		$string = "(DESCRIPTION=
			(
				ADDRESS_LIST=
				(	
					ADDRESS=(PROTOCOL=TCP)
					(HOST=" . $this->host . ")(PORT=" . $this->port . ")
				)
			)
			(CONNECT_DATA=(SID=" . $this->sid . "))
			)";
			
		$this->connection = oci_connect($this->user, $this->password, $string);
	}
	
	public function close() {
		if(isset($this->connection)) {
			oci_close($this->connection);
		}
	}


//$sentence takes string, $tags take array,$start and $end should be formatted like'10-APR-14',$session_user is user id


//$sentence takes string, $tags take array,$start and $end should be formatted like'10-APR-14',$session_user is user id
	public function get_tag_by_question ($question) {
		$i = 0;
		//echo("here");
		$query = "select description from (select description, question from category join belongs_to on category.id = belongs_to.category) where question = :question ";
		$stid = oci_parse($this->connection,$query);
		oci_bind_by_name($stid, ":question", $question);
		oci_execute($stid);
		
		while($result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) {
			$return[$i] = $result;
			//echo($return[$i]);
			$i++;
		}
		
		return $return;
		
	}
public function search($sentence = null, $tags = null, $session_user = null, $start = null, $end = null) {
		$i = 0;
	//	print_r($tags);
		$score = 0;
		
		$query = "create table temp as select 0 as score, id, title, content from questions";
		
		if($session_user != null || $start != null || $end != null) {
			$query = $query . " where ";
		}
		
		if($session_user != null) {
			$user = intval($session_user);
			$query = $query . "asker = " . $user;
			if($start != null || $end != null) {
				$query = $query . " and ";
			}
		}
		
		if($start != null) {
			$start = $this->convert_date($start);
		//	echo $start;
		}
		if($end != null) {
			$end = $this->convert_date($end);
		//	echo $end;
		}
		if($start != null && $end != null) {
			$query = $query . "DATE_TIME > TO_TIMESTAMP('".$start."') and TO_TIMESTAMP('".$end."') > DATE_TIME";
			$stid = oci_parse($this->connection,$query);
			//oci_bind_by_name($stid, ":start", $start,SQLT_CHR);
			//oci_bind_by_name($stid, ":end", $end,SQLT_CHR);
		} else if($start != null && $end == null) {
			$query = $query . "DATE_TIME > TO_TIMESTAMP('".$start."')";
			$stid = oci_parse($this->connection,$query);
			//oci_bind_by_name($stid, ":start", $start);
		}else if($start == null && $end != null) {
			$query = $query . "TO_TIMESTAMP('".$end."') > DATE_TIME";
			$stid = oci_parse($this->connection,$query);
			//oci_bind_by_name($stid, ":end", $end);
		} else {
			$stid = oci_parse($this->connection,$query);
		}
		
		oci_execute($stid);
		
		if($sentence != null) {
			$to_match = explode(" ",$sentence);
			foreach($to_match as $sen_match) {
				var_dump(filter_var($sen_match, FILTER_SANITIZE_STRING));
				$stid = oci_parse($this->connection,"update temp set score = score+10 where title like '%" . $sen_match."%' ");
				//oci_bind_by_name($stid, ":thingy", "%" . $sen_match . "%");
				oci_execute($stid);
				var_dump(filter_var($sen_match, FILTER_SANITIZE_STRING));
				$stid = oci_parse($this->connection,"update temp set score = score+1 where content like '%" . $sen_match."%' ");
				//oci_bind_by_name($stid, ":thingy", "%" . $sen_match . "%");
				oci_execute($stid);
			}
		}
		
		if($tags != null) {
			$pieces = explode(",", $tags);
			foreach($pieces as $tag_match) {
				//var_dump(filter_var($tag_match, FILTER_SANITIZE_STRING));
		//		echo $tag_match;
				$stid = oci_parse($this->connection,"update temp set score = score+10 where temp.id in (select belongs_to.QUESTION from category join belongs_to on belongs_to.CATEGORY = category.id where category.DESCRIPTION like '%" . $tag_match . "%') ");
				//oci_bind_by_name($stid, ":thingy", "%" . $tag_match . "%");
				
				
				oci_execute($stid);
			}
		}
		
		$stid = oci_parse($this->connection,"select * from temp order by score desc");
		oci_execute($stid);
		while($result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) {
			$return[$i] = $result;
			$i++;
		}
		
		$stid = oci_parse($this->connection,"drop table temp");
		oci_execute($stid);
		
		return $return;
		
	}
	
//$sentence takes string, $tags take array,$start and $end should be formatted like'10-APR-14',$session_user is user id

/*	public function search($sentence = null, $tags = null, $session_user = null, $start = null, $end = null) {
		$i = 0;
		
		$score = 0;
		//echo($session_user);
		$query = "create table temp as select 0 as score, id, title, content from questions";
		
		if($session_user != null || $start != null || $end != null) {
			$query = $query . " where ";
		}
		
		if($session_user != null) {
			$user = intval($session_user);
			$query = $query . "asker = " . $user;
			if($start != null || $end != null) {
				$query = $query . " and ";
			}
		}
		
		if($start != null) {
			//echo($start);
			$start = $this->convert_date($start);
			//echo($start);
		}
		if($end != null) {
			$end = $this->convert_date($end);
		}
		if($start != null && $end != null) {
			$query = $query . "DATE_TIME > TO_TIMESTAMP(:start) and TO_TIMESTAMP(:end) > DATE_TIME";
			$stid = oci_parse($this->connection,$query);
			oci_bind_by_name($stid, ":start", $start);
			oci_bind_by_name($stid, ":end", $end);
		} else if($start != null && $end == null) {
			$query = $query . "DATE_TIME > TO_TIMESTAMP(:start)";
			$stid = oci_parse($this->connection,$query);
			oci_bind_by_name($stid, ":start", $start);
		}else if($start == null && $end != null) {
			$query = $query . "TO_TIMESTAMP(:end) > DATE_TIME";
			$stid = oci_parse($this->connection,$query);
			oci_bind_by_name($stid, ":end", $end);
		} else {
			$stid = oci_parse($this->connection,$query);
		}
		
		oci_execute($stid);
		
		if($sentence != null) {
			$to_match = explode(" ",$sentence);
			foreach($to_match as $sen_match) {
				var_dump(filter_var($sen_match, FILTER_SANITIZE_STRING));
				$stid = oci_parse($this->connection,"update temp set score = score+10 where title like '%" . $sen_match."%' ");
				//oci_bind_by_name($stid, ":thingy", "%" . $sen_match . "%");
				oci_execute($stid);
				var_dump(filter_var($sen_match, FILTER_SANITIZE_STRING));
				$stid = oci_parse($this->connection,"update temp set score = score+1 where content like '%" . $sen_match."%' ");
				//oci_bind_by_name($stid, ":thingy", "%" . $sen_match . "%");
				oci_execute($stid);
			}
		}
		
		if($tags != null) {
			foreach($tags as $tag_match) {
				var_dump(filter_var($tag_match, FILTER_SANITIZE_STRING));
				echo $tag_match;
				$stid = oci_parse($this->connection,"update temp set score = score+10 where temp.id in (select belongs_to.QUESTION from category join belongs_to on belongs_to.CATEGORY = category.id where category.DESCRIPTION like '%" . $tag_match . "%') ");
				//oci_bind_by_name($stid, ":thingy", "%" . $tag_match . "%");
				
				
				oci_execute($stid);
			}
		}
		
		$stid = oci_parse($this->connection,"select * from temp order by score desc");
		oci_execute($stid);
		while($result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) {
			$return[$i] = $result;
			$i++;
		}
		
		$stid = oci_parse($this->connection,"drop table temp");
		oci_execute($stid);
		
		return $return;
		
	}
*/	
	public function convert_date($date) {
		$return = substr($date,8,2);
		$convert = substr($date,5,2);
		$return = $return . "-" . jdmonthname($convert,2);
		$return = $return . "-" . substr($date,0,4);
		return $return;
	}

public function get_user_name_by_id($id){
//	echo($id);
$stid = oci_parse($this->connection,"select user_name from users where id=:id");
oci_bind_by_name($stid, ":id", $id);
oci_execute($stid);

$result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS) ;
//print_r($result);
return $result['USER_NAME'];
}

public function get_id($name){
	$stid = oci_parse($this->connection, "select ID from USERS WHERE USER_NAME =:name");
	oci_bind_by_name($stid, ":name", $name);
	oci_execute($stid);
	$result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS);
	//print_r($result);
	return $result['ID'];
}
public function get_ques_user_ans($replier){
		$i = 0;
		$return=array();
		$stid = oci_parse($this->connection, "select distinct questions.id, questions.title from replies, questions 
								where replier = :replier and replies.REPLY_TO = questions.ID order by questions.ID desc");
		oci_bind_by_name($stid, ":replier", $replier);
		oci_execute($stid);
		while (($result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
			$return[$i] = $result;
			$i++;
		}
		return $return;
	}
public function act_log($session_user) {
		$i = 0;
	//	echo($session_user);
		$user = intval($session_user);
		
		$stid = oci_parse($this->connection,"create view ask as select 'asked a question' as act, DATE_TIME as date_time from questions where asker = " . $user);
		oci_execute($stid);
		$stid = oci_parse($this->connection,"create view reply as select 'answered to a question' as act, DATE_TIME as date_time from REPLIES where replier = " . $user);
		oci_execute($stid);
		$stid = oci_parse($this->connection,"create view get_ans as select 'own question answered' as act, replies.DATE_TIME as date_time from replies join questions on replies.reply_to = questions.ID where asker = " . $user);
		oci_execute($stid);
		
		$stid = oci_parse($this->connection,"select * from (select * from ask union select * from reply union select * from get_ans) order by date_time desc");
		oci_execute($stid);
		while($result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) {
			$return[$i] = $result;
			$i++;
		}
		$stid = oci_parse($this->connection,"drop view ask");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"drop view reply");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"drop view get_ans");
		oci_execute($stid);
		
		return $return;
		
	}
	
	public function authenticate($username, $password) {
		$stid = oci_parse($this->connection,"select id from users where user_name = :username and password = :password");
		
		oci_bind_by_name($stid, ":username", $username);
		oci_bind_by_name($stid, ":password", $password);
		
		oci_execute($stid);
		
		$id = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS);
		
		return $id;
	}
	public function post_question($asker, $title, $content, $tags) {
		$stid = oci_parse($this->connection,"select max(ID) from questions");		
		oci_execute($stid);
		$id = oci_fetch_array($stid,OCI_NUM+OCI_RETURN_NULLS);
		//print_r($id);
		
		$neo_id = $id[0]+1;
		$stid = oci_parse($this->connection,"insert into questions (Asker, Title, Content, Date_Time, ID) values (:asker, :title, :content, CURRENT_TIMESTAMP, :ID)");
		
		oci_bind_by_name($stid, ":asker", $asker);
		oci_bind_by_name($stid, ":title", $title);
		oci_bind_by_name($stid, ":content", $content);
		oci_bind_by_name($stid, ":ID", $neo_id);
		
		
		oci_execute($stid);
		
		$pieces = explode(",", $tags);
		foreach($pieces as $tag){
			$this->tag_question($neo_id, $tag);
		}
		return $neo_id;
	}
	
	//trying to add tags, given the id of question and string of tag, if tag not exist
	// create tag and then give question the tag
	public function tag_question($id,$tag){
		$stid = oci_parse($this->connection, "select id from category where description = :tag");
		oci_bind_by_name($stid,":tag",$tag);
		oci_execute($stid);
		$cid = oci_fetch_array($stid,OCI_NUM+OCI_RETURN_NULLS);
		$neo_id = $cid[0];
		
		if($neo_id == null){
		//we need first insert the tag into category table
			$stid2 = oci_parse($this->connection,"select max(ID) from category");
			oci_execute($stid2);
			$cid = oci_fetch_array($stid2,OCI_NUM+OCI_RETURN_NULLS);
			$neo_id = $cid[0]+1;
			
			$stid2 = oci_parse($this->connection,"insert into category (ID, Description) values (:id, :descrip)");
			oci_bind_by_name($stid2,":id",$neo_id);
			oci_bind_by_name($stid2,":descrip",$tag);
			oci_execute($stid2);
		}
		
		//just insert the relation into belongs_to table
		$stid = oci_parse($this->connection,"insert into belongs_to (Question, Category) values (:id, :tagid)");
		oci_bind_by_name($stid,":id",$id);
		oci_bind_by_name($stid,":tagid",$neo_id);
		oci_execute($stid);
		
		return $neo_id;
	}	
	public function get_profile ($asker) {
		$i = 0;
		
		$stid = oci_parse($this->connection, "select * from users 
												where USER_NAME = '". $asker ."'");
		oci_execute($stid);
		$result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS);
		return $result;
	}
	
		public function get_question_by_user ($asker) {
		$i = 0;
		
		$stid = oci_parse($this->connection,"create view upvote as select count(Voter) as inc_score,Question from Vote_Question where Vote = 1 group by Question");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"create view downvote as select count(Voter) as dec_score,Question from Vote_Question where Vote = 0 group by Question");
		oci_execute($stid);
		$stid = oci_parse($this->connection, "select questions.ID, title, content,date_time, questions.asker, inc_score as voteup, dec_score as votedown 
												from questions left join upvote on questions.ID = upvote.QUESTION 
												left join downvote on questions.ID = downvote.question
												where questions.asker = :asker");
		oci_bind_by_name($stid, ":asker", $asker);
		oci_execute($stid);
		while($result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) {
			$return[$i] = $result;
			$i++;
		}
		
		$stid = oci_parse($this->connection,"drop view upvote");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"drop view downvote");
		oci_execute($stid);
		
		return $return;
	}
	
	public function get_question_by_id ($id) {
	
		$stid = oci_parse($this->connection,"create view upvote as select count(Voter) as inc_score,Question from Vote_Question where Vote = 1 group by Question");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"create view downvote as select count(Voter) as dec_score,Question from Vote_Question where Vote = 0 group by Question");
		oci_execute($stid);
		
		$stid = oci_parse($this->connection,"select questions.ID, title, content,date_time, questions.asker, inc_score as voteup, dec_score as votedown 
												from questions left join upvote on questions.ID = upvote.QUESTION 
												left join downvote on questions.ID = downvote.question
												where questions.ID = :id");
		
		oci_bind_by_name($stid, ":id", $id);	
		oci_execute($stid);
		
		$result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS);
		
		$stid = oci_parse($this->connection,"drop view upvote");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"drop view downvote");
		oci_execute($stid);
		
		return $result;
	}
	
	public function get_reply_by_user ($replier) {
		$i = 0;
		
		$stid = oci_parse($this->connection,"select * from replies where replier = :replier");
		oci_bind_by_name($stid, ":replier", $replier);
		oci_execute($stid);
		
		while (($result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) != false) {
			$return[$i] = $result;
			$i++;
		}
		
		return $return;
	}

public function get_reply_by_question ($id) {

$i = 0;

$stid = oci_parse($this->connection,"create view upvote_reply as select count(Voter) as inc_score,Reply from Vote_Reply where Vote = 1 group by Reply");
oci_execute($stid);
$stid = oci_parse($this->connection,"create view downvote_reply as select count(Voter) as dec_score,Reply from Vote_Reply where Vote = 0 group by Reply");
oci_execute($stid);

$stid = oci_parse($this->connection,"select USER_NAME,DATE_TIME,TEXT,users.ID,inc_score as voteup, dec_score as votedown 
from replies left join users on replies.replier=users.ID 
left join upvote_reply on replies.ID = upvote_reply.reply
left join downvote_reply on replies.ID = downvote_reply.reply
where reply_to = :id");

oci_bind_by_name($stid, ":id", $id);

oci_execute($stid);

$return = array();
while (($result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) != false)
{
$return[$i] = $result;
$i++;
}

$stid = oci_parse($this->connection,"drop view upvote_reply");
oci_execute($stid);
$stid = oci_parse($this->connection,"drop view downvote_reply");
oci_execute($stid);

return $return;
}
	
/*	public function get_reply_by_question ($id) {
	
		$i = 0;
	
		$stid = oci_parse($this->connection,"select * from replies left join users on replies.replier=users.ID where reply_to = :id");
		
		oci_bind_by_name($stid, ":id", $id);
		
		oci_execute($stid);
		
		$return=array();		
		while (($result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) != false)
		{
			$return[$i] = $result;
			$i++;
		}
		
		return $return;
	}
	*/
		public function vote_question($voter,$question,$vote) {
	//	echo($voter);	
		$stid = oci_parse($this->connection,"select * from Vote_Question where Voter = :voter and Question = :question");
		oci_bind_by_name($stid, ":voter", $voter);
		oci_bind_by_name($stid, ":question", $question);
		oci_execute($stid);
		$flag = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS);
		if($flag) {
			if($flag['VOTE']==$vote){
				return false;
			} else {
				$stid = oci_parse($this->connection,"update Vote_Question set Vote = :vote where Voter = :voter and Question = :question");
				oci_bind_by_name($stid, ":voter", $voter);
				oci_bind_by_name($stid, ":question", $question);
				oci_bind_by_name($stid, ":vote", $vote);
				oci_execute($stid);
			}
		} else {
			$stid = oci_parse($this->connection,"insert into Vote_Question (Voter,Question,Vote) values (:voter,:question," . $vote . ")");
			oci_bind_by_name($stid, ":voter", $voter);
			oci_bind_by_name($stid, ":question", $question);
			oci_execute($stid);
		}
		//update score by 10 for the user who ask the question
		$stid = oci_parse($this->connection,"update users set score = score + :vote * 10 where id = (select asker from questions where questions.ID = :qid)");
		oci_bind_by_name($stid, ":qid", $question);
		oci_bind_by_name($stid, ":vote", $vote);
		oci_execute($stid);
				
		return true;	
		//$stid = oci_parse($this->connection,"insert into Vote_Question (Voter,Question,Vote) values");		
	}
	
	public function vote_reply($voter,$reply,$vote) {
		
		$stid = oci_parse($this->connection,"select * from Vote_Reply where Voter = :voter and Reply = :reply");
		oci_bind_by_name($stid, ":voter", $voter);
		oci_bind_by_name($stid, ":reply", $reply);
		oci_execute($stid);
		$flag = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS);
		if($flag) {
			if($flag['VOTE']==$vote){
				return false;
			} else {
				$stid = oci_parse($this->connection,"update Vote_Reply set Vote = :vote where Voter = :voter and Reply = :reply");
				oci_bind_by_name($stid, ":voter", $voter);
				oci_bind_by_name($stid, ":reply", $reply);
				oci_bind_by_name($stid, ":vote", $vote);
				oci_execute($stid);
			}
		} else {
			$stid = oci_parse($this->connection,"insert into Vote_Reply (Voter,Reply,Vote) values (:voter,:reply," . $vote . ")");
			oci_bind_by_name($stid, ":voter", $voter);
			oci_bind_by_name($stid, ":reply", $reply);
			oci_execute($stid);
		}
		
		//add score by 5 for user who make this reply
		$stid = oci_parse($this->connection,"update users set score = score + :vote * 5 where id = (select replier from replies where replies.ID = :rid)");
		oci_bind_by_name($stid, ":rid", $reply);
		oci_bind_by_name($stid,":vote",$vote);
		oci_execute($stid);
		
		return true;	
		//$stid = oci_parse($this->connection,"insert into Vote_Question (Voter,Question,Vote) values");		
	}
	
	public function post_answer ($question,$answer,$replier) {
		$stid = oci_parse($this->connection,"select max(ID) from replies");
		oci_execute($stid);
		$id = oci_fetch_array($stid,OCI_NUM+OCI_RETURN_NULLS);
		//print_r($id);
		$neo_id = $id[0]+1;
		$stid = oci_parse($this->connection,"insert into replies (ID, Text, Date_Time, Replier, Reply_To) values (:id, :answer, CURRENT_TIMESTAMP, :replier, :question)");
		
		oci_bind_by_name($stid, ":id", $neo_id);
		oci_bind_by_name($stid, ":answer", $answer);
		oci_bind_by_name($stid, ":replier", $replier);
		oci_bind_by_name($stid, ":question", $question);
		
		oci_execute($stid);
		
		return $question;
	}
	

	public function get_recent_question () {
		$i = 0;
		
		$stid = oci_parse($this->connection, "select  * from
												(select * from questions
												order by date_time desc)
												where rownum < 21");
		oci_execute($stid);
		while($result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) {
			$return[$i] = $result;
			$i++;
		}
		
		return $return;
	}

	public function get_top_question () {
		$i = 0;
		
		$stid = oci_parse($this->connection,"create view upvote as select count(Voter) as inc_score,Question from Vote_Question where Vote = 1 group by Question");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"create view downvote as select count(Voter) as dec_score,Question from Vote_Question where Vote = 0 group by Question");
		oci_execute($stid);
		$stid = oci_parse($this->connection, "select upvote.Question, title, inc_score - dec_score as vote from upvote join downvote on upvote.QUESTION = downvote.QUESTION join questions on questions.ID = upvote.QUESTION order by vote desc");
		oci_execute($stid);
		while($result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) {
			$return[$i] = $result;
			$i++;
		}
		//print_r($i);
		$stid = oci_parse($this->connection,"drop view upvote");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"drop view downvote");
		oci_execute($stid);
		
		return $return;
	}
	public function leaderboard2 () {
		$i = 0;
		
		$stid = oci_parse($this->connection, "select  * from
												(select * from users
												order by score desc)
												where rownum < 21");
		oci_execute($stid);
		while($result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) {
			$return[$i] = $result;
			$i++;
		}	
		return $return;
	}
	public function leaderboard() {
		$i = 0;
		
		$stid = oci_parse($this->connection,"create view Q_upvote as select count(Voter) as inc_score,Question from Vote_Question where Vote = 1 group by Question");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"create view Q_downvote as select count(Voter) as dec_score,Question from Vote_Question where Vote = 0 group by Question");
		oci_execute($stid);
		$stid = oci_parse($this->connection, "create view Q_totalvote as select Q_upvote.Question, asker, inc_score - dec_score as vote from Q_upvote join Q_downvote on Q_upvote.QUESTION = Q_downvote.QUESTION join questions on questions.ID = Q_upvote.QUESTION order by vote desc");
		oci_execute($stid);
		
		$stid = oci_parse($this->connection,"create view A_upvote as select count(Voter) as inc_score,Reply from Vote_Reply where Vote = 1 group by Reply");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"create view A_downvote as select count(Voter) as dec_score,Reply from Vote_Reply where Vote = 0 group by Reply");
		oci_execute($stid);
		$stid = oci_parse($this->connection, "create view A_totalvote as select A_upvote.Reply, replier, inc_score - dec_score as vote from A_upvote join A_downvote on A_upvote.Reply = A_downvote.Reply join Replies on Replies.ID = A_upvote.Reply order by vote desc");
		oci_execute($stid);
		
		$stid = oci_parse($this->connection, "create view Q_userscore as select sum(vote) as Q_score, asker from Q_totalvote group by ASKER");
		oci_execute($stid);
		$stid = oci_parse($this->connection, "create view A_userscore as select sum(vote) as A_score, replier from A_totalvote group by replier");
		oci_execute($stid);
		
		$stid = oci_parse($this->connection, "select Q_userscore.Q_score + A_userscore.A_score as score, Q_userscore.asker as uzer from Q_userscore join A_userscore on Q_userscore.asker = A_userscore.replier order by score desc");
		oci_execute($stid);
		

		while($result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) {
			$return[$i] = $result;
			$i++;
		}
		
		$stid = oci_parse($this->connection,"drop view Q_upvote");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"drop view Q_downvote");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"drop view Q_totalvote");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"drop view Q_userscore");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"drop view A_upvote");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"drop view A_downvote");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"drop view A_totalvote");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"drop view A_userscore");
		oci_execute($stid);
		
		return $return;
	}
	
	public function add_user($name,$password) {
		$stid = oci_parse($this->connection,"select max(ID) from users");
		oci_execute($stid);
		$id = oci_fetch_array($stid,OCI_NUM+OCI_RETURN_NULLS);
	//	print_r($id);
		$neo_id = $id[0]+1;
		$stid = oci_parse($this->connection,"insert into users (ID, User_Name, Password, Join_Date, User_type,SCORE) values (:id, :name,:password, CURRENT_TIMESTAMP, 0,0)");
		
		oci_bind_by_name($stid, ":id", $neo_id);
		oci_bind_by_name($stid, ":name", $name);
		oci_bind_by_name($stid, ":password", $password);
		
		oci_execute($stid);
		
		return true;
	}
	
	
	
public function recommended_for_you ($session_user) {
		$i = 0;
		//echo($session_user);
		$user = intval($session_user);
		
		$stid = oci_parse($this->connection,"create view raw_stuff as 
												select questions.id as question, 
        										belongs_to.category as tag, 
        										replies.replier as replier 
												from belongs_to join questions on belongs_to.question = questions.id 
                								join replies on replies.reply_to = questions.id");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"create view tag_score as select count(*) as weight, replier, tag from raw_stuff group by replier, tag" );
		oci_execute($stid);
		
		$stid = oci_parse($this->connection,"select sum(weight) as score, question, replier, questions.TITLE
											from tag_score join belongs_to on tag_score.tag = belongs_to.category 
              								join questions on question = questions.id
											where replier = " .$user. " group by question, replier, questions.TITLE order by replier, score desc");
		oci_execute($stid);
		$return=array();
		while($result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) {
			$return[$i] = $result;
		//	echo($return[$i]["TITLE"]);
			$i++;
		}
		$stid = oci_parse($this->connection,"drop view raw_stuff");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"drop view tag_score");
		oci_execute($stid);
		
		return $return;
		
	}	
	//too long; didn't write, lol
	/*public function fetch_quest_by_user($userid) {
		$stid = oci_parse($this->connection,"select count(),asker from questions where asker = :userid and password = :password");
		
		oci_bind_by_name($stid, ":username", $username);
		oci_bind_by_name($stid, ":password", $password);
		
		oci_execute($stid);
		
		$id = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS);
		
		$this->close();
		
		return $id;
	}*/
}
?>