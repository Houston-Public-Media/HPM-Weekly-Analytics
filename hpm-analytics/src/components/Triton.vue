<template>
	<div class="services" id="triton-service">
		<!-- Import each of the global graphing components you set up -->
		<h2 class="text-center">{{ msg }}</h2>
		<div class="row">
			<div id="triton-listen" class="col-12">
				<triton-listeners :chart-data="chartData"></triton-listeners>
			</div>
		</div>
		<div class="row justify-content-center">
			<div id="triton-news-device" class="col-sm-12 col-md-6">
				<triton-news :chart-data="chartData"></triton-news>
			</div>
			<div id="triton-classical-device" class="col-sm-12 col-md-6">
				<triton-classical :chart-data="chartData"></triton-classical>
			</div>
			<div id="triton-mixtape-device" class="col-sm-12 col-md-6">
				<triton-mixtape :chart-data="chartData"></triton-mixtape>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'Triton',
	data () {
		return {
			msg: 'Triton Webcast Metrics'
		}
	},
	props: [
		'chart-data'
	]
}
</script>