/* jce - 2.8.9 | 2020-02-29 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2020 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function($){var MediaManagerDialog={settings:{filebrowser:{}},mimes:{},mediatypes:null,preInit:function(){!function(data){var i,y,ext,items=data.split(/,/);for(i=0;i<items.length;i+=2)for(ext=items[i+1].split(/ /),y=0;y<ext.length;y++)MediaManagerDialog.mimes[ext[y]]=items[i]}("application/x-mplayer2,avi wmv wm asf asx wmx wvx,application/x-director,dcrvideo/divx,divxapplication/pdf,pdf,application/x-shockwave-flash,swf swfl,audio/mpeg,mpga mpega mp2 mp3 m4a,audio/ogg,ogg spx oga,audio/x-wav,wav,video/mpeg,mpeg mpg mpe,video/mp4,mp4 m4v,video/ogg,ogg ogv,video/webm,webm,video/quicktime,qt mov,video/x-flv,flv f4v,video/vnd.rn-realvideo,rvvideo/3gpp,3gpvideo/x-matroska,mkv")},ucfirst:function(s){return s=s.charAt(0).toUpperCase()+s.substring(1)},convertURL:function(url){var ed=tinyMCEPopup.editor;if(!url)return url;var query="",n=url.indexOf("?");return n===-1&&(url=url.replace(/&amp;/g,"&"),n=url.indexOf("&")),n>0&&(query=url.substring(n+1,url.length),url=url.substr(0,n)),url=ed.convertURL(url),url+(query?"?"+query:"")},removeQuery:function(s){return s?(s.indexOf("?")!==-1?s=s.substr(0,s.indexOf("?")):s.indexOf("&")!==-1&&(s=s.replace(/&amp;/g,"&"),s=s.substr(0,s.indexOf("&"))),s):s},getMimeType:function(s){s=this.removeQuery(s);var ext=Wf.String.getExt(s);return ext=ext.toLowerCase(),this.mimes[ext]||!1},getNodeName:function(s){return s=/mce-item-(audio|embed|object|video|iframe)/i.exec(s),s?s[1].toLowerCase():"object"},setMediaAttributes:function(data,type,prefix){var self=this;prefix=prefix||"","audio"!==type&&"video"!==type||"undefined"==typeof data.controls&&(data.controls=0);var x=0;$.each(data,function(k,v){switch(k){case"flashvars":$("#"+prefix+type+"_"+k).val(decodeURIComponent(v)).trigger("change");break;case"param":$.each(v,function(at,val){switch(at){case"movie":case"src":case"url":case"source":""===$("#src").val()&&$("#src").val(self.convertURL(val)).trigger("change");break;case"flashvars":$("#"+prefix+type+"_flashvars").val(decodeURIComponent(val)).trigger("change");break;default:var $na=$("#"+prefix+type+"_"+at);$na.is(":checkbox")?("false"!=val&&"0"!=val||(val=!1),$na.prop("checked",!!val).trigger("change")):$na.val(val).trigger("change")}});break;case"source":$.each(v,function(i,at){var src=self.convertURL(at.src),n=$('input[name="'+prefix+type+'_source[]"]',".media_option").get(i);n&&$(n).val(src).trigger("change")});break;case"object":break;default:var $na=$("#"+prefix+type+"_"+k);if($na.is(":checkbox")?("false"!=v&&"0"!=v||(v=!1),$na.prop("checked",!!v).trigger("change")):$na.val(v).trigger("change"),!$na.length&&!/^(src|type|width|height|class|classes|style|title|id|frameborder|allowfullscreen)$/.test(k)){var n=$(".uk-repeatable",".media_option."+type).eq(0);x>0&&$(n).clone(!0).appendTo($(n).parent());var elements=$(".uk-repeatable",".media_option."+type).eq(x).find("input, select");$(elements).eq(0).val(k),$(elements).eq(1).val(v),x++}}})},init:function(){tinyMCEPopup.restoreSelection();var mt,data,popup,self=this,ed=tinyMCEPopup.editor,s=ed.selection,n=s.getNode(),type="video";$("button#insert").on("click",function(e){self.insert(),e.preventDefault()}),this.mediatypes=this.mapTypes(),Wf.init();var node=this.getNodeName(n.className);if(WFPopups.setup(),WFAggregator.setup(),this.isMedia(n)){var cl=/mce-item-(flash|shockwave|windowsmedia|quicktime|realmedia|divx|silverlight|audio|video|iframe)/.exec(n.className);if(cl&&(type=cl[1].toLowerCase()),/mce-item-preview/.test(n.className)){var ifr=n.firstChild,data={iframe:{}};tinymce.each(ifr.attributes,function(attr){data.iframe[attr.name]=attr.value}),n=ifr,data.iframe.html=ifr.innerHTML||""}else data=$.parseJSON(ed.dom.getAttrib(n,"data-mce-json"));$("#insert").button("option","label",tinyMCEPopup.getLang("update","Update",!0)),$("#popup_list").prop("disabled",!0)}else if(popup=WFPopups.getPopup(n)){var data={width:popup.width||"",height:popup.height||"",popup:{}};delete popup.width,delete popup.height,popup.type&&(type=this.getMediaName(popup.type)),data.popup=popup,node="popup"}if(data){tinymce.each(["width","height"],function(s){var v=data[s]||parseFloat(ed.dom.getAttrib(n,s)||ed.dom.getStyle(n,s))||"";$("#"+s).val(v).data("tmp",v)}),data=data[node],(mt=WFAggregator.isSupported(data))&&(data=WFAggregator.setValues(mt,data),type=mt);var src=data.src||data.data||data.url;data.source&&$.isArray(data.source)&&(src?$.each(data.source,function(i,v){v&&v.src===src&&data.source.splice(i,1)}):src=data.source.shift()),data.html&&$("#html").val(data.html),$("#src").val(self.convertURL(src)),"audio"!=type&&"video"!=type||$(":input, select","#"+type+"_options").each(function(){$(this).is(":checkbox")?$(this).prop("checked",!1):$(this).val("")}),this.setMediaAttributes(data,type);var style=ed.dom.parseStyle(ed.dom.getAttrib(n,"style"));tinymce.each(["top","right","bottom","left"],function(pos){var val=self.getAttrib(n,"margin-"+pos);$("#margin_"+pos).val(val),delete style["margin-"+pos]}),tinymce.each(["width","style","color"],function(at){var val=self.getAttrib(n,"border-"+at);$("#border_"+at).val(val),delete style["border-"+at]}),delete style.width,delete style.height,$("#style").val(ed.dom.serializeStyle(style)),$("#classes").val(function(){var values=ed.dom.getAttrib(n,"class");return values=values.replace(/mce-item-(\w+)/gi,"").replace(/\s+/g," "),$.trim(values)}).trigger("change"),$("#id").val(ed.dom.getAttrib(n,"id")),$("#align").val(this.getAttrib(n,"align")),$("#title").val(ed.dom.getAttrib(n,"title"))}else Wf.setDefaults(this.settings.defaults);$("#media_type").val(type).trigger("change"),Wf.updateStyles(),src=this.removeQuery($("#src").val()),"external"===ed.settings.filebrowser_position?(Wf.createBrowsers($("#src"),function(files){var file=files.shift();$("#src").val(file.url).trigger("change"),file.width&&$("#width").val(file.width).data("tmp",file.width).trigger("change"),file.height&&$("#height").val(file.height).data("tmp",file.height).trigger("change"),files.length&&("video"===$("#media_type").val()&&$.each(files,function(i,file){$('[name^="video_source"]').eq(i).val(file.url)}),"audio"===$("#media_type").val()&&$.each(files,function(i,file){$('[name^="audio_source"]').eq(i).val(file.url)}))},"media"),$('[name^="audio_source"], [name^="video_source"]').addClass("browser media")):$("#src").filebrowser().on("filebrowser:onfileclick",function(e,file,data){self.selectFile(file,data)}),$("#src").on("change",function(){this.value&&self.selectType(this.value)}),$("#width, #height").on("change",function(){var n=$(this).attr("id"),v=this.value;"audio"===$("#media_type").val()&&self.addStyle(n,v)}),$("#border").change(),$(".uk-equalize-checkbox").trigger("equalize:update"),$(".uk-form-controls select:not(.uk-datalist)").datalist({input:!1}).trigger("datalist:update"),$(".uk-datalist").trigger("datalist:update")},getAttrib:function(node,attrib){return Wf.getAttrib(node,attrib)},getSiteRoot:function(){var s=tinyMCEPopup.getParam("document_base_url");return s.match(/.*:\/\/([^\/]+)(.*)/)[2]},setControllerHeight:function(t){var v=0;switch(t){case"quicktime":v=16;break;case"windowsmedia":v=16;break;case"divx":switch($("#divx_mode").val()){default:v=0;break;case"mini":v=20;break;case"large":v=65;break;case"full":v=90}}$("#controller_height").val(v)},isMedia:function(n){return n&&/mce-item-(flash|shockwave|windowsmedia|quicktime|realmedia|divx|silverlight|audio|video|iframe)/.test(n.className)},isIframe:function(n){return n&&n.className.indexOf("mce-item-iframe")!==-1},getMediaType:function(type){var mt={flash:"application/x-shockwave-flash",director:"application/x-director",shockwave:"application/x-director",quicktime:"video/quicktime",mplayer:"application/x-mplayer2",windowsmedia:"application/x-mplayer2",realaudio:"audio/x-pn-realaudio-plugin",real:"audio/x-pn-realaudio-plugin",divx:"video/divx",flv:"video/x-flv",silverlight:"application/x-silverlight-2"};return mt[type]||null},getMediaName:function(type){var mt={"application/x-shockwave-flash":"flash","application/x-director":"shockwave","video/quicktime":"quicktime","application/x-mplayer2":"windowsmedia","audio/x-pn-realaudio-plugin":"real","video/divx":"divx","video/mp4":"video","video/ogg":"video","video/webm":"video","audio/mpeg":"audio","audio/mp3":"audio","audio/x-wav":"audio","audio/ogg":"audio","audio/webm":"audio","application/x-silverlight-2":"silverlight","video/x-flv":"video"};return mt[type]||null},addStyle:function(style,value){var styles=$("<div />").attr("style",$("#style").val()).css(style,value).get(0).style.cssText;$("#style").val(styles)},insert:function(){var src=$("#src").val(),type=$("#media_type").val();return""==src?(Wf.Modal.alert(tinyMCEPopup.getLang("mediamanager_dlg.no_src","Please select a file or enter in a link to a file")),!1):$("#width").val()&&$("#height").val()?/(windowsmedia|mplayer|quicktime|divx)$/.test(type)?(Wf.Modal.confirm(tinyMCEPopup.getLang("mediamanager_dlg.add_controls_height","Add additional height for player controls?"),function(state){if(state){var h=$("#height").val(),ch=$("#controller_height").val();ch&&$("#height").val(parseInt(h)+parseInt(ch))}MediaManagerDialog.insertAndClose()}),!1):void this.insertAndClose():("audio"===type&&this.insertAndClose(),WFPopups.isEnabled()&&this.insertAndClose(),Wf.Modal.alert(tinyMCEPopup.getLang("mediamanager_dlg.no_dimensions","A width and height value are required."),{close:function(){$("#width, #height").map(function(){if(!this.value)return this}).first().focus()}}),!1)},insertAndClose:function(){tinyMCEPopup.restoreSelection();var n,src,cls="mce-item-media",ed=tinyMCEPopup.editor,type=$("#media_type").val();n=ed.selection.getNode(),type==WFAggregator.isSupported($("#src").val())&&(WFAggregator.onInsert(type),type=WFAggregator.getType(type));var args={title:$("#title").val(),style:$("#style").val(),id:$("#id").val()};"audio"!==type&&$.extend(args,{"data-mce-width":$("#width").val()||384,"data-mce-height":$("#height").val()||216});var node="object";switch(type){case"flash":cls+=" mce-item-flash";break;case"director":cls+=" mce-item-shockwave";break;case"quicktime":cls+=" mce-item-quicktime";break;case"mplayer":case"windowsmedia":cls+=" mce-item-windowsmedia";break;case"realaudio":case"real":cls+=" mce-item-realmedia";break;case"divx":cls+=" mce-item-divx";break;case"iframe":node="iframe";break;case"video":node="video";break;case"audio":delete args.width,delete args.height,node="audio";var agent=navigator.userAgent.match(/(Opera|Chrome|Safari|Gecko)/);agent&&(cls+=" mce-item-agent"+this.ucfirst(agent[0]));break;default:type&&(cls+=" mce-item-generic mce-item-"+type.toLowerCase())}cls+=" mce-item-"+node.toLowerCase();var classes=$("#classes").val();cls+=" "+classes;var data=this.serializeParameters(type);if(n&&this.isMedia(n)&&"iframe"!==type)ed.dom.setAttrib(n,"class",$.trim(cls)),ed.dom.setAttribs(n,$.extend(args,{"data-mce-json":data,"data-mce-type":type})),"audio"!==type&&ed.dom.setStyles(n,{width:args["data-mce-width"],height:args["data-mce-height"]});else if(WFPopups.isEnabled()&&($("#popup_text").is(":disabled")||""!=$("#popup_text").val()))data=$.parseJSON(data),data=data[node],src=data.src,delete data.src,$.extend(args,{type:this.getMediaType(type),src:src,data:{}}),delete data.type,$.each(data,function(k,v){"string"===$.type(v)?args.data[k]=v:("param"==k&&$.each(v,function(at,val){args.data[at]=val}),"source"==k&&$.each(v,function(i,p){$.each(p,function(at,val){args.data[at]=val})}))}),WFPopups.createPopup(n,args);else{var html='<img id="__mce_tmp" src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" class="'+$.trim(cls)+'" />';if("iframe"===type){var html="<iframe",innerHTML=$("#html").val();tinymce.extend(args,this.getParameters(type)),args.class=$.trim(cls),tinymce.each(args,function(v,k){k=k.replace("data-mce-",""),""!==v&&tinymce.is(v)&&(html+=" "+k+'="'+v+'"')});var innerHTML=$("#html").val();html+=">"+$.trim(innerHTML)+"</iframe>"}ed.execCommand("mceInsertContent",!1,html,{skip_undo:1});var n=ed.dom.get("__mce_tmp");n&&($.extend(args,{"data-mce-json":data,"data-mce-type":type}),ed.dom.setAttrib(n,"id",""),ed.dom.setAttribs(n,args),ed.dom.setStyles(n,{width:args["data-mce-width"],height:args["data-mce-height"]}))}ed.undoManager.add(),ed.nodeChanged(),tinyMCEPopup.close()},mapTypes:function(){var types={},mt=this.settings.media_types;return tinymce.each(tinymce.explode(mt,";"),function(v,k){v&&(v=v.replace(/([a-z0-9]+)=([a-z0-9,]+)/,function(a,b,c){types[b]=c.split(",")}))}),types},checkType:function(s){var mime=this.getMimeType(s);return!!mime&&(this.getMediaName(mime)||!1)},getType:function(v){var s,type,n,data={width:"",height:""};if(!v)return!1;/\.([a-z0-9]{3,4}$)/i.test(v)&&(type=this.checkType(v)),type||(s=WFAggregator.isSupported(v))&&(data=WFAggregator.getAttributes(s,v),type=s);for(n in data){var v=data[n];if(v)if("width"===n||"height"===n)$("#"+n).val(v).trigger("change");else{var $el=$("#"+n);$el.is(":checkbox")?$el.attr("checked",!!parseFloat(v)).prop("checked",!!parseFloat(v)):$el.val(v)}}return type},selectType:function(v){var type=this.getType(v);type&&$("#media_type").val(type).trigger("change")},changeType:function(type){var type=type||$("#media_type").val();this.setControllerHeight(type),$(".media_option","#media_tab").hide().filter("."+type).show()},checkPrefix:function(n){/^\s*www./i.test(n.value)&&confirm(tinyMCEPopup.getLang("mediamanager_dlg_is_external",!1,"The URL you entered seems to be an external link, do you want to add the required http:// prefix?"))&&(n.value="http://"+n.value)},getMediaAttributes:function(o,type,prefix){function setParam(k,v){params||(params={}),params[k]=v}var params;prefix=prefix||"";var media={flash:["play","loop","menu","swliveconnect","quality","scale","salign","wmode","base","flashvars","allowfullscreen"],quicktime:["loop","autoplay","cache","controller","correction","enablejavascript","kioskmode","autohref","playeveryframe","targetcache","scale","starttime","endtime","target","qtsrcchokespeed","volume","qtsrc"],director:["sound","progress","autostart","swliveconnect","swvolume","swstretchstyle","swstretchhalign","swstretchvalign"],windowsmedia:["autostart","enabled","enablecontextmenu","fullscreen","invokeurls","mute","stretchtofit","windowlessvideo","balance","baseurl","captioningid","currentmarker","currentposition","defaultframe","playcount","rate","uimode","volume"],real:["autostart","loop","autogotourl","center","imagestatus","maintainaspect","nojava","prefetch","shuffle","console","controls","numloop","scriptcallbacks"],divx:["mode","minversion","bufferingmode","previewimage","previewmessage","previewmessagefontsize","movietitle","allowcontextmenu","autoplay","loop","bannerenabled"],video:["poster","autoplay","loop","preload","controls","muted"],audio:["autoplay","loop","preload","controls","muted"],silverlight:[],iframe:["frameborder","marginwidth","marginheight","scrolling","longdesc","allowtransparency"]};if("undefined"==typeof media[type])return o;var states={quicktime:{autoplay:!0,controller:!0},flash:{play:!0,loop:!0,menu:!0},windowsmedia:{autostart:!0,enablecontextmenu:!0,invokeurls:!0},real:{autogotourl:!0,imagestatus:!0}};return $.each(media[type],function(i,k){var n=$("#"+prefix+type+"_"+k).get(0);if(n){var state,v=$(n).val();states[type]&&(state=states[type][k]),n&&"checkbox"==n.type?(v=n.checked,"audio"==type||"video"==type?v&&(o[k]="audio"==k?"muted":k):"undefined"==typeof state?v&&setParam(k,v):v!=state&&setParam(k,!state)):""!=v&&("audio"==type||"video"==type||"iframe"==type?o[k]=v:setParam(k,v))}}),params&&(o.param=params),o},getParameters:function(type){var ag,self=this,data=(tinyMCEPopup.editor,{}),src=$("#src").val();if($.each(["name"],function(i,k){v=$("#"+k).val(),v&&(data[k]=v)}),data=$.extend(data,this.getMediaAttributes(data,type)),WFAggregator.isSupported(src)&&(ag=!0),"audio"==type||"video"==type){var v,mime,sources=[];$('input[name="'+type+'_source[]"]',".media_option."+type).each(function(){if(v=$(this).val()){mime=self.getMimeType(v),mime=mime||type+"/mpeg",mime=mime.replace(/(audio|video)/,type);var at={src:v,type:mime};sources.push(at)}}),mime=self.getMimeType(src),mime=mime||type+"/mpeg",mime=mime.replace(/(audio|video)/,type),sources.length?sources.unshift({src:src,type:mime}):sources.push({src:src,type:mime}),$.extend(data,{src:src,source:sources})}else data.src||(data.src=src),ag&&$.extend(!0,data,WFAggregator.getValues($("#media_type").val(),src));return $(".uk-repeatable",".media_option."+type).each(function(){var k=$('input[name*="_attributes_name"]',this).val(),v=$('input[name*="_attributes_value"]',this).val();""!==k&&""!==v&&(data[k]=v)}),data},serializeParameters:function(type){var node=(tinyMCEPopup.editor,"object"),data=this.getParameters(type);$("#html").val()&&(data.html=$("#html").val()),"audio"!=type&&"video"!=type&&"iframe"!=type||(node=type);var o={};return o[node]=data,JSON.stringify(o)},setSourceFocus:function(n){$("input.uk-active").removeClass("uk-active"),$(n).addClass("uk-active")},selectFile:function(file,data){var name=data.title,src=data.url;$("#media_tab").hasClass("uk-active")?$("input.uk-active","#media_tab").val(src):($("#src").val(src),MediaManagerDialog.selectType(name),data.width&&data.height&&($("#width").val(data.width).data("tmp",data.width),$("#height").val(data.height).data("tmp",data.height)),WFAggregator.isSupported(src)&&WFAggregator.onSelectFile(name),this._getMediaProps(data).then(function(props){props.width&&$("#width").val(props.width).data("tmp",props.width).trigger("change"),props.height&&$("#height").val(props.height).data("tmp",props.height).trigger("change")}))},_getMediaProps:function(file){var deferred=new $.Deferred,props={};if(/\.(mp4|m4v|ogg|ogv|webm)$/i.test(file.preview)&&$.support.video){var video=document.createElement("video");video.onloadedmetadata=function(){props={duration:parseInt(video.duration/60,10)+":"+parseInt(video.duration%60,10),width:video.videoWidth,height:video.videoHeight},video=null,deferred.resolve(props)},video.src=file.preview}else if(/\.(mp3|oga|ogg)$/i.test(file.preview)&&$.support.audio){var audio=document.createElement("audio");audio.onloadedmetadata=function(){props={duration:parseInt(audio.duration/60,10)+":"+parseInt(audio.duration%60,10)},audio=null,deferred.resolve(props)},audio.src=file.preview}else deferred.reject();return deferred}};window.MediaManagerDialog=MediaManagerDialog,tinyMCEPopup.onInit.add(MediaManagerDialog.init,MediaManagerDialog),MediaManagerDialog.preInit()}(jQuery,tinyMCEPopup);