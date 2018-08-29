<?php
	$ESC_SEQ="\x1b[";
	$RESET_ALL=$ESC_SEQ."0m";
	$RESET_BOLD=$ESC_SEQ."21m";
	$RESET_UL=$ESC_SEQ."24m";
	
	# Foreground colours
	$FG_BLACK=$ESC_SEQ."30;";
	$FG_RED=$ESC_SEQ."31;";
	$FG_GREEN=$ESC_SEQ."32;";
	$FG_YELLOW=$ESC_SEQ."33;";
	$FG_BLUE=$ESC_SEQ."34;";
	$FG_MAGENTA=$ESC_SEQ."35;";
	$FG_CYAN=$ESC_SEQ."36;";
	$FG_WHITE=$ESC_SEQ."37;";
	$FG_BR_BLACK=$ESC_SEQ."90;";
	$FG_BR_RED=$ESC_SEQ."91;";
	$FG_BR_GREEN=$ESC_SEQ."92;";
	$FG_BR_YELLOW=$ESC_SEQ."93;";
	$FG_BR_BLUE=$ESC_SEQ."94;";
	$FG_BR_MAGENTA=$ESC_SEQ."95;";
	$FG_BR_CYAN=$ESC_SEQ."96;";
	$FG_BR_WHITE=$ESC_SEQ."97;";
	
	# Background colours (optional)
	$BG_BLACK="40;";
	$BG_RED="41;";
	$BG_GREEN="42;";
	$BG_YELLOW="43;";
	$BG_BLUE="44;";
	$BG_MAGENTA="45;";
	$BG_CYAN="46;";
	$BG_WHITE="47;";
	
	# Font styles
	$FS_REG="0m";
	$FS_BOLD="1m";
	$FS_UL="4m";
?>