!function(){var moduleFactory=function($){var module=this,exports=function(){var radioCheck=/radio|checkbox/i,keyBreaker=/[^\[\]]+/g,numberMatcher=/^[\-+]?[0-9]*\.?[0-9]+([eE][\-+]?[0-9]+)?$/,isNumber=function(value){return"number"==typeof value?!0:"string"!=typeof value?!1:value.match(numberMatcher)};$.fn.extend({formParams:function(params,convert){return!!params===params&&(convert=params,params=null),params?this.setParams(params):"form"==this[0].nodeName.toLowerCase()&&this[0].elements?$($.makeArray(this[0].elements)).getParams(convert):$("input[name], textarea[name], select[name]",this[0]).getParams(convert)
},setParams:function(params){this.find("[name]").each(function(){var $this,value=params[$(this).attr("name")];void 0!==value&&($this=$(this),$this.is(":radio")?$this.val()==value&&$this.attr("checked",!0):$this.is(":checkbox")?(value=$.isArray(value)?value:[value],$.inArray($this.val(),value)>-1&&$this.attr("checked",!0)):$this.val(value))})},getParams:function(convert){var current,data={};return convert=void 0===convert?!1:convert,this.each(function(){var el=this,type=el.type&&el.type.toLowerCase();if("submit"!=type&&el.name){var lastPart,key=el.name,value=$.data(el,"value")||$.fn.val.call([el]),isRadioCheck=radioCheck.test(el.type),parts=key.match(keyBreaker),write=!isRadioCheck||!!el.checked;
convert&&(isNumber(value)?value=parseFloat(value):"true"===value?value=!0:"false"===value&&(value=!1),""===value&&(value=void 0)),current=data;for(var i=0;i<parts.length-1;i++)current[parts[i]]||(current[parts[i]]={}),current=current[parts[i]];lastPart=parts[parts.length-1],current[lastPart]?($.isArray(current[lastPart])||(current[lastPart]=void 0===current[lastPart]?[]:[current[lastPart]]),write&&current[lastPart].push(value)):(write||!current[lastPart])&&(current[lastPart]=write?value:void 0)}}),data}})};exports(),module.resolveWith(exports)
};dispatch("mvc/dom.form_params").containing(moduleFactory).to("Foundry/2.1 Modules")}();