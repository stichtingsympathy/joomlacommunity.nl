/* jce - 2.8.1 | 2019-12-04 | https://www.joomlacontenteditor.net | Copyright (C) 2006 - 2019 Ryan Demmer. All rights reserved | GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html */
!function(){function partition(array,maxrows){var size=array.length,columns=Math.ceil(size/maxrows),fullrows=size-(columns-1)*maxrows,result=[];for(i=0;i<maxrows;++i){var n=array.splice(0,i<fullrows?columns:columns-1);result.push(n)}return result}function flattenObjectToArray(obj){var values=[];return each(obj,function(value,key){return!value||(!!tinymce.is(value,"function")||(tinymce.is(value,"object")&&(value=flattenObjectToArray(value)),"string"==typeof value&&(value=value.split(" ")),void(values=values.concat(value))))}),values}function uikit(){function apply(elm){var classes=elm.getAttribute("class"),suffix="",layout="";if(DOM.addClass(elm,"uk-flex"),classes.indexOf("wf-columns-stack-")!==-1){var stack=/wf-columns-stack-(small|medium|large)/.exec(classes)[1];suffix="@"+stack.charAt(0),DOM.addClass(elm,"uk-flex-wrap")}if(classes.indexOf("wf-columns-layout-")!==-1)if(layout=/wf-columns-layout-([0-9-]+|auto)/.exec(classes)[1],"auto"===layout)DOM.addClass(DOM.select(".wf-column",elm),"uk-flex-auto");else{var weight=layout.charAt(0),cls=mapLayout(layout)+suffix;parseInt(weight)>1?DOM.addClass(DOM.select(".wf-column:first-child",elm),cls):"1-2-1"===layout?DOM.addClass(DOM.select(".wf-column:nth(2)",elm),cls):DOM.addClass(DOM.select(".wf-column:last-child",elm),cls)}DOM.addClass(elm,"uk-child-width-expand"+suffix)}function remove(elm){if(DOM.hasClass(elm,"uk-flex")){var classes=elm.getAttribute("class");if(classes.indexOf("uk-child-width-expand")!==-1){var stack=/uk-child-width-expand(@s|@m|@l)?/.exec(classes);if(stack){var suffixMap=function(val){var map={"@s":"small","@m":"medium","@l":"large"};return map[val]||""},suffix=suffixMap(stack[1]);suffix&&DOM.addClass(elm,"wf-columns-stack-"+suffix)}DOM.removeClass(elm,"uk-flex-wrap")}var nodes=tinymce.grep(elm.childNodes,function(node){if("DIV"===node.nodeName)return node}),layout="wf-columns-layout-auto";each(nodes,function(node,i){var cls=node.getAttribute("class");if(cls&&cls.indexOf("uk-width-")!==-1){var match=/uk-width-([0-9-]+)(?:@s|@m|@l|)/.exec(cls),values=[];if(match&&(values=mapLayout("uk-width-"+match[1]),DOM.removeClass(node,match[0])),!values)return!0;layout=0===i?"wf-columns-layout-"+values[0]:i===nodes.length-1?"wf-columns-layout-"+values[values.length-1]:"wf-columns-layout-"+values[1]}}),DOM.addClass(elm,layout)}}var mapLayout=function(str){var cls;switch(str){case"1-2":case"2-1":cls="uk-width-2-3";break;case"1-3":case"3-1":cls="uk-width-3-4";break;case"2-1-1":case"1-2-2":case"1-2-1":cls="uk-width-1-2";break;case"uk-width-2-3":cls=["2-1","1-2"];break;case"uk-width-3-4":cls=["3-1","1-3"];break;case"uk-width-1-2":cls=["2-1-1","1-2-1","1-2-2"]}return cls};return{apply:apply,remove:remove}}function bootstrap(){function apply(elm){var classes=elm.getAttribute("class"),suffix="",layout="";if(DOM.addClass(elm,"row"),classes.indexOf("wf-columns-stack-")!==-1){var stack=/wf-columns-stack-(small|medium|large)/.exec(classes)[1],suffixMap=function(val){var map={small:"-sm",medium:"-md",large:"-lg"};return map[val]||""};suffix=suffixMap(stack)}if(classes.indexOf("wf-columns-layout-")!==-1)if(layout=/wf-columns-layout-([0-9-]+|auto)/.exec(classes)[1],"auto"===layout)DOM.addClass(DOM.select(".wf-column",elm),"col"+suffix);else{var pos=layout.charAt(0),cls="col"+suffix+"-"+mapLayout(layout);parseInt(pos)>1?DOM.addClass(DOM.select(".wf-column:first-child",elm),cls):"1-2-1"===layout?DOM.addClass(DOM.select(".wf-column:nth(2)",elm),cls):1===parseInt(pos)&&DOM.addClass(DOM.select(".wf-column:last-child",elm),cls)}DOM.addClass(DOM.select(".wf-column",elm),"col"+suffix)}function remove(elm){if(DOM.hasClass(elm,"row")){var nodes=DOM.select('div[class*="col"]',elm),layout="wf-columns-layout-auto",stack="";each(nodes,function(node,i){var cls=node.getAttribute("class");if(cls&&cls.indexOf("col-")!==-1){var match=/col-(sm|md|lg)(-[0-9]+)?/.exec(cls),values=[];if(match){values=mapLayout(match[0]),DOM.removeClass(node,match[0]);var suffixMap=function(val){var map={sm:"small",md:"medium",lg:"large"};return map[val]||""},suffix=suffixMap(match[1]);suffix&&(stack="wf-columns-stack-"+suffix),values&&(layout=0===i?"wf-columns-layout-"+values[0]:i===nodes.length-1?"wf-columns-layout-"+values[values.length-1]:"wf-columns-layout-"+values[1])}}}),DOM.removeClass(elm,"row"),DOM.addClass(elm,layout),DOM.addClass(elm,stack)}}var mapLayout=function(str){var cls;switch(str){case"1-2":case"2-1":cls="8";break;case"1-3":case"3-1":cls="9";break;case"2-1-1":case"1-2-2":case"1-2-1":cls="6";break;case"col-sm-8":case"col-md-8":case"col-lg-8":cls=["2-1","1-2"];break;case"col-sm-9":case"col-md-9":case"col-lg-9":cls=["3-1","1-3"];break;case"col-sm-6":case"col-md-6":case"col-lg-6":cls=["2-1-1","1-2-1","1-2-2"]}return cls};return{apply:apply,remove:remove}}function isColumn(elm){return elm&&"DIV"==elm.nodeName&&elm.className.indexOf("wf-column")!==-1}function removeColumn(editor){var node=getColumnNode(editor);if(node){for(var parent=editor.dom.getParent(node,".wf-columns");child=node.firstChild;)if(editor.dom.isEmpty(child)&&1===child.nodeType)editor.dom.remove(child);else{var num=parent.childNodes.length,idx=editor.dom.nodeIndex(node)+1;idx<=Math.ceil(num/2)?editor.dom.insertBefore(child,parent):editor.dom.insertAfter(child,parent)}editor.dom.remove(node);var cols=editor.dom.select(".wf-column",parent);if(cols.length){var col=cols[cols.length-1];col&&(editor.selection.select(col.firstChild),editor.selection.collapse(1))}else editor.dom.remove(parent,1);editor.nodeChanged()}editor.undoManager.add()}function createColumn(editor){var settings=editor.settings,childBlock=(settings.force_p_newlines?"p":"")||settings.forced_root_block,columnContent="&nbsp;";childBlock&&(columnContent=editor.dom.create(childBlock),tinymce.isIE||(columnContent.innerHTML='<br data-mce-bogus="1">'));var col=editor.dom.create("div",{class:"wf-column",contenteditable:"true","data-mce-column":1},columnContent);return col}function addColumn(editor,node,parentCol){var node=getColumnNode(editor,node),col=createColumn(editor);node?editor.dom.insertAfter(col,node):(editor.formatter.apply("column"),col=editor.dom.get("__tmp"),col&&(col.parentNode.insertBefore(parentCol,col),parentCol.appendChild(col),editor.dom.setAttrib(col,"id",""))),col.childNodes&&(editor.selection.select(col.firstChild),editor.selection.collapse(1),editor.nodeChanged())}function getColumnNode(editor,node){return node=node||editor.selection.getNode(),node===editor.getBody()?null:("DIV"!==node.nodeName&&(node=editor.dom.getParent(node,"DIV")),isColumn(node)?node:null)}function getSelectedBlocks(editor){var blocks=editor.selection.getSelectedBlocks(),nodes=tinymce.map(blocks,function(node){return"LI"===node.nodeName?node.parentNode:node});return nodes}function insertColumn(editor,data){var parentCol,node=getColumnNode(editor),cls=["wf-columns"],stack=data.stack,align=data.align,num=data.columns,layout=data.layout;if(stack&&cls.push("wf-columns-stack-"+stack),align&&cls.push("wf-columns-align-"+align),layout&&cls.push("wf-columns-layout-"+layout),node){parentCol=editor.dom.getParent(node,".wf-columns");var classes=parentCol.getAttribute("class");classes=tinymce.grep(classes.split(" "),function(val){if(val.indexOf("wf-columns")===-1)return val}),classes=classes.concat(cls),editor.dom.setAttrib(parentCol,"class",tinymce.trim(classes.join(" ")));var cols=editor.dom.select(".wf-column",parentCol),lastNode=cols[cols.length-1];num=Math.max(num-cols.length,0)}else{var lastNode,nodes=getSelectedBlocks(editor);nodes.length&&(lastNode=nodes[nodes.length-1]);var parentCol,columns=[],newCol=editor.dom.create("div",{class:"wf-column",contenteditable:!0,"data-mce-column":1});if(num<nodes.length){for(var groups=partition(nodes,num),i=0;i<groups.length;i++){var group=groups[i];editor.dom.wrap(group,newCol,!0),columns.push(group[0].parentNode)}num=0}else each(nodes,function(node){return num--,isColumn(node)||isColumn(node.parentNode)?(parentCol||(parentCol=editor.dom.getParent(node,".wf-columns")),node=editor.dom.getParent(node,".wf-column"),node&&columns.push(node),editor.dom.empty(parentCol),!0):(editor.dom.wrap(node,newCol),void columns.push(node.parentNode))});parentCol?each(columns,function(column){parentCol.appendChild(column)}):(parentCol=editor.dom.create("div",{class:cls.join(" "),contenteditable:!1,"data-mce-column":1}),editor.dom.wrap(columns,parentCol,!0))}if(num>0)for(;num--;)addColumn(editor,lastNode,parentCol);editor.undoManager.add(),editor.nodeChanged()}var DOM=tinymce.DOM,each=tinymce.each,VK=tinymce.VK,Event=tinymce.dom.Event,styleMap={uikit:{classes:["uk-flex","uk-child-width-expand","uk-flex-wrap","uk-child-width-expand@s","uk-child-width-expand@m","uk-child-width-expand@l","uk-flex-auto","uk-width-2-3,uk-width-3-4","uk-width-1-2"]},bootstrap:{classes:["row","col","col-sm","col-md","col-lg"]}},mappedClasses=flattenObjectToArray(styleMap);tinymce.create("tinymce.plugins.Columns",{init:function(editor,url){this.editor=editor,this.url=url;var framework=editor.getParam("columns_framework","");editor.onPreProcess.add(function(editor,o){var nodes=editor.dom.select(".wf-columns",o.node);each(nodes,function(elm){if(elm.setAttribute("data-wf-columns",1),elm.removeAttribute("contentEditable"),elm.removeAttribute("data-mce-column"),each(editor.dom.select(".wf-column",elm),function(node){node.removeAttribute("contentEditable"),node.removeAttribute("data-mce-column")}),!framework)return!0;"uikit"===framework&&(new uikit).apply(elm),"bootstrap"===framework&&(new bootstrap).apply(elm);var classes=elm.getAttribute("class");each(classes.split(" "),function(val){val.indexOf("wf-columns")!==-1&&editor.dom.removeClass(elm,val)}),editor.dom.removeClass(editor.dom.select(".wf-column",elm),"wf-column")})}),editor.onSetContent.add(function(editor,o){var columns=editor.dom.select("[data-wf-columns], .wf-columns",o.content);each(columns,function(column){editor.dom.addClass(column,"wf-columns"),column.setAttribute("contenteditable",!1),each(column.childNodes,function(node){return"DIV"!==node.nodeName||(editor.dom.addClass(node,"wf-column"),node.setAttribute("contenteditable",!0),void each(mappedClasses,function(name){editor.dom.removeClass(node,name)}))}),(new uikit).remove(column),(new bootstrap).remove(column),each(mappedClasses,function(name){editor.dom.removeClass(column,name)})})}),editor.addButton("columns_add",{title:"columns.add",onclick:function(){var node=editor.selection.getNode();addColumn(editor,node)}}),editor.addButton("columns_delete",{title:"columns.delete",onclick:function(){var node=editor.selection.getNode();removeColumn(editor,node)}}),editor.onInit.add(function(){editor.settings.compress.css||editor.dom.loadCSS(url+"/css/content.css"),editor.onKeyDown.addToTop(function(editor,e){e.keyCode===VK.ENTER&&e.ctrlKey&&getColumnNode(editor)&&(addColumn(editor),e.preventDefault(),e.stopPropagation()),e.keyCode!==VK.BACKSPACE&&e.keycode!==VK.DELETE||!e.ctrlKey||getColumnNode(editor)&&(removeColumn(editor),e.preventDefault(),e.stopPropagation())}),editor.formatter.register("column",{block:"div",classes:"wf-column",attributes:{id:"__tmp",contenteditable:"true","data-mce-column":"1"},wrapper:!0})}),editor.onNodeChange.add(function(ed,cm,n,co){"DIV"!==n.nodeName&&(n=ed.dom.getParent(n,"DIV"));var state=isColumn(n);if(state&&0===n.childNodes.length&&n.previousSibling){var col=n.previousSibling.lastChild;col&&ed.dom.remove(n)&&(editor.selection.select(col),editor.selection.collapse(0))}cm.setActive("columns",state),cm.setDisabled("columns_add",!state),cm.setDisabled("columns_delete",!state)})},createControl:function(n,cm){function createMenuGrid(cols,rows){var html="";for(html+='<div class="mceToolbarRow">',html+='   <div class="mceToolbarItem">',html+='       <table role="presentation" class="mceTableSplitMenu"><tbody>',i=0;i<rows;i++){for(html+="<tr>",x=0;x<cols;x++)html+='<td><a href="#"></a></td>';html+="</tr>"}return html+="       </tbody></table>",html+="   </div>",html+="</div>"}function menuGridMouseOver(e){var el=e.target;"TD"!==el.nodeName&&(el=el.parentNode);var tbody=DOM.getParent(el,"tbody");if(tbody){var i,z,rows=tbody.childNodes,row=el.parentNode,x=tinymce.inArray(row.childNodes,el),y=tinymce.inArray(rows,row);if(!(x<0||y<0))for(i=0;i<rows.length;i++)for(cells=rows[i].childNodes,z=0;z<cells.length;z++)z>x||i>y?DOM.removeClass(cells[z],"selected"):DOM.addClass(cells[z],"selected")}}function menuGridClick(e){var el=e.target,bookmark=0;"TD"!==el.nodeName&&(el=el.parentNode);var table=DOM.getParent(el,"table"),cls=["wf-columns"],stack=ed.getParam("columns_stack","medium");stack&&cls.push("wf-columns-stack-"+stack);var align=ed.getParam("columns_align","");align&&cls.push("wf-columns-align-"+align);for(var html='<div class="'+cls.join(" ")+'" contenteditable="false">',rows=tinymce.grep(DOM.select("tr",table),function(row){return DOM.select("td.selected",row).length}),block=ed.settings.forced_root_block||"",y=0;y<rows.length;y++)for(var cols=DOM.select("td.selected",rows[y]).length,x=0;x<cols;x++)html+='<div class="wf-column" contenteditable="true">',html+=block?ed.dom.createHTML(block,{},"&nbsp;"):'<br data-mce-bogus="1">',html+="</div>";return html+="</div>",bookmark&&(ed.selection.moveToBookmark(bookmark),ed.focus(),bookmark=0),ed.execCommand("mceInsertRawHTML",!1,html),tinymce.dom.Event.cancel(e)}function updateColumnValue(val,num){columnsNum.setDisabled(!1),val?(num=val.split("-").length,columnsNum.value(num)):columnsNum.value(num)}var self=this,ed=self.editor;if("columns"==n){var num=1,form=cm.createForm("columns_form"),columnsNum=cm.createTextBox("columns_num",{label:ed.getLang("columns.columns","Columns"),name:"columns",subtype:"number",attributes:{step:1,min:1},value:num,onchange:function(){num=columnsNum.value()}});form.add(columnsNum);var layoutList=cm.createListBox("columns_layout",{label:ed.getLang("columns.layout","Layout"),onselect:function(val){updateColumnValue(val,num)},name:"layout",max_height:"auto"});each(["","2-1","1-2","3-1","1-3","2-1-1","1-2-1","1-1-2"],function(val){val?key=ed.getLang("columns.layout_"+val,val):key=ed.getLang("columns.layout_auto","Auto"),layoutList.add(key,val,{icon:"columns_layout_"+val.replace(/-/g,"_")})});var stackList=cm.createListBox("columns_stack",{label:ed.getLang("columns.stack","Stack Width"),onselect:function(v){},name:"stack",max_height:"auto"});each(["","small","medium","large"],function(val){val?key=ed.getLang("columns.stack_"+val,val):key=ed.getLang("columns.stack_none","None"),stackList.add(key,val)}),form.add(stackList),form.add(layoutList);var ctrl=cm.createSplitButton("columns",{title:"columns.desc",onclick:function(){ed.windowManager.open({title:ed.getLang("columns.desc","Create Columns"),items:[form],size:"mce-modal-landscape-small",open:function(){var nodes=getSelectedBlocks(ed),stack=ed.getParam("columns_stack","medium"),num=ed.getParam("columns_num",1),layout=ed.getParam("columns_layout","");if(nodes.length&&(num=nodes.length,col=ed.dom.getParent(nodes[0],".wf-columns"),col)){var cols=ed.dom.select(".wf-column",col),cls=col.getAttribute("class");cols.length&&(num=cols.length),cls&&cls.indexOf("wf-columns-stack-")!==-1&&(stack=/wf-columns-stack-(small|medium|large)/.exec(col.className)[1]),cls&&cls.indexOf("wf-columns-layout-")!==-1&&(layout=/wf-columns-layout-([0-9-]+|auto)/.exec(cls)[1]),DOM.setHTML(this.id+"_insert",ed.getLang("update","Update"))}stackList.value(stack),layoutList.value(layout),updateColumnValue(layout,num)},buttons:[{title:ed.getLang("common.cancel","Cancel"),id:"cancel"},{title:ed.getLang("common.insert","Insert"),id:"insert",onsubmit:function(e){var data=form.submit();Event.cancel(e),insertColumn(ed,data)},classes:"primary",autofocus:!0}]})},class:"mce_columns"});return ctrl.onRenderMenu.add(function(c,m){var sb=m.add({onmouseover:menuGridMouseOver,onclick:menuGridClick,html:createMenuGrid(5,1),class:"mceColumns"});m.onShowMenu.add(function(){(n=DOM.get(sb.id))&&DOM.removeClass(DOM.select(".mceTableSplitMenu td",n),"selected")})}),ctrl}}}),tinymce.PluginManager.add("columns",tinymce.plugins.Columns)}();