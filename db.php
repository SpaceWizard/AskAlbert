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
	
	public function authenticate($username, $password) {
		$stid = oci_parse($this->connection,"select id from users where user_name = :username and password = :password");
		
		oci_bind_by_name($stid, ":username", $username);
		oci_bind_by_name($stid, ":password", $password);
		
		oci_execute($stid);
		
		$id = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS);
		
		return $id;
	}
	
	public function post_question($asker, $title, $content) {
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
		$result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS); 
			
		
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
	
		$stid = oci_parse($this->connection,"select * from replies left join users on replies.replier=users.ID where reply_to = :id");
		
		oci_bind_by_name($stid, ":id", $id);
		
		oci_execute($stid);
		
		
		while (($result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) != false)
		{
			$return[$i] = $result;
			$i++;
		}
		
		return $return;
	}
	
	public function vote_question($voter,$question,$vote) {
		
		$stid = oci_parse($this->connection,"select * from Vote_Question where Voter = :voter and Question = :question");
		oci_bind_by_name($stid, ":voter", $voter);
		oci_bind_by_name($stid, ":question", $question);
		oci_execute($stid);
		if(oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) {
			return false;
		}
		
		$stid = oci_parse($this->connection,"insert into Vote_Question (Voter,Question,Vote) values (:voter,:question," . $vote . ")");
		oci_bind_by_name($stid, ":voter", $voter);
		oci_bind_by_name($stid, ":question", $question);
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
		print_r($id);
		$neo_id = $id[0]+1;
		$stid = oci_parse($this->connection,"insert into users (ID, User_Name, Password, Join_Date, User_type) values (:id, :name,:password, CURRENT_TIMESTAMP, 0)");
		
		oci_bind_by_name($stid, ":id", $neo_id);
		oci_bind_by_name($stid, ":name", $name);
		oci_bind_by_name($stid, ":password", $password);
		
		oci_execute($stid);
		
		return true;
	}
	
	public function act_log($session_user) {
		$i = 0;
		
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
	
	public function recommended_for_you ($session_user) {
		$i = 0;
		
		$user = intval($session_user);
		
		$stid = oci_parse($this->connection,"create view raw_stuff as select questions.id as question, belongs_to.category as tag, replies.replier as replier from belongs_to join questions on belongs_to.question = questions.id join replies on replies.reply_to = questions.id");
		oci_execute($stid);
		$stid = oci_parse($this->connection,"create view tag_score as select count(*) as weight, replier, tag from raw_stuff group by replier, tag" );
		oci_execute($stid);
		
		$stid = oci_parse($this->connection,"select sum(weight) as score, question, replier from tag_score join belongs_to on tag_score.tag = belongs_to.category where replier = " . $user . " group by question,replier order by replier, score desc");
		oci_execute($stid);
		while($result = oci_fetch_array($stid,OCI_ASSOC+OCI_RETURN_NULLS)) {
			$return[$i] = $result;
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