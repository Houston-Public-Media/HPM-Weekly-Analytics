<template>
	<div class="services" id="youtube-service">
		<!-- Import each of the global graphing components you set up -->
		<h2 class="text-center">{{ msg }}</h2>
		<div class="row">
			<div id="yt-views" class="col-12">
				<youtube-views :chart-data="chartData" :height="800"></youtube-views>
			</div>
		</div>
		<div class="row">
			<div id="yt-likes" class="col-12">
				<youtube-engagement :chart-data="chartData" :height="800"></youtube-engagement>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'YouTube',
	data () {
		return {
			msg: 'YouTube Analytics'
		}
	},
	props: [
		'chart-data'
	]
}
</script>
