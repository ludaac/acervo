/*global define, Backbone*/
define(function (require, exports, module) {
    "use strict";
    
    var Book = Backbone.Model.extend({
        urlRoot: '/books'
    });
});