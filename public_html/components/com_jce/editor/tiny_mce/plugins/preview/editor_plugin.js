/* jce - 2.9.4 | 2021-03-16 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2021 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){function uid(){var i,guid=(new Date).getTime().toString(32);for(i=0;i<5;i++)guid+=Math.floor(65535*Math.random()).toString(32);return"wf_"+guid+(counter++).toString(32)}var DOM=tinymce.DOM,counter=(tinymce.dom.Event,0);tinymce.create("tinymce.plugins.PreviewPlugin",{init:function(ed,url){this.editor=ed;var self=this;ed.onInit.add(function(ed){var activeTab=sessionStorage.getItem("wf-editor-tabs-"+ed.id)||ed.settings.active_tab||"";"wf-editor-preview"===activeTab&&(ed.hide(),DOM.hide(ed.getElement()),self.toggle())})},hide:function(){DOM.hide(this.editor.id+"_editor_preview")},toggle:function(){function update(text){var doc=iframe.contentWindow.document,css=[],scripts=/<script[^>]+>[\s\S]*<\/script>/.exec(text);text=text.replace(/<script[^>]+>[\s\S]*<\/script>/gi,"");var html="<!DOCTYPE html>";html+='<head xmlns="http://www.w3.org/1999/xhtml">',html+='<base href="'+s.document_base_url+'" />',html+='<meta http-equiv="X-UA-Compatible" content="IE=7" />',html+='<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />',css=s.compress.css?[s.site_url+"index.php?option=com_jce&task=editor.pack&type=css&layout=preview&"+s.query]:tinymce.explode(s.content_css),tinymce.each(css,function(url){html+='<link href="'+url+'" rel="stylesheet" type="text/css" />'}),scripts&&tinymce.each(scripts,function(script){html+=""+script}),html+='</head><body style="margin:5px;">',html+=""+text,html+="</body></html>",doc.open(),doc.write(html),doc.close(),DOM.removeClass(container,"mce-loading")}var ed=this.editor,s=ed.settings,element=ed.getElement(),container=element.parentNode,header=DOM.getPrev(element,".wf-editor-header"),ifrHeight=parseInt(DOM.get(ed.id+"_ifr").style.height)||s.height,preview=DOM.get(ed.id+"_editor_preview"),iframe=DOM.get(ed.id+"_editor_preview_iframe"),o=tinymce.util.Cookie.getHash("TinyMCE_"+ed.id+"_size");if(o&&o.height&&(ifrHeight=o.height),!preview){var preview=DOM.add(container,"div",{role:"textbox",id:ed.id+"_editor_preview",class:"wf-editor-preview"});iframe=DOM.add(preview,"iframe",{frameborder:0,src:'javascript:""',id:ed.id+"_editor_preview_iframe"})}var height=ed.settings.container_height||sessionStorage.getItem("wf-editor-container-height")||ifrHeight;if(DOM.hasClass(container,"mce-fullscreen")){var vp=DOM.getViewPort();height=vp.h-header.offsetHeight}DOM.setStyle(preview,"height",height),DOM.setStyle(preview,"max-width","100%");var width=ed.settings.container_width||sessionStorage.getItem("wf-editor-container-width");width&&!DOM.hasClass(container,"mce-fullscreen")&&DOM.setStyle(preview,"max-width",width),DOM.show(preview);var query="",args={};tinymce.extend(args,{data:ed.getContent(),id:uid()});for(k in args)query+="&"+k+"="+encodeURIComponent(args[k]);tinymce.util.XHR.send({url:s.site_url+"index.php?option=com_jce&task=plugin.display&plugin=preview&"+tinymce.query,data:"json="+JSON.stringify({method:"showPreview"})+"&"+query,content_type:"application/x-www-form-urlencoded",success:function(x){var o={};try{o=JSON.parse(x)}catch(e){o.error=/[{}]/.test(o)?"The server returned an invalid JSON response":x}r=o.result,x&&!o.error||(r=ed.getContent()),update(r)},error:function(e,x){update(ed.getContent())}})}}),tinymce.PluginManager.add("preview",tinymce.plugins.PreviewPlugin)}();