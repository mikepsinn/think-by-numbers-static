var refreshMeasurementsRange = function(callback)
{
	Quantimodo.getMeasurementsRange({'user' : displayedUser}, function(range) {
		AnalyzePage.dateRangeStart = range['lowerLimit'];
		AnalyzePage.dateRangeEnd = range['upperLimit'];

		if (callback) {
			callback();
		}
	});
};

var refreshUnits = function(callback)
{   
	Quantimodo.getUnits({}, function(units)
	{
		jQuery.each(units, function(_, unit)
		{
			var category = AnalyzePage.quantimodoUnits[unit.category];
			if (category === undefined)
			{
				AnalyzePage.quantimodoUnits[unit.category] = [unit];
			}
			else
			{
				category.push(unit);
			}
		});
		jQuery.each(Object.keys(AnalyzePage.quantimodoUnits), function(_, category)
		{
			AnalyzePage.quantimodoUnits[category] = AnalyzePage.quantimodoUnits[category].sort();
		});

		if (callback) 
		{
			callback();
		}
	});
};

var refreshVariables = function(variables, callback)
{
	Quantimodo.getVariables({'user' : displayedUser}, function(variables)
	{
		var storedLastInputVariableName = window.localStorage['lastInputVariableName'],
			storedLastOutputVariableName = window.localStorage['lastOutputVariableName'];

		AnalyzePage.quantimodoVariables = {};
		jQuery.each(variables, function(_, variable)
		{
			if(variable.originalName == storedLastInputVariableName)
			{
				AnalyzePage.lastInputVariable = variable;
			}
			else if(variable.originalName == storedLastOutputVariableName)
			{
				AnalyzePage.lastOutputVariable = variable;
			}

			var category = AnalyzePage.quantimodoVariables[variable.category];
			if (category === undefined)
			{
				AnalyzePage.quantimodoVariables[variable.category] = [variable];
			}
			else
			{
				category.push(variable);
			}
		});
		jQuery.each(Object.keys(AnalyzePage.quantimodoVariables), function(_, category)
		{
			AnalyzePage.quantimodoVariables[category] = AnalyzePage.quantimodoVariables[category].sort(function(a, b)
			{
				return a.name.toLowerCase().localeCompare(b.name.toLowerCase());
			});
		});

		if(VariablePicker)
		{
			VariablePicker.refresh();
		}
		
		if (callback) 
		{
			callback(variables);
		}
	});
};

var refreshInputData = function()
{
	var variable = AnalyzePage.getInputVariable();
	if (variable == null)
	{
		return;
	}
	Quantimodo.getMeasurements({
		'user' : displayedUser,
		'variableName': variable.originalName,
		'startTime': AnalyzePage.getStartTime(),
		'endTime': AnalyzePage.getEndTime(),
		'groupingWidth': AnalyzePage.getPeriod(),
		'groupingTimezone': AnalyzePage.getTimezone()
	}, function(measurements) {
		AnalyzePage.inputMeasurements = measurements;
		AnalyzeChart.setInputData(variable, measurements);
	});
};

var refreshOutputData = function()
{
	var variable = AnalyzePage.getOutputVariable();
	Quantimodo.getMeasurements({
		'user' : displayedUser,
		'variableName': variable.originalName,
		'startTime': AnalyzePage.getStartTime(),
		'endTime': AnalyzePage.getEndTime(),
		'groupingWidth': AnalyzePage.getPeriod(),
		'groupingTimezone': AnalyzePage.getTimezone()
	}, function(measurements) {
		AnalyzePage.outputMeasurements = measurements;
		AnalyzeChart.setOutputData(variable, measurements); 
	});
};


var refreshData = function()
{             
	for (var i = 0; i < AnalyzePage.selectedVariables.length; i++)
	{
		var variable = AnalyzePage.selectedVariables[i];
		var filters = {
			'variableName': variable.originalName,
			'startTime': AnalyzePage.dateRangeStart,
			'endTime': AnalyzePage.dateRangeEnd,
			'groupingWidth': AnalyzePage.getPeriod(),
			'groupingTimezone': AnalyzePage.getTimezone()
		}
		if(variable.source != null && variable.source.length != 0)
		{
			filters.source = variable.source;
		}	
		if(variable.color == null)
		{
			variable.color = AnalyzePage.getRandomColor();
		}

		filters.user = displayedUser;

		Quantimodo.getMeasurements(filters,
		function(vari)
		{
			return function(measurements)
			{
				AnalyzeChart.addData(vari, measurements);
			}
		}(variable));
	}
};