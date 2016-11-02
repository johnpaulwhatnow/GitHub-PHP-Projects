'use strict';
angular.module('ui.bootstrap.navbar', ['ngAnimate', 'ui.bootstrap']);
angular.module('ui.bootstrap.navbar').controller('navbar', function ($scope) {
  $scope.isCollapsed = true;
});