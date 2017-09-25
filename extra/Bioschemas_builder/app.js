(function () {
	var app = angular.module('builder', []);

	app.controller('BuilderController', function(){
		this.items = specs;
	});

	app.controller('CreateController', ['$scope', '$log', function($scope, $log){
		this.specs = specs;
		this.values = make_spec(specs);

		function make_spec(json_var){
			var values = [];
			for (key in json_var) {
				object_name = json_var[key].name;
				spec_path = json_var[key].spec_path;

				/* GETTING THE JSON SPEC*/
				var request = new XMLHttpRequest();
				request.open("GET", spec_path, false);
			   	request.send(null);
			   	var my_JSON_object = JSON.parse(request.responseText);
			   	var fields = [];
			   	for (name in my_JSON_object.properties){
			   		fields.push({field_name : name, field_prop : my_JSON_object.properties[name]});
			   		// start parsing and make the spec here!
			   	}
			   	values.push({name : object_name, fields : fields});
			};
			return values;
		}

	}]);


	var specs = [
	{
		name : 'Tool',
		alias : 'SoftwareApplication',
		spec_path : 'specs/default/softwareapplication/specifications.json',
	},
	{
		name : 'Event',
		alias : 'Event',
		spec_path : 'specs/default/event/specifications.json',
	}
	];


})();



(function($)
	{
	  	$(document).ready(function()
	  	{
	  		$( ".addfield_button" ).click(function()
		  	{
		  		$('.field_values').show();
			});
		});
	}
)(jQuery);