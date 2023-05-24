<?php
	const ESC_SEQ = "\x1b[";
	const RESET_ALL = ESC_SEQ . "0m";
	const RESET_BOLD = ESC_SEQ . "21m";
	const RESET_UL = ESC_SEQ . "24m";
	
	# Foreground colours
	const FG_BLACK = ESC_SEQ . "30;";
	const FG_RED = ESC_SEQ . "31;";
	const FG_GREEN = ESC_SEQ . "32;";
	const FG_YELLOW = ESC_SEQ . "33;";
	const FG_BLUE = ESC_SEQ . "34;";
	const FG_MAGENTA = ESC_SEQ . "35;";
	const FG_CYAN = ESC_SEQ . "36;";
	const FG_WHITE = ESC_SEQ . "37;";
	const FG_BR_BLACK = ESC_SEQ . "90;";
	const FG_BR_RED = ESC_SEQ . "91;";
	const FG_BR_GREEN = ESC_SEQ . "92;";
	const FG_BR_YELLOW = ESC_SEQ . "93;";
	const FG_BR_BLUE = ESC_SEQ . "94;";
	const FG_BR_MAGENTA = ESC_SEQ . "95;";
	const FG_BR_CYAN = ESC_SEQ . "96;";
	const FG_BR_WHITE = ESC_SEQ . "97;";
	
	# Background colours (optional)
	const BG_BLACK = "40;";
	const BG_RED = "41;";
	const BG_GREEN = "42;";
	const BG_YELLOW = "43;";
	const BG_BLUE = "44;";
	const BG_MAGENTA = "45;";
	const BG_CYAN = "46;";
	const BG_WHITE = "47;";
	
	# Font styles
	const FS_REG = "0m";
	const FS_BOLD = "1m";
	const FS_UL = "4m";