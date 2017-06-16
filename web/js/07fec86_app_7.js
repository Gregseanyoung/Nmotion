'use strict';

var ngSetContentHeight = function () {
    // reset height
    $('.right_col').css('min-height', $(window).height());

    var bodyHeight = $('body').outerHeight(),
        footerHeight = $('body').hasClass('footer_fixed') ? 0 : $('.sidebar-footer').height(),
        leftColHeight = $('.left_col').eq(1).height() + $('.sidebar-footer').height(),
        contentHeight = bodyHeight < leftColHeight ? leftColHeight : bodyHeight;

    // normalize content
    contentHeight -= $('.nav_menu').height() + footerHeight;

    $('.right_col').css('min-height', contentHeight);
};

var app = angular.module('DashboardApp', []).config(function($interpolateProvider){
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
});

app.controller('DashboardController', ['$scope', function($scope) {
    $scope.message = 'Hello world';
    $scope.showSearch = function() {
        return false;
    };
}]);

app.directive('searchBox', [function() {
    return {
        restrict: 'AE',
        replace: true,
        controller: ('SearchController', ['$rootScope', '$scope', function($rootScope, $scope) {
            $scope.showSearch = function() {
                return false;
            }
        }]),
        templateUrl: function(element, attrs) {
            if ( typeof attrs.templateUrl == 'undefined' ) {
                return 'template/search.html';
            } else {
                return attrs.templateUrl;
            }
        }
    };
}]);

app.directive('topNavigation', [function() {
    return {
        restrict: 'AE',
        replace: true,
        controller: ('TopNavigationController', ['$rootScope', '$scope', function($rootScope, $scope) {
            $scope.menu_toggle = function() {
                if ($('#body').hasClass('nav-md')) {
                    $('#sidebar_menu').find('li.active ul').hide();
                    $('#sidebar_menu').find('li.active').addClass('active-sm').removeClass('active');
                } else {
                    $('#sidebar_menu').find('li.active-sm ul').show();
                    $('#sidebar_menu').find('li.active-sm').addClass('active').removeClass('active-sm');
                }

                $('#body').toggleClass('nav-md nav-sm');

                ngSetContentHeight();
            }
        }]),
        templateUrl: function(element, attrs) {
            if ( typeof attrs.templateUrl == 'undefined' ) {
                return 'template/top-menu.html';
            } else {
                return attrs.templateUrl;
            }
        }
    };
}]);
app.directive('mainMenu', [function() {
   return {
       restrict: 'AE',
       replace: true,
       controller: ('MainMenuController', ['$rootScope','$scope', function($rootScope, $scope) {
           $scope.menuItems = [
               { desc: 'Dashboard', icon: 'home', active: 'active', child: 'block', children: [
                   { desc: 'Config list', icon: '', link: ''}
               ]},
               { desc: 'Restaurants management', active: '', icon: 'edit', child: 'none', children: [
                   { desc: 'List restaurants', icon: '', link: ''},
                   { desc: 'Add restaurants', icon: '', link: ''}
               ]},
               { desc: 'User management', icon: 'desktop', active: '', child: 'none', children: [
                   { desc: 'List all users', icon: '', link: ''}
               ]},
               { desc: 'Order management', icon: 'table', active: '', child: 'none', children: [
                   { desc: 'List all orders', icon: '', link: ''}
               ]},
           ];
           $scope.menuClick = function(elem) {
               $scope.menuItems.forEach(function(item) {
                   item.active = '';
                   item.child = 'none';
               });

               elem.active = 'active';
               elem.child = 'block';
           }
       }]),
       templateUrl: function(element, attrs) {
           if ( typeof attrs.templateUrl == 'undefined' ) {
               return 'template/main-menu.html';
           } else {
               return attrs.templateUrl;
           }
       }
   };
}]);
