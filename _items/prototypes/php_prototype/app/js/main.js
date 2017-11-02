$(function()
{
	$("#camera").on("ended", function()
	{
		$(".status").fadeIn(250, function()
		{
			setTimeout(function()
			{
				$(".loading").fadeOut(100, function()
				{
					$(".done").fadeIn(100, function()
					{
							setTimeout(function()
							{
								$(".status").animate({
									"left":   "75px",
									"top":    "266px",
									"width":  "170px",
									"height": "50px"
								}, function()
								{
									$(".status-text").fadeIn(100, function()
									{
										setTimeout(function()
										{
											$(".status").fadeOut(250);
										}, 500);
									});
								});

								$(".done").animate({
									"width":  "32px",
									"height": "32px",
									"margin": "9px"
								});
								$(".status-text").animate({
									"width":       "95px",
									"height":      "50px",
									"line-height": "50px"
								});
							}, 1000);
					});
				});
			}, 1000);
		});
	});
});
