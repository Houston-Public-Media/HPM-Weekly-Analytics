<?php
	function notification_toast( $message, $title, $subtitle, $sound ) {
		exec('osascript -e \'display notification "'.$message.'" with title "'.$title.'" subtitle "'.$subtitle.'" sound name "'.$sound.'"\'');
	}
?>