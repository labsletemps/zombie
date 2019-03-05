
$.widget('seriel.reportingNav', $.seriel.navigator, {
	_create: function () {
        this._super();
        
        $('.line.inp', this.element).bind('mousedown', $.proxy(this.lineMouseDown, this));
        
		$('.region_widget', this.element).multiChoiceWidget();
		$('.type_division_widget', this.element).multiChoiceWidget();
		$('.select_fournisseur_widget', this.element).multiChoiceWidget();
		$('.marque_vehicule_widget', this.element).multiChoiceWidget();
		$('.modele_vehicule_widget', this.element).multiChoiceWidget();
		$('.statut_widget', this.element).multiChoiceWidget();
		$('.services_widget', this.element).multiChoiceWidget();
		$('.section_widget', this.element).multiChoiceWidget();
		$('.trend_widget', this.element).multiChoiceWidget();
		$('.orderby_widget', this.element).multiChoiceWidget();
		$('.min_max_widget', this.element).minmaxWidget();

		$('.select_fournisseur_widget', this.element).bind('change', $.proxy(this.fournisseurIdChanged, this));
		
		$('.empty_button', this.element).bind('click', $.proxy(this.emptySearch, this));

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
        
        initUncheckableRadio(this.element);
        
        this.options.reportings = $('.reporting_container', this.element);
        this.options.reportings.reporting();

        this.options.accordion = $('#reporting_accordion', this.element);
        this.options.accordion.serielAccordion({heightStyle: 'fill', animate: 200, beforeActivate: $.proxy(this.beforeAccordionChange, this), activate: $.proxy(this.accordionChanged, this)});
        $(' > div', this.options.accordion).perfectScrollbar();

        $('.widget:not(.widget_initialized)', this.options.accordion).ser_widget({});        
        $('.action', this.element).bind('click', $.proxy(this.actionClicked, this));
        
        this.firstLoadCheck();
	},
	lineMouseDown: function(event) {
		var target = $(event.currentTarget);
		if (target.hasClass('disabled')) {
			event.stopPropagation();
			return false;
		}
		return true;
	},
	beforeAccordionChange: function(event, ui) {
	},
	accordionChanged: function(event, ui) {
		var newHeader = ui.newHeader;
		var index = (newHeader.index() / 2);
		this.showContentForIndex(index);
		
		var newDiv = newHeader.next();
		var type = newDiv.attr('type');
		
		if (this.options.lastHashByType[type]) {
			hc().setHash('reporting/'+this.options.lastHashByType[type]);
		} else {
			hc().setHash('reporting/'+type);
		}
	},
	showAccordionForType: function(type) {
		// click on H3.
		$('h3[type='+type+']', this.element).trigger('click');
	},
	showContentForIndex: function(index) {
		var elem = this.options.reportings.get(index);
		this.options.reportings.addClass('hidden');
		if (elem) {
			$(elem).removeClass('hidden');
		}
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
	},
	prestaIdChanged: function(event) {
		var target = $(event.currentTarget);
		var value = target.multiChoiceWidget('getVal');
		
		var container = target.closest('.content');
		
		if (value) {
			$('.line.inp:not(.line_prestataire_select)', container).addClass('disabled');
			
			var inputs = $('input', container);
			inputs.attr('readonly', 'readonly');
			inputs.trigger('change');
			
			var multiChoiceWidgets = $('.line.inp:not(.line_prestataire_select) > .inp_container > .ser_multichoice_widget', container);
			multiChoiceWidgets.multiChoiceWidget('setDisable', true);
			multiChoiceWidgets.trigger('change');
		} else {
			$('.line.inp', container).removeClass('disabled');
			
			var inputs = $('input', container);
			inputs.removeAttr('readonly', 'readonly');
			inputs.trigger('change');
			
			var multiChoiceWidgets = $('.line.inp:not(.line_prestataire_select) > .inp_container > .ser_multichoice_widget', container);
			
			multiChoiceWidgets.multiChoiceWidget('setDisable', false);
			multiChoiceWidgets.trigger('change');
		}
	},
	getReportingForType: function(type) {
		var res = this.options.reportings.filter('.rc_'+type);
		if (res.size() == 1) return res;
		return null;
	},
	getSearchStrForType: function(type) {
		var container = $(' > div.'+type, this.options.accordion);
		return buildSearchStr(container);
	},
	firstLoadCheck: function() {
		var hash = getCleanHash();
		var serhash = parseHash(hash);
		
		if (serhash && count(serhash) > 0) {
			var elem = serhash[0];
			var label = elem.getLabel();
			if (label != 'reporting') return;
			
			if (count(serhash) > 1) {
				elem2 = serhash[1];
					
				var params = elem2.getParams();
				if (count(params) > 0) {
					this.checkContent(hash, hash, serhash);
				}
			}
		}
	},
	checkContent : function(url, key, serhash) {
		var hash = getCleanHash();
		
		if (this.options.lastCheckedHash && this.options.lastCheckedHash == hash) return;

		$('ul#main_menu > li.reporting > a').attr('href', '#' + hash);
		
		if (!serhash) serhash = parseHash(hash);

		if (serhash && count(serhash) > 1) {
			var elem = serhash[1];
			
			var type = elem.getLabel();
			this.showAccordionForType(type);
			
			var params = elem.getParams();
			
			this.loadFieldsFromParams(type, params);
			
			if (count(params) > 0) {
				var reporting = this.getReportingForType(type);
				if (reporting) reporting.reporting('run', params);
			}
		}
		
		this.options.lastCheckedHash = hash;
	},
	loadFieldsSearchFromParams: function(type, params) {
		var container = $(' > .'+type, this.options.accordion);
		
		// remove params that not search concerned .
		var searchParams = {};
		for (var key in params) {
			if (key == 'row' || key == 'col' || key == 'datas') continue;
			searchParams[key] = params[key];
		}
		
		loadFieldsSearchFromParams(searchParams, container);
	},
	loadFieldsFromParams: function(type, params) {
		if (!params) return;
		
		var row = params['row'];
		var col = params['col'];
		var datas = params['datas'];
		
		var searchParams = {};
		for (var key in params) {
			if (key == 'row' || key == 'col' || key == 'datas') continue;
			searchParams[key] = params[key];
		}
		
		this.loadFieldsSearchFromParams(type, searchParams);
		
		var reporting = this.getReportingForType(type);
		if (reporting) reporting.reporting('loadFieldsFromParams', row, col, datas);
	},
	loadReportingFromSauvegarde: function(rs_id, type, reporting) {
		reporting_str = reporting;
		hc().setHash('reporting/'+type+'[' + reporting_str + ']');
	},
	options: {
		accordion: null,
		reportings: null,
		lastHashByType: {},
		
		lastCheckedHash: null
	}
});

