/* jce - 2.8.2 | 2019-12-18 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2019 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){tinymce.each;tinymce.create("tinymce.plugins.IframePlugin",{init:function(ed,url){var t=this;t.editor=ed,t.url=url,ed.addCommand("mceIframe",function(){ed.windowManager.open({file:ed.getParam("site_url")+"index.php?option=com_jce&task=plugin.display&plugin=iframe",width:785+parseInt(ed.getLang("iframe.delta_width",0)),height:340+parseInt(ed.getLang("iframe.delta_height",0)),inline:1,popup_css:!1},{plugin_url:url})}),ed.addButton("iframe",{title:"iframe.desc",cmd:"mceIframe"}),ed.onNodeChange.add(function(ed,cm,n){"mce-item-shim"===n.className&&(n=n.parentNode);var state=n.className.indexOf("mce-item-iframe")!==-1;cm.setActive("iframe",state)})}}),tinymce.PluginManager.add("iframe",tinymce.plugins.IframePlugin,["media"])}();