!function(){var moduleFactory=function($){var module=this,jQuery=$;$.require().script("ui/effect").done(function(){var exports=function(){!function($){$.effects.effect.transfer=function(o,done){var elem=$(this),target=$(o.to),targetFixed="fixed"===target.css("position"),body=$("body"),fixTop=targetFixed?body.scrollTop():0,fixLeft=targetFixed?body.scrollLeft():0,endPosition=target.offset(),animation={top:endPosition.top-fixTop,left:endPosition.left-fixLeft,height:target.innerHeight(),width:target.innerWidth()},startPosition=elem.offset(),transfer=$('<div class="ui-effects-transfer"></div>').appendTo(document.body).addClass(o.className).css({top:startPosition.top-fixTop,left:startPosition.left-fixLeft,height:elem.innerHeight(),width:elem.innerWidth(),position:targetFixed?"fixed":"absolute"}).animate(animation,o.duration,o.easing,function(){transfer.remove(),done()
})}}(jQuery)};exports(),module.resolveWith(exports)})};dispatch("ui/effect-transfer").containing(moduleFactory).to("Foundry/2.1 Modules")}();