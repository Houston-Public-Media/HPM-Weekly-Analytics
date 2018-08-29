<template>
	<div class="services" id="apple-news-service">
		<!-- Import each of the global graphing components you set up -->
		<h2 class="text-center">{{ msg }}</h2>
		<div class="row justify-content-center">
			<div id="apple-demo" class="col-sm-12 col-md-6">
				<apple-demo :chart-data="chartData"></apple-demo>
			</div>
			<div id="apple-age" class="col-sm-12 col-md-6">
				<apple-age :chart-data="chartData"></apple-age>
			</div>
			<div id="apple-reach" class="col-sm-12 col-md-6">
				<apple-reach :chart-data="chartData"></apple-reach>
			</div>
			<div id="apple-engage" class="col-sm-12 col-md-6">
				<apple-engage :chart-data="chartData"></apple-engage>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'AppleNews',
	data () {
		return {
			msg: 'Apple News Analytics'
		}
	},
	props: [
		'chart-data'
	]
}
</script>