// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import BootstrapVue from 'bootstrap-vue'
import { HorizontalBar,Bar,Line,Doughnut,Pie } from 'vue-chartjs'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import $ from 'jquery'

Vue.use(BootstrapVue)
Vue.config.productionTip = false

// Set up our main data variables
let chartData = []
let options = []
let weekSelected = ''

/* eslint-disable no-new */
// I set up all of the graphs as global components. There's probably a better way to do it,
// but I couldn't get it working. They are also set to rerender when the dataset is updated,
// which is not ideal, but it was the only way I could get them to work correctly

// Google Analytics Main - Top 20 articles by pageviews, broken down by source
Vue.component('ga-main-articles', {
	extends: HorizontalBar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Top 20 Articles - Pageviews by Source',
					fontSize: 16
				},
				tooltips: {
					callbacks: {
						title: function(tooltipItem, data) {
							return data.labels[tooltipItem[0].index]
						}
					}
				},
				scales: {
					xAxes: [{
						stacked: true,
					}],
					yAxes: [{
						stacked: true,
						ticks: {
							callback: function(value, index, values) {
								if ( value.length < 35 ) {
									return value
								} else {
									var trimmed = value.substring(0,35)
									return trimmed+'...'
								}
							}
						}
					}]
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['ga-main-articles'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['ga-main-articles'], this.options )
		}
	}
})

// Google Analytics Main - Users by hour
Vue.component('ga-main-hourly', {
	extends: Line,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Site Users By Hour',
					fontSize: 16
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['ga-main-hourly'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['ga-main-hourly'], this.options )
		}
	}
})

// Google Analytics Main - Users by Device Type
Vue.component('ga-main-devices', {
	extends: Doughnut,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Users by Device Category',
					fontSize: 16
				},
				animation: {
					animateScale: true,
					animateRotate: true
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['ga-main-devices'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['ga-main-devices'], this.options )
		}
	}
})

// Google Analytics AMP - Top 20 articles by pageviews, broken down by source
Vue.component('ga-amp-articles', {
	extends: HorizontalBar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Top 20 Articles - Pageviews by Source',
					fontSize: 16
				},
				tooltips: {
					callbacks: {
						title: function(tooltipItem, data) {
							return data.labels[tooltipItem[0].index]
						}
					}
				},
				scales: {
					xAxes: [{
						stacked: true,
					}],
					yAxes: [{
						stacked: true,
						ticks: {
							callback: function(value, index, values) {
								if ( value.length < 35 ) {
									return value
								} else {
									var trimmed = value.substring(0,35)
									return trimmed+'...'
								}
							}
						}
					}]
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['ga-amp-articles'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['ga-amp-articles'], this.options )
		}
	}
})

// Google Analytics AMP - Users by hour
Vue.component('ga-amp-hourly', {
	extends: Line,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Site Users By Hour',
					fontSize: 16
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['ga-amp-hourly'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['ga-amp-hourly'], this.options )
		}
	}
})

// Google Analytics AMP - Users by Device Type
Vue.component('ga-amp-devices', {
	extends: Doughnut,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Users by Device Category',
					fontSize: 16
				},
				animation: {
					animateScale: true,
					animateRotate: true
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['ga-amp-devices'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['ga-amp-devices'], this.options )
		}
	}
})

// Instagram Stats
Vue.component('instagram-stats', {
	extends: Bar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: false
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['instagram-stats'], this.options)
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['instagram-stats'], this.options)
		}
	}
})

// Facebook Impressions and Reach
Vue.component('facebook-reach', {
	extends: Bar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Impressions and Reach',
					fontSize: 16
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['facebook-impressions'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['facebook-impressions'], this.options )
		}
	}
})

// Facebook Lifetime Total Page Likes
Vue.component('facebook-likes', {
	extends: Bar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Lifetime Total Page Likes',
					fontSize: 16
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['facebook-likes'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['facebook-likes'], this.options )
		}
	}
})

// FAcebook Post Reactions by Type
Vue.component('facebook-reactions', {
	extends: Bar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Post Reactions by Type',
					fontSize: 16
				},
				scales: {
					xAxes: [{
						stacked: true,
					}],
					yAxes: [{
						stacked: true
					}]
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['facebook-reactions'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['facebook-reactions'], this.options )
		}
	}
})

