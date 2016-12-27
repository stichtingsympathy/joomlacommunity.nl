var globalCacheForjQueryReplacement=window.jQuery;/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
 */
if(window.jQuery=window.kQuery,/*! jQuery UI - v1.11.4 - 2016-01-08
 * http://jqueryui.com
 * Includes: widget.js
 * Copyright jQuery Foundation and other contributors; Licensed MIT */
function(a){"function"==typeof define&&define.amd?define(["jquery"],a):a(kQuery)}(function(a){/*!
     * jQuery UI Widget 1.11.4
     * http://jqueryui.com
     *
     * Copyright jQuery Foundation and other contributors
     * Released under the MIT license.
     * http://jquery.org/license
     *
     * http://api.jqueryui.com/jQuery.widget/
     */
var b=0,c=Array.prototype.slice;a.cleanData=function(b){return function(c){var d,e,f;for(f=0;null!=(e=c[f]);f++)try{d=a._data(e,"events"),d&&d.remove&&a(e).triggerHandler("remove")}catch(a){}b(c)}}(a.cleanData),a.widget=function(b,c,d){var e,f,g,h,i={},j=b.split(".")[0];return b=b.split(".")[1],e=j+"-"+b,d||(d=c,c=a.Widget),a.expr[":"][e.toLowerCase()]=function(b){return!!a.data(b,e)},a[j]=a[j]||{},f=a[j][b],g=a[j][b]=function(a,b){return this._createWidget?void(arguments.length&&this._createWidget(a,b)):new g(a,b)},a.extend(g,f,{version:d.version,_proto:a.extend({},d),_childConstructors:[]}),h=new c,h.options=a.widget.extend({},h.options),a.each(d,function(b,d){return a.isFunction(d)?void(i[b]=function(){var a=function(){return c.prototype[b].apply(this,arguments)},e=function(a){return c.prototype[b].apply(this,a)};return function(){var b,c=this._super,f=this._superApply;return this._super=a,this._superApply=e,b=d.apply(this,arguments),this._super=c,this._superApply=f,b}}()):void(i[b]=d)}),g.prototype=a.widget.extend(h,{widgetEventPrefix:f?h.widgetEventPrefix||b:b},i,{constructor:g,namespace:j,widgetName:b,widgetFullName:e}),f?(a.each(f._childConstructors,function(b,c){var d=c.prototype;a.widget(d.namespace+"."+d.widgetName,g,c._proto)}),delete f._childConstructors):c._childConstructors.push(g),a.widget.bridge(b,g),g},a.widget.extend=function(b){for(var d,e,f=c.call(arguments,1),g=0,h=f.length;g<h;g++)for(d in f[g])e=f[g][d],f[g].hasOwnProperty(d)&&void 0!==e&&(a.isPlainObject(e)?b[d]=a.isPlainObject(b[d])?a.widget.extend({},b[d],e):a.widget.extend({},e):b[d]=e);return b},a.widget.bridge=function(b,d){var e=d.prototype.widgetFullName||b;a.fn[b]=function(f){var g="string"==typeof f,h=c.call(arguments,1),i=this;return g?this.each(function(){var c,d=a.data(this,e);return"instance"===f?(i=d,!1):d?a.isFunction(d[f])&&"_"!==f.charAt(0)?(c=d[f].apply(d,h),c!==d&&void 0!==c?(i=c&&c.jquery?i.pushStack(c.get()):c,!1):void 0):a.error("no such method '"+f+"' for "+b+" widget instance"):a.error("cannot call methods on "+b+" prior to initialization; attempted to call method '"+f+"'")}):(h.length&&(f=a.widget.extend.apply(null,[f].concat(h))),this.each(function(){var b=a.data(this,e);b?(b.option(f||{}),b._init&&b._init()):a.data(this,e,new d(f,this))})),i}},a.Widget=function(){},a.Widget._childConstructors=[],a.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",defaultElement:"<div>",options:{disabled:!1,create:null},_createWidget:function(c,d){d=a(d||this.defaultElement||this)[0],this.element=a(d),this.uuid=b++,this.eventNamespace="."+this.widgetName+this.uuid,this.bindings=a(),this.hoverable=a(),this.focusable=a(),d!==this&&(a.data(d,this.widgetFullName,this),this._on(!0,this.element,{remove:function(a){a.target===d&&this.destroy()}}),this.document=a(d.style?d.ownerDocument:d.document||d),this.window=a(this.document[0].defaultView||this.document[0].parentWindow)),this.options=a.widget.extend({},this.options,this._getCreateOptions(),c),this._create(),this._trigger("create",null,this._getCreateEventData()),this._init()},_getCreateOptions:a.noop,_getCreateEventData:a.noop,_create:a.noop,_init:a.noop,destroy:function(){this._destroy(),this.element.unbind(this.eventNamespace).removeData(this.widgetFullName).removeData(a.camelCase(this.widgetFullName)),this.widget().unbind(this.eventNamespace).removeAttr("aria-disabled").removeClass(this.widgetFullName+"-disabled ui-state-disabled"),this.bindings.unbind(this.eventNamespace),this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus")},_destroy:a.noop,widget:function(){return this.element},option:function(b,c){var d,e,f,g=b;if(0===arguments.length)return a.widget.extend({},this.options);if("string"==typeof b)if(g={},d=b.split("."),b=d.shift(),d.length){for(e=g[b]=a.widget.extend({},this.options[b]),f=0;f<d.length-1;f++)e[d[f]]=e[d[f]]||{},e=e[d[f]];if(b=d.pop(),1===arguments.length)return void 0===e[b]?null:e[b];e[b]=c}else{if(1===arguments.length)return void 0===this.options[b]?null:this.options[b];g[b]=c}return this._setOptions(g),this},_setOptions:function(a){var b;for(b in a)this._setOption(b,a[b]);return this},_setOption:function(a,b){return this.options[a]=b,"disabled"===a&&(this.widget().toggleClass(this.widgetFullName+"-disabled",!!b),b&&(this.hoverable.removeClass("ui-state-hover"),this.focusable.removeClass("ui-state-focus"))),this},enable:function(){return this._setOptions({disabled:!1})},disable:function(){return this._setOptions({disabled:!0})},_on:function(b,c,d){var e,f=this;"boolean"!=typeof b&&(d=c,c=b,b=!1),d?(c=e=a(c),this.bindings=this.bindings.add(c)):(d=c,c=this.element,e=this.widget()),a.each(d,function(d,g){function h(){if(b||f.options.disabled!==!0&&!a(this).hasClass("ui-state-disabled"))return("string"==typeof g?f[g]:g).apply(f,arguments)}"string"!=typeof g&&(h.guid=g.guid=g.guid||h.guid||a.guid++);var i=d.match(/^([\w:-]*)\s*(.*)$/),j=i[1]+f.eventNamespace,k=i[2];k?e.delegate(k,j,h):c.bind(j,h)})},_off:function(b,c){c=(c||"").split(" ").join(this.eventNamespace+" ")+this.eventNamespace,b.unbind(c).undelegate(c),this.bindings=a(this.bindings.not(b).get()),this.focusable=a(this.focusable.not(b).get()),this.hoverable=a(this.hoverable.not(b).get())},_delay:function(a,b){function c(){return("string"==typeof a?d[a]:a).apply(d,arguments)}var d=this;return setTimeout(c,b||0)},_hoverable:function(b){this.hoverable=this.hoverable.add(b),this._on(b,{mouseenter:function(b){a(b.currentTarget).addClass("ui-state-hover")},mouseleave:function(b){a(b.currentTarget).removeClass("ui-state-hover")}})},_focusable:function(b){this.focusable=this.focusable.add(b),this._on(b,{focusin:function(b){a(b.currentTarget).addClass("ui-state-focus")},focusout:function(b){a(b.currentTarget).removeClass("ui-state-focus")}})},_trigger:function(b,c,d){var e,f,g=this.options[b];if(d=d||{},c=a.Event(c),c.type=(b===this.widgetEventPrefix?b:this.widgetEventPrefix+b).toLowerCase(),c.target=this.element[0],f=c.originalEvent)for(e in f)e in c||(c[e]=f[e]);return this.element.trigger(c,d),!(a.isFunction(g)&&g.apply(this.element[0],[c].concat(d))===!1||c.isDefaultPrevented())}},a.each({show:"fadeIn",hide:"fadeOut"},function(b,c){a.Widget.prototype["_"+b]=function(d,e,f){"string"==typeof e&&(e={effect:e});var g,h=e?e===!0||"number"==typeof e?c:e.effect||c:b;e=e||{},"number"==typeof e&&(e={duration:e}),g=!a.isEmptyObject(e),e.complete=f,e.delay&&d.delay(e.delay),g&&a.effects&&a.effects.effect[h]?d[b](e):h!==b&&d[h]?d[h](e.duration,e.easing,f):d.queue(function(c){a(this)[b](),f&&f.call(d[0]),c()})}});a.widget}),function(a,b,c){c.widget("koowa.scopebar",{widgetEventPrefix:"scopebar:",options:{template:function(){}},_create:function(){var a=c(".k-js-filter-prototype");this.template=a.clone(),this.template.removeClass(".k-js-filter-prototype"),a.remove(),this._addEvents();var b=c(".k-js-filter-container");c(".k-js-filters div[data-filter]").each(function(d,e){var f=a.clone();e=c(this),e.addClass("k-js-dropdown-content k-scopebar-dropdown__body__content"),f.find(".k-js-dropdown-body").prepend(e),f.find(".k-js-dropdown-title").html(e.data("title"));var g=f.find(".k-js-dropdown-button"),h=g.data("tooltip-title");h&&(h=h.replace("%s",e.data("title")),g.ktooltip({container:".k-ui-container",delay:{show:500,hide:50},title:h}));var i=f.find(".k-js-dropdown-label"),j=e.data("label"),k=e.data("count");k&&k>0&&(j=k),j?i.attr("data-has-label","").html(j):i.removeAttr("data-has-label").hide(),e.show(),f.show(),b.append(f);var l=b.find(".k-js-dropdown-label[data-has-label]").length,m=c(".k-js-filter-count");l?m.show():m.hide()})},_addEvents:function(){var a=this,d=function(){return c(".k-js-dropdown").hasClass("k-is-active")};c(b).keyup(function(b){if(39==b.keyCode&&d()){var e=c(".k-js-dropdown.k-is-active").next().find(c(".k-js-dropdown-button"));e.hasClass("k-js-dropdown-button")&&(a.closeDropdown(),a.openDropdown(e))}if(37==b.keyCode&&d()){var f=c(".k-js-dropdown.k-is-active").prev().find(c(".k-js-dropdown-button"));f.hasClass("k-js-dropdown-button")&&(a.closeDropdown(),a.openDropdown(f))}27==b.keyCode&&d()&&a.closeDropdown()}),c("html").click(function(b){var d=c(b.target),e=b.target.className.search("select2-")!==-1,f=d.parents(".datepicker-dropdown").length>0||d.is("td")&&d.hasClass("day");e||f||0!==d.parents(".k-js-filter-container").length||a.closeDropdown()}),this.element.on("click","*",function(b){var d=c(b.target);d.hasClass("k-js-dropdown-button")||(d=d.parents(".k-js-dropdown-button")),0!==d.length&&(d.parent().hasClass("k-is-active")?a.closeDropdown():a.openDropdown(d),b.stopPropagation())}),this.element.on("mouseenter mouseleave","*",function(b){var e=c(b.target);e.hasClass("k-js-dropdown-button")||(e=e.parents(".k-js-dropdown-button")),0!==e.length&&d()&&!e.parent().hasClass("k-is-active")&&(a.closeDropdown(),a.openDropdown(e),e.focus())}),submitForm=function(b,d){d.find("select").each(function(a,d){var e=c(d).val();if(!e||""===e||"object"==typeof e&&1===e.length&&""===e[0]){var f=c(d).attr("name");f=f.replace("[]",""),c(d).removeAttr("name"),c(b).append('<input type="hidden" name="'+f+'" value="" />')}}),a._trigger("beforeSubmitForm",null,{form:b,box:d}),b.submit()},this.element.on("click",".k-js-clear-filter",function(a){a.preventDefault();var b=c(a.target).parents(".k-js-dropdown");b.find(":input").not(":button, :submit, :reset, :hidden").removeAttr("checked").removeAttr("selected").not(":checkbox, :radio").val("").filter("select").trigger("change");var d=a.target.form;d&&submitForm(d,b)}).on("click",".k-js-apply-filter",function(a){a.preventDefault();var b=a.target.form,d=c(a.target).parents(".k-js-dropdown");b&&submitForm(b,d)})},openDropdown:function(a){var b=a.parent();this.closeDropdown(),b.addClass("k-is-active");var c=b.find("select");1===c.length&&c.data("select2"),a.focus()},closeDropdown:function(){var a=c(".k-js-dropdown.k-is-active"),b=a.find("select");a.removeClass("k-is-active"),b.data("select2")&&b.select2("close")}})}(window,document,kQuery),!Koowa)var Koowa={};Function.prototype.bind||(Function.prototype.bind=function(a){if("function"!=typeof this)throw new TypeError("Function.prototype.bind - what is trying to be bound is not callable");var b=Array.prototype.slice.call(arguments,1),c=this,d=function(){},e=function(){return c.apply(this instanceof d&&a?this:a,b.concat(Array.prototype.slice.call(arguments)))};return d.prototype=this.prototype,e.prototype=new d,e});/*!
 * klass: a classical JS OOP façade
 * https://github.com/ded/klass
 * License MIT (c) Dustin Diaz & Jacob Thornton 2012
 */
