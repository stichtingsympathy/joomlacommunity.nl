/* JCE Editor - 2.5.28 | 07 October 2016 | http://www.joomlacontenteditor.net | Copyright (C) 2006 - 2016 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
(function(){var each=tinymce.each;tinymce.create('tinymce.plugins.FormatSelectPlugin',{init:function(ed,url){var self=this;this.editor=ed;ed.onNodeChange.add(function(ed,cm,n){var c=cm.get('formatselect'),p;if(c){p=ed.dom.getParent(n,ed.dom.isBlock);if(p){c.select(p.nodeName.toLowerCase());}}});},createControl:function(n,cf){var ed=this.editor;switch(n){case"formatselect":if(ed.getParam('formatselect_blockformats')){return this._createBlockFormats();}
break;}},_createBlockFormats:function(){var self=this,ed=this.editor,PreviewCss=tinymce.util.PreviewCss;var c,fmts={'p':'advanced.paragraph','address':'advanced.address','pre':'advanced.pre','h1':'advanced.h1','h2':'advanced.h2','h3':'advanced.h3','h4':'advanced.h4','h5':'advanced.h5','h6':'advanced.h6','div':'advanced.div','div_container':'advanced.div_container','blockquote':'advanced.blockquote','code':'advanced.code','samp':'advanced.samp','span':'advanced.span','section':'advanced.section','article':'advanced.article','aside':'advanced.aside','figure':'advanced.figure','dt':'advanced.dt','dd':'advanced.dd'};c=ed.controlManager.createListBox('formatselect',{title:'advanced.block',onselect:function(v){ed.execCommand('FormatBlock',false,v);return false;}});if(c){each(ed.getParam('formatselect_blockformats','','hash'),function(v,k){c.add(ed.translate(k!=v?k:fmts[v]),v,{'class':'mce_formatPreview mce_'+v,style:function(){return PreviewCss(ed,{'block':v});}});});}
return c;}});tinymce.PluginManager.add('formatselect',tinymce.plugins.FormatSelectPlugin);})();