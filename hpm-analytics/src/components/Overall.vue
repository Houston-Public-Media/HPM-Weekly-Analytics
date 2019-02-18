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
							<th scope="col">Total</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="3"><strong>Website</strong></td>
						</tr>
						<tr>
							<td></td>
							<td>{{ chartData['overall-totals']['ga-main']['name'] }}</td>
							<td>{{ chartData['overall-totals']['ga-main']['data'] }}</td>
						</tr>
						<tr>
							<td></td>
							<td>{{ chartData['overall-totals']['ga-amp']['name'] }}</td>
							<td>{{ chartData['overall-totals']['ga-amp']['data'] }}</td>
						</tr>
						<tr>
							<td colspan="3"><strong>Social</strong></td>
						</tr>
						<tr>
							<td></td>
							<td>{{ chartData['overall-totals']['facebook']['name'] }}</td>
							<td>{{ chartData['overall-totals']['facebook']['data'] }}</td>
						</tr>
						<tr>
							<td></td>
							<td>{{ chartData['overall-totals']['instagram']['name'] }}</td>
							<td>{{ chartData['overall-totals']['instagram']['data'] }}</td>
						</tr>
						<tr>
							<td></td>
							<td>{{ chartData['overall-totals']['twitter']['name'] }}</td>
							<td>{{ chartData['overall-totals']['twitter']['data'] }}</td>
						</tr>
						<tr>
							<td></td>
							<td>{{ chartData['overall-totals']['youtube']['name'] }}</td>
							<td>{{ chartData['overall-totals']['youtube']['data'] }}</td>
						</tr>
						<tr>
							<td></td>
							<td>{{ chartData['overall-totals']['apple-news']['name'] }}</td>
							<td>{{ chartData['overall-totals']['apple-news']['data'] }}</td>
						</tr>
						<tr>
							<td colspan="3"><strong>Streaming</strong></td>
						</tr>
						<tr>
							<td></td>
							<td>{{ chartData['overall-totals']['triton-news']['name'] }}</td>
							<td>{{ chartData['overall-totals']['triton-news']['data'] }}</td>
						</tr>
						<tr>
							<td></td>
							<td>{{ chartData['overall-totals']['triton-classical']['name'] }}</td>
							<td>{{ chartData['overall-totals']['triton-classical']['data'] }}</td>
						</tr>
						<tr>
							<td></td>
							<td>{{ chartData['overall-totals']['triton-mixtape']['name'] }}</td>
							<td>{{ chartData['overall-totals']['triton-mixtape']['data'] }}</td>
						</tr>
					</tbody>
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
	]
}
</script>