var klass=function(){function a(a){return e.call(b(a)?a:function(){},a,1)}function b(a){return typeof a===h}function c(a,b,c){return function(){var d=this.supr;this.supr=c[j][a];var e={}.fabricatedUndefined,f=e;try{f=b.apply(this,arguments)}finally{this.supr=d}return f}}function d(a,d,e){for(var f in d)d.hasOwnProperty(f)&&(a[f]=b(d[f])&&b(e[j][f])&&i.test(d[f])?c(f,d[f],e):d[f])}function e(a,c){function e(){}function f(){this.initialize?this.initialize.apply(this,arguments):(c||i&&g.apply(this,arguments),k.apply(this,arguments))}e[j]=this[j];var g=this,h=new e,i=b(a),k=i?a:this,l=i?{}:a;return f.methods=function(a){return d(h,a,g),f[j]=h,this},f.methods.call(f,l).prototype.constructor=f,f.extend=arguments.callee,f[j].implement=f.statics=function(a,b){return a="string"==typeof a?function(){var c={};return c[a]=b,c}():a,d(this,a,g),this},f}var f=this,g=f.klass,h="function",i=/xyz/.test(function(){xyz})?/\bsupr\b/:/.*/,j="prototype";return a.noConflict=function(){return f.klass=g,this},a}();/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2007 - 2014 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
 */
