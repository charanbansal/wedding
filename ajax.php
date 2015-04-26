<html>
<head>
<title>No content</title>
 <meta property="og:image" content="https://graph.facebook.com/me/picture?type=large" />
</head>
<body>
<?php ob_start(); ?>

 <?php  
 
 require_once('php-sdk/facebook.php');

  $config = array(
    'appId' => '663630510342293',
    'secret' => 'f6721c3a2c2487594ccea17bce0fa992',
    'allowSignedRequest' => false // optional but should be set to false for non-canvas apps
  );

  $facebook = new Facebook($config);
  $user_id = $facebook->getUser();
?>

<?php
include('auth.php'); 
 include('config.php'); 
 ?>

<?php

if(isset($_POST['mode'])) { $mode = addslashes($_POST['mode']); }
if(isset($_POST['visitor'])) { $visitor = addslashes($_POST['visitor']); }
if(isset($_POST['receiver'])) { $receiver = addslashes($_POST['receiver']); }
if(isset($_POST['request'])) { $request = addslashes($_POST['request']); }
if(isset($_POST['sender'])) { $user = addslashes($_POST['sender']); }
if(isset($_POST['sendername'])) { $sendername = addslashes($_POST['sendername']); }
if(isset($_POST['sendto'])) { $sendto = addslashes($_POST['sendto']); }
if(isset($_POST['owner'])) { $owner = addslashes($_POST['owner']); }
if(isset($_POST['accepter'])) { $accepter = addslashes($_POST['accepter']); }
if(isset($_POST['requester'])) { $requester = addslashes($_POST['requester']); }
if(isset($_POST['test_owner_id'])) { $test_owner_id = addslashes($_POST['test_owner_id']); }
if(isset($_POST['test_requester_id'])) { $test_requester_id = addslashes($_POST['test_requester_id']); }
if(isset($_POST['pid'])) { $pid = addslashes($_POST['pid']); }
if(isset($_POST['board'])) { $board = addslashes($_POST['board']); }
if(isset($_POST['follower'])) { $follower = addslashes($_POST['follower']); }
if(isset($_POST['unfollower'])) { $unfollower = addslashes($_POST['unfollower']); }
if(isset($_POST['unfollow_board'])) { $unfollow_board = addslashes($_POST['unfollow_board']); }
if(isset($_POST['testimonial'])) { $testimonial_to_vote = addslashes($_POST['testimonial']); }
if(isset($_POST['voter'])) { $voter = addslashes($_POST['voter']); }
if(isset($_POST['videoid'])) { $videoid = addslashes($_POST['videoid']); }




