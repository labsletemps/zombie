
$.widget('seriel.footer', {
    _create: function () {
    	this.options.counter_di = $('.counter_di', this.element);
    	this.options.counter_dd = $('.counter_dd', this.element);
    	this.options.counter_comp = $('.counter_comp', this.element);
    	this.options.counter_email = $('.counter_email', this.element);
    	this.options.counter_fax = $('.counter_fax', this.element);
    	
        this.options.elem_di = $(' > .qte', this.options.counter_di);
        this.options.elem_dd = $(' > .qte', this.options.counter_dd);
        this.options.elem_comp = $(' > .qte', this.options.counter_comp);
        this.options.elem_email = $(' > .qte', this.options.counter_email);
        this.options.elem_fax = $(' > .qte', this.options.counter_fax);

        this.options.menuFooterOverlay = $('#menu_footer_overlay');
        this.options.menuContainer = $('.menu_container', this.element);
        
        this.options.menuButton = $('.button_menu', this.element);
        
        this.options.menuFooterOverlay.bind('click', $.proxy(this.hideMenu, this));
        this.options.menuButton.bind('click', $.proxy(this.menuButtonClicked, this));
        
        this.options.counter_di.bind('click', $.proxy(this.counterDiClicked, this));
        this.options.counter_dd.bind('click', $.proxy(this.counterDdClicked, this));
        this.options.counter_comp.bind('click', $.proxy(this.counterCompClicked, this));
        this.options.counter_email.bind('click', $.proxy(this.counterEmailClicked, this));
        this.options.counter_fax.bind('click', $.proxy(this.counterFaxClicked, this));
        
        $('.head .btn', this.options.menuContainer).bind('click', $.proxy(this.btnMenuClicked, this));
        
        this.initEmails();
        
        this.checkMenuHeights();
        
        this.refreshCounters();
        
        $('.footer_menu > .cnt > ul > li', this.element).bind('click', $.proxy(this.elemClicked, this));
    },
    initEmails: function() {
    	var emails = $('.footer_menu > .cnt > ul > li.email > span', this.element);
    	$('.body', emails).remove(); // remove body of mail
		emails.addClass('tuile tuile_email');
    },
    elemClicked: function(event) {
    	var target = $(event.currentTarget);
    	var hash = target.attr('hash');
    	document.location.href = "#"+hash;
    	
    	if ((!target.hasClass('demandesupp')) && (!target.hasClass('email'))) target.addClass('disappear');
    	
    	this.refreshCounters();
    	
    	this.hideMenu();
    },
    refreshCounters: function() {
    	var numDis = $('li.demandeintervention:not(.disappear)', this.options.menuContainer).size();
    	var numDds = $('li.demandedevis:not(.disappear)', this.options.menuContainer).size();
    	var numDss = $('li.demandesupp:not(.disappear)', this.options.menuContainer).size();
    	var numEmails = $('li.email:not(.disappear)', this.options.menuContainer).size();
    	var numFax = $('li.fax:not(.disappear)', this.options.menuContainer).size();
    	
    	this.options.elem_di.html(numDis);
    	this.options.elem_dd.html(numDds);
    	this.options.elem_comp.html(numDss);
    	this.options.elem_email.html(numEmails);
    	this.options.elem_fax.html(numFax);
    	
    	if (numDis == 0) {
    		this.options.elem_di.stop(true);
    		this.clean_di();
    		this.options.elem_di.attr('class', 'qte empty');
    	}
    	else this.options.elem_di.removeClass('empty');
    	
    	if (numDds == 0) {
    		this.options.elem_dd.stop(true);
    		this.clean_dd();
    		this.options.elem_dd.attr('class', 'qte empty');
    	}
    	else this.options.elem_dd.removeClass('empty');
    	
    	if (numDss == 0) {
    		this.options.elem_comp.stop(true);
    		this.clean_comp();
    		this.options.elem_comp.attr('class', 'qte empty');
    	}
    	else this.options.elem_comp.removeClass('empty');
    	
    	if (numEmails == 0) {
    		this.options.elem_email.stop(true);
    		this.clean_email();
    		this.options.elem_email.attr('class', 'qte empty');
    	}
    	else this.options.elem_email.removeClass('empty');
    	
    	if (numFax == 0) {
    		this.options.elem_fax.stop(true);
    		this.clean_fax();
    		this.options.elem_fax.attr('class', 'qte empty');
    	}
    	else this.options.elem_fax.removeClass('empty');
    },
    addWarningsFromListIds: function(list_ids) {
    	if (this.options.refresh_warnings_ids === null) this.options.refresh_warnings_ids = {};
    	if (list_ids) {
    		for (var i = 0; i < count(list_ids); i++) {
    			var id = list_ids[i];
    			this.options.refresh_warnings_ids[id] = id;
    		}
    	}
    	
    	// create delay random  for server.
    	if (this.options.last_rand_warning_delay === null && (!this.options.warnings_refresh_timeoutvar)) {
    		this.options.last_rand_warning_delay = rand(0,15);
    		this.options.warnings_refresh_timeoutvar = setTimeout($.proxy(this.addWarningsFromListIds, this), this.options.last_rand_warning_delay * 500);
    		console.log('rand warning time : '+(this.options.last_rand_warning_delay / 2)+' s');
    		return;
    	}
    	
    	if (this.options.last_rand_warning_delay !== null) {
    		this.options.warnings_refresh_timeoutvar = null;
    	}
    	this.options.last_rand_warning_delay = null;
    	
    	var currTime = time();
		var d = new Date();
		var currMillis = d.getMilliseconds() / 1000;
		if (this.options.lastWarningsRefreshTime) {
			var diffMillis = currMillis - this.options.lastWarningsRefreshTimeMillis;
			var diff = currTime - this.options.lastWarningsRefreshTime;
			diff += diffMillis;
			if (diff < this.options.refresh_warnings_delay) {
				if (!this.options.warnings_refresh_timeoutvar) {
					this.options.warnings_refresh_timeoutvar = setTimeout($.proxy(this.addWarningsFromListIds, this), (this.options.refresh_warnings_delay - diff + 1) * 1000);
				}
				
				return;
			}
		}
		
		this.options.warnings_refresh_timeoutvar = null;
		
		if (this.options.refresh_warnings_ids === null || count(this.options.refresh_warnings_ids) == 0) return;
		
		this.options.lastWarningsRefreshTime = time();
		this.options.lastWarningsRefreshTimeMillis = currMillis;
    	
    	var data = {};
    	list_ids = array_keys(this.options.refresh_warnings_ids);
		for (var i = 0; i < count(list_ids); i++) {
			var id = list_ids[i];
			data['warnings['+i+']'] = id;
		}
		
		$.post(getUrlPrefix()+'/datas/getWarnings', data, $.proxy(this.ajaxWarningsReceived, this));
		
		this.options.refresh_warnings_ids = {};
    },
    ajaxWarningsReceived: function(response) {
    	var res = $(response);
    	if (res.hasClass('success')) {
    		// Ok, get response.
    		var dis = $(' > .tuile_di', res);
    		var dds = $(' > .tuile_dd', res);
    		var dss = $(' > .tuile_ds', res);
    		var emails = $(' > .email', res);
    		
    		if (dis.size() > 0) this.addDis(dis);
    		if (dds.size() > 0) this.addDds(dds);
    		if (dss.size() > 0) this.addComp(dss);
    		if (emails.size() > 0) {
    			if (emailFaxNav) {
    				emailFaxNav.newEmailsReceived(response);
    			} else {
    				this.addEmails(emails);    				
    			}
    		}
    	}
    	
    	// elements to remove.
    	var to_remove = $('.to_hide > span', response);
    	for (var i = 0; i < to_remove.size(); i++) {
    		var doi = $(to_remove.get(i)).html();
    		var splitted = explode('-', doi);
    		if (count(splitted) == 2) {
    			var type = splitted[0];
    			var id = splitted[1];
    			
    			if (type == 'DemandeIntervention') this.removeDi(id);
    			else if (type == 'DemandeDevis') this.removeDd(id);
    			else if (type == 'DemandeSupp') this.removeComp(id);
    			else if (type == 'Email') {
    				this.removeEmail(id);
    				if (emailFaxNav) {
    					emailFaxNav.hideEmail(id);
    				}
    			}
    		}
    	}
    },
    addDisFromListIds: function(list_ids) {
    	var data = {};
		for (var i = 0; i < count(list_ids); i++) {
			var id = list_ids[i];
			data['ids['+i+']'] = id;
		}
		
		$.post(getUrlPrefix()+'/datas/getDis', data, $.proxy(this.ajaxDisReceived, this));
    },
    ajaxDisReceived: function(response) {
    	var res = $(response);
    	if (res.hasClass('success')) {
    		// Ok, get response.
    		var dis = $(' > .tuile_di', res);
    		
    		if (dis.size() > 0) this.addDis(dis);
    	}
    },
    addDis: function(dis) {
    	var added = false;
    	
    	if (dis && dis.size() > 0) {
        	for (var i = dis.size() - 1; i >= 0; i--) {
    			var di = $(dis.get(i));
    			var di_id = di.attr('di_id');
    			
    			// Do we have it
    			var old_di = $('li.demandeintervention[di_id='+di_id+']', this.options.menuContainer);
    			
    			// insert message.
    			var clone = di.clone();
    			clone.addClass('tuile tuile_di');
    			
    			var ticket_id = $('.di_informations_infos_1 > .num', clone).html();
    			var li = $('<li hash="ticket['+ticket_id+']/di['+di_id+']" class="invisible demandeintervention" di_id="'+di_id+'"></li>');
    			li.append(clone);
    			
    			li.bind('click', $.proxy(this.elemClicked, this));
    			
    			if (old_di.size() > 0) {
    				old_di.replaceWith(li);
    				if (old_di.hasClass('disappear')) added = true;
    			} else {
    				$('.footer_menu > .cnt > ul', this.element).append(li);
    				added = true;
    			}
    			
    			li.removeClass('invisible');
    		}
        	
        	this.refreshCounters();
        	if (added) this.highlightDis();
    	}
    },
    removeDi: function(di_id) {
    	$('.footer_menu > .cnt > ul > li[di_id='+di_id+']', this.element).addClass('disappear');
    	this.refreshCounters();
    },
    highlightDis: function () {
        this.options.elem_di.stop(true);
        this.clean_di();
        this.options.elem_di.switchClass('', 'big', 200, 'easeInQuad', $.proxy(function () {
            this.options.elem_di.switchClass('big', 'small', 200, 'easeInQuad', $.proxy(function () {
                this.options.elem_di.switchClass('small', 'alert', 100, 'easeInQuad', $.proxy(function () {
                    this.options.elem_di.switchClass('alert', '', 7000, 'linear')
                }, this))
            }, this))
        }, this));
    },
    clean_di: function () {
        this.options.elem_di.attr('style', '');
        this.options.elem_di.removeClass('big small red');
    },
    addDdsFromListIds: function(list_ids) {
    	var data = {};
		for (var i = 0; i < count(list_ids); i++) {
			var id = list_ids[i];
			data['ids['+i+']'] = id;
		}
		
		$.post(getUrlPrefix()+'/datas/getDds', data, $.proxy(this.ajaxDdsReceived, this));
    },
    ajaxDdsReceived: function(response) {
    	var res = $(response);
    	if (res.hasClass('success')) {
    		// Ok, get response.
    		var dds = $(' > .tuile_dd', res);
    		
    		if (dds.size() > 0) this.addDds(dds);
    	}
    },
    addDds: function(dds) {
    	var added = false;
    	if (dds && dds.size() > 0) {
        	for (var i = dds.size() - 1; i >= 0; i--) {
    			var dd = $(dds.get(i));
    			var dd_id = dd.attr('dd_id');
    			
    			// Do we have it
    			var old_dd = $('li.demandedevis[dd_id='+dd_id+']', this.options.menuContainer);
    			
    			// insert message.
    			var clone = dd.clone();
    			clone.addClass('tuile tuile_dd');
    			
    			var ticket_id = $('.dd_informations_infos_1 > .num', clone).html();
    			var li = $('<li hash="ticket['+ticket_id+']/dd['+dd_id+']" class="invisible demandedevis" dd_id="'+dd_id+'"></li>');
    			li.append(clone);
    			
    			li.bind('click', $.proxy(this.elemClicked, this));

    			if (old_dd.size() > 0) {
    				old_dd.replaceWith(li);
    				if (old_dd.hasClass('disappear')) added = true;
    			} else {
    				$('.footer_menu > .cnt > ul', this.element).append(li);
    				added = true;
    			}
    			
    			li.removeClass('invisible');
    		}
        	
        	this.refreshCounters();
        	if (added) this.highlightDds();
    	}
    },
    removeDd: function(dd_id) {
    	$('.footer_menu > .cnt > ul > li[dd_id='+dd_id+']', this.element).addClass('disappear');
    	this.refreshCounters();
    },
    highlightDds: function () {
        this.options.elem_dd.stop(true);
        this.clean_dd();
        this.options.elem_dd.switchClass('', 'big', 200, 'easeInQuad', $.proxy(function () {
            this.options.elem_dd.switchClass('big', 'small', 200, 'easeInQuad', $.proxy(function () {
                this.options.elem_dd.switchClass('small', 'alert', 100, 'easeInQuad', $.proxy(function () {
                    this.options.elem_dd.switchClass('alert', '', 7000, 'linear')
                }, this))
            }, this))
        }, this));
    },
    clean_dd: function () {
        this.options.elem_dd.attr('style', '');
        this.options.elem_dd.removeClass('big small red');
    },
    addComp: function(dss) {
    	var added = false;
    	if (dss && dss.size() > 0) {
        	for (var i = dss.size() - 1; i >= 0; i--) {
    			var ds = $(dss.get(i));
    			var ds_id = ds.attr('ds_id');
    			
    			// Do we have it
    			var old_ds = $('li.demandesupp[ds_id='+ds_id+']', this.options.menuContainer);
    			
    			// insert message.
    			var clone = ds.clone();
    			clone.addClass('tuile tuile_ds');
    			
    			var link = ds.attr('hash');
    			var li = $('<li hash="'+link+'" class="invisible demandesupp" ds_id="'+ds_id+'"></li>');
    			li.append(clone);
    			
    			li.bind('click', $.proxy(this.elemClicked, this));

    			if (old_ds.size() > 0) {
    				old_ds.replaceWith(li);
    				if (old_ds.hasClass('disappear')) added = true;
    			} else {
    				$('.footer_menu > .cnt > ul', this.element).append(li);
    				added = true;
    			}
    			
    			li.removeClass('invisible');
    		}
        	
        	this.refreshCounters();
        	if (added) this.highlightComp();
    	}
    },
    removeComp: function(ds_id) {
    	$('.footer_menu > .cnt > ul > li[ds_id='+ds_id+']', this.element).addClass('disappear');
    	this.refreshCounters();
    },
    highlightComp: function () {
        this.options.elem_ds.stop(true);
        this.clean_comp();
        this.options.elem_ds.switchClass('', 'big', 200, 'easeInQuad', $.proxy(function () {
            this.options.elem_ds.switchClass('big', 'small', 200, 'easeInQuad', $.proxy(function () {
                this.options.elem_ds.switchClass('small', 'alert', 100, 'easeInQuad', $.proxy(function () {
                    this.options.elem_ds.switchClass('alert', '', 7000, 'linear')
                }, this))
            }, this))
        }, this));
    },
    clean_comp: function () {
        this.options.elem_comp.attr('style', '');
        this.options.elem_comp.removeClass('big small red');
    },
    addEmails: function(emails) {
    	if (emails && (emails.size() > 0)) {
        	for (var i = emails.size() - 1; i >= 0; i--) {
    			var email = $(emails.get(i));
    			var email_id = email.attr('email_id');
    			
    			// Do we have it
    			var old_email = $('li.email[email_id='+email_id+']', this.options.menuContainer);
    			if (old_email.size() != 0) continue;
    			
    			// Insert message.
    			var clone = email.clone();
    			$('.body', clone).remove(); // remove body of mail
    			clone.addClass('tuile tuile_email');
    			
    			var li = $('<li hash="email['+email_id+']" class="invisible email" email_id="'+email_id+'"></li>');
    			li.append(clone);
    			
    			li.bind('click', $.proxy(this.elemClicked, this));
    			
    			$('.footer_menu > .cnt > ul', this.element).append(li);
    			
    			li.removeClass('invisible');
    		}
        	
        	this.refreshCounters();
        	this.highlightEmails();    		
    	}
    },
    removeEmail: function(email_id) {
    	$('.footer_menu > .cnt > ul > li[email_id='+email_id+']', this.element).addClass('disappear');
    	this.refreshCounters();
    },
    highlightEmails: function () {
        this.options.elem_email.stop(true);
        this.clean_email();
        this.options.elem_email.switchClass('', 'big', 200, 'easeInQuad', $.proxy(function () {
            this.options.elem_email.switchClass('big', 'small', 200, 'easeInQuad', $.proxy(function () {
                this.options.elem_email.switchClass('small', 'alert', 100, 'easeInQuad', $.proxy(function () {
                    this.options.elem_email.switchClass('alert', '', 7000, 'linear')
                }, this))
            }, this))
        }, this));
    },
    clean_email: function () {
        this.options.elem_email.attr('style', '');
        this.options.elem_email.removeClass('big small red');
    },
    addFax: function(faxs) {
    	if (faxs && faxs.size() > 0) {
        	for (var i = faxs.size() - 1; i >= 0; i--) {
    			var fax = $(faxs.get(i));
    			var fax_id = fax.attr('fax_id');
    			
    			// Do we have it
    			var old_fax = $('li.fax[fax_id='+fax_id+']', this.options.menuContainer);
    			if (old_fax.size() != 0) continue;
    			
    			// insert message.
    			var clone = fax.clone();
    			clone.addClass('tuile tuile_fax');
    			
    			var li = $('<li hash="email[fax:'+fax_id+']" class="invisible fax" fax_id="'+fax_id+'"></li>');
    			li.append(clone);
    			
    			li.bind('click', $.proxy(this.elemClicked, this));
    			
    			$('.footer_menu > .cnt > ul', this.element).append(li);
    			
    			li.removeClass('invisible');
    		}
        	
        	this.refreshCounters();
        	this.highlightFax();
    	}
    },
    removeFax: function(fax_id) {
    	$('.footer_menu > .cnt > ul > li[fax_id='+fax_id+']', this.element).addClass('disappear');
    	this.refreshCounters();
    },
    highlightFax: function () {
        var lastVal = intval(this.options.elem_fax.html());
        var val = lastVal + 1;
        this.options.elem_fax.html(val);
        this.options.elem_fax.stop(true);
        this.clean_fax();
        this.options.elem_fax.switchClass('', 'big', 200, 'easeInQuad', $.proxy(function () {
            this.options.elem_fax.switchClass('big', 'small', 200, 'easeInQuad', $.proxy(function () {
                this.options.elem_fax.switchClass('small', 'alert', 100, 'easeInQuad', $.proxy(function () {
                    this.options.elem_fax.switchClass('alert', '', 7000, 'linear')
                }, this))
            }, this))
        }, this));
    },
    clean_fax: function () {
        this.options.elem_fax.attr('style', '');
        this.options.elem_fax.removeClass('big small red');
    },
    menuButtonClicked: function() {
    	this.options.menuButton.toggleClass('selected');
    	
    	if (this.options.menuButton.hasClass('selected')) {
    		this.showMenu();
    	} else {
    		this.hideMenu();
    	}
    },
    getMenuHeight: function() {
    	return intval($(document).height() / 1.3);
    },
    checkMenuHeights: function() {
    	//get height of menu.
    	var head = $('.head', this.options.menuContainer);
    	var height = head.height() + intval(head.css('padding-top')) + intval(head.css('padding-bottom'));
    	
    	var cnt = $('.footer_menu > .cnt', this.options.menuContainer);
    	cnt.css('top', height+'px');
    },
    showMenu: function() {
    	this.options.menuContainer.css('height', this.getMenuHeight()+'px');
    	this.options.menuFooterOverlay.addClass('visible');
    	
    	if (!this.options.menuButton.hasClass('selected')) this.options.menuButton.addClass('selected');
    },
    hideMenu: function() {
    	this.options.menuContainer.css('height', '0px');
    	this.options.menuFooterOverlay.removeClass('visible');
    	
    	if (this.options.menuButton.hasClass('selected')) this.options.menuButton.removeClass('selected');
    },
    counterDiClicked: function() {
    	this.onlyDi();
    	this.showMenu();
    },
    counterDdClicked: function() {
    	this.onlyDd();
    	this.showMenu();
    },
    counterCompClicked: function() {
    	this.onlyComp();
    	this.showMenu();
    },
    counterEmailClicked: function() {
    	this.onlyEmail();
    	this.showMenu();
    },
    counterFaxClicked: function() {
    	this.onlyFax();
    	this.showMenu();
    },
    btnMenuClicked: function(event) {
    	var target = $(event.currentTarget);
    	target.toggleClass('selected');
    	
    	if (target.hasClass('btn_di')) {
    		if (target.hasClass('selected')) $('.footer_menu > .cnt', this.options.menuContainer).addClass('di_visible');
    		else $('.footer_menu > .cnt', this.options.menuContainer).removeClass('di_visible');
    	}
    	if (target.hasClass('btn_dd')) {
    		if (target.hasClass('selected')) $('.footer_menu > .cnt', this.options.menuContainer).addClass('dd_visible');
    		else $('.footer_menu > .cnt', this.options.menuContainer).removeClass('dd_visible');
    	}
    	if (target.hasClass('btn_comp')) {
    		if (target.hasClass('selected')) $('.footer_menu > .cnt', this.options.menuContainer).addClass('comp_visible');
    		else $('.footer_menu > .cnt', this.options.menuContainer).removeClass('comp_visible');
    	}
    	if (target.hasClass('btn_email')) {
    		if (target.hasClass('selected')) $('.footer_menu > .cnt', this.options.menuContainer).addClass('email_visible');
    		else $('.footer_menu > .cnt', this.options.menuContainer).removeClass('email_visible');
    	}
    	if (target.hasClass('btn_fax')) {
    		if (target.hasClass('selected')) $('.footer_menu > .cnt', this.options.menuContainer).addClass('fax_visible');
    		else $('.footer_menu > .cnt', this.options.menuContainer).removeClass('fax_visible');
    	}
    },
    showDi: function(show) {
    	if (!show) {
    		$('.btn_di', this.options.menuContainer).removeClass('selected');
    		$('.footer_menu > .cnt', this.options.menuContainer).removeClass('di_visible');
    	} else {
    		$('.btn_di', this.options.menuContainer).addClass('selected');
    		$('.footer_menu > .cnt', this.options.menuContainer).addClass('di_visible');
    	}
    },
    showDd: function(show) {
    	if (!show) {
    		$('.btn_dd', this.options.menuContainer).removeClass('selected');
    		$('.footer_menu > .cnt', this.options.menuContainer).removeClass('dd_visible');
    	} else {
    		$('.btn_dd', this.options.menuContainer).addClass('selected');
    		$('.footer_menu > .cnt', this.options.menuContainer).addClass('dd_visible');
    	} 
    },
    showComp: function(show) {
    	if (!show) {
    		$('.btn_comp', this.options.menuContainer).removeClass('selected');
    		$('.footer_menu > .cnt', this.options.menuContainer).removeClass('comp_visible');
    	} else {
    		$('.btn_comp', this.options.menuContainer).addClass('selected');
    		$('.footer_menu > .cnt', this.options.menuContainer).addClass('comp_visible');
    	} 
    },
    showEmail: function(show) {
    	if (!show) {
    		$('.btn_email', this.options.menuContainer).removeClass('selected');
    		$('.footer_menu > .cnt', this.options.menuContainer).removeClass('email_visible');
    	} else {
    		$('.btn_email', this.options.menuContainer).addClass('selected');
    		$('.footer_menu > .cnt', this.options.menuContainer).addClass('email_visible');
    	} 
    },
    showFax: function(show) {
    	if (!show) {
    		$('.btn_fax', this.options.menuContainer).removeClass('selected');
    		$('.footer_menu > .cnt', this.options.menuContainer).removeClass('fax_visible');
    	} else {
    		$('.btn_fax', this.options.menuContainer).addClass('selected');
    		$('.footer_menu > .cnt', this.options.menuContainer).addClass('fax_visible');
    	} 
    },
    onlyDi: function() {
    	this.showDi(true);
    	this.showDd(false);
    	this.showComp(false);
    	this.showEmail(false);
    	this.showFax(false);
    },
    onlyDd: function() {
    	this.showDi(false);
    	this.showDd(true);
    	this.showComp(false);
    	this.showEmail(false);
    	this.showFax(false);
    },
    onlyComp: function() {
    	this.showDi(false);
    	this.showDd(false);
    	this.showComp(true);
    	this.showEmail(false);
    	this.showFax(false);
    },
    onlyEmail: function() {
    	this.showDi(false);
    	this.showDd(false);
    	this.showComp(false);
    	this.showEmail(true);
    	this.showFax(false);
    },
    onlyFax: function() {
    	this.showDi(false);
    	this.showDd(false);
    	this.showComp(false);
    	this.showEmail(false);
    	this.showFax(true);
    },
    newEmailsReceive: function(ids) {
    	if (!ids) return;
    	
    	var qte = count(ids);
    	
    	if (qte > 0) {
    		var data = {};
    		for (var i = 0; i < count(ids); i++) {
    			var id = ids[i];
    			data['ids['+i+']'] = id;
    		}
    		
    		$.post(getUrlPrefix()+'/email/get', data, $.proxy(this.newEmailsReceived, this));
    		return;
    	}
    },
    newEmailsReceived: function(result) {
    	var res = $(result);
    	if (res.hasClass('success')) {
    		// Ok, get response
    		var emails = $(' > .email', res);
    		
    		foot().addEmails(emails);
    	}

    },
    options: {
    	counter_di: null,
    	counter_dd: null,
    	counter_comp: null,
    	counter_email: null,
    	counter_fax: null,
    	
    	elem_di: null,
        elem_dd: null,
        elem_comp: null,
        elem_email: null,
        elem_fax: null,
        
        menuFooterOverlay: null,
        menuContainer: null,
    	menuButton: null,
    	
    	last_rand_warning_delay: null,
    	refresh_warnings_ids: {},
    	refresh_warnings_delay: 8, // 8 seconds
    	lastWarningsRefreshTime: null,
    	lastWarningsRefreshTimeMillis: null
    }
});


var serFoot = null;

function initSerFoot() {
	var dt = $('#footer').data('seriel-footer');
	if (!dt) dt = $('#footer').data('serielFooter');
	
	if (dt) {
		serFoot = dt;
        return;
    }
    $('#footer').footer();

    dt = $('#footer').data('seriel-footer');
	if (!dt) dt = $('#footer').data('serielFooter');
	
	serFoot = dt;
}

function foot() {
    if (!serFoot) {
    	initSerFoot();
    }

    return serFoot;
}