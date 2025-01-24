<?php
	$graphs = [
		'overall-totals' => [
			'ga-combined' => [
				'name' => 'Google Analytics (Combined) Users',
				'data' => 0
			],
			'facebook' => [
				'name' => 'Facebook Total Reach',
				'data' => 0
			],
			'instagram' => [
				'name' => 'Instagram Total Reach',
				'data' => 0
			],
			'twitter' => [
				'name' => 'Twitter Impressions',
				'data' => 0
			],
			'X' => [
				'name' => 'X Impressions',
				'data' => 0
			],
			'triton-news' => [
				'name' => 'News Streaming Listeners',
				'data' => 0
			],
			'triton-classical' => [
				'name' => 'Classical Streaming Listeners',
				'data' => 0
			],
			'triton-mixtape' => [
				'name' => 'Mixtape Streaming Listeners',
				'data' => 0
			],
			'youtube' => [
				'name' => 'YouTube Viewers',
				'data' => 0
			],
			'apple-news' => [
				'name' => 'Apple News Unique Views',
				'data' => 0
			],
			'podcasts' => []
		],
		'ga-combined-articles' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Direct',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Google',
					'backgroundColor' => 'rgba(219,68,55,0.2 )',
					'borderColor' => 'rgba(219,68,55,1 )',
					'data' => []
				],
				2 => [
					'label' => 'Facebook',
					'backgroundColor' => 'rgba(59,89,152,0.2 )',
					'borderColor' => 'rgba(59,89,152,1 )',
					'data' => []
				],
				3 => [
					'label' => 'Twitter',
					'backgroundColor' => 'rgba(29,161,242,0.2 )',
					'borderColor' => 'rgba(29,161,242,1 )',
					'data' => []
				],
				4 => [
					'label' => 'RSS',
					'backgroundColor' => 'rgba(255,187,0,0.2 )',
					'borderColor' => 'rgba(255,187,0,1 )',
					'data' => []
				],
				5 => [
					'label' => 'Newsbreak',
					'backgroundColor' => 'rgba(65,0,147,0.2 )',
					'borderColor' => 'rgba(65,0,147,1 )',
					'data' => []
				],
				6 => [
					'label' => 'Other',
					'backgroundColor' => 'rgba(0,0,0,0.2 )',
					'borderColor' => 'rgba(0,0,0,1 )',
					'data' => []
				]
			]
		],
		'ga-combined-hourly' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Users',
					'backgroundColor' => [ 'rgba( 0, 0, 255, 0.2 )' ],
					'borderColor' => [ 'rgba( 0, 0, 255, 1 )' ],
					'data' => []
				]
			]
		],
		'ga-combined-devices' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Users',
					'backgroundColor' => [],
					'data' => []
				]
			]
		],
		'ga-houston-matters-articles' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Direct',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Google',
					'backgroundColor' => 'rgba(219,68,55,0.2 )',
					'borderColor' => 'rgba(219,68,55,1 )',
					'data' => []
				],
				2 => [
					'label' => 'Facebook',
					'backgroundColor' => 'rgba(59,89,152,0.2 )',
					'borderColor' => 'rgba(59,89,152,1 )',
					'data' => []
				],
				3 => [
					'label' => 'Twitter',
					'backgroundColor' => 'rgba(29,161,242,0.2 )',
					'borderColor' => 'rgba(29,161,242,1 )',
					'data' => []
				],
				4 => [
					'label' => 'RSS',
					'backgroundColor' => 'rgba(255,187,0,0.2 )',
					'borderColor' => 'rgba(255,187,0,1 )',
					'data' => []
				],
				5 => [
					'label' => 'Newsbreak',
					'backgroundColor' => 'rgba(65,0,147,0.2 )',
					'borderColor' => 'rgba(65,0,147,1 )',
					'data' => []
				],
				6 => [
					'label' => 'Other',
					'backgroundColor' => 'rgba(0,0,0,0.2 )',
					'borderColor' => 'rgba(0,0,0,1 )',
					'data' => []
				]
			]
		],
		'facebook-impressions' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Total Reach',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Paid Impressions',
					'backgroundColor' => 'rgba(255,0,0,0.2 )',
					'borderColor' => 'rgba(255,0,0,1 )',
					'data' => []
				],
				2 => [
					'label' => 'Organic Impressions',
					'backgroundColor' => 'rgba(0,0,255,0.2 )',
					'borderColor' => 'rgba(0,0,255,1 )',
					'data' => []
				],
				3 => [
					'label' => 'Viral Impressions',
					'backgroundColor' => 'rgba(255,0,255,0.2 )',
					'borderColor' => 'rgba(255,0,255,1 )',
					'data' => []
				]
			]
		],
		'facebook-likes' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Page Likes',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				]
			]
		],
		'facebook-reactions' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Like',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Love',
					'backgroundColor' => 'rgba(255,0,0,0.2 )',
					'borderColor' => 'rgba(255,0,0,1 )',
					'data' => []
				],
				2 => [
					'label' => 'Wow',
					'backgroundColor' => 'rgba(0,0,255,0.2 )',
					'borderColor' => 'rgba(0,0,255,1 )',
					'data' => []
				],
				3 => [
					'label' => 'Haha',
					'backgroundColor' => 'rgba(255,0,255,0.2 )',
					'borderColor' => 'rgba(255,0,255,1 )',
					'data' => []
				],
				4 => [
					'label' => 'Sorry',
					'backgroundColor' => 'rgba(255,255,0,0.2 )',
					'borderColor' => 'rgba(255,255,0,1 )',
					'data' => []
				],
				5 => [
					'label' => 'Anger',
					'backgroundColor' => 'rgba(0,255,255,0.2 )',
					'borderColor' => 'rgba(0,255,255,1 )',
					'data' => []
				]
			]
		],
		'instagram-stats' => [
			'labels' => [],
			'datasets' => [
				0  => [
					'label' => 'Views',
					'backgroundColor' => 'rgba( 255, 0, 0, 0.2 )',
					'borderColor' => 'rgba( 255, 0, 0, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Reach',
					'backgroundColor' => 'rgba( 255, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 255, 0, 255, 1 )',
					'data' => []
				]
			]
		],
		'twitter-tweets-by-impression' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Impressions',
					'backgroundColor' => 'rgba( 29,161,242, 0.2 )',
					'borderColor' => 'rgba( 29,161,242, 1 )',
					'data' => []
				]
			]
		],
		'twitter-tweets-by-engagement' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Retweets',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Replies',
					'backgroundColor' => 'rgba(0,0,255,0.2)',
					'borderColor' => 'rgba(0,0,255,1)',
					'data' => []
				],
				2 => [
					'label' => 'Likes',
					'backgroundColor' => 'rgba(255,0,0,0.2)',
					'borderColor' => 'rgba(255,0,0,1)',
					'data' => []
				],
				3 => [
					'label' => 'URL Clicks',
					'backgroundColor' => 'rgba(255,0,255,0.2)',
					'borderColor' => 'rgba(255,0,255,1)',
					'data' => []
				],
				4 => [
					'label' => 'Hashtag Clicks',
					'backgroundColor' => 'rgba(0,255,255,0.2)',
					'borderColor' => 'rgba(0,255,255,1)',
					'data' => []
				],
				5 => [
					'label' => 'Detail Expands',
					'backgroundColor' => 'rgba(255,255,0,0.2)',
					'borderColor' => 'rgba(255,255,0,1)',
					'data' => []
				],
				6 => [
					'label' => 'Media Views',
					'backgroundColor' => 'rgba(128,128,128,0.2)',
					'borderColor' => 'rgba(128,128,128,1)',
					'data' => []
				]
			]
		],
		'twitter-account-tweets' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Tweets',
					'backgroundColor' => 'rgba( 29,161,242, 0.2 )',
					'borderColor' => 'rgba( 29,161,242, 1 )',
					'data' => []
				]
			]
		],
		'twitter-account-impressions' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Organic Impressions',
					'backgroundColor' => 'rgba( 29,161,242, 0.2 )',
					'borderColor' => 'rgba( 29,161,242, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Paid Impressions',
					'backgroundColor' => 'rgba( 128,128,128,0.2 )',
					'borderColor' => 'rgba( 128,128,128,1 )',
					'data' => []
				]
			]
		],
		'twitter-account-engagements' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'URL Clicks',
					'backgroundColor' => 'rgba(255,0,255,0.2 )',
					'borderColor' => 'rgba(255,0,255,1 )',
					'data' => []
				],
				1 => [
					'label' => 'Retweets',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				2 => [
					'label' => 'Replies',
					'backgroundColor' => 'rgba(0,0,255, 0.2 )',
					'borderColor' => 'rgba(0,0,255, 1 )',
					'data' => []
				],
				3 => [
					'label' => 'Likes',
					'backgroundColor' => 'rgba(255,0,0,0.2 )',
					'borderColor' => 'rgba(255,0,0,1 )',
					'data' => []
				]
			]
		],
		'triton-hourly' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'News',
					'backgroundColor' => 'rgba( 255, 0, 0, 0.2 )',
					'borderColor' => 'rgba( 255, 0, 0, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Classical',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				],
				2 => [
					'label' => 'Mixtape',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				]
			]
		],
		'triton-news-devices' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'CUME by Device Type',
					'backgroundColor' => [],
					'borderWidth' => 1,
					'data' => []
				]
			]
		],
		'triton-classical-devices' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'CUME by Device Type',
					'backgroundColor' => [],
					'borderWidth' => 1,
					'data' => []
				]
			]
		],
		'triton-mixtape-devices' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'CUME by Device Type',
					'backgroundColor' => [],
					'borderWidth' => 1,
					'data' => []
				]
			]
		],
		'youtube-videos-by-views' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Views',
					'backgroundColor' => 'rgba( 255, 0, 0, 0.2 )',
					'borderColor' => 'rgba( 255, 0, 0, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Estimated Minutes Watched',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				]
			]
		],
		'youtube-videos-by-engagement' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Comments',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Subscribed Gained',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				2 => [
					'label' => 'Likes',
					'backgroundColor' => 'rgba( 255, 0, 0, 0.2 )',
					'borderColor' => 'rgba( 255, 0, 0, 1 )',
					'data' => []
				]
			]
		],
		'apple-demo' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Male',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Female',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				]
			]
		],
		'apple-age' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Users',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				]
			]
		],
		'apple-reach' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Total Views',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Unique Views',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				2 => [
					'label' => 'Reach',
					'backgroundColor' => 'rgba( 255, 0, 0, 0.2 )',
					'borderColor' => 'rgba( 255, 0, 0, 1 )',
					'data' => []
				]
			]
		],
		'apple-engage' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Shares',
					'backgroundColor' => 'rgba( 0, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 0, 0, 255, 1 )',
					'data' => []
				],
				1 => [
					'label' => 'Likes',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				2 => [
					'label' => 'Favorites',
					'backgroundColor' => 'rgba( 255, 0, 0, 0.2 )',
					'borderColor' => 'rgba( 255, 0, 0, 1 )',
					'data' => []
				],
				3 => [
					'label' => 'Saved Articles',
					'backgroundColor' => 'rgba( 255, 0, 255, 0.2 )',
					'borderColor' => 'rgba( 255, 0, 255, 1 )',
					'data' => []
				]
			]
		],
		'x-impressions' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'Impressions',
					'backgroundColor' => 'rgba( 29,161,242, 0.2 )',
					'borderColor' => 'rgba( 29,161,242, 1 )',
					'data' => []
				]
			]
		],
		'x-engagements' => [
			'labels' => [],
			'datasets' => [
				0 => [
					'label' => 'URL Clicks',
					'backgroundColor' => 'rgba(255,0,255,0.2 )',
					'borderColor' => 'rgba(255,0,255,1 )',
					'data' => []
				],
				1 => [
					'label' => 'Retweets',
					'backgroundColor' => 'rgba( 0, 255, 0, 0.2 )',
					'borderColor' => 'rgba( 0, 255, 0, 1 )',
					'data' => []
				],
				2 => [
					'label' => 'Replies',
					'backgroundColor' => 'rgba(0,0,255, 0.2 )',
					'borderColor' => 'rgba(0,0,255, 1 )',
					'data' => []
				],
				3 => [
					'label' => 'Likes',
					'backgroundColor' => 'rgba(255,0,0,0.2 )',
					'borderColor' => 'rgba(255,0,0,1 )',
					'data' => []
				]
			]
		]
	];
?>