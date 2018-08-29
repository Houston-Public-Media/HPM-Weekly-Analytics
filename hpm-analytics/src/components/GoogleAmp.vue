<template>
	<div class="services" id="google-amp-service">
		<!-- Import each of the global graphing components you set up -->
		<h2 class="text-center">{{ msg }}</h2>
		<div class="row">
			<div id="ga-amp-articles" class="col-12">
				<ga-amp-articles :chart-data="chartData" :height="800"></ga-amp-articles>
			</div>
		</div>
		<div class="row">
			<div id="ga-amp-hourly" class="col-sm-12 col-lg-8">
				<ga-amp-hourly :chart-data="chartData"></ga-amp-hourly>
			</div>
			<div id="ga-amp-devices" class="col-sm-12 col-lg-4">
				<ga-amp-devices :chart-data="chartData"></ga-amp-devices>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'GoogleAmp',
	data () {
		return {
			msg: 'Google Analytics (Accelerated Mobile Pages)'
		}
	},
	props: [
		'chart-data'
	]
}
</script>