<template>
	<div id="app">
		<!-- Main template for the app. Each service is saved as it's own component -->
		<header class="container-fluid">
			<div class="row align-items-center">
				<div class="col-md-12 col-lg-5">
					<h3>HPM Analytics Report</h3>
				</div>
				<div class="col-md-12 col-lg-7">
					<div class="row align-items-center">
						<label for="weekSelect" class="col-md-4 col-lg-6 text-right">Week Select:</label>
						<div class="col-md-8 col-lg-6">
							<!--
								The reports.json data is used to populate this dropdown, which is used to switch between datasets
							-->
							<select class="form-control" v-model="week">
								<option v-for="(option, index) in options" :key="index" :value="option.value">
									{{ option.text }}
								</option>
							</select>
						</div>
					</div>
				</div>
			</div>
		</header>
		<div class="container-fluid body-wrap">
			<div id="tab" class="row">
				<!--
					If you modified the PHP script to not pull one or more of these services,
					don't forget to remove their entries here
				-->
				<div v-on:click="updateTab('overall-totals')" id="overall-tab" class="col-sm-3 col-lg overall tab-active">Overall</div>
				<div v-on:click="updateTab('google-main')" id="google-main-tab" class="col-sm-3 col-lg google">Google (Main)</div>
				<div v-on:click="updateTab('google-amp')" id="google-amp-tab" class="col-sm-3 col-lg google">Google (AMP)</div>
				<div v-on:click="updateTab('facebook')" id="facebook-tab" class="col-sm-3 col-lg facebook">Facebook</div>
				<div v-on:click="updateTab('instagram')" id="instagram-tab" class="col-sm-3 col-lg instagram">Instagram</div>
				<div v-on:click="updateTab('twitter')" id="twitter-tab" class="col-sm-3 col-lg twitter">Twitter</div>
				<div v-on:click="updateTab('triton')" id="triton-tab" class="col-sm-3 col-lg triton">Triton</div>
				<div v-on:click="updateTab('youtube')" id="youtube-tab" class="col-sm-3 col-lg youtube">YouTube</div>
				<div v-on:click="updateTab('apple-news')" id="apple-news-tab" class="col-sm-3 col-lg apple">Apple News</div>
			</div>
			<div id="chart-wrap">
				<!--
					And here too
				-->
				<Overall :chart-data="chartData"></Overall>
				<GoogleMain :chart-data="chartData"></GoogleMain>
				<GoogleAmp :chart-data="chartData"></GoogleAmp>
				<Facebook :chart-data="chartData"></Facebook>
				<Instagram :chart-data="chartData"></Instagram>
				<Twitter :chart-data="chartData"></Twitter>
				<Triton :chart-data="chartData"></Triton>
				<YouTube :chart-data="chartData"></YouTube>
				<AppleNews :chart-data="chartData"></AppleNews>
			</div>
		</div>
	</div>
</template>

<script>
// Remove the entry here too, and in the "components:" section below
import $ from 'jquery'
import Overall from '@/components/Overall'
import GoogleMain from '@/components/GoogleMain'
import GoogleAmp from '@/components/GoogleAmp'
import Facebook from '@/components/Facebook'
import Instagram from '@/components/Instagram'
import Twitter from '@/components/Twitter'
import Triton from '@/components/Triton'
import YouTube from '@/components/YouTube'
import AppleNews from '@/components/AppleNews'
export default {
	name: 'App',
	props: [
		'options', 'week-selected', 'chart-data'
	],
	components: { Overall, GoogleMain, GoogleAmp, Facebook, Twitter, Instagram, Triton, YouTube, AppleNews },
	data() {
		return {
			week: this.weekSelected
		}
	},
	watch: {
		week: function (newWeek, oldWeek) {
			// when the dropdown is changed, change the weekSelected data in $root
			if (oldWeek !== newWeek) {
				this.$root.weekSelected = this.week;
				var root = this.$root;
				// Pull the data for the new week and overwrite chartData with it
				$.getJSON("https://cdn.hpm.io/assets/analytics/"+this.week+".json").always(function(data){
					root.chartData = data;
				});
			}
		}
	},
	methods: {
		updateTab: function(service) {
			// Helper function for switching tabs
			if ($('#'+service+'-tab').hasClass('tab-active')) {
				return false;
			} else  {
				$('.services').removeClass('service-active');
				$('#tab div').removeClass('tab-active');
				$('#'+service+'-tab').addClass('tab-active');
				$('#'+service+'-service').addClass('service-active');
			}
		}
	}
}
</script>

