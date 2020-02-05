/* jce - 2.8.5 | 2020-01-29 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2020 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){function isMceItem(node){return node.className.indexOf("mce-item-")!==-1}function getImageProps(ed,value){var params=ed.getParam("imgmanager_ext",{}),attribs=["alt","title","id","dir","class","usemap","style","longdesc"],args={src:value,alt:""};if(tinymce.each(attribs,function(key){tinymce.is(params[key])&&(args[key]=params[key])}),args.style){var styles=ed.dom.parseStyle(ed.dom.serializeStyle(args.style));args.style=ed.dom.serializeStyle(styles,"IMG")}return args}function validateImage(ed,value){return new Promise(function(resolve,reject){if(!value)return resolve();var img=new Image;img.onload=function(){resolve()},img.onerror=function(){reject()},img.src=ed.documentBaseURI.toAbsolute(value)})}tinymce.each,tinymce.dom.Event,tinymce.DOM;tinymce.create("tinymce.plugins.ImageManagerExtended",{init:function(ed,url){this.editor=ed,this.url=url,ed.onPreInit.add(function(){ed.parser.addNodeFilter("img",function(nodes){for(var node,i=nodes.length;i--;){node=nodes[i];var src=node.attr("src");if(src&&src.indexOf("?")===-1&&/\.(jpg|jpeg|png)$/.test(src)){var stamp="?"+(new Date).getTime();src=ed.convertURL(src,"src",node.name),node.attr("src",src+stamp),node.attr("data-mce-src",src)}}})}),ed.addCommand("mceImageManagerExtended",function(){var n=ed.selection.getNode();"IMG"==n.nodeName&&isMceItem(n)||ed.windowManager.open({file:ed.getParam("site_url")+"index.php?option=com_jce&task=plugin.display&plugin=imagepro",width:780+ed.getLang("imgmanager_ext.delta_width",0),height:700+ed.getLang("imgmanager_ext.delta_height",0),inline:1,popup_css:!1,size:"mce-modal-portrait-full"},{plugin_url:url})}),ed.addButton("imgmanager_ext",{title:"imgmanager_ext.desc",cmd:"mceImageManagerExtended"}),ed.onNodeChange.add(function(ed,cm,n){cm.setActive("imgmanager_ext","IMG"==n.nodeName&&!isMceItem(n))}),ed.onInit.add(function(){if(ed&&ed.plugins.contextmenu&&ed.plugins.contextmenu.onContextMenu.add(function(th,m,e){m.add({title:"imgmanager_ext.desc",icon:"imgmanager_ext",cmd:"mceImageManagerExtended"})}),ed.settings.compress.css||ed.dom.loadCSS(url+"/css/content.css"),ed.getParam("imgmanager_convert_img_links",1)&&ed.plugins.clipboard){var data=ed.getParam("imgmanager_ext",{}),filetypes=data.filetypes||["jpg","jpeg","png","gif","webp"],ux="^((http|https)://[-!#$%&'*+\\/0-9=?A-Z^_`a-z{|}~;]+[-!#$%&*+\\/0-9=?A-Z^_`a-z{|}~;.]+?).("+filetypes.join("|")+")$";ed.onGetClipboardContent.add(function(ed,content){var value=content["text/plain"]||"";if(value){var match=new RegExp(ux).exec(value);match&&(content["text/plain"]="",content["text/html"]=content["x-tinymce/html"]=ed.dom.createHTML("img",getImageProps(ed,match[0])),ed.setProgressState(!0),validateImage(ed,value).then(function(){ed.setProgressState(!1)},function(){ed.setProgressState(!1)}))}})}})},insertUploadedFile:function(o){var ed=this.editor,data=this.getUploadConfig();if(data&&data.filetypes&&new RegExp(".("+data.filetypes.join("|")+")$","i").test(o.file)){var args={src:o.file,alt:o.alt||o.name,style:{}},attribs=["alt","title","id","dir","class","usemap","style","longdesc"];if(o.style){var s=ed.dom.parseStyle(ed.dom.serializeStyle(o.style,"IMG"));tinymce.extend(args.style,s),delete o.style}return tinymce.each(attribs,function(k){"undefined"!=typeof o[k]&&(args[k]=o[k])}),ed.dom.create("img",args)}return!1},getUploadURL:function(file){var ed=this.editor,data=this.getUploadConfig();return!!(data&&data.filetypes&&new RegExp(".("+data.filetypes.join("|")+")$","i").test(file.name))&&ed.getParam("site_url")+"index.php?option=com_jce&task=plugin.display&plugin=imagepro"},getUploadConfig:function(){var ed=this.editor,data=ed.getParam("imgmanager_ext",{});return data.upload||{}}}),tinymce.PluginManager.add("imgmanager_ext",tinymce.plugins.ImageManagerExtended)}();