$.widget('seriel.accueilReports', {
	_create: function() {
		$('.region_widget', this.element).multiChoiceWidget();
		$('.type_division_widget', this.element).multiChoiceWidget();
		$('.select_fournisseur_widget', this.element).multiChoiceWidget();
		$('.marque_vehicule_widget', this.element).multiChoiceWidget();
		$('.modele_vehicule_widget', this.element).multiChoiceWidget();
		$('.statut_widget', this.element).multiChoiceWidget();
		$('.services_widget', this.element).multiChoiceWidget();
		$('.section_widget', this.element).multiChoiceWidget();
		$('.trend_widget', this.element).multiChoiceWidget();	
		$('.min_max_widget', this.element).minmaxWidget();
		
		this.element.reporting({ 'caller': this, 'type': this.element.attr('type') });
		
		$('.reporting_first_step .widget:not(.widget_initialized)', this.element).ser_widget({});
		
		var report_str = this.element.attr('report_str');
		hash = parseHash('report['+report_str+']');
		var elem = hash[0];
		var params = elem.getParams();
		this.loadFieldsFromParams(params);
		
		this.options.searchWidget = $('.list_search_widget', this.element);
        if (this.options.searchWidget.size() == 1) {
        	this.options.searchWidget.ser_listSearchWidget();
        	this.options.searchWidget.bind('filters', $.proxy(this.refreshWithFitlters, this));
       		this.options.searchWidget.ser_listSearchWidget('initValues', params);
        }
	},
	refreshWithFitlters: function() {
		this.element.reporting('refresh');
    },
	loadFieldsSearchFromParams: function(params) {
		var container = $('.reporting_first_step .cnt', this.element);
		
		// remove params that not search concerned .
		var searchParams = {};
		for (var key in params) {			
			if (key == 'row' || key == 'col' || key == 'datas') continue;
			searchParams[key] = params[key];
		}
		
		loadFieldsSearchFromParams(searchParams, container);
	},
	loadFieldsFromParams: function(params) {
		var row = params['row'];
		var col = params['col'];
		var datas = params['datas'];
		
		var searchParams = {};
		for (var key in params) {
			if (key == 'row' || key == 'col' || key == 'datas') continue;
			searchParams[key] = params[key];
		}
		
		this.loadFieldsSearchFromParams(searchParams);
		
		this.element.reporting('loadFieldsFromParams', row, col, datas);
	},
	getSearchStrForType: function(type) {
		//  ignore type.
		//var container = $('.reporting_first_step .cnt', this.element);
		//var search_str = buildSearchStr(container);
		var search_str = this.element.attr('report_str');
		if (this.options.searchWidget && this.options.searchWidget.size() == 1) {
			hash = parseHash('report['+search_str+']');
			var elem = hash[0];
			var params = elem.getParams();
			extra_values = this.options.searchWidget.ser_listSearchWidget('getVal', true);
			if (extra_values) {
				for (var key in extra_values) {
					params[key] = extra_values[key];
				}
				
				var elems = [];
				for (var key in params) {
					elems.push(key+'='+params[key]);
				}
				
				search_str = implode(',', elems);
			}
		}
		return search_str;
	},
	runIfNeverRunned: function() {
		this.element.reporting('runIfNeverRunned');
	},
	options: {
		searchWidget: null
	}
});