if(function(a){Koowa.Class=klass({options:{},getOptions:function(){return{}},initialize:function(){this.setOptions(this.getOptions())},setOptions:function(b){return"object"==typeof b&&(this.options=a.extend(!0,{},this.options,b)),this}})}(window.kQuery),/**
 * Nooku Framework - http://nooku.org/framework
 *
 * @copyright	Copyright (C) 2015 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		https://github.com/nooku/nooku-framework for the canonical source repository
 */
"undefined"==typeof Koowa&&(Koowa={}),function(a){Koowa.Grid=Koowa.Class.extend({initialize:function(b){var c=this;this.element=a(b),this.form=this.element.is("form")?this.element:this.element.closest("form"),this.checkall=this.element.find(".k-js-grid-checkall"),this.checkboxes=this.element.find(".k-js-grid-checkbox").filter(function(b,c){return!a(c).prop("disabled")}),this.checkboxes.length||this.checkall.prop("disabled",!0),this.checkall.on("change.koowa",function(b,d){d||c.checkAll(a(this).prop("checked"))}),this.checkboxes.on("change.koowa",function(a,b){b||c.setCheckAll()}),this.setScopebar(),this.setTableHeaders(),this.setTableRows()},setScopebar:function(){var b=this;a(".k-js-filter-container",this.form).scopebar({beforeSubmitForm:function(){b.uncheckAll()}})},setTableHeaders:function(){this.form.on("click.koowa","th",function(b){var c=a(b.target),d=c.find("a");if(d.length)d.prop("href")?window.location.href=d.prop("href"):d.trigger("click",b);else{var e=c.find(".k-js-grid-checkall");e.length&&e.prop("checked",!e.is(":checked")).trigger("change")}})},setTableRows:function(){this.form.on("click.koowa","tr",function(b){var c=a(b.target);if(!c.is("[type=radio], [type=checkbox], a[href], span.footable-toggle")){var d=c.is("tr")?c:c.parents("tr"),e=d.find(".k-js-grid-checkbox");1!=d.data("readonly")&&e.length&&e.length&&e.prop("checked",!e.prop("checked")).trigger("change")}}),a(".k-js-grid-checkbox").on("change.koowa",function(b){var c,d=a(b.target),e=d.parents("tr"),f=e.parent();d.is("[type=radio]")&&f.find(".k-is-selected").removeClass("k-is-selected"),a(this).prop("checked")?e.addClass("k-is-selected"):e.removeClass("k-is-selected"),c=f.find(".k-is-selected").length,c>1?f.addClass("k-has-selected-multiple").removeClass("k-has-selected-single"):1===c?f.removeClass("k-has-selected-multiple").addClass("k-has-selected-single"):f.removeClass("k-has-selected-multiple").removeClass("k-has-selected-single")}).trigger("change",!0)},checkAll:function(b){var c=this.checkboxes.filter(function(c,d){return a(d).prop("checked")!==b});this.checkboxes.prop("checked",b),c.trigger("change",!0)},uncheckAll:function(){this.checkAll(!1)},setCheckAll:function(){var b=this.checkboxes.filter(function(b,c){return a(c).prop("checked")!==!1}).length;this.checkall.prop("checked",this.checkboxes.length===b),this.checkall.trigger("change",!0)}}),Koowa.Grid.getAllSelected=function(b){return a(".k-js-grid-checkbox:checked",b)},Koowa.Grid.getIdQuery=function(a){return decodeURIComponent(this.getAllSelected(a).serialize())},a(function(){a(".k-js-grid").each(function(b,c){new Koowa.Grid(a(c))})})}(window.kQuery),!Koowa)var Koowa={};!function(a){a(function(){a(".k-js-submittable").on("click.koowa",function(b){b.preventDefault(),new Koowa.Form(a(b.target).data("config")).submit()}),a(".k-js-grid-controller").each(function(){new Koowa.Controller.Grid({form:this})}),a(".k-js-form-controller").each(function(){new Koowa.Controller.Form({form:this})})}),Koowa.Translator||(Koowa.Translator=Koowa.Class.extend({translations:{},translate:function(a,b){if("undefined"!=typeof this.translations[a.toLowerCase()]&&(a=this.translations[a.toLowerCase()]),"object"==typeof b&&null!==b)for(var c in b)if(b.hasOwnProperty(c)){var d="{"+c+"}".replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g,"\\$&");a=a.replace(new RegExp(d,"g"),b[c])}return a},loadTranslations:function(a){for(var b in a)a.hasOwnProperty(b)&&(this.translations[b.toLowerCase()]=a[b]);return this}}),Koowa.translator=new Koowa.Translator,Koowa.translate=Koowa.translator.translate.bind(Koowa.translator)),Koowa.Form=Koowa.Class.extend({initialize:function(b){this.config=b,this.config.element?this.form=a(document[this.config.element]):(this.form=a("<form/>",{name:"dynamicform",method:this.config.method||"POST",action:this.config.url}),a(document.body).append(this.form))},addField:function(b,c){if(a.isArray(c)){var d,e=this;"[]"===b.substr(-2)&&(b=b.substr(0,b.length-2)),a.each(c,function(a,c){d=b+"["+a+"]",e.addField(d,c)})}else{var f=a("<input/>",{name:b,value:c,type:"hidden"});f.appendTo(this.form)}return this},submit:function(){var b=this;this.config.params&&a.each(this.config.params,function(a,c){b.addField(a,c)}),this.form.submit()}}),Koowa.Controller=Koowa.Class.extend({form:null,toolbar:null,buttons:null,token_name:null,token_value:null,getOptions:function(){return a.extend(this.supr(),{toolbar:".k-toolbar",url:window.location.href})},initialize:function(b){var c=this;this.supr(),this.setOptions(b),this.form=a(this.options.form),this.setOptions(this.form.data()),this.form.prop("action")&&(this.options.url=this.form.attr("action")),this.toolbar=a(this.options.toolbar),this.form.data("controller",this),this.on("execute",function(){return c.execute.apply(c,arguments)}),this.token_name=this.form.data("token-name"),this.token_value=this.form.data("token-value"),this.toolbar&&this.setToolbar()},setToolbar:function(){var b=this;this.buttons=this.toolbar.find(".toolbar[data-action]"),this.buttons.each(function(){var c=a(this),d={},e=c.data(),f=e.data;e.eventAdded||("object"!=typeof f&&(f=f&&"string"===a.type(f)?a.parseJSON(f):{}),b.token_name&&(f[b.token_name]=b.token_value),d.validate="novalidate"!==e.novalidate,d.data=f,d.action=e.action,c.on("click.koowa",function(a){if(a.preventDefault(),d.trigger=c,!c.hasClass("k-is-disabled")){var f=c.data("prompt");if(f&&!confirm(f))return;b.setOptions(e),b.trigger("execute",[d])}}),c.data("event-added",!0))})},execute:function(a,b){if(b.action[0]){var c=b.action[0].toUpperCase()+b.action.substr(1),d="_action"+c;"undefined"==typeof b.validate&&(b.validate=!0),this.trigger("before"+c,b)&&(d=this[d]?d:"_actionDefault",this[d].call(this,b),this.trigger("after"+c,b))}return this},on:function(a,b){return this.form.on("koowa:"+a,b)},off:function(a,b){return this.form.off("koowa:"+a,b)},trigger:function(b,c){var d=a.Event("koowa:"+b);return this.form.trigger(d,c),!d.isDefaultPrevented()},checkValidity:function(){var a;this.buttons&&(this.trigger("beforeValidate"),a=this.buttons.filter('[data-novalidate!="novalidate"]'),this.trigger("validate")?a.removeClass("k-is-disabled"):a.addClass("k-is-disabled"),this.trigger("afterValidate"))}}),Koowa.Controller.Grid=Koowa.Controller.extend({getOptions:function(){return a.extend(this.supr(),{inputs:".k-js-grid-checkbox, .k-js-grid-checkall",ajaxify:!1})},initialize:function(a){var b=this;this.supr(a),this.grid=new Koowa.Grid(this.form),this.on("validate",this.validate),this.options.inputs&&this.buttons&&(this.checkValidity(),this.form.find(this.options.inputs).on("change.koowa",function(a,c){c||b.checkValidity()})),this.token_name=this.form.data("token-name"),this.token_value=this.form.data("token-value"),this.setTableRows(),this.form.find("thead select, tfoot select, .k-pagination select").on("change.koowa",function(){b.grid.uncheckAll(),b.options.ajaxify&&(event.preventDefault(),b.options.transport(b.options.url,b.form.serialize(),"get")),b.form.submit()})},setTableRows:function(){var b=this,c=this.form.find("tbody tr .k-js-grid-checkbox");this.form.find("tbody tr").each(function(){var d=a(this),e=d.find(".k-js-grid-checkbox");1!=d.data("readonly")&&e.length&&d.find("[data-action]").each(function(){var d=a(this),f={},g=d.data("data"),h=d.data(),i=d.data("event-type");"object"!=typeof g&&(g=g&&"string"===a.type(g)?a.parseJSON(g):{}),b.token_name&&(g[b.token_name]=b.token_value),i||(i=d.is('[type="radio"],[type="checkbox"],select')?"change":"click"),f.validate="novalidate"!==h.novalidate,f.data=g,f.action=h.action,d.on(i+".k-ui-namespace",function(){c.prop("checked",""),e.prop("checked","checked"),c.trigger("change",!0),f.trigger=d,b.setOptions(h),b.trigger("execute",[f])})})})},validate:function(){return Koowa.Grid.getIdQuery()||!1},_actionDelete:function(a){return a.method="delete",this._actionDefault(a)},_actionDefault:function(b){var c,d=Koowa.Grid.getIdQuery(),e=this.options.url.match(/\?/)?"&":"?";return!(b.validate&&!this.trigger("validate",[b]))&&(c={method:"post",url:this.options.url+(d?e+d:""),params:a.extend({},{_action:b.action},b.data)},b.method&&(c.params._method=b.method),void new Koowa.Form(c).submit())}}),Koowa.Controller.Form=Koowa.Controller.extend({_actionDefault:function(b){return!(b.validate&&!this.trigger("validate",[b]))&&(this.form.append(a("<input/>",{name:"_action",type:"hidden",value:b.action})),this.trigger("submit",[b]),void this.form.submit())}})}(window.kQuery),window.jQuery=globalCacheForjQueryReplacement,globalCacheForjQueryReplacement=void 0;
//# sourceMappingURL=koowa.js.map