<style>
html {
	height: 100%;
	-webkit-box-sizing: border-box;
	-moz-box-sizing: border-box;
	box-sizing: border-box;
	width: 100%;
}
body {
	position: relative;
	min-height: 100%;
}
header,
footer {
	padding: 0 1em;
	background: rgb(25,25,25);
	z-index: 9999;
}
header {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	width: 100%;
}
footer {
	position: fixed;
	bottom: 0;
	left: 0;
	right: 0;
	width: 100%;
}
header h3 {
	color: white;
	margin: 0;
}
footer p {
	color: white;
	margin: 0;
	padding: 0.25em 0;
}
header div {
	padding: 0.125em 0;
}
form {
	margin: 2em 0;
}
.form-row {
	padding-top: 1em;
	padding-bottom: 1em;
}
label {
	font-weight: bolder;
	font-size: 150%;
}
header label {
	color: white;
	font-size: 100%;
	padding: 0;
	margin: 0;
}
label.form-check-label {
	font-weight: normal;
	font-size: 100%;
}
.alert {
	margin-bottom: 0 !important;
}
.body-wrap {
	padding-top: 9em;
	padding-bottom: 5em;
}
#tab {
	padding: 0 1em 2em;
}
#tab div {
	padding: 0.25em;
	text-align: center;
	margin: 0.5em 0;
}
#tab div:hover {
	opacity: 0.75;
	cursor: pointer;
}
#tab div.google {
	background-color: rgba(219,68,55,0.2);
	border-top: 2px solid rgba(219,68,55,1);
}
#tab div.facebook {
	background-color: rgba(59,89,152,0.2);
	border-top: 2px solid rgba(59,89,152,1);
}
#tab div.instagram {
	background-color: rgba(138,58,185,0.2);
	border-top: 2px solid rgba(138,58,185,1);
}
#tab div.twitter {
	background-color: rgba(29,161,242,0.2);
	border-top: 2px solid rgba(29,161,242,1);
}
#tab div.triton {
	background-color: rgba(5,106,178,0.2);
	border-top: 2px solid rgba(5,106,178,1);
}
#tab div.youtube {
	background-color: rgba(255,0,0,0.2);
	border-top: 2px solid rgba(255,0,0,1);
}
#tab div.apple,
#tab div.overall {
	background-color: rgba(128,128,128,0.2);
	border-top: 2px solid rgba(128,128,128,1);
}
#tab div.tab-active {
	font-weight: bolder;
	color: white;
}
#tab div.tab-active.google {
	background-color: rgba(219,68,55,0.75);
}
#tab div.tab-active.facebook {
	background-color: rgba(59,89,152,0.75);
}
#tab div.tab-active.instagram {
	background-color: rgba(138,58,185,0.75);
}
#tab div.tab-active.twitter {
	background-color: rgba(29,161,242,0.75);
}
#tab div.tab-active.triton {
	background-color: rgba(5,106,178,0.75);
}
#tab div.tab-active.youtube {
	background-color: rgba(255,0,0,0.75);
}
#tab div.tab-active.apple,
#tab div.tab-active.overall {
	background-color: rgba(128,128,128,0.75);
}
#chart-wrap {
	position: relative;
	min-height: 20em;
}
#chart-wrap .services {
	width: 100%;
	position: absolute;
	visibility: hidden;
	top: 0;
	margin-bottom: 2em;
}
#chart-wrap .services.service-active {
	visibility: visible;
}
#chart-wrap .row div {
	margin-bottom: 1em;
}
@media (min-width: 576px) {
	.body-wrap {
		padding-top: 7em;
	}
	#tab div {
		border-left: 2px solid white;
		border-right: 2px solid white;
	}
}
@media (min-width: 992px) {
	.body-wrap {
		padding-top: 5em;
	}
}
</style>
