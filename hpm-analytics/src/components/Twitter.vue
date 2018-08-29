<template>
	<div class="services" id="twitter-service">
		<!-- Import each of the global graphing components you set up -->
		<h2 class="text-center">{{ msg }}</h2>
		<div class="row">
			<div id="twitter-impressions" class="col-12">
				<twitter-impressions :chart-data="chartData"></twitter-impressions>
			</div>
		</div>
		<div class="row">
			<div id="twitter-engagements" class="col-12">
				<twitter-engagements :chart-data="chartData"></twitter-engagements>
			</div>
		</div>
		<div class="row">
			<div id="twitter-acct-tweets" class="col-4">
				<twitter-acct-tweets :chart-data="chartData"></twitter-acct-tweets>
			</div>
			<div id="twitter-acct-impressions" class="col-4">
				<twitter-acct-impressions :chart-data="chartData"></twitter-acct-impressions>
			</div>
			<div id="twitter-acct-engagements" class="col-4">
				<twitter-acct-engagements :chart-data="chartData"></twitter-acct-engagements>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'Twitter',
	data () {
		return {
			msg: 'Twitter Analytics'
		}
	},
	props: [
		'chart-data'
	]
}
</script>
