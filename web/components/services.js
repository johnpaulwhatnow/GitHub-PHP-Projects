angular.module('DataResource', []).factory('DataResource', function() {
	var data = {};
	data.detailedRepos = {};
	data.repos = [];
	return data;
});
