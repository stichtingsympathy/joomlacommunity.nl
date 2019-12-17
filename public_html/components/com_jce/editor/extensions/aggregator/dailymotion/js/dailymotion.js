/* jce - 2.8.1 | 2019-12-04 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2019 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
WFAggregator.add("dailymotion",{params:{width:480,height:270,autoPlay:!1},props:{autoPlay:0,start:0},setup:function(){$("#dailymotion_autoPlay").prop("checked",this.params.autoPlay),$("#dailymotion_player_size").on("change",function(){var v=parseInt(this.value);$("#dailymotion_player_size_custom").toggleClass("uk-hidden",!!this.value),v&&($("#width").val(v),$("#height").val(Math.round(9*v/16)))}),$("#dailymotion_player_size_custom").on("change",function(){var v=parseInt(this.value);v&&($("#width").val(v),$("#height").val(Math.round(16*v/9)))})},getTitle:function(){return this.title||this.name},getType:function(){return"iframe"},isSupported:function(v){return"object"==typeof v&&(v=v.src||v.data||""),!!/dai\.?ly(motion)?(\.com)?/.test(v)&&"dailymotion"},getValues:function(src){var self=this,data={},args={},id=(this.getType(),"");src.indexOf("=")!==-1&&$.extend(args,Wf.String.query(src)),$("input, select","#dailymotion_options").each(function(){var k=$(this).attr("id"),v=$(this).val();k=k.substr(k.indexOf("_")+1),$(this).is(":checkbox")&&(v=$(this).is(":checked")?1:0),k.indexOf("player_size")===-1&&self.props[k]!==v&&""!==v&&(args[k]=v)});var m=src.match(/dai\.?ly(motion\.com)?\/(embed)?\/?(swf|video)?\/?([a-z0-9]+)_?/);m&&(id=m.pop()),src="//www.dailymotion.com/embed/video/"+id;var query=$.param(args);return query&&(src=src+(/\?/.test(src)?"&":"?")+query),data.src=src,$.extend(data,{frameborder:0,allowfullscreen:"allowfullscreen"}),data},setValues:function(data){var src=data.src||data.data||"",id="";if(!src)return data;var query=Wf.String.query(src);$.extend(data,query),src=src.replace(/&amp;/g,"&");var m=src.match(/dai\.?ly(motion\.com)?\/(embed)?\/?(swf|video)?\/?([a-z0-9]+)_?/);return m&&(id=m.pop()),data.src="//www.dailymotion.com/embed/video/"+id,data},getAttributes:function(src){var args={},data=this.setValues({src:src})||{};return $.each(data,function(k,v){"src"!=k&&(args["dailymotion_"+k]=v)}),$.extend(args,{src:data.src||src,width:this.params.width,height:this.params.height}),args},setAttributes:function(){},onSelectFile:function(){},onInsert:function(){}});