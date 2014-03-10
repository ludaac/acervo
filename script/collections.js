/*global define, Backbone*/
define(function (require, exports, module) {
    "use strict";
    
    var SearchItems = Backbone.Collection.extend({
        url: '/books'
    });
    
    exports.SearchItems = SearchItems;
});