(function (angular, $, _) {

  var apilogging = angular.module('apilogging');

  apilogging.config(
    function ($routeProvider) {
      $routeProvider.when('/apilogging/log', {
        controller: 'ApiloggingApiLogCtrl',
        templateUrl: '~/apilogging/ApiLogCtrl.html'
      });
    }
  );

  apilogging.controller('ApiloggingApiLogCtrl',
    ['$scope', 'crmApi', 'crmStatus', 'crmUiHelp',
      function ($scope, crmApi, crmStatus, crmUiHelp) {

        var ts = $scope.ts = CRM.ts('apilogging');
        var hs = $scope.hs = crmUiHelp({file: 'CRM/apilogging/ApiLogCtrl'});

        $scope.search = function () {
          crmApi('ApiloggingLog', 'get', {
            "sequential": 1,
            "return": [
              "time_stamp",
              "id",
              "calling_contact_id",
              "calling_contact_id.display_name",
              "entity",
              "action",
              "parameters"
            ],
            "options": {"limit": ""}
          }).then(function (result) {
            $scope.results = result;
          });
        }

      }
    ]
  );

})(angular, CRM.$, CRM._);