// Twitter - Top 10 Tweets by Impression
Vue.component('twitter-impressions', {
	extends: HorizontalBar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Top 10 Tweets by Impressions',
					fontSize: 16
				},
				tooltips: {
					callbacks: {
						title: function(tooltipItem, data) {
							var label = data.labels[tooltipItem[0].index]
							if ( label.length < 120 ) {
								return label
							} else {
								var trimmed = label.substring(0,120)
								return trimmed+'...';
							}
						}
					}
				},
				scales: {
					yAxes: [{
						ticks: {
							// Include a dollar sign in the ticks
							callback: function(value, index, values) {
								if ( value.length < 45 ) {
									return value
								} else {
									var trimmed = value.substring(0,45)
									return trimmed+'...';
								}
							}
						}
					}]
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['twitter-tweets-by-impression'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['twitter-tweets-by-impression'], this.options )
		}
	}
})

// Twitter - Top 10 Tweets by Engagement Type
Vue.component('twitter-engagements', {
	extends: HorizontalBar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Top 10 Tweets By Engagement Type',
					fontSize: 16
				},
				tooltips: {
					callbacks: {
						title: function(tooltipItem, data) {
							var label = data.labels[tooltipItem[0].index]
							if ( label.length < 120 ) {
								return label
							} else {
								var trimmed = label.substring(0,120)
								return trimmed+'...';
							}
						}
					}
				},
				scales: {
					xAxes: [{
						stacked: true,
					}],
					yAxes: [{
						stacked: true,
						ticks: {
							// Include a dollar sign in the ticks
							callback: function(value, index, values) {
								if ( value.length < 45 ) {
									return value
								} else {
									var trimmed = value.substring(0,45)
									return trimmed+'...';
								}
							}
						}
					}]
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['twitter-tweets-by-engagement'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['twitter-tweets-by-engagement'], this.options )
		}
	}
})

// Twitter - Overall Tweet Impressions by Type
Vue.component('twitter-acct-impressions', {
	extends: Bar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Overall Tweet Impressions by Type',
					fontSize: 16
				},
				scales: {
					xAxes: [{
						stacked: true,
					}],
					yAxes: [{
						stacked: true,
					}]
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['twitter-account-impressions'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['twitter-account-impressions'], this.options )
		}
	}
})

// Twitter - Tweets per Day
Vue.component('twitter-acct-tweets', {
	extends: Bar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Tweets per Day',
					fontSize: 16
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['twitter-account-tweets'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['twitter-account-tweets'], this.options )
		}
	}
})

// Twitter - Overall Daily Engagements by Type
Vue.component('twitter-acct-engagements', {
	extends: Bar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Overall Tweet Engagements by Type',
					fontSize: 16
				},
				scales: {
					xAxes: [{
						stacked: true,
					}],
					yAxes: [{
						stacked: true,
					}]
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['twitter-account-engagements'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['twitter-account-engagements'], this.options )
		}
	}
})

// YouTube - Top 20 Videos by Views
Vue.component('youtube-views', {
	extends: HorizontalBar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Top 20 Videos by Views',
					fontSize: 16
				},
				tooltips: {
					callbacks: {
						title: function(tooltipItem, data) {
							var label = data.labels[tooltipItem[0].index]
							if ( label.length < 120 ) {
								return label
							} else {
								var trimmed = label.substring(0,120)
								return trimmed+'...';
							}
						}
					}
				},
				scales: {
					yAxes: [{
						ticks: {
							// Include a dollar sign in the ticks
							callback: function(value, index, values) {
								if ( value.length < 45 ) {
									return value
								} else {
									var trimmed = value.substring(0,45)
									return trimmed+'...';
								}
							}
						}
					}]
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['youtube-videos-by-views'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['youtube-videos-by-views'], this.options )
		}
	}
})

