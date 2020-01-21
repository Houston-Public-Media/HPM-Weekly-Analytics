<template>
	<div class="services service-active" id="overall-totals-service">
		<!-- Import each of the global graphing components you set up -->
		<h2 class="text-center">{{ msg }}</h2>
		<div class="row">
			<div id="overall-totals" class="col-12">
				<table class="table table-striped">
					<thead>
						<tr>
							<th scope="col">Segment</th>
							<th scope="col">Service</th>
							<th scope="col" colspan="2">Total</th>
						</tr>
					</thead>
					<tbody v-html="renderPods(chartData)"></tbody>
				</table>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'Overall',
	data () {
		return {
			msg: 'Overall Totals'
		}
	},
	props: [
		'chart-data'
	],
	methods: {
		renderPods: function(chartData) {
			var output = "<tr>" +
							"<td colspan=\"2\"><strong>Website</strong></td>" +
							"<td><strong>Users</strong></td>" +
							"<td></td>" +
						"</tr>" +
						"<tr>" +
							"<td></td>" +
							"<td>" + chartData['overall-totals']['ga-main']['name'] + "</td>" +
							"<td>" + chartData['overall-totals']['ga-main']['data'] + "</td>" +
							"<td></td>" +
						"</tr>" +
						"<tr>" +
							"<td></td>" +
							"<td>" + chartData['overall-totals']['ga-amp']['name'] + "</td>" +
							"<td>" + chartData['overall-totals']['ga-amp']['data'] + "</td>" +
							"<td></td>" +
						"</tr>" +
						"<tr>" +
							"<td colspan=\"2\"><strong>Social</strong></td>" +
							"<td><strong>Reach</strong></td>" +
							"<td></td>" +
						"</tr>" +
						"<tr>" +
							"<td></td>" +
							"<td>" + chartData['overall-totals']['facebook']['name'] + "</td>" +
							"<td>" + chartData['overall-totals']['facebook']['data'] + "</td>" +
							"<td></td>" +
						"</tr>" +
						"<tr>" +
							"<td></td>" +
							"<td>" + chartData['overall-totals']['instagram']['name'] + "</td>" +
							"<td>" + chartData['overall-totals']['instagram']['data'] + "</td>" +
							"<td></td>" +
						"</tr>" +
						"<tr>" +
							"<td></td>" +
							"<td>" + chartData['overall-totals']['twitter']['name'] + "</td>" +
							"<td>" + chartData['overall-totals']['twitter']['data'] + "</td>" +
							"<td></td>" +
						"</tr>" +
						"<tr>" +
							"<td></td>" +
							"<td>" + chartData['overall-totals']['youtube']['name'] + "</td>" +
							"<td>" + chartData['overall-totals']['youtube']['data'] + "</td>" +
							"<td></td>" +
						"</tr>" +
						"<tr>" +
							"<td></td>" +
							"<td>" + chartData['overall-totals']['apple-news']['name'] + "</td>" +
							"<td>" + chartData['overall-totals']['apple-news']['data'] + "</td>" +
							"<td></td>" +
						"</tr>" +
						"<tr>" +
							"<td colspan=\"2\"><strong>Streaming</strong></td>" +
							"<td><strong>Users</strong></td>" +
							"<td></td>" +
						"</tr>" +
						"<tr>" +
							"<td></td>" +
							"<td>" + chartData['overall-totals']['triton-news']['name'] + "</td>" +
							"<td>" + chartData['overall-totals']['triton-news']['data'] + "</td>" +
							"<td></td>" +
						"</tr>" +
						"<tr>" +
							"<td></td>" +
							"<td>" + chartData['overall-totals']['triton-classical']['name'] + "</td>" +
							"<td>" + chartData['overall-totals']['triton-classical']['data'] + "</td>" +
							"<td></td>" +
						"</tr>" +
						"<tr>" +
							"<td></td>" +
							"<td>" + chartData['overall-totals']['triton-mixtape']['name'] + "</td>" +
							"<td>" + chartData['overall-totals']['triton-mixtape']['data'] + "</td>" +
							"<td></td>" +
						"</tr>" +
						"<tr>" +
							"<td colspan=\"2\"><strong>Podcasts</strong></td>" +
							"<td><strong>Downloads</strong></td>" +
							"<td><strong>Downloaders</strong></td>" +
						"</tr>";
			var pod = chartData['overall-totals']['podcasts'];
			for (var p in pod) {
				var name = pod[p]['name'].replace( 'Hpm', "HPM" );
				output += "<tr>" +
					"<td></td>" +
					"<td>" + name + "</td>" +
					"<td>" + pod[p]['data']['downloads'] + "</td>" +
					"<td>" + pod[p]['data']['downloaders'] + "</td>" +
				"</tr>";
			}
			return output;
		}
	}
}
</script>
