window.dlUrl = 'https://cdn.hpm.io/assets/analytics/';
window.currentReport = {};
window.currentData = [];
window['config'] = {
	'ga-main-articles': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
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
						return data.labels[tooltipItem[0].index];
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
								return value;
							} else {
								var trimmed = value.substring(0,35);
								return trimmed+'...';
							}
						}
					}
				}]
			}
		},
		'data': [],
		'type': 'horizontalBar'
	},
	'ga-main-hourly': {
		'options': {
			maintainAspectRatio: true,
			responsive: true,
			legend: {
				position: 'bottom',
			},
			title: {
				display: true,
				text: 'Site Users By Hour',
				fontSize: 16
			}
		},
		'data': [],
		'type': 'line'
	},
	'ga-main-devices': {
		'options': {
			maintainAspectRatio: true,
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
		},
		'data': [],
		'type': 'doughnut'
	},
	'ga-amp-articles': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
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
						return data.labels[tooltipItem[0].index];
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
								return value;
							} else {
								var trimmed = value.substring(0,35);
								return trimmed+'...';
							}
						}
					}
				}]
			}
		},
		'data': [],
		'type': 'horizontalBar'
	},
	'ga-amp-hourly': {
		'options': {
			maintainAspectRatio: true,
			responsive: true,
			legend: {
				position: 'bottom',
			},
			title: {
				display: true,
				text: 'Site Users By Hour',
				fontSize: 16
			}
		},
		'data': [],
		'type': 'line'
	},
	'ga-amp-devices': {
		'options': {
			maintainAspectRatio: true,
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
		},
		'data': [],
		'type': 'doughnut'
	},
	'facebook-impressions': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
			responsive: true,
			legend: {
				position: 'bottom',
			},
			title: {
				display: true,
				text: 'Impressions and Reach',
				fontSize: 16
			}
		},
		'data': [],
		'type': 'bar'
	},
	'facebook-likes': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
			responsive: true,
			legend: {
				position: 'bottom',
			},
			title: {
				display: true,
				text: 'Lifetime Total Page Likes',
				fontSize: 16
			}
		},
		'data': [],
		'type': 'bar'
	},
	'facebook-reactions': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
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
		},
		'data': [],
		'type': 'bar'
	},
	'instagram-stats': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
			responsive: true,
			legend: {
				position: 'bottom',
			},
			title: {
				display: false
			}
		},
		'data': [],
		'type': 'bar'
	},
	'twitter-tweets-by-impression': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
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
						var label = data.labels[tooltipItem[0].index];
						if ( label.length < 120 ) {
							return label;
						} else {
							var trimmed = label.substring(0,120);
							return trimmed+'...';
						}
					}
				}
			},
			scales: {
				yAxes: [{
					ticks: {
						callback: function(value, index, values) {
							if ( value.length < 45 ) {
								return value;
							} else {
								var trimmed = value.substring(0,45);
								return trimmed+'...';
							}
						}
					}
				}]
			}
		},
		'data': [],
		'type': 'horizontalBar'
	},
	'twitter-tweets-by-engagement': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
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
						var label = data.labels[tooltipItem[0].index];
						if ( label.length < 120 ) {
							return label;
						} else {
							var trimmed = label.substring(0,120);
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
						callback: function(value, index, values) {
							if ( value.length < 45 ) {
								return value;
							} else {
								var trimmed = value.substring(0,45);
								return trimmed+'...';
							}
						}
					}
				}]
			}
		},
		'data': [],
		'type': 'horizontalBar'
	},
	'twitter-account-tweets': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
			responsive: true,
			legend: {
				position: 'bottom',
			},
			title: {
				display: true,
				text: 'Tweets per Day',
				fontSize: 16
			}
		},
		'data': [],
		'type': 'bar'
	},
	'twitter-account-impressions': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
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
		},
		'data': [],
		'type': 'bar'
	},
	'twitter-account-engagements': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
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
		},
		'data': [],
		'type': 'bar'
	},
	'triton-hourly': {
		'options': {
			maintainAspectRatio: true,
			responsive: true,
			legend: {
				position: 'bottom',
			},
			title: {
				display: true,
				text: 'Hourly Listeners by Stream',
				fontSize: 16
			}
		},
		'data': [],
		'type': 'line'
	},
	'triton-news-devices': {
		'options': {
			maintainAspectRatio: true,
			responsive: true,
			legend: {
				position: 'right',
			},
			title: {
				display: true,
				text: 'News: CUME by Device Type',
				fontSize: 16
			}
		},
		'data': [],
		'type': 'pie'
	},
	'triton-classical-devices': {
		'options': {
			maintainAspectRatio: true,
			responsive: true,
			legend: {
				position: 'right',
			},
			title: {
				display: true,
				text: 'Classical: CUME by Device Type',
				fontSize: 16
			}
		},
		'data': [],
		'type': 'pie'
	},
	'triton-mixtape-devices': {
		'options': {
			maintainAspectRatio: true,
			responsive: true,
			legend: {
				position: 'right',
			},
			title: {
				display: true,
				text: 'Mixtape: CUME by Device Type',
				fontSize: 16
			}
		},
		'data': [],
		'type': 'pie'
	},
	'youtube-videos-by-views': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
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
						var label = data.labels[tooltipItem[0].index];
						if ( label.length < 120 ) {
							return label;
						} else {
							var trimmed = label.substring(0,120);
							return trimmed+'...';
						}
					}
				}
			},
			scales: {
				yAxes: [{
					ticks: {
						callback: function(value, index, values) {
							if ( value.length < 45 ) {
								return value;
							} else {
								var trimmed = value.substring(0,45);
								return trimmed+'...';
							}
						}
					}
				}]
			}
		},
		'data': [],
		'type': 'horizontalBar'
	},
	'youtube-videos-by-engagement': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
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
						var label = data.labels[tooltipItem[0].index];
						if ( label.length < 120 ) {
							return label;
						} else {
							var trimmed = label.substring(0,120);
							return trimmed+'...';
						}
					}
				}
			},
			scales: {
				yAxes: [{
					ticks: {
						callback: function(value, index, values) {
							if ( value.length < 45 ) {
								return value;
							} else {
								var trimmed = value.substring(0,45);
								return trimmed+'...';
							}
						}
					}
				}]
			}
		},
		'data': [],
		'type': 'horizontalBar'
	},
	'apple-demo': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
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
		},
		'data': [],
		'type': 'bar'
	},
	'apple-age': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
			responsive: true,
			legend: {
				position: 'bottom',
			},
			title: {
				display: true,
				text: 'Channel Age Groups (Percentage)',
				fontSize: 16
			}
		},
		'data': [],
		'type': 'bar'
	},
	'apple-reach': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
			responsive: true,
			legend: {
				position: 'bottom',
			},
			title: {
				display: true,
				text: 'Reach and Views Per Day',
				fontSize: 16
			}
		},
		'data': [],
		'type': 'bar'
	},
	'apple-engage': {
		'options': {
			elements: {
				rectangle: {
					borderWidth: 2,
				}
			},
			maintainAspectRatio: true,
			responsive: true,
			legend: {
				position: 'bottom',
			},
			title: {
				display: true,
				text: 'Article Engagements Per Day',
				fontSize: 16
			}
		},
		'data': [],
		'type': 'bar'
	}
};
window.dlUrl = 'https://cdn.hpm.io/assets/analytics/';
window.tabs = document.querySelectorAll('.tabs ul li');
function GetIEVersion() {
	var sAgent = window.navigator.userAgent;
	var Idx = sAgent.indexOf("MSIE");
	if (Idx > 0) {
		return parseInt(sAgent.substring(Idx+ 5, sAgent.indexOf(".", Idx)));
	} else if (!!navigator.userAgent.match(/Trident\/7\./)) {
		return 11;
	} else {
	  return 0;
	}
}
window.getJSON = function(url, callback) {
	var xhr = new XMLHttpRequest();
	xhr.open('GET', url, true);
	xhr.responseType = 'json';
	xhr.onload = function() {
		var status = xhr.status;
		if (status === 200) {
			callback(null, xhr.response);
		} else {
			callback(status, xhr.response);
		}
	};
	xhr.send();
};
function graphUpdate(report) {
	getJSON( dlUrl + report + ".json", function(err,data) {
		if (err !== null) {
			console.log(err);
		} else {
			window.currentData = data;
			for ( var d in currentData ) {
				if ( d === 'overall-totals') {
					overallGen(currentData[d]);
				} else {
					window['config'][d]['data'] = currentData[d];
					window[d+'-graph'].data = window['config'][d]['data'];
					window[d+'-graph'].update();
				}
			}
		}
	});
}
function overallGen(data) {
	var output = "<table class=\"table is-bordered is-striped is-hoverable is-fullwidth\">" +
		"<thead>" +
		"<tr>" +
			"<th scope=\"col\">Service</th>" +
			"<th scope=\"col\" colspan=\"2\">Total</th>" +
		"</tr>" +
	"</thead>" +
	"<tbody>" +
		"<tr class=\"overall-section\">" +
			"<td>Website</td>" +
			"<td colspan=\"2\">Users</td>" +
		"</tr>" +
		"<tr>" +
			"<td>" + data['ga-main']['name'] + "</td>" +
			"<td>" + data['ga-main']['data'] + "</td>" +
			"<td></td>" +
		"</tr>" +
		"<tr>" +
			"<td>" + data['ga-amp']['name'] + "</td>" +
			"<td>" + data['ga-amp']['data'] + "</td>" +
			"<td></td>" +
		"</tr>" +
		"<tr class=\"overall-section\">" +
			"<td>Social</td>" +
			"<td colspan=\"2\">Reach</td>" +
		"</tr>" +
		"<tr>" +
			"<td>" + data['facebook']['name'] + "</td>" +
			"<td>" + data['facebook']['data'] + "</td>" +
			"<td></td>" +
		"</tr>" +
		"<tr>" +
			"<td>" + data['instagram']['name'] + "</td>" +
			"<td>" + data['instagram']['data'] + "</td>" +
			"<td></td>" +
		"</tr>" +
		"<tr>" +
			"<td>" + data['twitter']['name'] + "</td>" +
			"<td>" + data['twitter']['data'] + "</td>" +
			"<td></td>" +
		"</tr>" +
		"<tr>" +
			"<td>" + data['youtube']['name'] + "</td>" +
			"<td>" + data['youtube']['data'] + "</td>" +
			"<td></td>" +
		"</tr>" +
		"<tr>" +
			"<td>" + data['apple-news']['name'] + "</td>" +
			"<td>" + data['apple-news']['data'] + "</td>" +
			"<td></td>" +
		"</tr>" +
		"<tr class=\"overall-section\">" +
			"<td>Streaming</td>" +
			"<td colspan=\"2\">Users</td>" +
		"</tr>" +
		"<tr>" +
			"<td>" + data['triton-news']['name'] + "</td>" +
			"<td>" + data['triton-news']['data'] + "</td>" +
			"<td></td>" +
		"</tr>" +
		"<tr>" +
			"<td>" + data['triton-classical']['name'] + "</td>" +
			"<td>" + data['triton-classical']['data'] + "</td>" +
			"<td></td>" +
		"</tr>" +
		"<tr>" +
			"<td>" + data['triton-mixtape']['name'] + "</td>" +
			"<td>" + data['triton-mixtape']['data'] + "</td>" +
			"<td></td>" +
		"</tr>" +
		"<tr class=\"overall-section\">" +
			"<td>Podcasts</td>" +
			"<td>Downloads</td>" +
			"<td>Downloaders</td>" +
		"</tr>";
	var pod = data['podcasts'];
	for (var p in pod) {
		var name = pod[p]['name'].replace( 'Hpm', "HPM" );
		output += "<tr>" +
			"<td>" + name + "</td>" +
			"<td>" + pod[p]['data']['downloads'] + "</td>" +
			"<td>" + pod[p]['data']['downloaders'] + "</td>" +
		"</tr>";
	}
	output += "</tbody></table>";
	document.getElementById('overall-totals').innerHTML = output;
}
(function(){
	if (GetIEVersion() > 0) {
		alert("Internet Explorer " + GetIEVersion() + " is no longer being actively supported by Microsoft. Please try a different browser, such as Firefox, Chrome, Edge, or Safari.");
	} else {
		tabs.forEach(function(tab){
			tab.addEventListener('click', function(){
				if (this.classList.contains('is-active')) {
					return false;
				} else {
					tabs.forEach(function(tab){
						tab.classList.remove('is-active');
					});
					this.classList.add('is-active');
					var tabId = this.getAttribute('id');
					document.querySelectorAll('.services').forEach(function(serve){
						var sId = serve.getAttribute('id');
						if ( tabId+'-service' === sId ) {
							serve.classList.add('service-active');
						} else {
							serve.classList.remove('service-active');
						}
					});
				}
			});
		});
		getJSON( dlUrl + "reports.json", function(err,data) {
			if (err !== null) {
				console.log(err);
			} else {
				window.currentReport = data[0];
				var selector = document.querySelector( '#weekSelect' );
				for (var i = 0; i < data.length; i++ ) {
					var option = document.createElement('option');
					option.text = data[i].text;
					option.value = data[i].value;
					if (i == 0 ) {
						option.selected = 'selected';
					}
					selector.add(option);
				}
				selector.addEventListener("change",function(){
					graphUpdate(selector.value);
				});
				getJSON( dlUrl + currentReport.value + ".json", function(err,data) {
					if (err !== null) {
						console.log(err);
					} else {
						window.currentData = data;
						for ( var d in currentData ) {
							if ( d === 'overall-totals') {
								overallGen(currentData[d]);
							} else {
								var container = document.getElementById(d);
								var canvas = document.createElement('canvas');
								container.appendChild(canvas).setAttribute('id',d+'-graph');
								var ctx = document.getElementById(d+'-graph').getContext('2d');
								window['config'][d]['data'] = currentData[d];
								window[d+'-graph'] = new Chart(ctx, {
									type: window['config'][d]['type'],
									data: window['config'][d]['data'],
									options: window['config'][d]['options']
								});
							}
						}
					}
				});
			}
		});
	}
}());