// YouTube - Audience Engagement with Top 20 Videos
Vue.component('youtube-engagement', {
	extends: HorizontalBar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Audience Engagement with Top 20 Videos',
					fontSize: 16
				},
				tooltips: {
					callbacks: {
						title: function(tooltipItem, data) {
							var label = data.labels[tooltipItem[0].index]
							if ( label.length < 120 ) {
								return label
							} else {
								var trimmed = label.substring(0,120)
								return trimmed+'...';
							}
						}
					}
				},
				scales: {
					yAxes: [{
						ticks: {
							// Include a dollar sign in the ticks
							callback: function(value, index, values) {
								if ( value.length < 45 ) {
									return value
								} else {
									var trimmed = value.substring(0,45)
									return trimmed+'...';
								}
							}
						}
					}]
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['youtube-videos-by-engagement'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['youtube-videos-by-engagement'], this.options )
		}
	}
})

// Triton - Hourly Listeners by Stream
Vue.component('triton-listeners', {
	extends: Line,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Hourly Listeners by Stream (3-Hour Average)',
					fontSize: 16
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['triton-hourly'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['triton-hourly'], this.options )
		}
	}
})

// Triton - News CUME by Device Type
Vue.component('triton-news', {
	extends: Pie,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'right',
				},
				title: {
					display: true,
					text: 'News: CUME by Device Type',
					fontSize: 16
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['triton-news-devices'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['triton-news-devices'], this.options )
		}
	}
})

// Triton - Classical CUME by Device Type
Vue.component('triton-classical', {
	extends: Pie,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'right',
				},
				title: {
					display: true,
					text: 'Classical: CUME by Device Type',
					fontSize: 16
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['triton-classical-devices'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['triton-classical-devices'], this.options )
		}
	}
})

// Triton - Mixtape CUME by Device Type
Vue.component('triton-mixtape', {
	extends: Pie,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'right',
				},
				title: {
					display: true,
					text: 'Mixtape: CUME by Device Type',
					fontSize: 16
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['triton-mixtape-devices'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['triton-mixtape-devices'], this.options )
		}
	}
})

// Apple News - User Gender by Day
Vue.component('apple-demo', {
	extends: Bar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'User Gender by Day',
					fontSize: 16
				},
				scales: {
					yAxes: [{
						stacked: true
					}],
					xAxes: [{
						stacked: true
					}]
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['apple-demo'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['apple-demo'], this.options )
		}
	}
})

// Apple News - Channel Age Groups
Vue.component('apple-age', {
	extends: Bar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Channel Age Groups (Percentage)',
					fontSize: 16
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['apple-age'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['apple-age'], this.options )
		}
	}
})

// Apple News - Reach and Views per Day
Vue.component('apple-reach', {
	extends: Bar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Reach and Views Per Day',
					fontSize: 16
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['apple-reach'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['apple-reach'], this.options )
		}
	}
})

// Apple News - Article Engagements per Day
Vue.component('apple-engage', {
	extends: Bar,
	props: [ 'chartData' ],
	data() {
		return {
			options: {
				elements: {
					rectangle: {
						borderWidth: 2,
					}
				},
				maintainAspectRatio: false,
				responsive: true,
				legend: {
					position: 'bottom',
				},
				title: {
					display: true,
					text: 'Article Engagements Per Day',
					fontSize: 16
				}
			}
		}
	},
	mounted () {
		this.renderChart(chartData['apple-engage'], this.options )
	},
	watch: {
		chartData: function(newChart,oldChart) {
			this.renderChart(newChart['apple-engage'], this.options )
		}
	}
})

// Pull in the reports.json file, enter it as an initial dataset for the Vue app
// Also, set the first entry in that file as the selected week
$.getJSON("https://cdn.hpm.io/assets/analytics/reports.json").done(function(data){
	$.each( data, function( key, value ) {
		if ( key === 0 ) {
			weekSelected = value.value;
		}
	});
	options = data;
	// Download the data for that selected week, enter it as chartData and instantiate the app
	$.getJSON("https://cdn.hpm.io/assets/analytics/"+weekSelected+".json").always(function(data){
		chartData = data;
		new Vue({
			el: '#app',
			components: { App },
			template: '<App :week-selected="weekSelected" :options="options" :chart-data="chartData"></App>',
			data: {
				weekSelected: weekSelected,
				options: options,
				chartData: chartData
			}
		})
	});
});
