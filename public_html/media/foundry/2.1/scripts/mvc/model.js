!function(){var moduleFactory=function($){var module=this;$.require().script("mvc/class","mvc/lang.string").done(function(){var exports=function(){var $String=$.String,getObject=$String.getObject,underscore=$String.underscore,classize=$String.classize,isArray=$.isArray,makeArray=$.makeArray,extend=$.extend,each=$.each,trigger=function(obj,event,args){$.event.trigger(event,args,obj,!0)},ajax=function(ajaxOb,data,success,error,fixture,type,dataType){if("string"==typeof ajaxOb){var sp=ajaxOb.indexOf(" ");ajaxOb=sp>-1?{url:ajaxOb.substr(sp+1),type:ajaxOb.substr(0,sp)}:{url:ajaxOb}
}return ajaxOb.data="object"!=typeof data||isArray(data)?data:extend(ajaxOb.data||{},data),ajaxOb.url=$String.sub(ajaxOb.url,ajaxOb.data,!0),$.ajax(extend({type:type||"post",dataType:dataType||"json",fixture:fixture,success:success,error:error},ajaxOb))},fixture=function(model,extra,or){var u=underscore(model.shortName),f="-"+u+(extra||"");return $.fixture&&$.fixture[f]?f:or||"//"+underscore(model.fullName).replace(/\.models\..*/,"").replace(/\./g,"/")+"/fixtures/"+u+(extra||"")+".json"},addId=function(model,attrs,id){attrs=attrs||{};
var identity=model.id;return attrs[identity]&&attrs[identity]!==id&&(attrs["new"+$String.capitalize(id)]=attrs[identity],delete attrs[identity]),attrs[identity]=id,attrs},getList=function(type){var listType=type||$.Model.List||Array;return new listType},getId=function(inst){return inst[inst.constructor.id]},unique=function(items){var collect=[];return each(items,function(i,item){item["__u Nique"]||(collect.push(item),item["__u Nique"]=1)}),each(collect,function(i,item){delete item["__u Nique"]})},makeRequest=function(self,type,success,error,method){var jqXHR,deferred=$.Deferred(),resolve=function(data){self[method||type+"d"](data),deferred.resolveWith(self,[self,data,type])
},reject=function(data){deferred.rejectWith(self,[data])},args=[self.serialize(),resolve,reject],model=self.constructor,promise=deferred.promise();return"destroy"==type&&args.shift(),"create"!==type&&args.unshift(getId(self)),deferred.then(success),deferred.fail(error),jqXHR=model[type].apply(model,args),jqXHR&&jqXHR.abort&&(promise.abort=function(){jqXHR.abort()}),promise},isObject=function(obj){return"object"==typeof obj&&null!==obj&&obj},$method=function(name){return function(){return $.fn[name].apply($([this]),arguments)
}},bind=$method("bind"),unbind=$method("unbind");ajaxMethods={create:function(str){return function(attrs,success,error){return ajax(str||this._shortName,attrs,success,error,fixture(this,"Create","-restCreate"))}},update:function(str){return function(id,attrs,success,error){return ajax(str||this._shortName+"/{"+this.id+"}",addId(this,attrs,id),success,error,fixture(this,"Update","-restUpdate"),"put")}},destroy:function(str){return function(id,success,error){var attrs={};return attrs[this.id]=id,ajax(str||this._shortName+"/{"+this.id+"}",attrs,success,error,fixture(this,"Destroy","-restDestroy"),"delete")
}},findAll:function(str){return function(params,success,error){return ajax(str||this._shortName,params,success,error,fixture(this,"s"),"get","json "+this._shortName+".models")}},findOne:function(str){return function(params,success,error){return ajax(str||this._shortName+"/{"+this.id+"}",params,success,error,fixture(this),"get","json "+this._shortName+".model")}}},$.Class($.globalNamespace+".Model",{setup:function(superClass){var self=this,fullName=this.fullName;if(each(["attributes","validations"],function(i,name){self[name]&&superClass[name]!==self[name]||(self[name]={})
}),each(["convert","serialize"],function(i,name){superClass[name]!=self[name]&&(self[name]=extend({},superClass[name],self[name]))}),this._fullName=underscore(fullName.replace(/\./g,"_")),this._shortName=underscore(this.shortName),0!=fullName.indexOf($.globalNamespace)){this.listType&&(this.list=new this.listType([])),each(ajaxMethods,function(name,method){var prop=self[name];"function"!=typeof prop&&(self[name]=method(prop))});var converters={},convertName="* "+this._shortName+".model";converters[convertName+"s"]=this.proxy("models"),converters[convertName]=this.proxy("model"),$.ajaxSetup({converters:converters})
}},attributes:{},model:function(attributes){return attributes?(attributes instanceof this&&(attributes=attributes.serialize()),new this(isObject(attributes[this._shortName])||isObject(attributes.data)||isObject(attributes.attributes)||attributes)):null},models:function(instancesRawData){if(!instancesRawData)return null;for(var res=getList(this.List),arr=isArray(instancesRawData),ML=$.Model.List,ml=ML&&instancesRawData instanceof ML,raw=arr?instancesRawData:ml?instancesRawData.serialize():instancesRawData.data,length=raw?raw.length:null,i=0;length>i;i++)res.push(this.model(raw[i]));
return arr||each(instancesRawData,function(prop,val){"data"!==prop&&(res[prop]=val)}),res},id:"id",addAttr:function(property,type){var stub,attrs=this.attributes;return stub=attrs[property]||(attrs[property]=type),type},convert:{date:function(str){var type=typeof str;return"string"===type?isNaN(Date.parse(str))?null:Date.parse(str):"number"===type?new Date(str):str},number:function(val){return parseFloat(val)},"boolean":function(val){return Boolean("false"===val?0:val)},"default":function(val,error,type){var realType,construct=getObject(type),context=window;
return type.indexOf(".")>=0&&(realType=type.substring(0,type.lastIndexOf(".")),context=getObject(realType)),"function"==typeof construct?construct.call(context,val):val}},serialize:{"default":function(val){return isObject(val)&&val.serialize?val.serialize():val},date:function(val){return val&&val.getTime()}},bind:bind,unbind:unbind,_ajax:ajax},{setup:function(attributes){this._init=!0,this.attrs(extend({},this.constructor.defaults,attributes)),delete this._init},update:function(attrs,success,error){return this.attrs(attrs),this.save(success,error)
},errors:function(attrs){attrs&&(attrs=isArray(attrs)?attrs:makeArray(arguments));var errors={},self=this,addErrors=function(attr,funcs){each(funcs,function(i,func){var res=func.call(self);res&&(errors[attr]||(errors[attr]=[]),errors[attr].push(res))})},validations=this.constructor.validations;return each(attrs||validations||{},function(attr,funcs){"number"==typeof attr&&(attr=funcs,funcs=validations[attr]),addErrors(attr,funcs||[])}),$.isEmptyObject(errors)?null:errors},attr:function(attribute,value,success,error){var cap=classize(attribute),get="get"+cap;
if(void 0!==value){var setName="set"+cap,old=this[attribute],self=this,errorCallback=function(errors){var stub;stub=error&&error.call(self,errors),trigger(self,"error."+attribute,errors)};if(this[setName]&&void 0===(value=this[setName](value,this.proxy("_updateProperty",attribute,value,old,success,errorCallback),errorCallback)))return;return this._updateProperty(attribute,value,old,success,errorCallback),this}return this[get]?this[get]():this[attribute]},bind:bind,unbind:unbind,_updateProperty:function(property,value,old,success,errorCallback){var val,args,globalArgs,Class=this.constructor,type=Class.attributes[property]||Class.addAttr(property,"string"),converter=Class.convert[type]||Class.convert["default"],errors=null,prefix="",global="updated.",callback=success,list=Class.list;
val=this[property]=null===value?null:converter.call(Class,value,function(){},type),this._init||(errors=this.errors(property)),args=[val],globalArgs=[property,val,old],errors&&(prefix=global="error.",callback=errorCallback,globalArgs.splice(1,0,errors),args.unshift(errors)),old===val||this._init||(!errors&&trigger(this,prefix+property,args),trigger(this,global+"attr",globalArgs)),callback&&callback.apply(this,args),property===Class.id&&null!==val&&list&&(old?old!=val&&(list.remove(old),list.push(this)):list.push(this))
},removeAttr:function(attr){var old=this[attr],deleted=!1,attrs=this.constructor.attributes;this[attr]&&delete this[attr],attrs[attr]&&(delete attrs[attr],deleted=!0),!this._init&&deleted&&old&&trigger(this,"updated.attr",[attr,null,old])},attrs:function(attributes){var key,constructor=this.constructor,attrs=constructor.attributes;if(attributes){var idName=constructor.id;for(key in attributes)key!=idName&&this.attr(key,attributes[key]);idName in attributes&&this.attr(idName,attributes[idName])}else{attributes={};
for(key in attrs)attrs.hasOwnProperty(key)&&(attributes[key]=this.attr(key))}return attributes},serialize:function(){var type,converter,attr,Class=this.constructor,attrs=Class.attributes,data={};attributes={};for(attr in attrs)attrs.hasOwnProperty(attr)&&(type=attrs[attr],converter=Class.serialize[type]||Class.serialize["default"],data[attr]=converter.call(Class,this[attr],type));return data},isNew:function(){var id=getId(this);return void 0===id||null===id||""===id},save:function(success,error){return makeRequest(this,this.isNew()?"create":"update",success,error)
},destroy:function(success,error){return makeRequest(this,"destroy",success,error,"destroyed")},identity:function(){var id=getId(this),constructor=this.constructor;return(constructor._fullName+"_"+(constructor.escapeIdentity?encodeURIComponent(id):id)).replace(/ /g,"_")},elements:function(context){var id=this.identity();return this.constructor.escapeIdentity&&(id=id.replace(/([ #;&,.+*~\'%:"!^$[\]()=>|\/])/g,"\\$1")),$("."+id,context)},hookup:function(el){var shortName=this.constructor._shortName,models=$.data(el,"models")||$.data(el,"models",{});
$(el).addClass(shortName+" "+this.identity()),models[shortName]=this}}),each(["created","updated","destroyed"],function(i,funcName){$.Model.prototype[funcName]=function(attrs){var stub,constructor=this.constructor;return"destroyed"===funcName&&constructor.list&&constructor.list.remove(getId(this)),stub=attrs&&"object"==typeof attrs&&this.attrs(attrs.attrs?attrs.attrs():attrs),trigger(this,funcName),trigger(constructor,funcName,this),[this].concat(makeArray(arguments))}}),$.fn.models=function(){var kind,ret,collection=[];
return this.each(function(){each($.data(this,"models")||{},function(name,instance){kind=void 0===kind?instance.constructor.List||null:instance.constructor.List===kind?kind:null,collection.push(instance)})}),ret=getList(kind),ret.push.apply(ret,unique(collection)),ret},$.fn.model=function(type){return type&&type instanceof $.Model?(type.hookup(this[0]),this):this.models.apply(this,arguments)[0]}};exports(),module.resolveWith(exports)})};dispatch("mvc/model").containing(moduleFactory).to("Foundry/2.1 Modules")
}();