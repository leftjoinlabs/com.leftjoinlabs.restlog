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

  apilogging.filter('formatJsonString', function () {
    return function (jsonString) {
      try {
        return JSON.stringify(JSON.parse(jsonString), null, 1);
      }
      catch (error) {
        return '';
      }
    };
  });

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
          "sort": "time_stamp desc",
          "offset": 0
        }
      };
      this.results = {
        count: 0,
        data: [],
        isComplete: false,
        isEmpty: true
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
          this.results.isEmpty = (result.values.length === 0);
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

  apilogging.controller('ApiloggingApiLogCtrl', [
    '$scope',
    'crmApi',
    'crmStatus',
    'crmUiHelp',
    'Searcher',
    'formatJsonStringFilter',
    function ($scope, crmApi, crmStatus, crmUiHelp, Searcher, formatJsonStringFilter) {
      $scope.ts = CRM.ts('apilogging');
      $scope.hs = crmUiHelp({file: 'CRM/apilogging/ApiLogCtrl'});
      $scope.searcher = new Searcher('ApiloggingLog');
      $scope.formValues = {
        entity: [],
        action: [],
        callingContact: [],
        startDate: '',
        endDate: ''
      };

      /**
       * Looks at $scope.formValues. Then assembles and returns an object
       *
       * @returns object
       */
      $scope.getSearchParams = function () {
        var params = {};
        if ($scope.formValues.entity.length > 0) {
          _.merge(params, {"entity": {"IN": $scope.formValues.entity}});
        }
        if ($scope.formValues.action.length > 0) {
          _.merge(params, {"action": {"IN": $scope.formValues.action}});
        }
        if ($scope.formValues.callingContact.length > 0) {
          _.merge(params, {"calling_contact_id": {"IN": $scope.formValues.callingContact}});
        }
        if ($scope.formValues.startDate.length > 0 && $scope.formValues.endDate.length > 0) {
          _.merge(params, {
            "time_stamp": {"BETWEEN": [$scope.formValues.startDate, $scope.formValues.endDate]}
          });
        }
        else {
          if ($scope.formValues.startDate.length > 0) {
            _.merge(params, {"time_stamp": {">=": $scope.formValues.startDate}});
          }
          else {
            if ($scope.formValues.endDate.length > 0) {
              _.merge(params, {"time_stamp": {"<=": $scope.formValues.endDate}});
            }
          }
        }
        return params;
      };

      $scope.refresh = function () {
        $scope.searcher.freshSearch($scope.getSearchParams());
        $scope.options = {};
        crmApi('ApiloggingLog', 'getuniquevalues', {'field': 'entity'})
          .then(function (data) {
            $scope.options.entity = data.values;
          });
        crmApi('ApiloggingLog', 'getuniquevalues', {'field': 'action'})
          .then(function (data) {
            $scope.options.action = data.values;
          });
        crmApi('ApiloggingLog', 'getuniquevalues', {'field': 'calling_contact'})
          .then(function (data) {
            $scope.options.callingContact = data.values;
          });
      };

      // Start with a search already performed
      $scope.refresh();

    }
  ]);

})(angular, CRM.$, CRM._);
