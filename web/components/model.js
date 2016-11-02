'use strict';
angular.module('Model',['ngResource']).factory('Repos', function($resource) {
	return $resource('/ajax/repos', {},
		{

		});
});