$.widget('seriel.reporting', {
	_create: function() {
		this.options.report_content = $('.reporting_fourth_step > .cnt', this.element);
		
		this.options.report_content.addClass('scrollable');
		
		this.options.ligne_select = $('.ligne_select', this.element);
		this.options.col_select = $('.col_select', this.element);
		
		this.options.ligne_options_block = $('.options_line', this.element);
		this.options.col_options_block = $('.options_col', this.element);
		
		this.options.ligne_select.bind('change', $.proxy(this.ligneChanged, this));
		this.options.col_select.bind('change', $.proxy(this.colChanged, this));
		
		this.options.datas_selector = $('.select_datas_report_widget', this.element);
		this.options.datas_selector.multiChoiceWidget();
		
		if (this.element.hasClass('rc_article')) this.options.type = 'article';
		else {
			console.log('DIDN\'T FIND REPORTING TYPE : '+this.element.attr('class'));
		}
		
		
		this.options.refresh_button = $('.reporting_button', this.element);
		this.options.refresh_button.bind('click', $.proxy(this.refreshClicked, this));
		
		$('.right_buttons .action', this.element).bind('click', $.proxy(this.actionClicked, this));
		
		this.ligneChanged();
		this.colChanged();
	},
	initTable: function() {
		if (this.options.table) {
			var id = 'report_content_'+this.options.table.attr('id');
			this.options.report_content.attr('id', id);
			
			// count number of column.
			var qte_total = $('thead > tr:last-child > .total', this.options.table).size();

			this.options.table.tablesorter({
	    		showProcessing: true,
	    		emptyTo: 'top',
	    		
	    		widgets: ['resizable'],
	            widgetOptions: {
	            	resizable: false,
	            	resizable_addLastColumn: true
	            }
	        });
			
			this.options.table.tableHeadFixer({'head' : true, 'foot' : true, 'left': 1, 'right': qte_total});
			
			$('tfoot > tr > th:first-child', this.options.table).css('z-index', 3);
		}
	},
	loadFieldsFromParams: function(row, col, datas) {
		var row_sub = null;
		var col_sub = null;
		
		if (row) {
			var index = strpos(row, '(');
			if (index) {
				row_sub = substr(row, index+1, strlen(row) - (index+1) - 1);
				row = substr(row, 0, index);
			}			
		}

		if (col) {
			var index = strpos(col, '(');
			if (index) {
				col_sub = substr(col, index+1, strlen(col) - (index+1) - 1);
				col = substr(col, 0, index);
			}			
		}
		
		this.options.ligne_select.val(row);
		this.options.ligne_select.trigger('change');
		this.options.col_select.val(col);
		this.options.col_select.trigger('change');
		
		if (row_sub) {
			this.setLigneOptVal(row_sub);
		}
		if (col_sub) {
			this.setColOptVal(col_sub);
		}
		
		this.options.datas_selector.multiChoiceWidget('setVal', datas);
	},
	ligneChanged: function() {
		$(' > .option_block.visible', this.options.ligne_options_block).removeClass('visible');
		
		var newLineOptionBlock = $(' > .option_block[code="'+this.options.ligne_select.val()+'"]', this.options.ligne_options_block);
		newLineOptionBlock.addClass('visible');
		
		// Do we have a widget to activate ?
		var widget_name = $('.render_option_widget', newLineOptionBlock).html();
		if (widget_name) {
			// it is initialized ?
			
			if (!newLineOptionBlock.hasClass('render_option_widget')) {
				eval('newLineOptionBlock.' + widget_name + '();');
			}
		}
	},
	colChanged: function() {
		$(' > .option_block.visible', this.options.col_options_block).removeClass('visible');
		
		var newColOptionBlock = $(' > .option_block[code="'+this.options.col_select.val()+'"]', this.options.col_options_block);
		newColOptionBlock.addClass('visible');
		
		// Do we have a widget to activate ?
		var widget_name = $('.render_option_widget', newColOptionBlock).html();
		if (widget_name) {
			// it is initialized ?
			
			if (!newColOptionBlock.hasClass('render_option_widget')) {
				eval('newColOptionBlock.' + widget_name + '();');
			}
		}
	},
	getLigneOptVal: function() {
		// get option block selected.
		var block = $(' > .option_block.visible', this.options.ligne_options_block);
		if (block.size() == 1) {
			var widget = getReportingOptionWidgetFromElem(block);
			if (widget) {
				return widget.getVal();
			}
		}
		
		return null;
	},
	setLigneOptVal: function(val) {
		// get option block selected.
		var block = $(' > .option_block.visible', this.options.ligne_options_block);
		if (block.size() == 1) {
			var widget = getReportingOptionWidgetFromElem(block);
			if (widget) {
				return widget.setVal(val);
			}
		}
		
	},
	getColOptVal: function() {
		// get option block selected.
		var block = $(' > .option_block.visible', this.options.col_options_block);
		if (block.size() == 1) {
			var widget = getReportingOptionWidgetFromElem(block);
			if (widget) {
				return widget.getVal();
			}
		}
		
		return null;
	},
	setColOptVal: function(val) {
		// get option block selected.
		var block = $(' > .option_block.visible', this.options.col_options_block);
		if (block.size() == 1) {
			var widget = getReportingOptionWidgetFromElem(block);
			if (widget) {
				widget.setVal(val);
			}
		}
	},
	buildReportStr: function() {
		var search_str = "";
		if (this.options.caller) search_str = this.options.caller.getSearchStrForType(this.options.type);
		else search_str = this.getReportingNavigator().getSearchStrForType(this.options.type);
		
		var ligne = this.options.ligne_select.val();
		var ligneOptVal = this.getLigneOptVal();
		
		var col = this.options.col_select.val();
		var colOptVal = this.getColOptVal();
		
		var datas = this.options.datas_selector.multiChoiceWidget('getVal');
		
		var report_str = search_str;
		if (report_str) report_str += ',';
		report_str += this.options.ligne_p_name+'='+ligne+(ligneOptVal?'('+ligneOptVal+')':'')+','+this.options.col_p_name+'='+col+(colOptVal?'('+colOptVal+')':'')+','+this.options.data_p_name+'='+datas;
		
		return report_str;
	},
	actionClicked: function(event) {
		var target = $(event.currentTarget);
		
		var report_str = this.buildReportStr();

		if (target.hasClass('report_save')) {
			options = { 'post': { 'report': report_str }, 'width': 1000, 'height': 600};
			options['width'] = 1400;

			this.getReportingNavigator().openModal('Enregistrer le reporting', getUrlPrefix() + '/reporting/save/'+this.options.type, options);
		} else if (target.hasClass('report_load')) {
			this.getReportingNavigator().openModal('Charger un reporting', getUrlPrefix() + '/reporting/load/'+this.options.type);
		} else if (target.hasClass('report_config')) {
			this.getReportingNavigator().openModal('Gérer mes reportings', getUrlPrefix() + '/reporting/config/'+this.options.type);
		} else if (target.hasClass('report_export')) {
			this.exportExcel();
		}
	},
	exportExcel: function() {
		this.setLoading();
    	
    	var type = this.options.type;
    	
    	// get data table and sent (post).
    	var table = $('.report_table', this.element);
    	var thead = $(' > thead', table);
    	var thead_lines = $('> tr', thead);
    	
    	var hl1 = $(thead_lines.get(0));
    	var hl2 = $(thead_lines.get(1));
    	
    	var line1_head = [];
    	var line2_head = [];
    	
    	var lines = [];

    	var ths_1 = $(' > th', hl1);
    	for (var i = 0; i < ths_1.size(); i++) {
    		var th = $(ths_1.get(i));
    		line1_head[i] = html_entity_decode(strip_tags(th.html()));
    	}
    	
    	var tds_2 = $(' > td', hl2);
    	for (var i = 1; i < tds_2.size(); i++) {
    		var td = $(tds_2.get(i));
    		var val = html_entity_decode(strip_tags(td.html()));
    		var type = td.attr('val');
    		line2_head[i-1] = { 'type': type, 'val': val };
    	}
    	
    	var lines = [];
    	
    	// get All rows.
    	var tbody = $(' > tbody', table);
    	var tbody_lines = $('> tr', tbody);
    	
    	for (var i = 0; i < tbody_lines.size(); i++) {
    		var line = $(tbody_lines.get(i));
    		var th = $(' > th', line);
    		
    		var line_data = {};
    		line_data['titre'] = html_entity_decode(th.html());
    		line_data['values'] = [];
    		var tds = $(' > td', line);
    		for (var j = 0; j < tds.size(); j++) {
    			var td = $(tds.get(j));
    			var td_val = html_entity_decode(td.html());
    			line_data['values'][j] = td_val;
    		}
    		
    		lines[i] = line_data;
    	}
    	
    	var footer = {};
    	var tfoot = $(' > tfoot', table);
    	var tfoot_th = $(' > tr > th', tfoot);
    	
    	footer['titre'] = html_entity_decode(tfoot_th.html());
    	footer['values'] = [];
    	
    	var tfoot_tds = $(' > tr > td', tfoot);
    	for (var i = 0; i < tfoot_tds.size(); i++) {
    		var td = $(tfoot_tds.get(i));
    		footer['values'][i] = html_entity_decode(td.html());
    	}
    	
    	var datas = {};
    	
    	datas['type'] = this.options.type;
    	
    	for (var i = 0; i < count(line1_head); i++) {
    		var value = line1_head[i];
    		datas['line1_head['+i+']'] = value;
    	}
    	for (var i = 0; i < count(line2_head); i++) {
    		var value = line2_head[i];
    		datas['line2_head['+i+'][type]'] = value['type'];
    		datas['line2_head['+i+'][val]'] = value['val'];
    	}
    	
    	for (var i = 0; i < count(lines); i++) {
    		var line = lines[i];
    		datas['lines['+i+'][title]'] = line['titre'];
    		for (var j = 0; j < count(line['values']); j++) {
    			var value = line['values'][j];
    			datas['lines['+i+'][values]['+j+']'] = value;
    		}
    	}
    	
    	datas['footer[title]'] = footer['titre'];
    	for (var i = 0; i < count(footer['values']); i++) {
    		var value = footer['values'][i];
    		datas['footer[values]['+i+']'] = value;
    	}
    	
    	$.post(getUrlPrefix() + '/reporting/export', datas, $.proxy(this.exported, this));
	},
	exported: function(result) {
		var res = $(result);
    	if (res.hasClass('success')) {
    		var filename = $('.filename', res).html();
    		downloadURL(filename);
    	}
    	
    	this.hideLoading();
	},
	runIfNeverRunned: function() {
		if (!this.options.first_loaded) this.refresh();
	},
	refreshClicked: function() {
		this.refresh();
	},
	refresh: function() {
		var report_str = this.buildReportStr();
		
		if (this.options.caller) {
			
			hash = parseHash('report['+report_str+']');

			var elem = hash[0];
				
			var params = elem.getParams();
			
			this.options.first_loaded = true;
			this.run(params);
		} else {
			// verify if url is equals, run manual search
			var currentHash = getCleanHash();
			var newHash = 'reporting/'+this.options.type+'[' + report_str + ']';
			if (newHash == currentHash) {
				this.forceRun(newHash);
				return;
			}
			hc().setHash(newHash);			
		}
	},
	setLoading: function() {
		var loadingDiv = $('.loading', this.options.report_content.parent());
		if (loadingDiv.size() == 0) {
			// Create loading.
			loadingDiv = $('<div class="loading"></div>');
			loadingDiv.appendTo(this.options.report_content.parent());
		}
		
		loadingDiv.removeClass('hidden');
	},
	hideLoading: function() {
		$('.loading', this.options.report_content.parent()).addClass('hidden');
	},
	getReportingNavigator: function() {
		if (!this.options.reporting_navigator) {
			this.options.reporting_navigator = pnv(this.element);
		}
		
		return this.options.reporting_navigator;
	},
	forceRun: function(hash) {
		$('ul#main_menu > li.reporting > a').attr('href', '#' + hash);
		
		var serhash = parseHash(hash);

		if (serhash && count(serhash) > 1) {
			var elem = serhash[1];
			
			var type = elem.getLabel();
			
			var params = elem.getParams();
			
			if (count(params) > 0) {
				this.run(params);
			}
		}
	},
	run: function(params) {
		// Let's go.
		this.setLoading();
		
		$.ajax({ 'url': getUrlPrefix() + '/reporting/'+this.options.type+'/render', 'method': 'POST', 'data': params, 'success': $.proxy(this.runSuccess, this) });
	},
	runSuccess: function(data, textStatus, jqXHR) {
		this.options.report_content.html(data);
		
		this.bindContent();
		
		this.options.table = $(' > table', this.options.report_content);
		this.initTable();
		
		this.hideLoading();
	},
	bindContent: function() {
		$('td[ids]', this.options.report_content).bind('click', $.proxy(this.caseClicked, this));
	},
	caseClicked: function(event) {
		var target = $(event.currentTarget);
		var ids = target.attr('ids');
		
		var type = null;
		if (strtolower(this.options.type) == 'article') type = 'ZombieBundle\\Entity\\News\\Article';
		else type = 'ZombieBundle\\Entity\\News\\'+ucfirst(this.options.type);
		
		this.getReportingNavigator().openModal('Liste des éléments', getUrlPrefix() + '/liste/fromIds', { 'post': { 'type': type, 'ids': ids }, 'width': 1200, 'height': 600});
	},
	options: {
		caller: null,
		
		ligne_p_name: 'row',
		col_p_name: 'col',
		data_p_name: 'datas',
		
		ligne_options_block: null,
		col_options_block: null,
		
		datas_selector: null,
		
		type: null,
		refresh_button: null,
		report_content: null,
		reporting_navigator: null,
		ligne_select: null,
		col_select: null,
		
		table: null,
		
		first_loaded: false
	}
});

