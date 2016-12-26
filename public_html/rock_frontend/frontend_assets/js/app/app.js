app = angular.module('app', []);
app.controller("NavCtrl",function($scope,$http){
	$http.get('/rock.api/NavApi.php',{cahce: true}).then(function (results) {
        $scope.categories = results.data;
        
        console.log(results.data);
    });
});


