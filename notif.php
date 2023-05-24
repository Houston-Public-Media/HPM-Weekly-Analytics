<?php
	function notification_toast( $message, $title, $subtitle, $sound ): void {
		exec('osascript -e \'display notification "' . $message . '" with title "' . $title . '" subtitle "' . $subtitle . '" sound name "' . $sound . '"\'');
	}