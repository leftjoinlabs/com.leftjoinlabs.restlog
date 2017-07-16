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

  /**
   * @ngdoc filter
   * @name formatJsonString
   *
   * @description
   *
   * Reads a string containing JSON data and formats this string a little bit
   * nicer. For example, if the input string does not have any spaces, this
   * filter will add spaces.
   *
   * @param {string} jsonString
   * @returns {string}
   *
   */
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

  /**
   * @ngdoc service
   * @name Searcher
   *
   * @description
   *
   * Provides an interface to the server for retrieving ApiloggingLog data.
   */
  apilogging.factory('Searcher', ['crmApi', function (crmApi) {

    var Searcher = function (entity) {

      /**
       * @property {string} entity
       *   The name of the entity for which we would like to perform the search.
       *   e.g. "contact"
       */
      this.entity = entity;

      /**
       * @property {boolean} isBusy
       *   True when this Searcher instance is in the process of fetching data
       *   from the server.
       */
      this.isBusy = false;

      /**
       * @property {integer} pageSize
       *   The number of records to fetch from the server at one time.
       */
      this.pageSize = 50;

      /**
       * @property {object} defaultParams
       *   Basic parameters to be passed to the CiviCRM API when fetching
       *   records from the server.
       */
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

      /**
       * @property {object} results
       *   The result set, along with metrics about it.
       */
      this.results = {

        /**
         * @property {integer} count
         *   The total number of results on the server (even if all of those
         *   results have not been fetched)
         */
        count: 0,

        /**
         * @property {array.object} data
         *   The records fetched from the server
         */
        data: [],

        /**
         * @property {boolean} isComplete
         *   True when `data` contains all the records which exist on the
         *   server. False when we have only fetched a partial data set.
         */
        isComplete: false,

        /**
         * @property {boolean} isEmpty
         *   True when `data` contains zero records.
         */
        isEmpty: true
      };
    };

    /**
     * @method search
     *
     * @description Fetch records from the server
     *
     * @param {object} searchParams
     *   Parameters expected by the CiviCRM API when `action` = `get` and
     *   `entity` = `ApiloggingLog`.
     *
     * @param {boolean} isFresh
     *   - Pass `true` to perform a search from scratch and fetch the first
     *     records.
     *   - Pass `false` to continue fetching the results from the previously
     *     run search. One more page will be fetched according to
     *     `Searcher.pageSize`.
     */
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

    /**
     * @method freshSearch
     * @param {object} searchParams
     * @see Searcher.search for more details
     */
    Searcher.prototype.freshSearch = function (searchParams) {
      this.search(searchParams, true);
    };

    /**
     * @method continuedSearch
     * @param {object} searchParams
     * @see Searcher.search for more details
     */
    Searcher.prototype.continuedSearch = function (searchParams) {
      this.search(searchParams, false);
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
       * Looks at `$scope.formValues`. Then assembles and returns an object
       * containing appropriate parameters to pass to the CiviCRM API.
       *
       * @returns {object}
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

      /**
       * Does the following:
       *
       *  - Fetches data from the server according to search form.
       *  - Refreshes option values for search form elements
       */
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
