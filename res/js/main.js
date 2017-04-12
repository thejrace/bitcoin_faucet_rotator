	var BitcoinDatabase = {
		"version": 1.0,
		"vdate"  : "23.09.2015",
		"reqTo"  : "",
		"list"   : "web-list"
	};


	function hasClass(element, cls) {
		return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1;
	}

	function addClass(element, cls){
		if( !hasClass(element, cls ) ) element.className += ' ' + cls;
	}

	function removeClass(element, cls) {
		var newClass = ' ' + element.className.replace( /[\t\r\n]/g, ' ') + ' ';
		// console.log( newClass );
		if( hasClass(element, cls ) ){
			while( newClass.indexOf(' ' + cls + ' ' ) >= 0 ){
				newClass = newClass.replace( ' ' + cls + ' ', ' ' );
			}
			element.className = newClass.replace( /^\s+|\s+$/g, '' );
		}
	}

	var Com = {

		$A: function(elem){
			return document.getElementById(elem);
		},
		show: function (elem){
			elem.style.display = 'block';
		},

		hide: function (elem){
			elem.style.display = 'none';
		},

		sieses: function (elem, rule, value){
			switch( rule ){
				case "left":
				elem.style.left = value;
				break;

				case "marginLeft":
				elem.style.marginLeft = value;
				break

				case "opacity":
				elem.style.opacity = value;
				break;
			}

		},

		IsNumeric: function(input){
			return (input - 0) == input && (''+input).trim().length > 0;
		}

	};

	var PopUp = {
		"overlay": "popup-overlay",
		"popup"  : "popup",

		on: function( data ){
			Com.show( Com.$A(this.overlay) );

			var	i = Com.$A(this.popup);
			Com.show(i);

			// Once datalari yazdir
			i.innerHTML = "<div id='popup-buton' onclick='PopUp.off()'>X</div>" + data;

			// Ölç - ortala
			Com.sieses(i, "left", "50%");
			Com.sieses(i, "marginLeft", "-" + ( i.offsetWidth / 2 ) + "px");
		},

		off: function(){
			Com.hide(Com.$A(this.overlay));
	        Com.$A(this.popup).innerHTML = "";
			Com.hide(Com.$A(this.popup));
		}

	}

	function formCheck(formid){
		var nodes  = Com.$A(formid).childNodes,
			count  = nodes.length,
			error  = [],
			notf   = Com.$A("form-notf");
		for( var i = 0; i < count; i++ ){
			for( var x = 0; x < nodes[i].childNodes.length; x++ ){
				if( nodes[i].childNodes[x].nodeName == "INPUT" && nodes[i].childNodes[x].type == "text" ) {
					input = nodes[i].childNodes[x];
					if( input.value == "" ){
						input.style.borderColor = "#ff0000";
						error.push( nodes[i].childNodes[x] );
					}
				}
			}
		}

		if( error.length > 0 ) {
			notf.innerHTML = " Formda eksiklikler var. ( Payout ve Timer numerik olmalı ).";
			return false; 
		} else {
			notf.innerHTML = "";
			return true;
		}
    }

    function c_status(elem){
    	if( elem.hasClass("checked") ){
    		elem.removeClass("checked");
    	} else {
    		elem.addClass("checked");
        }
    }

     function rq(d, cb){

     	if( d == "" ){
     		d = "hodo=reload";
     	}

     	// console.log(d);

     	$.ajax({
     		data: d,
     		dataType: 'json',
     		url: BitcoinDatabase.reqTo,
     		type: 'post',
     		success: function(r){
     			if(typeof cb == 'function'){
     				// console.log(r);
     				cb(r);
     			}
     			
            }

            }).fail(function(){
            	console.log("failed");
            });

        }

    function reloadList(data){
        document.getElementById(BitcoinDatabase.list).innerHTML = data;
        countdownCheck();
    }

    function filterAction( type, elem ){
    	c_status( $(elem).parent() );
    	if( $(elem).is(':checked') ){
    		$("." + type +" .filter-buton").removeClass("checked");
    		$(elem).parent().addClass("checked");
    	}
    	rq( $("#filters").serialize()  + '&req=reload_list', function(r){ reloadList(r.data) } );
    }

    Object.size = function(obj){
		var size = 0, key;
		for(key in obj){
			if(obj.hasOwnProperty(key)) size++;
		}
		return size;
	};

	function ajaxNotf( s, str ){
		var cont = Com.$A("ajax-notf"),
		hidebtn = '<span id="hide-notf">X</span>';
		Com.show(cont);
		cont.style.top = document.body.scrollTop + "px";
		// console.log(document.body.scrollTop );
		removeClass(cont, "success");
		removeClass(cont, "fail");
		( s ) ?
			addClass(cont, "success") :
			addClass(cont, "fail");
		cont.innerHTML = '<span>'+str+'</span>' + hidebtn ;
	}

	function hideajaxNotf(){
		var cont = Com.$A("ajax-notf");
		cont.innerHTML = "";
		Com.hide(cont);
	}

    $(document).ready(function(){

    	// Pencere kaydiginda ajax-notf en tepeye yapistir
    	$(document).on( 'scroll', function(){
		    Com.$A("ajax-notf").style.top = document.body.scrollTop + "px";
		});

    	// Ajax notf gizleme
    	$(document).on("click", '#hide-notf', function(){
    		hideajaxNotf();
    	});

	    $(document).on("click", ".filter-radios input[type='radio']", function(e){        
	    	filterAction( "filter-radios", this );
	    	e.stopPropagation();
	    });

	    $(document).on("click", ".sort-radios input[type='radio']", function(e){
	    	filterAction( "sort-radios", this );
	    	e.stopPropagation();
	    });

	    $(document).find("[tabdiv]").each(function(){
			$(this).jwTab({
				tabCont: $( $(this).attr("tabdiv") ),
				efekt: 'normal'
			});
		});
	});


	(function($) {
	
		$.fn.jwTab = function(options){

			var settings = $.extend({
				tabCont: '.tab-icerik',
				efekt: 'fade',
				ajaxData: null
			}, options);

			return this.each(function(){

				var tabButon = $(this).find("li.tab-btn"),
					tabCont = $(settings.tabCont),
					isActive = $(this).find("li.tab-btn").hasClass("selected"),
					aktifIndex = $(this).find("li.tab-btn aktif").index(),
					ajax = false;

				// tab icerik kac tane var bul
				var contcount = 0;
				tabCont.each(function(){
					contcount++;
				});

				// Birinci haric digerlerini gizle css' e gerek kalmadan
				tabCont.slice(1, contcount).hide();
				

				if(settings.ajaxData != null){
					ajax = true;
				}

				// tekrar aktif olanı goster
				if(isActive) {

					if(ajax){

						tabCont.eq(aktifIndex + 1).empty().append(settings.ajaxData).show();
					} else {
						tabCont.eq(aktifIndex + 1).show();
					}
					
				}

			
				tabButon.click(function(e){

					e.preventDefault();

					switch (settings.efekt) {
						case 'fade':

							if(ajax) {

								tabCont.empty().stop().fadeOut(50);
								tabButon.removeClass("selected");

								$(this).addClass("selected");
								tabCont.eq($(this).index()).empty().append(settings.ajaxData).stop().fadeIn();

							} else {
								tabCont.stop().fadeOut(50);
								tabButon.removeClass("selected");

								$(this).addClass("selected");
								tabCont.eq($(this).index()).stop().fadeIn();
							}

							
						break;
						case 'normal':

							if(ajax){
								tabCont.empty().stop().hide();
								tabButon.removeClass("selected");

								$(this).addClass("selected");
								tabCont.eq($(this).index()).empty().append(settings.ajaxData).stop().show();
							} else {
								tabCont.stop().hide();
								tabButon.removeClass("selected");

								$(this).addClass("selected");
								tabCont.eq($(this).index()).stop().show();
							}	
						break;
							
					}

						
				});
			});
		};
	}(jQuery));