switch($mode)
{

     case 'follow_request':
    
	   if($user_id) {

      // We have a user ID, so probably a logged in user.
      // If not, we'll get an exception, which we handle below.
      try {
        $ret_obj = $facebook->api('/me/feed', 'POST',
                                    array(
                                      'link' => 'http://scribzoo.com/board.php?board='.$board,
                                      'message' => 'I have just followed board - '.$board.' on scribzoo'
                                 ));
        echo '<pre>Post ID: ' . $ret_obj['id'] . '</pre>';

        // Give the user a logout link 
        echo '<br /><a href="' . $facebook->getLogoutUrl() . '">logout</a>';
      } catch(FacebookApiException $e) {
        // If the user is logged out, you can have a 
        // user ID even though the access token is invalid.
        // In this case, we'll get an exception, so we'll
        // just ask the user to login again here.
        $login_url = $facebook->getLoginUrl( array(
                       'scope' => 'publish_stream'
                       )); 
        echo 'Please <a href="' . $login_url . '">login.</a>';
        error_log($e->getType());
        error_log($e->getMessage());
      }   
    } else {

      // No user, so print a link for the user to login
      // To post to a user's wall, we need publish_stream permission
      // We'll use the current URL as the redirect_uri, so we don't
      // need to specify it here.
      $login_url = $facebook->getLoginUrl( array( 'scope' => 'publish_stream' ) );
      echo 'Please <a href="' . $login_url . '">login.</a>';

    } 

	
	$qry_follow = "insert into board_follow set board = '$board', follower = '$follower', followed_on = NOW()"; 
	 $insert_follow = mysql_query($qry_follow);
	 
	 $qry_select_board = mysql_query("select testimonial_id from board_pin where board_name = '$board'");
	 while($result_select_board = mysql_fetch_array($qry_select_board))
	 {
	 $pid = $result_select_board['testimonial_id'];
	 
	$qry_add_stream = mysql_query("UPDATE posts SET stream_id = CONCAT( stream_id, ',$follower,') where pid = '$pid'");
	
	}
	
	break;

    case 'unfollow_request':
    
	$qry_unfollow = "delete from board_follow where board = '$unfollow_board' and follower = '$unfollower'";
	 $delete_unfollow = mysql_query($qry_unfollow);
	
	break;





   case 'insertrequest':
     $sel1 = "select id from matches where sender = '$visitor' and receiver = '$receiver'";   
     $qry1 = mysql_query($sel1);
	 $count = mysql_num_rows($qry1);
	 
	 if($count == '0'){
	  $sel = "insert into matches set sender = '$visitor', receiver = '$receiver', request = '$request'";
	 $qry = mysql_query($sel);
	  
	 }else{
	 $update = "update matches set sender = '$visitor', receiver = '$receiver', request = '$request'";
	 $update_qry = mysql_query($update);
	 
	 }
	
	 
	  
	  
	break;
	 
	 case 'insertvideoid':
    
	  $sel = "insert into visits set home_page = '$videoid'";
	 $qry = mysql_query($sel);
	
	  
	break; 
	
	 case 'fetchconvo':
	
	?> 
   <div class="row-fluid sortable">	
				<div class="box span12">
					<div class="box-header well" data-original-title>
					
						<h2><i class="icon-user"></i> <?php echo $sendername ?></h2>
						<div class="box-icon">
							<a href="#" class="btn btn-setting btn-round"><i class="icon-cog"></i></a>
							<a href="#" class="btn btn-minimize btn-round"><i class="icon-chevron-up"></i></a>
							<a href="#" class="btn btn-close btn-round"><i class="icon-remove"></i></a>
						</div>
					</div>
					<div class="box-content">
						<table class="table table-bordered table-striped table-condensed" id="convo">
							  <thead>
							 <tr>
							<form action="" method="post" >
							<input type="hidden" id="sender" name="sender" value="<?php echo $sendto ?>" />
							<input type="hidden" id="receiver" name="receiver" value="<?php echo $user ?>" />
							<textarea placeholder="Write your message here" rows="1" name="message" id="replymessage" style="height:40px; width:700px;"></textarea>
						
							  <button type="submit" class="btn btn-primary" value="send" name="send">send</button>
							 </form>
						
							 </tr>
								  <tr>
									  <th style="width:100px">sent on</th>
									   <th>from</th>
									  <th>message </th>
									                                           
								  </tr>
							  </thead>   
							   <?php 
							  $qry_ok = mysql_query("update tbl_message set read_status = 'yes' where sent_to = '$sendto' and sent_from = '$user'"); 
							  
							  
						 $sel = "select * from tbl_message where (sent_to = '$user' and sent_from = '$sendto') or (sent_from = '$user' and sent_to = '$sendto')  order by sent_on desc ";
                               $qry = mysql_query($sel);
							  while($result = mysql_fetch_array($qry))
							  {
							  ?>
							  <tbody>
							  
								<tr>
									
									<td class="center"><?php echo $result['sent_on'];  ?></td>
									<td><?php if($result['sent_from'] == $user)
									 { 
									 echo $sendername;
									  } else 
									  { echo "you"; } ?></td>
									<td><?php echo $result['message']; ?></td>
									                                    
								</tr>
								                        
							  </tbody>
							   <?php } ?>      
						 </table>  
						      
					</div>
				</div><!--/span-->
			</div>
<?php	  
	  
	break; 
     case 'clearnoti':
    
	//echo "update tbl_noti set seen = 'yes' where owner = '$owner'"; exit;
	 $qry = mysql_query("update tbl_noti set seen = 'yes' where owner = '$owner'");
	  header('Location: /stream.php');
	  
	break;
	 case 'connect_request':
    $qry_connect = mysql_query("insert into tbl_connect set accepter = '$accepter', requester = '$requester', scribzoo = '0', connection_time = NOW()");
	//echo "update tbl_noti set seen = 'yes' where owner = '$owner'"; exit;
	 $qry = mysql_query("insert into tbl_noti set owner = '$accepter', subject = 'new connection request', link_to='connections.php', seen = 'no'");
	 //send the email
	 $qry_mail_request = mysql_query("select login,firstname,lastname,user from member where m_id = '$requester'");
	 $result_mail_request = mysql_fetch_array($qry_mail_request);
	 $semail = $result_mail_request['login'];
	 $semail_encode = base64_encode($semail);
	$user = $result_mail_request['user'];
	$sname = $result_mail_request['firstname'].' '.$result_mail_request['lastname'];
	 $qry_mail = mysql_query("select login,firstname from member where m_id = '$accepter'");
	 $result_mail = mysql_fetch_array($qry_mail);
		$to = $result_mail['login'];
		$fname = $result_mail['firstname'];
		
		$subject= "New Connection request by $sname on scribzoo";
		$from = "notification@scribzoo.com";
		
                $headers = "From: " . strip_tags($from) . "\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";   
                
                $body = "<html><body>";
                $body .= "<div id='nav-bar' style='min-height: 40px;padding-left: 20px;padding-right: 20px;background-color: #2D3954;'><a class='brand' style='float: left;display: block;padding: 0px 20px 12px;margin-left: -20px;font-size: 36px;font-weight: 200;line-height: 1;color: #f5f5f5;text-transform: lowercase;font-family:calibri'>Scribzoo</a>
	                   </div>
					   <div>
	<div align='left' style='width:20%;float:left'><img src='https://graph.facebook.com/$user/picture?type=small' style='border-radius:150px 150px 150px 150px; margin-left:5px' /></div>
	<div align='right'><p style='font-family:calibri;text-align:left'>Hi  $fname,</p><p style='text-size:14px;text-align:left'>I'd like to connect with you on scribzoo</br></p></div>
	<div align='right'><h2 style='font-family:calibri;text-align:left'><a href='http://scribzoo.com/profile.php?user=$semail_encode'>$sname</a></h2></div>
	<div align='center'><button style='font-family:calibri;float:left; height:60px'><a href='http://scribzoo.com'>Click to accept</a></button></div>
	</div>
	
	<br/><br/>
	<div id='nav-bar' style='min-height: 40px;margin-top:40px;padding-left: 20px;padding-right: 20px;background-color: #2D3954;'><p style='text-align:center; color:#f5f5f5; font-family:calibri'>Team Scribzoo | website : www.scribzoo.com</p></div>
	
	 ";
                $body .= '</body></html>';
		
	
		
	
        
             

		mail($to, $subject, $body,$headers);
	 
	  header('Location: /stream.php');
	  
	break;   
	 case 'unconnect_request':
    $qry_connect = mysql_query("delete from tbl_connect where (accepter = '$accepter' and requester = '$requester') or (requester = '$accepter' and accepter = '$requester')");
	
	  
	break; 
	 case 'connect_approve':
	
    $qry_connect = mysql_query("update tbl_connect set scribzoo = '1' where accepter = '$accepter' and requester = '$requester'");
	//echo "update tbl_noti set seen = 'yes' where owner = '$owner'"; exit;
	
	 $qry = mysql_query("insert into tbl_noti set owner = '$requester', subject = 'connection confirmed', link_to='connections.php', seen = 'no', field_time = NOW()");
	 
	  //send the email
	 $qry_mail = mysql_query("select login,firstname from member where m_id = '$requester'");
	 $result_mail = mysql_fetch_array($qry_mail);
		$to = $result_mail['login'];
		$fname = $result_mail['firstname'];
		$subject= "connection confirmed";
		$from = "Scribzoo@scribzoo.com";
		
                $headers = "From: " . strip_tags($from) . "\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";   
                
                $body = "<html><body>";
                $body .= "<h1>Hi,  $fname </h1><h2 style='font-size:13px '>  Your connection request has been confirmed.  <br/> <a href='www.scribzoo.com/connections.php'>View here</a><br/>Scribzoo team</h2>";
                $body .= '</body></html>';
	 
	  header('Location: /stream.php');
	  
	break;   
	
	 case 'view_testimonial':
	$qry_v_t = mysql_query("select firstname,lastname from member where m_id = '$test_requester_id'");
	$result_v_t = mysql_fetch_array($qry_v_t);
    $requester_name = $result_v_t['firstname'].' '.$result_v_t['lastname']; 
	
	 $qry = mysql_query("insert into tbl_noti set owner = '$test_owner_id', subject = '$requester_name', verb =' asked to see a testimonial', link_to='testimonial.php?id=$pid&request=approval&stalker_id=$test_requester_id&stalker_name=$requester_name', seen = 'no', field_time = NOW()");
	  header('Location: /stream.php');
	  
	break;   
	
	 case 'approve_view_req':
	 $qry_connect = mysql_query("UPDATE posts SET allowed = CONCAT(allowed,',$test_requester_id') WHERE pid = '$pid'");
	
	 $qry = mysql_query("insert into tbl_noti set owner = '$test_requester_id', verb =' request to view the testimonial has been approved', link_to='testimonial.php?id=$pid', seen = 'no', field_time = NOW()");
	  header('Location: /stream.php');
	  
	break;
	
	 case 'vote_up':
	 
	
	 
    $qry_voteup = mysql_query("UPDATE posts SET vote = vote+1 WHERE pid = '$testimonial_to_vote'");
	$qry_track_vote = mysql_query("Insert into track_votes set voter = '$voter', testimonial_id = '$testimonial_to_vote', update_time = NOW()");
	
	$result_select_owner = mysql_fetch_array(mysql_query("select m_id,login,firstname from member where login in (select s_email from posts where pid = '$testimonial_to_vote')"));
	$notify_sender = $result_select_owner['m_id'];
	$email_sender = $result_select_owner['login'];
	$firstname_sender = $result_select_owner['firstname'];
	
	$result_select_voter = mysql_fetch_array(mysql_query("select firstname,lastname,user from member where m_id = '$voter'"));
	$voter_name = $result_select_voter['firstname'].' '.$result_select_voter['lastname'];
	$fb_user = $result_select_voter['user'];

	
	
	
	 $qry_notify = mysql_query("insert into tbl_noti set owner = '$notify_sender', verb = ' $voter_name voted up your testimonial', link_to='testimonial.php?id=$testimonial_to_vote', seen = 'no', field_time = NOW()");
	 
	 //fb status update
	  if($user_id) {

      // We have a user ID, so probably a logged in user.
      // If not, we'll get an exception, which we handle below.
      try {
        $ret_obj = $facebook->api('/me/feed', 'POST',
                                    array(
                                      'link' => 'http://scribzoo.com/testimonial.php?id='.$testimonial_to_vote,
                                      'message' => 'I have just upvoted a testimonial on scribzoo'
                                 ));
        echo '<pre>Post ID: ' . $ret_obj['id'] . '</pre>';

        // Give the user a logout link 
        echo '<br /><a href="' . $facebook->getLogoutUrl() . '">logout</a>';
      } catch(FacebookApiException $e) {
        // If the user is logged out, you can have a 
        // user ID even though the access token is invalid.
        // In this case, we'll get an exception, so we'll
        // just ask the user to login again here.
        $login_url = $facebook->getLoginUrl( array(
                       'scope' => 'publish_stream'
                       )); 
        echo 'Please <a href="' . $login_url . '">login.</a>';
        error_log($e->getType());
        error_log($e->getMessage());
      }   
    } else {

      // No user, so print a link for the user to login
      // To post to a user's wall, we need publish_stream permission
      // We'll use the current URL as the redirect_uri, so we don't
      // need to specify it here.
      $login_url = $facebook->getLoginUrl( array( 'scope' => 'publish_stream' ) );
      echo 'Please <a href="' . $login_url . '">login.</a>';

    } 
	 
	 //////////
	 
	 $to = $email_sender;
		$fname = $firstname_sender;
		
		$subject= "$voter_name voted up your testimonial on scribzoo";
		$from = "notification@scribzoo.com";
		
                $headers = "From: " . strip_tags($from) . "\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";   
                
                $body = "<html><body>";
                $body .= "<div id='nav-bar' style='min-height: 40px;padding-left: 20px;padding-right: 20px;background-color: #2D3954;'><a class='brand' style='float: left;display: block;padding: 0px 20px 12px;margin-left: -20px;font-size: 36px;font-weight: 200;line-height: 1;color: #f5f5f5;text-transform: lowercase;font-family:calibri'>Scribzoo</a>
	                   </div>
					   <div>
	<div align='left' style='width:20%;float:left'><img src='https://graph.facebook.com/$fb_user/picture?type=small' style='border-radius:150px 150px 150px 150px; margin-left:5px' /></div>
	<div align='right'><p style='font-family:calibri;text-align:left'>Hi  $fname,</p><p style='text-size:14px;text-align:left'>$voter_name upvoted your testimonial on scribzoo</br></p></div>
	
	<div align='center'><button style='font-family:calibri;float:left; height:60px'><a href='http://scribzoo.com/testimonial.php?id=$testimonial_to_vote'>Click to see</a></button></div>
	</div>
	
	<br/><br/>
	<div id='nav-bar' style='min-height: 40px;margin-top:40px;padding-left: 20px;padding-right: 20px;background-color: #2D3954;'><p style='text-align:center; color:#f5f5f5; font-family:calibri'>Team Scribzoo | website : www.scribzoo.com</p></div>
	
	 ";
                $body .= '</body></html>';
		
	
		
	
        
             

		mail($to, $subject, $body,$headers);
		
		

	  
	break; 
	
	 case 'unvote':
	
    $qry_voteup = mysql_query("UPDATE posts SET vote = vote-1 WHERE pid = '$testimonial_to_vote'");
	$qry_track_vote = mysql_query("delete from track_votes where voter = '$voter' and testimonial_id = '$testimonial_to_vote'");

	  
	break; 
	  
}


?>

</body>
</html>