/* jce - 2.6.6 | 2017-01-02 | http://www.joomlacontenteditor.net | Copyright (C) 2006 - 2017 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
JCEMediaBox={Popup:{addons:{},setAddons:function(n,o){"undefined"==typeof this.addons[n]&&(this.addons[n]={}),$.extend(this.addons[n],o)},getAddons:function(n){return n?this.addons[n]:this.addons},getAddon:function(v,n){var r,cp=!1,addons=this.getAddons(n);return $.each(addons,function(addon,o){var fn=o[addon]||function(){};r=fn.call(this,v),"undefined"!=typeof r&&(cp=r)}),cp}},trim:function(s){return $.trim(s)}},WFPopups.addPopup("jcemediabox",{params:{attribute:"data-mediabox",popup_group:"",popup_icon:1,popup_icon_position:"",popup_autopopup:"",popup_hide:0,popup_mediatype:""},setup:function(){var self=this;$("#jcemediabox_popup_icon").change(function(){self.setIcon()}),$.each(this.params,function(k,v){"popup_icon_position"===k&&(v=v.replace("icon-","zoom-")),$("#jcemediabox_"+k).val(v)})},check:function(n){return/jce(popup|_popup|lightbox)/.test(n.className)},getMediaType:function(n){var mt;switch(n.type){case"image/gif":case"image/jpeg":case"image/png":case"image/*":case"image":mt="image";break;case"iframe":mt="iframe";break;case"director":case"application/x-director":mt="application/x-director";break;case"windowsmedia":case"mplayer":case"application/x-mplayer2":mt="application/x-mplayer2";break;case"quicktime":case"video/quicktime":mt="video/quicktime";break;case"real":case"realaudio":case"audio/x-pn-realaudio-plugin":mt="audio/x-pn-realaudio-plugin";break;case"divx":case"video/divx":mt="video/divx";break;case"flash":case"application/x-shockwave-flash":mt="application/x-shockwave-flash";break;case"ajax":case"text/xml":case"text/html":mt="text/html"}if(!mt&&n.href){JCEMediaBox.options={popup:{google_viewer:0,pdfjs:0}};var o=JCEMediaBox.Popup.getAddon(n.href);o&&o.type&&(mt=o.type)}return mt||n.type||""},getImageType:function(s){var e=/\.(jp(eg|g)|png|bmp|gif|tiff)$/.exec(s);return e?("jpg"===e[1]&&(e[1]="jpeg"),"image/"+e[1]):"image/jpeg"},remove:function(n){var ed=tinyMCEPopup.editor;$.each(["jcepopup","jcelightbox","jcebox","icon-left","icon-right","icon-top-left","icon-top-right","icon-bottom-left","icon-bottom-right","zoom-left","zoom-right","zoom-top-left","zoom-top-right","zoom-bottom-left","zoom-bottom-right","noicon","noshow","autopopup-single","autopopup-multiple"],function(i,v){ed.dom.removeClass(n,v)}),ed.dom.setAttrib(n,"data-mediabox",null),ed.dom.setAttrib(n,"data-mediabox-title",null),ed.dom.setAttrib(n,"data-mediabox-caption",null),ed.dom.setAttrib(n,"data-mediabox-group",null)},convertData:function(s){function trim(s){return s.replace(/:"([^"]+)"/,function(a,b){return':"'+b.replace(/^\s+|\s+$/,"").replace(/\s*::\s*/,"::")+'"'})}if(/^{[\w\W]+}$/.test(s))return $.parseJSON(trim(s));if(/\w+\[[^\]]+\]/.test(s)){var data={};return tinymce.each(tinymce.explode(s,";"),function(p){var args=p.match(/([\w-]+)\[(.*)\]$/);args&&3===args.length&&(data[args[1]]=args[2])}),data}},getAttributes:function(n,index){var rv,v,ed=tinyMCEPopup.editor,data={};index=index||0,index=index||0;var rel=(ed.dom.getAttrib(n,"title"),ed.dom.getAttrib(n,"rel")),icon=/noicon/g.test(n.className),hide=/noshow/g.test(n.className);if(/(autopopup(.?|-single|-multiple))/.test(n.className)&&(v=/autopopup-multiple/.test(n.className)?"autopopup-multiple":"autopopup-single",$("#jcemediabox_popup_autopopup").val(v)),$("#jcemediabox_popup_icon").val(icon?0:1),$("#jcemediabox_popup_icon_position").prop("disabled",icon),$("#jcemediabox_popup_hide").val(hide?1:0),s=/(zoom|icon)-(top-right|top-left|bottom-right|bottom-left|left|right)/.exec(n.className)){var v=s[0];v&&(v=v.replace("icon-","zoom-"),$("#jcemediabox_popup_icon_position").val(v))}var relRX=/(\w+|alternate|stylesheet|start|next|prev|contents|index|glossary|copyright|chapter|section|subsection|appendix|help|bookmark|nofollow|licence|tag|friend)\s+?/g,json=ed.dom.getAttrib(n,"data-json")||ed.dom.getAttrib(n,"data-mediabox");if(json&&(data=this.convertData(json)),rel&&/\w+\[.*\]/.test(rel)){var ra="";(rv=relRX.exec(rel))&&(ra=rv[1],rel=rel.replace(relRX,"")),/^\w+\[/.test(rel)&&(data=this.convertData($.trim(rel))||{},data.rel=ra)}else{var group=$.trim(rel.replace(relRX,""));$("#jcemediabox_popup_group").val(group)}if($.isEmptyObject(data)&&$.each(ed.dom.getAttribs(n),function(i,at){var name=at.name;if(name&&name.indexOf("data-mediabox-")!==-1){var k=name.replace("data-mediabox-","");data[k]=ed.dom.getAttrib(n,name)}}),data.title&&/::/.test(data.title)){var parts=data.title.split("::");parts.length>1&&(data.caption=parts[1]),data.title=parts[0]}$.each(data,function(k,v){if($("#jcemediabox_popup_"+k).get(0)&&""!==v){if("title"==k||"caption"==k||"group"==k)try{v=decodeURIComponent(v)}catch(e){}v=tinymce.DOM.decode(v),$("#jcemediabox_popup_"+k).val(v),"title"!=k&&"caption"!=k||$('input[name^="jcemediabox_popup_'+k+'"]').eq(index).val(v),delete data[k]}});var x=0;return $.each(data,function(k,v){if(""!==v){try{v=decodeURIComponent(v)}catch(e){}var n=$(".uk-repeatable").eq(0);x>0&&$(n).clone(!0).appendTo($(n).parent());var elements=$(".uk-repeatable").eq(x).find("input, select");$(elements).eq(0).val(k),$(elements).eq(1).val(v)}x++}),$("#jcemediabox_popup_mediatype").val(this.getMediaType(n)),$.extend(data,{src:ed.dom.getAttrib(n,"href"),type:ed.dom.getAttrib(n,"type")||""}),data},setAttributes:function(n,args,index){var ed=tinyMCEPopup.editor;index=index||0,this.remove(n),index=index||0,ed.dom.addClass(n,"jcepopup");var auto=$("#jcemediabox_popup_autopopup").val();auto&&ed.dom.addClass(n,auto);var data={};args.title&&(ed.dom.setAttrib(n,"title",args.title),delete args.title),$.each(["group","width","height","title","caption"],function(i,k){var v=$("#jcemediabox_popup_"+k).val()||args[k]||"";if("title"==k||"caption"==k){var mv=$('input[name^="jcemediabox_popup_'+k+'"]').eq(index).val();"undefined"!=typeof mv&&(v=mv)}data[k]=v}),$(".uk-repeatable","#jcemediabox_popup_params").each(function(){var k=$('input[name^="jcemediabox_popup_params_name"]',this).val(),v=$('input[name^="jcemediabox_popup_params_value"]',this).val();""!==k&&""!==v&&(data[k]=v)}),data=$.extend(data,args.data||{});var mt=$("#jcemediabox_popup_mediatype").val()||n.type||data.type||"";"image"==mt&&(mt=this.getImageType(n.href)),ed.dom.setAttrib(n,"type",mt),data.type&&delete data.type;var rel=ed.dom.getAttrib(n,"rel","");rel&&(rel=rel.replace(/([a-z0-9]+)(\[([^\]]+)\]);?/gi,"")),$(".uk-repeatable").each(function(){var elements=$("input, select",this),key=$(elements).eq(0).val(),value=$(elements).eq(1).val();data[key]=value});var i,attrs=n.attributes;for(i=attrs.length-1;i>=0;i--){var attrName=attrs[i].name;attrName&&attrName.indexOf("data-mediabox-")!==-1&&n.removeAttribute(attrName)}$.each(data,function(k,v){ed.dom.setAttrib(n,"data-mediabox-"+k,ed.dom.encode(v))}),ed.dom.setAttrib(n,"rel",$.trim(rel)),0==$("#jcemediabox_popup_icon").val()?ed.dom.addClass(n,"noicon"):ed.dom.addClass(n,$("#jcemediabox_popup_icon_position").val()),1==$("#jcemediabox_popup_hide").val()&&ed.dom.addClass(n,"noshow"),ed.dom.setAttrib(n,"target","_blank")},setIcon:function(){var v=$("#jcemediabox_popup_icon").val();parseInt(v)?$("#jcemediabox_popup_icon_position").removeAttr("disabled"):$("#jcemediabox_popup_icon_position").attr("disabled","disabled")},onSelect:function(){},onSelectFile:function(args){$.each(args,function(k,v){$("#jcemediabox_popup_"+k).val(v)})}});