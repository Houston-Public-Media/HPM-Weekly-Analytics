<template>
	<div class="services" id="google-main-service">
		<!-- Import each of the global graphing components you set up -->
		<h2 class="text-center">{{ msg }}</h2>
		<div class="row">
			<div id="ga-main-articles" class="col-12">
				<ga-main-articles :chart-data="chartData" :height="800"></ga-main-articles>
			</div>
		</div>
		<div class="row">
			<div id="ga-main-hourly" class="col-sm-12 col-lg-8">
				<ga-main-hourly :chart-data="chartData"></ga-main-hourly>
			</div>
			<div id="ga-main-devices" class="col-sm-12 col-lg-4">
				<ga-main-devices :chart-data="chartData"></ga-main-devices>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'GoogleMain',
	data () {
		return {
			msg: 'Google Analytics (Main Account)'
		}
	},
	props: [
		'chart-data'
	]
}
</script>
