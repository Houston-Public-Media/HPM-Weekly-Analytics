<template>
	<div class="services" id="instagram-service">
		<!-- Import each of the global graphing components you set up -->
		<h2 class="text-center">{{ msg }}</h2>
		<div class="row justify-content-center">
			<div id="instagram-stats" class="col-sm-12 col-md-11">
				<instagram-stats :chart-data="chartData"></instagram-stats>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'Instagram',
	data () {
		return {
			msg: 'Instagram Insights'
		}
	},
	props: [
		'chart-data'
	]
}
</script>
