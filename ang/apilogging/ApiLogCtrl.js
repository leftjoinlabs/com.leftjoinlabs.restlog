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

  apilogging.factory('Searcher', ['crmApi', function (crmApi) {

    var Searcher = function (entity) {
      this.entity = entity;
      this.isBusy = false;
      this.pageSize = 50;
      this.defaultParams = {
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
          "limit": this.pageSize,
          "sort": "id", // "time_stamp desc" TODO
          "offset": 0
        }
      };
      this.results = {
        count: 0,
        data: [],
        isComplete: false
      };
    };

    Searcher.prototype.search = function (searchParams, isFresh) {
      var ignore = this.isBusy || (this.results.isComplete && !isFresh);
      if (ignore) return;
      this.isBusy = true;
      var params = {};
      _.merge(params, this.defaultParams);
      _.merge(params, searchParams);
      if (!isFresh) {
        // If continuing, then use an offset
        params.options.offset = this.results.data.length;
      }
      crmApi(this.entity, 'get', params).then(function (result) {
        if (isFresh) {
          // Clear existing results if doing a fresh search
          this.results.data = [];
          this.results.isComplete = false;
        }
        if (result.values.length === 0) {
          this.results.isComplete = true;
        }
        // Add new results to existing results
        this.results.data = this.results.data.concat(result.values);
        if (isFresh) {
          crmApi(this.entity, 'getcount', params).then(function (result) {
            this.results.count = result.result;
            this.isBusy = false;
          }.bind(this));
        }
        else {
          this.isBusy = false;
        }
      }.bind(this));
    };

    Searcher.prototype.freshSearch = function (searchParams) {
      this.search(searchParams, true);
    };

    Searcher.prototype.continuedSearch = function (searchParams) {
      this.search(searchParams, false)
    };

    return Searcher;

  }]);

  apilogging.controller('ApiloggingApiLogCtrl',
    function ($scope, crmApi, crmStatus, crmUiHelp, entityOptions, actionsOptions, callingContactOptions, Searcher) {

      $scope.ts = CRM.ts('apilogging');
      $scope.hs = crmUiHelp({file: 'CRM/apilogging/ApiLogCtrl'});
      $scope.entityOptions = entityOptions;
      $scope.actionOptions = actionsOptions;
      $scope.callingContactOptions = callingContactOptions;
      $scope.searcher = new Searcher('ApiloggingLog');
      $scope.formValues = {          entity: [],
        action: [],
        callingContact: []
      };

      /**
       * Looks at $scope.formValues. Then assembles and returns an object
       *
       * @returns object
       */
      $scope.getSearchParams = function () {
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

      // TODO: Search, filter one value, then clear that value. Should update. Doesn't.


      // start with search already done
      $scope.searcher.freshSearch($scope.getSearchParams());

      $scope.delete = function () {
        alert('TODO');
      }

    },
    ['$scope', 'crmApi', 'crmStatus', 'crmUiHelp', 'entityOptions', 'actionsOptions', 'callingContactOptions', 'Searcher'
    ]
  );

})(angular, CRM.$, CRM._);
