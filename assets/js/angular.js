'use strict';
var app = angular.module("deliveryApp", []);

app.controller('deliveryCtrl', function ($scope, $http) {

    $scope.range = {
        from: 0,
        to: 0
    };

    $scope.deliveryMethod = {
        id: false,
        name: 'Delivery Name',
        url: 'http://',
        weight_from: 0,
        weight_to: 0,
        notes: '',
        showRanges: false,
        ranges: [angular.copy($scope.range)]
    };

    $scope.deliveryMethods = [];

    $scope.add = function (argument, argumentPropery, element, event) {
        if (!argumentPropery) {
            argument.push(angular.copy($scope[element]));
        } else {
            argument[argumentPropery].push(angular.copy($scope[element]));
        }

    }
    $scope.deleteMe = function (objectArray, index, deleteFirst) {
        if (deleteFirst == true) {
            objectArray.splice(index, 1);
        } else {
            if (objectArray.length > 1) {
                objectArray.splice(index, 1);
            }
        }

    }

    $http.get('/get.php').then(function (json) {
        angular.forEach(json.data, function (data, index) {
            $scope.deliveryMethods[index] = data;
        });
    });


    $scope.submitForm = function () {
        var url = '/save.php';
        var data = $scope.deliveryMethods;
        $http.post(url, data).then(function (json) {
            angular.forEach(json.data, function (data, index) {
                $scope.deliveryMethods[index] = data;
            });
        });
    }
});
