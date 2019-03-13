/* jce - 2.7.2 | 2019-03-09 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2019 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){tinymce.create("tinymce.plugins.Directionality",{init:function(b,c){function a(e){var g,h=b.dom,f=b.selection.getSelectedBlocks();f.length&&(g=h.getAttrib(f[0],"dir"),tinymce.each(f,function(i){h.getParent(i.parentNode,"*[dir='"+e+"']",h.getRoot())||(g!=e?h.setAttrib(i,"dir",e):h.setAttrib(i,"dir",null))}),b.nodeChanged())}var d=this;d.editor=b,b.addCommand("mceDirectionLTR",function(){a("ltr")}),b.addCommand("mceDirectionRTL",function(){a("rtl")}),b.addButton("ltr",{title:"directionality.ltr_desc",cmd:"mceDirectionLTR"}),b.addButton("rtl",{title:"directionality.rtl_desc",cmd:"mceDirectionRTL"}),b.onNodeChange.add(d._nodeChange,d)},getInfo:function(){return{longname:"Directionality",author:"Moxiecode Systems AB",authorurl:"http://tinymce.moxiecode.com",infourl:"http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/directionality",version:tinymce.majorVersion+"."+tinymce.minorVersion}},_nodeChange:function(b,a,e){var c,d=b.dom;return(e=d.getParent(e,d.isBlock))?(c=d.getAttrib(e,"dir"),a.setActive("ltr","ltr"==c),a.setDisabled("ltr",0),a.setActive("rtl","rtl"==c),void a.setDisabled("rtl",0)):(a.setDisabled("ltr",1),void a.setDisabled("rtl",1))}}),tinymce.PluginManager.add("directionality",tinymce.plugins.Directionality)}();