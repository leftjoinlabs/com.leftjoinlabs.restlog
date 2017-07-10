(function (angular, $, _) {

  var apilogging = angular.module('apilogging');

  apilogging.config(
    function ($routeProvider) {
      $routeProvider.when('/apilogging/log', {
        controller: 'ApiloggingApiLogCtrl',
        templateUrl: '~/apilogging/ApiLogCtrl.html',

        resolve: {

          entityOptions: function (crmApi) {
            return crmApi('ApiloggingLog', 'getuniquevalues', {'field': 'entity'})
              .then(function (data) {
                return data.values;
              }, function (error) {
                if (error.is_error) {
                  CRM.alert(error.error_message, ts("Error"), "error");
                }
                else {
                  return error;
                }
              });
          },

          actionsOptions: function (crmApi) {
            return crmApi('ApiloggingLog', 'getuniquevalues', {'field': 'action'})
              .then(function (data) {
                return data.values;
              }, function (error) {
                if (error.is_error) {
                  CRM.alert(error.error_message, ts("Error"), "error");
                }
                else {
                  return error;
                }
              });
          },

          callingContactOptions: function (crmApi) {
            return crmApi('ApiloggingLog', 'getuniquevalues', {'field': 'calling_contact'})
              .then(function (data) {
                return data.values;
              }, function (error) {
                if (error.is_error) {
                  CRM.alert(error.error_message, ts("Error"), "error");
                }
                else {
                  return error;
                }
              });
          }

        }
      });
    }
  );

  apilogging.controller('ApiloggingApiLogCtrl',
    ['$scope', 'crmApi', 'crmStatus', 'crmUiHelp', 'entityOptions', 'actionsOptions', 'callingContactOptions',
      function ($scope, crmApi, crmStatus, crmUiHelp, entityOptions, actionsOptions, callingContactOptions) {

        $scope.entityOptions = entityOptions;
        $scope.actionOptions = actionsOptions;
        $scope.callingContactOptions = callingContactOptions;

        $scope.formValues = {
          entity: [],
          action: [],
          callingContact: []
        };

        var ts = $scope.ts = CRM.ts('apilogging');
        var hs = $scope.hs = crmUiHelp({file: 'CRM/apilogging/ApiLogCtrl'});

        /**
         * Looks at $scope.formValues. Then assembles and returns an object
         *
         * @returns object
         */
        var getSearchParams = function () {
          var params = {};
          if ($scope.formValues.entity.length > 0) {
            params = _.merge(params, {"entity": {"IN": $scope.formValues.entity}});
          }
          if ($scope.formValues.action.length > 0) {
            params = _.merge(params, {"action": {"IN": $scope.formValues.action}});
          }
          if ($scope.formValues.callingContact.length > 0) {
            params = _.merge(params, {"calling_contact_id": {"IN": $scope.formValues.callingContact}});
          }
          return params;
        };

        // Make the API request for the search
        $scope.search = function () {
          var params = {
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
            "options": {
              "limit": 50,
              "sort": "time_stamp desc"
            }
          };
          var searchParams = getSearchParams();
          params = _.merge(params, searchParams);
          console.log(searchParams);
          crmApi('ApiloggingLog', 'get', params).then(function (results) {
            $scope.results = results;
          });
        };

        // perform the search now so that the page begins with some results
        $scope.search();

        $scope.delete = function () {
          alert('TODO');
        }

      }
    ]
  );

})(angular, CRM.$, CRM._);
