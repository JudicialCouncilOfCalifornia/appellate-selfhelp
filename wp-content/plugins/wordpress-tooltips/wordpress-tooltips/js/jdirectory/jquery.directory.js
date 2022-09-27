/*!
 * jQuery.Directory. The jQuery directory plugin
 *
 * Copyright (c) 2014 - 2020 Tomas Zhu
 * http://tomas.zhu.bz
 * Support: http://tomas.zhu.bz/jquery-directory-plugin.html
 * Licensed under GPLv3 licenses
 * http://www.gnu.org/licenses/gpl.html
 *
 * Launch  : June 2014
 * Version : 3.5.2
 * Released: 10 June, 2014 - 00:00
 * 
 */
(function($)
{
	$.fn.directory = function(options)
	{
		if ( options['language'] == 'sv' ) 
		{
			if ( options['number'] == 'yes' )
			{
				$.fn.directory.defaults.navigation = 
					['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'å', 'ä', 'ö']				
			}
			if ( options['number'] == 'no' )
			{
				$.fn.directory.defaults.navigation = 
					['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'å', 'ä', 'ö']				
			}
		};
		
		if ( options['language'] == 'en' )
		{
			if ( options['number'] == 'yes' )
			{
				$.fn.directory.defaults.navigation = 
					['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z']				
			}

			if ( options['number'] == 'no' )
			{
				$.fn.directory.defaults.navigation = 
					['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z']				
			}
		}
		
		if ( options['language'] == 'de' )
		{
			if ( options['number'] == 'yes' )
			{
				$.fn.directory.defaults.navigation = 
					['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'ä', 'ö', 'ü', 'ß']
			}
			if ( options['number'] == 'no' )
			{
				$.fn.directory.defaults.navigation = 
					['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'ä', 'ö', 'ü', 'ß']
			}
		}
		if ( options['language'] == 'fr' )
		{
			if ( options['number'] == 'yes' )
			{
				$.fn.directory.defaults.navigation = 
					['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'é', 'è', 'ç', 'ë', 'ò', 'ô', 'ö', 'ù', 'à', 'â']				
			}
			if ( options['number'] == 'no' )
			{
				$.fn.directory.defaults.navigation = 
					['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'é', 'è', 'ç', 'ë', 'ò', 'ô', 'ö', 'ù', 'à', 'â']				
			}								
		}
		if ( options['language'] == 'es' )
		{
			if ( options['number'] == 'yes' )
			{
				$.fn.directory.defaults.navigation = 
					['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'ñ', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z']				
			}
			if ( options['number'] == 'no' )
			{
				$.fn.directory.defaults.navigation = 
					['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'ñ', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z']				
			}
		}
		if ( options['language'] == 'fi' )
		{
			if ( options['number'] == 'yes' )
			{
				$.fn.directory.defaults.navigation = 
					['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 'š', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'ž', 'å', 'ä', 'ö']								
			}
			if ( options['number'] == 'no' )
			{
				$.fn.directory.defaults.navigation = 
					['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 'š', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'ž', 'å', 'ä', 'ö']								
			}			
		}
		
		if ( options['language'] == 'ru' )
		{
			if ( options['number'] == 'yes' )
			{
				$.fn.directory.defaults.navigation = 
					['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'k', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю','я']								
			}
			if ( options['number'] == 'no' )
			{
				$.fn.directory.defaults.navigation = 
					['a', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'k', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю','я']								
			}			
		}

		if ( options['language'] == 'custom' )
		{
			if ( options['number'] == 'yes' )
			{
				if (options['numberletters'] == undefined)
				{
					options['numberletters'] = '0,1,2,3,4,5,6,7,8,9';
				}
				if (options['alphabetletters'] == undefined)
				{
					options['alphabetletters'] = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z';
				}
				
				var customizedalphabetletters = options['numberletters'] + ',' + options['alphabetletters'];
				var customizedalphabetlettersarray = customizedalphabetletters.split(',');
				$.fn.directory.defaults.navigation = customizedalphabetlettersarray;
			}
			if ( options['number'] == 'no' )
			{
				if (options['alphabetletters'] == undefined)
				{
					options['alphabetletters'] = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z';
				}
				
				var customizedalphabetletters =  options['alphabetletters'];
				var customizedalphabetlettersarray = customizedalphabetletters.split(',');
				$.fn.directory.defaults.navigation = customizedalphabetlettersarray;
			}
		}
		
		
		if ( options['language'] == 'custom' )
		{
			if (options['alphabetletters'] == undefined)
			{
				options['alphabetletters'] = 'ALL';
			}
			var customizedwordofselectall = $.trim(options['wordofselectall']);
		}
		else
		{
			var customizedwordofselectall = 'ALL';
		}
		
		
		var opts = $.extend($.fn.directory.defaults, options || {});
		$('.navitem').css('font-size',$.fn.directory.defaults.navitemdefaultsize);

		
		var tooltipsShowNav = function(tooltipsNavBar)
		{
			var currentThis = $(tooltipsNavBar);

			$(currentThis).find('span').each(function()
			{
				var currentString = $.trim($(this).text()).toLowerCase();
				var firstAlpha = currentString.charAt(0);

				$(this).data('alpha',firstAlpha);
			});
			
			var navbar = '<a navname="navnameall" class="navitem allDirectory" href="#navnameall">'+customizedwordofselectall+'</a>';
			var navname = '';
			$.each($.fn.directory.defaults.navigation, function(i,val)
			{
				var directorySelectors = $.fn.directory.defaults.selectors
				var ttt = $(directorySelectors);
				var countStore = 0;
				$.each(ttt,function()
				{
					var nowVal = $(this);
					var alphacount = $(nowVal).data('alpha');
					if (val == alphacount)
					{
						countStore = countStore + 1;
					}
				}
				)
				
				var navname = 'navname' + val;
				var navnamehref = 'navnameall';
				if (countStore == 0 ) 
				{
					if ( $.fn.directory.defaults.hidezeronumberitem == 'yes' )
					{
						navbar = navbar + '<a name="' + navname + '" class="navitem navitemhidden '+val+'" data-counter='+countStore+' href="#' + navnamehref + '">'+val.toUpperCase()+'</a>';
					}
					else
					{
						navbar = navbar + '<a name = "' + navname + '" class="navitem  '+val+'" data-counter='+countStore+' href="#' + navnamehref + '">'+val.toUpperCase()+'</a>';
					}
						
				}
				else 
				{
						navbar = navbar + '<a name = "' + navname + '" class="navitem '+val+'" href="#' + navnamehref +'">'+val.toUpperCase()+'<span class="tooltiplist_count">'+countStore+'</span></a>';
				}
  			});
  			navbar = '<div class="navitems">' + navbar + '</div>';
			currentThis.prepend(navbar);

			$('.navitem').css('color',$.fn.directory.defaults.navitembackground);
			$('.navitem').click(function()
			{
				$('.navitem').removeClass('selecteddirectory');
				$('.navitem').css('font-size',$.fn.directory.defaults.navitemdefaultsize);
				$(this).addClass('selecteddirectory'); 
				$(this).css('font-size',$.fn.directory.defaults.navitemselectedsize);
				$('.navitem').css('background','#fff');

				$(this).css('background',$.fn.directory.defaults.navitembackground);
				$('.navitem').css('color',$.fn.directory.defaults.navitembackground);
				$(this).css('color','#fff');
				var currentCheck = $(this);
				var clickedAlpha = $.trim(currentCheck.text()).toLowerCase();
				var clickedAlphaFirst = clickedAlpha.charAt(0);

				$(currentThis).find('.tooltips_list>span').each(function()
				{
					var alpha = $(this).data('alpha');
					
					if (clickedAlphaFirst == alpha)
					{
						$(this).parent().css('display','block');
						$('.tooltiplist_count').css('display','inline-block');
					}
					else
					{
						$(this).parent().css('display','none');
						$('.tooltiplist_count').css('display','inline-block');
					}

					var customizedwordofselectalllow = customizedwordofselectall.toLowerCase()
					if (clickedAlpha == customizedwordofselectalllow)
					{
						$(this).parent().css('display','block');
					}
				});
  			});			
		}		
		
		var tooltipsResults = this.each(function () 
		{
			tooltipsShowNav(this);
		});
		return tooltipsResults;
		
   };
   
   $.fn.directory.defaults = {
			navigation: ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'],				
			frontground: 'red',
			navitembackground: '#007DBD',
			methods:	'list',
			navitemdefaultsize:'16px',
			navitemselectedsize:'25px',
			number:'yes',
			numberletters:'0,1,2,3,4,5,6,7,8,9',
			alphabetletters:'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z',
			wordofselectall:'ALL',
			hidezeronumberitem:'no',
			selectors:	'.tooltips_list > span'
			};
})(jQuery);