function getReportingOptionWidgetFromElem(elem) {
	if (!elem) return null;
	if (!elem.hasClass('seriel_reporting_option')) return null;
	
	var className = elem.attr('reporting_option');
	if (className) {
		var navObj = elem.data(className);
		if (navObj) return navObj;
	}
	
	return null;
}

$.widget('seriel.reporting_option', {
	_create: function() {
		this.element.addClass('seriel_reporting_option');
		this.element.attr('reporting_option', this.getReportingOptionClassName());
	},
	getVal: function() {
		return null;
	},
	getReportingOptionClassName: function() {
		var data = this.element.data();
		for (var key in data) {
			var obj = data[key];
			try {
				var res = obj.isReportingOption();
				if (res == true) return key;
			} catch (ex) {
				
			}
		}
		
		return null;
	},
	isReportingOption: function() {
		return true;
	},
	options: {
		
	}
});

$.widget('seriel.date_heure_reporting_option', $.seriel.reporting_option, {
	_create: function() {
		this._super();
		this.options.widget = $('.date_heure_reporting_option_widget', this.element);
		this.options.widget.multiChoiceWidget();
	},
	getVal: function() {
		return this.options.widget.multiChoiceWidget('getVal');
	},
	setVal: function(val) {
		this.options.widget.multiChoiceWidget('setVal', val);
	},
	options: {
		widget: null
	}
});

$.widget('seriel.percent_reporting_option', $.seriel.reporting_option, {
	_create: function() {
		this._super();
		this.options.widget = $('.percent_reporting_option_widget', this.element);
		this.options.widget.multiChoiceWidget();
	},
	getVal: function() {
		return this.options.widget.multiChoiceWidget('getVal');
	},
	setVal: function(val) {
		this.options.widget.multiChoiceWidget('setVal', val);
	},
	options: {
		widget: null
	}
});
$.widget('seriel.duration_reporting_option', $.seriel.reporting_option, {
	_create: function() {
		this._super();
		this.options.widget = $('.duration_reporting_option_widget', this.element);
		this.options.widget.multiChoiceWidget();
	},
	getVal: function() {
		return this.options.widget.multiChoiceWidget('getVal');
	},
	setVal: function(val) {
		this.options.widget.multiChoiceWidget('setVal', val);
	},
	options: {
		widget: null
	}
});