'use strict';
// Declare app level module which depends on views, and components
angular.module('myApp', [
	'ngAnimate',
	'ui.bootstrap',
	'ui.router',
	'ui.bootstrap.navbar',
	'Model',
	'DataResource',
	'angularMoment',
	'ngResource'
]).config(['$httpProvider', function($httpProvider) {
	$httpProvider.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
}]).config(function($stateProvider, $urlRouterProvider){
	$stateProvider
        // home states and nested views using UI Router
        .state('app', {
            url: '/',
            views:{
            	'nav':{ templateUrl:'templates/nav.html'},
            	'content':{
					templateUrl:'templates/home.html',
					controller: function($state){
						$state.go('app.data');
					}
				},
            },
            
        })
		//route used to get data from server. This route uses promises in case we ever need more than one request.
        .state('app.data',{
        	url:'get-data', 
        	views:{
        		'content@':{
        			templateUrl: 'templates/home.html',
        			controller:'DataCtrl'
        		}
        	}
        	
        })
		// route to list all the repos
		.state('app.repos',{
			url:'repos',
			views:{
				'content@':{
					templateUrl: 'templates/repos.html',
					controller:'ReposCtrl'
				}
			}

		})
		//detail view for a repo
		.state('app.repos.detail', {
		url: '/:id',
		views: {
			'content@': {
				templateUrl: 'templates/repo.html',
				controller: 'RepoCtrl'
			}
		}
	});
	  $urlRouterProvider.otherwise('/get-data');

})



.controller("ReposCtrl", function($scope, DataResource, $resource, $state) {

	//do we have data already
	//have we already queried this repo? if so, we've stored it
	if(_.isEmpty(DataResource.repos)) {
		$state.go('app.data');
	} else{
		$scope.repos = DataResource.repos;
	}
	//set up the time-based sorters
	_.forEach($scope.repos, function(key){

		//first, the "createdAt" property
		var timestamp = key.createdAt.timestamp + key.createdAt.offset;
		var time = moment.unix(timestamp);
		key.created_at_unix =timestamp;
		key.created_at_moment = time;

		//same thing for the LastPushAt
		var timestamp = key.lastPushDate.timestamp + key.lastPushDate.offset;
		var time = moment.unix(timestamp);
		key.last_push_date_unix =timestamp;
		key.last_push_date_moment = time;
	});

	//init the sort
	var sorters = [];
	//star sort
	var repoStars = { key:'stars', display:'Stars', default:'desc'};
	sorters.push( repoStars );
	//name sort
	var repoName = { key:'name', display:'Name', default:'asc'};
	sorters.push( repoName );
	//create time sort
	var time = { key:'created_at_unix', display:'Created Time', default:'created_at_unix'};
	sorters.push( time );
	//last push date sort time
	var time = { key:'last_push_date_unix', display:'Last Push Date', default:'last_push_date_unix'};
	sorters.push( time );

	$scope.sorters = sorters;
	$scope.sortBy="-stars";

})
.controller("DataCtrl", function($scope, $state, DataResource,Repos, $q) {
	var self = this;
	$scope.repos = Repos.query();

	$q.all([
		$scope.repos.$promise
	]).then(function() {
		//CODE AFTER RESOURCES ARE LOADED
		DataResource.repos = $scope.repos;

		$state.go('app.repos');


	});

})
.controller("RepoCtrl", function($scope, $stateParams, DataResource, Repos, $resource) {
	var repoId = $stateParams.id;

	//have we already queried this repo? if so, we've stored it
	if(DataResource.detailedRepos[repoId] !==  undefined) {
		var selectedRepo = DataResource.detailedRepos[repoId];

		var timestamp = selectedRepo.createdAt.timestamp + selectedRepo.createdAt.offset;
		var time = moment.unix(timestamp);
		selectedRepo.createdAtMoment = time;

		// now for lastPushDate
		var timestamp = selectedRepo.lastPushDate.timestamp + selectedRepo.lastPushDate.offset;
		var time = moment.unix(timestamp);
		selectedRepo.lastPushDatetMoment = time;

		//add to scope
		$scope.repo = selectedRepo;
	} else{
		var RepoResource = $resource('/ajax/repo/:repoId', {repoId:'@id'});
		var selectedRepo = RepoResource.get({repoId:repoId}, function() {
			//first, the "createdAt" property
			var timestamp = selectedRepo.createdAt.timestamp + selectedRepo.createdAt.offset;
			var time = moment.unix(timestamp);
			selectedRepo.createdAtMoment = time;

			// now for lastPushDate
			var timestamp = selectedRepo.lastPushDate.timestamp + selectedRepo.lastPushDate.offset;
			var time = moment.unix(timestamp);
			selectedRepo.lastPushDatetMoment = time;

			//add to scope
			$scope.repo = selectedRepo;
			//let's add this repo to our existing list
			var repoId = selectedRepo.id;
			DataResource.detailedRepos[repoId] = selectedRepo;
		});
	}
})
.directive("sort", function() {
    return {
        
        templateUrl:'templates/sort.html',
        link: function(scope, elem, attrs) {
        	//var buttons = elem.find('button');
        	//console.log(buttons);
        	scope.setSortBy = function(param){

        		scope.sortBy = param;
        	}
        }
    }
});
