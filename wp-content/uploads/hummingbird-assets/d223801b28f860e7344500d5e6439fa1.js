/**handles:directoryjs**/
/*!
 * jQuery.Directory. The jQuery directory plugin
 *
 * Copyright (c) 2014 - 2017 Tomas Zhu
 * http://tomas.zhu.bz
 * Support: http://tomas.zhu.bz/jquery-directory-plugin.html
 * License: GPLv3 or later
 * https://www.gnu.org/licenses/gpl-3.0.html
 * https://www.gnu.org/licenses/quick-guide-gplv3.html
 * This program comes with ABSOLUTELY NO WARRANTY;
 * Launch  : June 2014
 * Version : 1.2.0
 * Released: 10 June, 2014 - 00:00
 * 
 */
(function($)
{
	$.fn.directory = function(options)
	{
		var defaults = {
				navigation: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'],
				frontground: 'red',    
    			background: 'yellow'    // moved color to directory.css 
  			};
		var opts = $.extend(defaults, options);
		return this.each(function () {
			
			$currentthis = $(this);
			$($currentthis).find('span').each(function()
			{
				var currentstring = $.trim($(this).text()).toLowerCase();
				var firstAlpha = currentstring.charAt(0);
				$(this).data('alpha',firstAlpha);
			});
			var navbar = '<a class="navitem allDirectory" href="#">ALL</a>';
			$.each(defaults.navigation, function(i,val)
			{
				//navbar = navbar + '<a class="navitem '+val+'" href="#">'+val.toUpperCase()+'</a>';
				var ttt = $('.tooltips_list > span');
				var countstore = 0;
				$.each(ttt,function()
				{
					$nowval = $(this);
					var alphacount = $($nowval).data('alpha');
					if (val == alphacount)
					{
						countstore = countstore + 1;
					}
				}
				)
				if (countstore == 0 ) 
				{
					navbar = navbar + '<a class="navitem '+val+'" data-counter='+countstore+' href="#">'+val.toUpperCase()+'</a>';	
				}
				else {
				navbar = navbar + '<a class="navitem '+val+' href="#">'+val.toUpperCase()+'<span class="tooltiplist_count">'+countstore+'</span></a>';
				}
  			});
  			navbar = '<div class="navitems">' + navbar + '</div>';
			$currentthis.prepend(navbar);
			$('.navitem').css('color','#007DBD');
			$('.navitem').click(function()
			{
				$('.navitem').removeClass('selecteddirectory');				
				$('.navitem').css('font-size','14px');
				$(this).addClass('selecteddirectory'); 				
				$(this).css('font-size','18px');
				$('.navitem').css('background','#fff');
				$(this).css('background','#007DBD');
				$('.navitem').css('color','#007DBD');
				$(this).css('color','#fff');
				$currentcheck = $(this);
				$clickedAlpha = $.trim($(this).text()).toLowerCase();
				$clickedAlphaFirst = $clickedAlpha.charAt(0);

				$($currentthis).find('span').each(function()
				{
					var alpha = $(this).data('alpha');
					
					if ($clickedAlphaFirst == alpha)
					{
						$(this).css('display','inline-block');
						$('.tooltiplist_count').css('display','inline-block');
					}
					else
					{
						$(this).css('display','none');
						$('.tooltiplist_count').css('display','inline-block');
					}

					if ($clickedAlpha == 'all')
					{
						$(this).css('display','inline-block');
					}
				});
  			});
		});
   };
})(jQuery);