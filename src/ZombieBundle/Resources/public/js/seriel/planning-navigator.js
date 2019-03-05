$.widget('seriel.planningNav', $.seriel.navigator, {
	_create: function () {
        this._super();
        
        $('.line.inp', this.element).bind('mousedown', $.proxy(this.lineMouseDown, this));
        
        initUncheckableRadio(this.element);
        
        this.options.articletitre = $('.titre_inp', this.element);
        this.options.articletitre.bind('change', $.proxy(this.actuButtonClicked, this));
        this.options.articlechapeau = $('.chapeau_inp', this.element);
        this.options.articlechapeau.bind('change', $.proxy(this.actuButtonClicked, this));
        this.options.articlemotcle = $('.motcle_inp', this.element);
        this.options.articlemotcle.bind('change', $.proxy(this.actuButtonClicked, this));
        this.options.articleauteur = $('.auteur_inp', this.element);
        this.options.articleauteur.bind('change', $.proxy(this.actuButtonClicked, this));
        
		this.options.section_widget = $('.section_widget', this.element);
		this.options.section_widget.multiChoiceWidget();
        this.options.type_elem_planning_widget = $('.type_elem_planning_widget', this.element);
        this.options.type_elem_planning_widget.multiChoiceWidget();
        
        
  
        $('input[type=radio]', this.element).bind('change', $.proxy(this.actuButtonClicked, this));
        
        this.options.planning_container = $('.planning_container', this.element);
        
        this.options.dateWidget = $('.ser_date_widget[name=periode]', this.element);

        this.options.dateWidget.bind('change', $.proxy(this.dateChanged, this));
        

        
        
        this.options.type_elem_planning_widget = $('.type_elem_planning_widget', this.element);
     
        
        this.options.type_elem_planning_widget.multiChoiceWidget();
    
        
        $('.empty_button', this.element).bind('click', $.proxy(this.emptySearch, this));
        
        this.options.actu_button = $('.planning_button', this.element);
        this.options.actu_button.bind('click', $.proxy(this.actuButtonClicked, this));

        var westBlock = $(' > .menu_left', this.element);
        var westWidth = westBlock.attr('layout-size');
        if (!westWidth)
            westWidth = westBlock.width();

        var eastBlock = $(' > .menu_right', this.element);
        var eastWidth = eastBlock.attr('layout-size');
        if (!eastWidth)
            eastWidth = eastBlock.width();

        this.element.serielLayout({
            defaults: {
                spacing_open: 0,
                spacing_closed: 0
            },
            west: {
                size: westWidth ? westWidth : 'auto'
            },
            east: {
                size: eastWidth ? eastWidth : 'auto'
            }
        });

        $('#planning_accordion', this.element).accordion({heightStyle: 'fill', animate: 200});
        $('#planning_accordion > div', this.element).perfectScrollbar();

        this.options.destDiv = $(' > .cont > .search_list_container', this.element);

        $('.planning_button', this.element).bind('click', $.proxy(this.runSearch, this));
        $('.widget:not(.widget_initialized)', this.element).ser_widget({});
        
        this.options.section_widget.bind('change', $.proxy(this.actuButtonClicked, this));
        
        this.options.type_elem_planning_widget.bind('change', $.proxy(this.actuButtonClicked, this));


        
        this.initEvents();

        setTimeout($.proxy(this.checkNewElems, this), 50);
	},
	actionClicked: function(event) {
		var target = $(event.currentTarget);

		if (target.hasClass('planning_export')) {
			this.exportExcel();
		}
	},
	exportExcel: function() {
		// get ids of visible element
		var elems = $('.planning_event:not(.hidden_by_titre):not(.hidden_by_chapeau):not(.hidden_by_motcle):not(.hidden_by_section):not(.hidden_by_auteur)', this.options.planning_container);
		
		if (elems.size() == 0) {
			alert('Aucun element a afficher.');
			return;
		}
		var type = 'ZombieBundle\\Entity\\News\\Article';
		var ids = [];
		
		for (var i = 0; i < elems.size(); i++) {
			var elem = $(elems.get(i));
			var elem_id = elem.attr('id_elem');
			ids.push(elem_id);
		}
		
		this.openModal('Liste des éléments', getUrlPrefix() + '/liste/fromIds', { 'post': { 'type': type, 'ids': implode(',', ids) }, 'width': 1200, 'height': 600});
	},
	lineMouseDown: function(event) {
		var target = $(event.currentTarget);
		if (target.hasClass('disabled')) {
			event.stopPropagation();
			return false;
		}
		return true;
	},
	initEvents: function() {
		this.options.events = $('.planning_event:not(.toRemove, .toRemoveTmp)', this.element);
        this.options.events.bind('dblclick', $.proxy(this.eventClicked, this));
        
        $('.planning_head .action', this.element).bind('click', $.proxy(this.actionClicked, this));
                
        this.positionEvents();
	},
	dateChanged: function() {
		var newVal = this.options.dateWidget.ser_dateWidget('getVal');
		hc().setHash('planning/'+newVal);
		
	},
	checkContent : function(url, key, serhash) {
		var hash = getCleanHash();
		
		if (this.options.lastCheckedHash && this.options.lastCheckedHash == hash) return;
		
		$('ul#main_menu > li.planning > a').attr('href', '#' + hash);
		
		if (!serhash) serhash = parseHash(hash);

		if (serhash && count(serhash) > 1) {
			var elem = serhash[1];
			
			var date = elem.getLabel();
			var currDate = $('.planning_head .date_filter_value', this.element).html();
			if (date != currDate && date !='') {
				// refresh planning.
				this.setPlanningLoading();
				
				if (this.options.ajaxResquest) {
	    			try {
	    				this.options.ajaxResquest.abort();
	    			} catch (ex) {
	    				console.log('EXCEPTION STOPING AJAX REQUEST IN PLANNING');
	    			}
	    		}
	    		
	    		this.options.ajaxResquest = $.ajax({type: 'POST', url: getUrlPrefix() + '/planning/week/'+date, success: $.proxy(this.loaded, this)});
			}
		}
		
		this.options.lastCheckedHash = hash;
	},
	loaded: function(result) {
		var res = $(result);
		if (res.hasClass('success')) {
			$('.planning_event', this.options.planning_container).addClass('toRemoveTmp');
			
			// get Data per day.
			var eventDay1 = $('.planning_datas td.day1 > span.planning_event', this.options.planning_container);
			var eventDay2 = $('.planning_datas td.day2 > span.planning_event', this.options.planning_container);
			var eventDay3 = $('.planning_datas td.day3 > span.planning_event', this.options.planning_container);
			var eventDay4 = $('.planning_datas td.day4 > span.planning_event', this.options.planning_container);
			var eventDay5 = $('.planning_datas td.day5 > span.planning_event', this.options.planning_container);
			var eventDay6 = $('.planning_datas td.day6 > span.planning_event', this.options.planning_container);
			var eventDay7 = $('.planning_datas td.day7 > span.planning_event', this.options.planning_container);
			
			$('.planning_datas td.day1', res).prepend(eventDay1);
			$('.planning_datas td.day2', res).prepend(eventDay2);
			$('.planning_datas td.day3', res).prepend(eventDay3);
			$('.planning_datas td.day4', res).prepend(eventDay4);
			$('.planning_datas td.day5', res).prepend(eventDay5);
			$('.planning_datas td.day6', res).prepend(eventDay6);
			$('.planning_datas td.day7', res).prepend(eventDay7);
			
			this.options.planning_container.html(res.html());
			
			this.actuButtonClicked();
			this.initEvents();
			
			setTimeout($.proxy(this.checkNewElems, this), 50);
			setTimeout($.proxy(this.checkRemovingElems, this), 50);
			setTimeout($.proxy(this.checkRemovingElems, this), 200);
			setTimeout($.proxy(this.checkRemovingElems, this), 500);
			
			setTimeout($.proxy(this.hidePlanningLoading, this), 100);
			
		} else {
			this.hidePlanningLoading();
		}
		
		
	},
	eventClicked: function(event) {
		var target = $(event.currentTarget);
		var hash = target.attr('hash');
		hc().setHash(hash);
	},
	compareEventsByTime: function(evt1, evt2) {
		evt1 = $(evt1);
		evt2 = $(evt2);
		
		var date1 = evt1.attr('date');
		var date2 = evt2.attr('date');
		
		var splitted = explode(' ', date1);
		var h1 = splitted[1];
		
		var splitted = explode(' ', date2);
		var h2 = splitted[1];
		
		return strcmp(h1, h2);

	},
	compteIdChanged: function(event) {
		var target = $(event.currentTarget);
		var value = target.multiChoiceWidget('getVal');
		
		var container = target.closest('.content');
		
		if (value) {
			$('.line.inp:not(.line_compte_select)', container).addClass('disabled');
			
			var inputs = $('input', container);
			inputs.attr('readonly', 'readonly');
			inputs.trigger('change');
			
			var multiChoiceWidgets = $('.line.inp:not(.line_compte_select) > .inp_container > .ser_multichoice_widget', container);
			multiChoiceWidgets.multiChoiceWidget('setDisable', true);
			multiChoiceWidgets.trigger('change');
		} else {
			$('.line.inp', container).removeClass('disabled');
			
			var inputs = $('input', container);
			inputs.removeAttr('readonly', 'readonly');
			inputs.trigger('change');
			
			var multiChoiceWidgets = $('.line.inp:not(.line_compte_select) > .inp_container > .ser_multichoice_widget', container);
			
			multiChoiceWidgets.multiChoiceWidget('setDisable', false);
			multiChoiceWidgets.trigger('change');
		}
		
		this.actuButtonClicked();
	},
	actuButtonClicked: function() {

		if (this.options.disable_refresh == true) return;
		
		var widgets = $('ul.filter_ul > li > .widget', this.element);
		
		var args = {};
        for (var i = 0; i < widgets.size(); i++) {
            var widget = $(widgets.get(i));
            var variables = null;
            var widgetName = null;
            
            if (widget.data('ser_widget_object')) {
                obj = widget.data('ser_widget_object');

               variables = obj.getVal();
               widgetName = obj.getName();
            }
            
            if (variables) {
            	if (typeof variables == 'string') args[widgetName] = variables;
            	else args = array_merge(args, variables); 
            }
        }
        
        var realArgs = {};
        for (var key in args) {
            var val = args[key];
            if (!val)
                continue;

            realArgs[key] = val;
        }

        args = realArgs;
        
		
		
        var titre = null;
        if (args['titre']) titre = args['titre'];
  
        var chapeau = null;
        if (args['chapeau']) chapeau = args['chapeau'];
        
        var motcle = null;
        if (args['motcle']) motcle = args['motcle'];
 
        var auteur = null;
        if (args['auteur']) auteur = args['auteur'];
        
        var sections = null;
        if (args['section']) sections = explode('-', args['section']);
        
      
        
        
        if (!titre) {
        	$('.planning_event', this.element).removeClass('hidden_by_titre');
        }
        if (!chapeau) {
        	$('.planning_event', this.element).removeClass('hidden_by_chapeau');
        }
        if (!motcle) {
        	$('.planning_event', this.element).removeClass('hidden_by_motcle');
        }
        if (!auteur) {
        	$('.planning_event', this.element).removeClass('hidden_by_auteur');
        }
        if (!sections) {
        	$('.planning_event', this.element).removeClass('hidden_by_section');
        }
        

        

        if (titre || chapeau || motcle || auteur || sections) {
        	var events = $('.planning_event', this.element);
        	
        	var str_patt_titre = null;
        	var str_patt_chapeau = null;
        	var str_patt_motcle = null;
        	var str_patt_auteur = null;    
        	var patt_titre = null;
        	var patt_chapeau = null;
          	var patt_motcle = null;
          	var patt_auteur = null; 
          	
        	if (titre) {
        		str_patt_titre = ''+str_replace('*', '.*', titre)+'';
        		patt_titre = new RegExp(str_patt_titre, 'i');
        	}
        	if (chapeau) {
        		str_patt_chapeau = ''+str_replace('*', '.*', chapeau)+'';
        		patt_chapeau = new RegExp(str_patt_chapeau, 'i');
        	}
        	if (motcle) {
        		str_patt_motcle = ''+str_replace('*', '.*', motcle)+'';
        		patt_motcle = new RegExp(str_patt_motcle, 'i');
        	}      
        	if (auteur) {
        		str_patt_auteur = ''+str_replace('*', '.*', auteur)+'';
        		patt_auteur = new RegExp(str_patt_auteur, 'i');
        	}      

        	for (var i = 0; i < events.size(); i++) {
        		
        		
        		var event = $(events.get(i));
        		
        		if (event.hasClass('toRemove') || event.hasClass('toRemoveTmp')) {
    				if (floatval(event.css('opacity')) == 0) event.remove();
    				continue;
        		}
        		
        		if (titre) {
        			
        			var num = event.attr('titre');
        			var match = false;
        			if (num) {
        				if (patt_titre.test(num)) {
        					match = true;
        				}
        			}
        			if (match) event.removeClass('hidden_by_titre');
            		else event.addClass('hidden_by_titre');

        		}
        		
        		if (chapeau) {
        			var num = event.attr('chapeau');
        			var match = false;
        			if (num) {
        				if (patt_chapeau.test(num)) {
        					match = true;
        				}
        			}
        			
        			if (match) event.removeClass('hidden_by_chapeau');
            		else event.addClass('hidden_by_chapeau');
        		}

        		if (motcle) {
        			var num = event.attr('motcle');
        			var match = false;
        			if (num) {
        				if (patt_motcle.test(num)) {
        					match = true;
        				}
        			}
        			
        			if (match) event.removeClass('hidden_by_motcle');
            		else event.addClass('hidden_by_motcle');
        		}
        		
        		if (auteur) {
        			var num = event.attr('auteur');
        			var match = false;
        			if (num) {
        				if (patt_auteur.test(num)) {
        					match = true;
        				}
        			}
        			
        			if (match) event.removeClass('hidden_by_auteur');
            		else event.addClass('hidden_by_auteur');
        		}
        		if (sections) {        			
        			var section = event.attr('section');
            		if (in_array(section, sections)) event.removeClass('hidden_by_section');
            		else event.addClass('hidden_by_section');
        		}

        	
        		
        	}
        }

        
        this.positionEvents();
	},
	createHourSep: function(heure) {
		return $('<span class="hour_sep hour_sep_'+heure+'" heure="'+heure+'"><span>'+heure+'</span><table><tbody><tr><td class="day1"><span>'+heure+'h</span></td><td class="day2"><span>'+heure+'h</span></td><td class="day3"><span>'+heure+'h</span></td><td class="day4"><span>'+heure+'h</span></td><td class="day5"><span>'+heure+'h</span></td><td class="day6"><span>'+heure+'h</span></td><td class="day7"><span>'+heure+'h</span></td></tr></tbody></table></span>')
	},
	positionEvents: function() {
		console.log('test 0 '+time());
		// Let's get all events and order them by time.
		this.options.events = $('.planning_event:not(.toRemove, .toRemoveTmp)', this.element);
		this.options.events.sort($.proxy(this.compareEventsByTime));
		
		$('.hour_sep', this.element).attr('newtop', -10);
		
		// Work with calculate position.
		var posByDay = { 'day1': 1, 'day2': 1, 'day3': 1, 'day4': 1, 'day5': 1, 'day6': 1, 'day7': 1 };
		
		// Lets work on that now !
		
		var minPos = 0;
		
		var lastRealTop = 0;
		var lastElemHeight = 0;
		var lastRealBottom = 0;
		
		var lastHour = null;
		var lastHourMinute = null;
		
		var posByHourMinute = {};
		var maxPosByHourMinute = {};
		
		console.log('test 1 '+time());
		
		for (var i = 0; i < this.options.events.size(); i++) {
			var event = $(this.options.events.get(i));
			var date = event.attr('date');
			
			var classes = explode(' ', event.attr('class'));
			var isHidden = false;
			if (classes) {
				for (var j = 0; j < count(classes); j++) {
					var cl = classes[j];
					if (substr(cl, 0, 7) == 'hidden_') {
						isHidden = true;
						break;
					}
				}
			}
			if (isHidden) {
				event.attr('newtop', lastRealTop);
				continue;
			}
			
			var hour = intval(substr(date, 11, 2));
			var hourMinute = substr(date, 11, 5);
			
			if (posByHourMinute[hourMinute]) minPos = posByHourMinute[hourMinute];
			
			if (hourMinute != lastHourMinute && lastHourMinute !== null) {
				if (maxPosByHourMinute[lastHourMinute]) {
					var tmp = maxPosByHourMinute[lastHourMinute];
					if (tmp > minPos) minPos = tmp;
				}
			}
			
			var diffMinute = 0;
			if (lastHourMinute) {
				diffMinute = SerielUtils.calcMinutesDiff(lastHourMinute, hourMinute);
			}
			
			pixIncrement = diffMinute * this.options.minMinuteHeight;
			minPos += pixIncrement;
			
			var day = event.closest('td').attr('day');
			var top = posByDay['day'+day] + this.options.eventDefMargin;
			
			var realTop = top;
			
			var hourDiff = 0;
			if (lastHour !== null) {
				hourDiff = hour - lastHour;
			}
			
			if (hourDiff > 0) {
				// insert hours necessary.
				
				if (maxPosByHourMinute[lastHourMinute]) lastRealTop = maxPosByHourMinute[lastHourMinute];
				
				var h_lastHourMinute = lastHourMinute;
				var h_lastPos = lastRealTop;
				
				
				
				for (var j = lastHour + 1; j <= hour; j++) {
					var h_hourMinute = j+':00';
					var diff = SerielUtils.calcMinutesDiff(h_lastHourMinute, h_hourMinute);
					var pix = diff * this.options.minMinuteHeight;
					var hourPos = h_lastPos + pix;
					
					if (hourDiff == 1) {
						var nextEvtPos = realTop;
						if (realTop >= minPos) {
							var diff2 = abs(SerielUtils.calcMinutesDiff(hourMinute, h_hourMinute));
						
							if (diff2 < diff) {
								var pix2 = diff2 * this.options.minMinuteHeight;
								hourPos = realTop - pix2;
							}
						}

					}
					
					var heure = j;
					var hour_sep = $('.hour_sep_'+heure, this.element);
					if (hour_sep.size() == 0) hour_sep = this.createHourSep(heure);
					hour_sep.attr('newtop', intval(hourPos));
					
					$('.planning_datas', this.element).append(hour_sep);
					
					h_lastHourMinute = h_hourMinute;
					h_lastPos = hourPos;
				}
			}
			
			if (realTop < minPos) realTop = minPos;
			
			event.attr('newtop', realTop);
			
			lastRealTop = realTop;
			lastElemHeight = event.height() + intval(event.css('padding-top')) + intval(event.css('padding-bottom')) + 3;
			lastRealBottom = lastRealTop + lastElemHeight;
			
			lastHour = hour;
			lastHourMinute = hourMinute;
			
			posByDay['day'+day] = lastRealBottom-1
			
			if (!posByHourMinute[hourMinute]) posByHourMinute[hourMinute] = realTop;
			
			if (!maxPosByHourMinute[hourMinute]) {
				maxPosByHourMinute[hourMinute] = realTop;
			} else {
				var oldMax = intval(maxPosByHourMinute[hourMinute]);
				if (intval(realTop) > intval(oldMax)) maxPosByHourMinute[hourMinute] = realTop;
			}
			minPos = lastRealTop;
		}
		
		console.log('test 2 '+time());
		
		$('table.days', this.element).css('height', (lastRealBottom + this.options.eventDefMargin)+'px');
		
		for (var i = 0; i < this.options.events.size(); i++) {
			var event = $(this.options.events.get(i));
			
			var top = event.attr('newtop');
			event.css('top', top+'px');
		}
		
		console.log('test 3 '+time());
		
		var hour_seps = $('.hour_sep', this.element);
		for (var i = 0; i < hour_seps.size(); i++) {
			var sep = $(hour_seps.get(i));
			var newtop = sep.attr('newtop');
			if (newtop) sep.css('top', newtop+'px');
		}
		console.log('test 4 '+time());
	},
	setPlanningLoading: function() {
		var loader = $(' > .loading', this.options.planning_container);
		if (loader.size() == 0) {
			loader = $('<div class="loading"></div>');
			loader.appendTo(this.options.planning_container);
		}
		
		loader.css('z-index', '3000');
		loader.css('display', 'block');
	},
	hidePlanningLoading: function() {
		$(' > .loading', this.options.planning_container).css('display', 'none');
	},
	checkNewElems: function() {
		$('.planning_event.event_new', this.element).removeClass('event_new');
	},
	checkRemovingElems: function() {
		var events = $('.planning_event.toRemove', this.element);
    	for (var i = 0; i < events.size(); i++) {
    		var event = $(events.get(i));
			if (floatval(event.css('opacity')) == 0) event.remove();
    	}
    	
    	$('.planning_event.toRemoveTmp', this.element).removeClass('toRemoveTmp').addClass('toRemove');
	},
	emptySearch: function(event) {
		this.options.disable_refresh = true;
		var target = $(event.currentTarget);
		var container = target.parent();
		
		var periode = this.options.dateWidget.ser_dateWidget('getVal');
		
		loadFieldsSearchFromParams({ 'periode': periode }, container);
		
		this.options.disable_refresh = false;
		this.actuButtonClicked()
	},
	options: {
		eventDefMargin: 8,
		minMinuteHeight: 1.5,
				  
        articletitre: null,
        articlechapeau: null,
        articlemotcle: null,
        articleauteur: null,
        section_widget: null,
        type_elem_planning_widget: null,
  
        lastCheckedHash: null,
		
		actu_button: null,
		disable_refresh: false,
		
		ajaxResquest: null
	}
});