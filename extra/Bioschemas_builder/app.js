(function () {
	var app = angular.module('builder', []);

	app.controller('PanelController', function(){
		this.tab = -1;
		this.subTab = false;
		this.subValue = null;
		this.current_object = null;

		this.selectTab = function(setTab){
			this.tab = setTab;
			this.subTab = false;
			this.subValue = null;
			this.current_object = null;
		}

		this.isSelected = function(checkTab){
			return this.tab === checkTab;
		}

		this.selectSubTab = function(){
			this.subTab = true;
		}
	});

	app.controller('CreateController', function(){
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
			   	for (name in my_JSON_object.properties) {
			   		
			   		var card = false;
			   		var allowed = [];
			   		var vocabulary = [];

			   		var description = my_JSON_object.properties[name].description;

			   		if (my_JSON_object.required.includes(name)){
			   			var state = 'required';
			   		}
			   		else if (my_JSON_object.recommended.includes(name)){
			   			var state = 'recommended';
			   		}
			   		else {
			   			var state = 'optionnal';
			   		}

			   		
			   		for (subkey in my_JSON_object.properties[name].oneOf){
			   			if (typeof my_JSON_object.properties[name].oneOf[subkey].format != 'undefined'){
			   				allowed.push(my_JSON_object.properties[name].oneOf[subkey].format);
			   			}
			   			if (my_JSON_object.properties[name].oneOf[subkey].type == 'array'){
			   				card = true;
			   			}
			   			else if (my_JSON_object.properties[name].oneOf[subkey].type == 'string'){
			   				if (!allowed.includes('string')){
			   					allowed.push("string");
			   				}
			   			}
			   			else if (my_JSON_object.properties[name].oneOf[subkey].type == 'object'){
			   				for (allowed_object in my_JSON_object.properties[name].oneOf[subkey].properties.type.enum){
			   					allowed.push( my_JSON_object.properties[name].oneOf[subkey].properties.type.enum[allowed_object]);
			   				}
			   			}
			   			if (typeof my_JSON_object.properties[name].oneOf[subkey].enum == "object"){
			   				vocabulary = my_JSON_object.properties[name].oneOf[subkey].enum;
			   			}
			   		}

			   		if (vocabulary.length < 1){
			   			vocabulary = false;
			   		}
			   		fields.push({field_name : name, 
			   					field_prop : my_JSON_object.properties[name], 
			   					cardinality: card,
			   					description: description, 
			   					expected_types: allowed,
			   					vocabulary: vocabulary,
			   					state: state});
			   	}
			   	values.push({name : object_name, fields : fields});
			};
			return values;
		}
	});

	app.controller('OutputController', function(){
		this.json = {};

		this.initiate = function(object, specs){
			this.json = {};
			this.json["@type"] = object.alias;
			for (key in specs){
				if (specs[key].name == object.name){
					for (subkey in specs[key].fields){
						var field = specs[key].fields[subkey];
						if (field.state == 'required'){
							this.json[field.field_name] = "null";
						}
					}
				}
			}
		}

		this.addItem = function(){
			console.log('Form was validated');
		}
	});


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
		},
		{
			name : 'Training Material',
			alias : 'CreativeWork',
			spec_path : 'specs/default/creativework/specifications.json',
		}
	];


	app.directive('outputCode', function(){
		return {
			restrict: 'A',
			templateUrl: 'include/output-snippet.html'
		};
	});

	app.directive('fieldEditor', function(){
		return {
			restrict: 'A',
			templateUrl: 'include/field-editor.html'
		};
	});

	app.directive('fieldSelecttor', function(){
		return {
			restrict: 'A',
			templateUrl: 'include/field-selector.html'
		};
	});

	app.directive('objectSelector', function(){
		return {
			restrict: 'A',
			templateUrl: 'include/object-selector.html'
		};
	});

})();



