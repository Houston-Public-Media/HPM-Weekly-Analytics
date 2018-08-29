<template>
	<div class="services" id="facebook-service">
		<!-- Import each of the global graphing components you set up -->
		<h2 class="text-center">{{ msg }}</h2>
		<div class="row justify-content-center">
			<div id="fb-reach" class="col-sm-12 col-md-11">
				<facebook-reach :chart-data="chartData"></facebook-reach>
			</div>
		</div>
		<div class="row">
			<div id="fb-likes" class="col-6">
				<facebook-likes :chart-data="chartData"></facebook-likes>
			</div>
			<div id="fb-reactions" class="col-6">
				<facebook-reactions :chart-data="chartData"></facebook-reactions>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'Facebook',
	data () {
		return {
			msg: 'Facebook Insights'
		}
	},
	props: [
		'chart-data'
	]
}
</script>