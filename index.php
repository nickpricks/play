<?php
  $servername = "localhost";
	$username = "root";
	$password = "root";
	$dbname = "play";
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);

  $ip_list = array('127.0.0.1', '192.168.1.171', '192.168.1.206', '192.168.1.202', '192.168.1.195', '192.168.1.125', '192.168.1.153');
  $ip_names = array('0.0.0.0' => 'All', '127.0.0.1' => 'Funny', '192.168.1.171' => 'Don', '192.168.1.206' => 'Dhaka', '192.168.1.202' => 'Chander', '192.168.1.195' => 'Kachroo', '192.168.1.125' => 'Devender', '192.168.1.153' => 'Hamender');
  $ip = $_SERVER['HTTP_CLIENT_IP']?:($_SERVER['HTTP_X_FORWARDE‌​D_FOR']?:$_SERVER['REMOTE_ADDR']);
	if ($_POST['submit']) {
    if (in_array($ip, $ip_list)) {
    		$mimes	=	array('audio/x-mp3', 'audio/x-mpeg-3', 'audio/mpeg3', 'audio/mpeg', 'audio/mp3');
				if (is_uploaded_file($_FILES['mp3']['tmp_name']) && in_array($_FILES['mp3']['type'], $mimes)) {
	$uploadDir = 'songs/';
					$uploadFile = $uploadDir . basename($_FILES["mp3"]["name"]);
          if (file_exists($uploadFile)) {
          	echo "Don't sumbit Same file again. Otherwise upload blocked Mr. " . $ip_names[$ip]; 
          }
					else {
						if (move_uploaded_file($_FILES['mp3']['tmp_name'], $uploadFile)) {		
								$sql = "INSERT INTO songs (name, author, path, ip, created)
								VALUES ('". basename($uploadFile, '.mp3') ."', '" . $ip_names[$ip] . "', '" . $uploadFile . "', '" . $ip . "', " . time() .")";
								if ($conn->query($sql) === TRUE) {
										echo 'Uploaded MP3! Mr. ' . $ip_names[$ip];
								} 
						}
						else {
								echo 'Error: Could Not Upload MP3! Mr.' . $ip_names[$ip];
						}
					}
				}
				else {
					echo 'Error: Could Not Upload MP3! Invalid File Mr.' . $ip_names[$ip];
				}
    }
		else {
			echo "Sorry Ma'am You are not allowed to upload. Please get permission from admin";
		}
	}

  $audios = '';
  $oursongs = '';
	$i = 0;
	/*foreach(glob('songs/*.*') as $file) {
     $audios .=  '<source src="' . $file . '" type="audio/mpeg" data-album="'. $file .'" data-artist="Mohit Sharma" data-image="http://192.168.1.195/xXx/TEAM%20B%20/IMG_20160817_141911.jpg"/>';
   	 $oursongs .= '<li class="bb-track" data-track="' . $i . '"><a href="javascript:void(0);">' . basename($file, '.mp3') . '</a></li>';
		 $i++;
	} */
	$sql = "SELECT * FROM songs where 1";
  if ($_GET['author'] && $_GET['author'] != 'All') {
  	$sql .= ' and author="' . $_GET['author'] . '"'; 
  }
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
		  // output data of each row
      $i = 0;
		  while($row = $result->fetch_assoc()) {
        $audios .=  '<source src="' . $row['path'] . '" type="audio/mpeg" data-album="'. $row['path'] .'" data-artist="Mohit Sharma" data-image="http://192.168.1.195/xXx/TEAM%20B%20/IMG_20160817_141911.jpg"/>';
				$oursongs .= '<tr><td class="bb-track" data-track="' . $i . '"><a href="javascript:void(0);">' . $row['name'] . '</a></td> <td>' . $row['author'] . '</td><td>' . date('m-d-Y h:i', $row['created']) . '</td></tr>';
				$i++;
		  }
	} else {
		  $oursongs = '<tr><td colspan="3"> No songs uploaded </td></tr>';
	}
	$conn->close();
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Mohit Sharma Presents</title>
    <link rel="stylesheet" href="css/msplayer-minimal.css"/>
		<link rel="stylesheet" href="css/main.css" /> <!-- This is a css file for this page, you can remove it-->
		<link rel="stylesheet" href="css/jquery.equalizer.css" />
		<link rel="stylesheet" type="text/css" href="css/normalize.css" />
		<link rel="stylesheet" type="text/css" href="css/demo.css" />
		<link rel="stylesheet" type="text/css" href="css/component.css" />
		<link href='http://fonts.googleapis.com/css?family=Raleway:200,400,800' rel='stylesheet' type='text/css'>
		<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->

  </head>
  <body>
		<div class="container demo-1">
			<div class="content">
				<div id="large-header" class="large-header">
					<canvas id="demo-canvas"></canvas>
					 <div class="main-container">
    <div class="main-window">
    <?php echo "<h2 style='color:#efcd11;'>Welcome Mr. " . ($ip_names[$ip] ? $ip_names[$ip] : "Unknown !!") . '</h2>'; ?>
    
    <div style="float:left;width:100%;height:100px;">
			<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
					Select Song to upload:
					<input type="file" name="mp3" id="mp3">
					<input type="submit" value="Upload" name="submit">
			</form>
		</div>
		<section id="main_section">
					<div class="song1 equalizer"></div>
          <div class="authors">
		        <h3> Filter By Author: </h3>
						<ul>
		        <?php foreach($ip_names as $key => $value) {
							echo '<li><a href="?author=' . $value . '">' . $value . '</a></li>';
		         } ?>
						</ul>
					</div>
		 </section>
    <div class="bbplayer" style="float:left;width:100%;">
      <span class="bb-rewind"></span>
      <span class="bb-play"></span>
      <span class="bb-forward"></span>
      <div class="playerWindow">
        <div class="bb-track-display">
          <span class="bb-trackTime">--:--</span>
          <span class="bb-trackTitle">&nbsp;</span>
          <span class="bb-trackLength">--:--</span>
        </div>
        <div class="bb-album-display">
          <span class="bb-artist"></span> -
          <span class="bb-album"></span>
        </div>
      </div>
      <!-- <span class="bb-albumCover"></span> -->
      <audio loop id="song1">
				<?php print $audios; ?>
        HTML5 Audio Not Available
      </audio>
      <!--
      <div>Optional debug panel:</div>
      <div class="bb-debug"></div>
      -->
    </div>
		<div class="songslist" style="float:left;width:100%;">
			<table>
        <tr><th>Song</th><th>Author</th><th>Uploaded</th></tr>
				<?php print $oursongs; ?>
      </table>
    </div>
    </div>
		<div class="chat-window">
      <h2>Chit Chat</h2>
			<ul class="chat-list">
      </ul>
      <input type="text" name="chattext" id="chattext" />
      <?php 
 				if ($ip == '192.168.1.195') {
         //echo '<input type="text" name="callto" id="callto" />';
         echo '<button id="start-call">start call</button>';
       }
       ?>
		</div>
    
	</div>
				</div>
     </div>
  </div>
  <script src="js/msplayer.js"></script>
	<script src="js/jquery.min.js"></script>
	<script type="text/javascript" src="js/jquery.reverseorder.js"></script>
	<script type="text/javascript" src="js/jquery.equalizer.js"></script>
	<script>
		$(document).ready(function(){
			$('#song1').equalizer({
				color: "#f2b400",
				color1: '#a94442',
				color2: '#f2b400'
			});
		});
	</script>
	<script src="js/TweenLite.min.js"></script>
	<script src="js/EasePack.min.js"></script>
	<script src="js/rAF.js"></script>
	<script src="js/demo-1.js"></script>
	<script src="js/peer.js"></script>
  <script src="//www.WebRTC-Experiment.com/RecordRTC.js"></script>
	<script>
		var peer = new Peer('<?php print $ip_names[$ip] ? $ip_names[$ip]: "Unknown"; ?>', {key: 'nce2bbcu1ls7nwmi', config: {'iceServers': [
            { url: 'stun:stun1.l.google.com:19302' },
            { url: 'turn:numb.viagenie.ca', credential: 'muazkh', username: 'webrtc@live.com' }
        ]}}); 
		var conn = peer.connect('Funny');
		var messageCount = 0;
		$("#chattext").keyup(function (e) {
				if (e.keyCode == 13) {
		      if (messageCount > 5) {
		      	alert("Go Slow Piche Bhag raha ha koi !!");
		      }
		      else {
						  conn.send({message: $(this).val(), name: '<?php print $ip_names[$ip]; ?>'});
							$('.chat-window ul').append('<li><b>' + '<?php print $ip_names[$ip]; ?>' +':  </b>' + $(this).val() + '<li>');
							$(this).val('');
		          messageCount++;
					}
				}
		});
    peer.on('connection', function(conn) {
			conn.on('data', function(data){
				$('.chat-window ul').append('<li><b>' + data.name +':  </b>' + data.message + '<li>');
				var ReplyBack = peer.connect(data.name);
        ReplyBack.send({message: data.message, name: data.name});
			});
		});

    peer.on('open', function(id) {
			//console.log('My peer ID is: ' + id);
		});

		peer.on('call', onReceiveCall);

		$('#start-call').click(function(){

				console.log('starting call...');
        var to = 'Unknown';
				getAudio(
				    function(MediaStream){

				        console.log('now calling All ');
				        var h1 = peer.call('Hamender', MediaStream);
 								var c1 = peer.call('Chander', MediaStream);
 								var k1 = peer.call('Kachroo', MediaStream);
 								var d1 = peer.call('Devender', MediaStream);
 								var d2 = peer.call('Dhaka', MediaStream);
								//peer.call(to, MediaStream);
                //console.log(call);
				        /*h1.on('stream', onReceiveStream);
 								c1.on('stream', onReceiveStream);
								k1.on('stream', onReceiveStream);
								d1.on('stream', onReceiveStream);
								d2.on('stream', onReceiveStream); */
				    },
				    function(err){
				        console.log('an error occured while getting the audio');
				        console.log(err);
				    }
				);

		});

  function getAudio(successCallback, errorCallback){
    navigator.getUserMedia({
        audio: true,
        video: false
    }, successCallback, errorCallback);
	}
	function onReceiveCall(call){
		  /*getAudio(
		      function(MediaStream){
		          call.answer(MediaStream);
		          console.log('answering call started...');
		      },
		      function(err){
		          console.log('an error occured while getting the audio');
		          console.log(err);
		      }
		  ); */
			call.answer();
		  call.on('stream', onReceiveStream);
	}
	function onReceiveStream(stream){
      console.log("Coming here");
		  var audio = document.querySelector('audio');
		  audio.src = window.URL.createObjectURL(stream);
		  audio.onloadedmetadata = function(e){
		      console.log('now playing the audio');
		      audio.play();
		  } 
	}
  </script>
 </body>
</html>
