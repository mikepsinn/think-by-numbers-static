var SearchPage = function() {
    
    var quantimodoVariables = {};

    var initEvents = function()
    {

    };
    
    var initVariables = function()
    {
        Quantimodo.getVariables({'user': displayedUser}, function(variables)
        {
            SearchPage.quantimodoVariables = {};
            jQuery.each(variables, function(_, variable)
            {
                var category = SearchPage.quantimodoVariables[variable.category];
                
                if (category === undefined)
                {
                    SearchPage.quantimodoVariables[variable.category] = [variable];
                }
                else
                {
                    category.push(variable);
                }
            });
            jQuery.each(Object.keys(SearchPage.quantimodoVariables), function(_, category)
            {
                SearchPage.quantimodoVariables[category] = SearchPage.quantimodoVariables[category].sort(function(a, b)
                {
                    return a.name.toLowerCase().localeCompare(b.name.toLowerCase());
                });
                
            });
           initAutoComplete();
        });        
    };

    var initAutoComplete = function() {
        jQuery.widget("custom.catcomplete", jQuery.ui.autocomplete, {
            _renderMenu: function(ul, items) {
                ul.addClass("variablePickerUL");
                var that = this,
                currentCategory = "";
                jQuery.each(items, function(index, item) {
                    if (item.category != currentCategory) {
                        ul.append("<li class='ui-autocomplete-category variablePickerCategory'>" + item.category + "</li>");
                        currentCategory = item.category;
                    }
                    that._renderItemData(ul, item);
                });
            }
        });

        var varnames = [];
			jQuery.each(Object.keys(SearchPage.quantimodoVariables).sort(function(a, b)
			{
				return a.toLowerCase().localeCompare(b.toLowerCase());
			}), function(_, category)
			{
				var variables = SearchPage.quantimodoVariables[category];
				for(var n = 0; n < variables.length; n++)
				{
					var currentVariable = variables[n];
					varnames.push({label: currentVariable.name, category: currentVariable.category});
				}
			});

        

        jQuery("#searchVariable").catcomplete({
            source: varnames,
            select: function(event, ui) {
                
            },
            focus: function( event, ui ) {}
        });
    };

    return {
        quantimodoVariables: quantimodoVariables,
        init: function()
        {
            initVariables();
            initEvents();
        }
    };
}();

jQuery(SearchPage.init);

