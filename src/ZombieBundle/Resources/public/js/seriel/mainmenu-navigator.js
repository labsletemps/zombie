
$.widget('seriel.mainMenuNavigator', $.seriel.navigator, {
    _create: function () {
        this._super();

        this.options.dockBlock = $('#dock_block');
        this.options.destDiv = $('#main_block');

        nav().declareSubNavigator('accueil', this);
        nav().declareSubNavigator('rechercher', this);
        nav().declareSubNavigator('planning', this);
        nav().declareSubNavigator('reporting', this);
        nav().declareSubNavigator('admin', this);
        
        this.options.user_infos_block = $('#header .current_user');
        
        //  bind button "nouvelle commande"
        this.options.newDemandeButton = $('#main_menu_buttons > .new_demande');
        this.options.newDemandeButton.bind('click', $.proxy(this.newCommande, this));
        
        $(window).resize($.proxy(this.checkMainMenuDisplay, this));
        this.checkMainMenuDisplay();
    },
    checkMainMenuDisplay: function() {
    
    },
    newCommande: function() {
		this.openModal('Nouvelle commande', getUrlPrefix() + '/accueil/commande');
	},
    load: function (dest) {
        this.options.dockBlock.css('z-index', 1);
        this.options.destDiv.css('z-index', 2);

        if (serDockNav)
            serDockNav.element.removeClass('active');
        this.element.addClass('active');
        
        if ($.type(dest) === 'string')
            dest = parseHash(dest);

        var key = null;
        if (dest && count(dest) > 0)
            key = dest[0].getLabel();

        $(' > li.selected:not(.' + key + ')', this.element).removeClass('selected');
        $(' > li.' + key, this.element).addClass('selected');

        if (key == 'rechercher') {
            // Let's get parameters.
            var params = dest[0].getParams();

            var params_str = "";

            for (var name in params) {
                if (params_str == '')
                    params_str = "?";
                else
                    params_str += '&';
                params_str += htmlentities(name) + "=" + htmlentities(params[name]);
            }

            var url = getUrlPrefix()+'/' + key + params_str;
            this.loadContent(url, "rechercher", dest);
            return true;
        }
        this.loadContent(getUrlPrefix()+'/' + key);
        return true;
    },
    contentLoaded: function (res) {

    },
    getDestDiv: function () {
        return this.options.destDiv;
    },
    adaptLink: function(link, data) {
    	var lk = $(' > li.'+link+' > a', this.element);
    	// TODO : cleaner
    	if (link == 'email') {
    		if (data) lk.attr('href', '#email['+data+']');
    		else  lk.attr('href', '#email');
    	}
    },
	openModal : function(title, url, options) {
		this.openModalInsideParentNavigator(title, url, options);
	},
	openModalWithContent : function(title, content, options) {
		this.openModalWithContentInsideParentNavigator(title, content, options);
	},
    options: {
        destDiv: null,
        
        user_infos_block: null,
        newDemandeButton: null
    }
});

function initMainMenuNavigator() {
    var mainMenu = $('#main_menu');
    if (mainMenu.size() == 1) {
    	var dt = mainMenu.data('seriel-mainMenuNavigator');
    	if (!dt) dt = mainMenu.data('serielMainMenuNavigator');
    	if (dt) {
    		serMainMenuNav = dt;
            return;
        }

        mainMenu.mainMenuNavigator();

        var dt = mainMenu.data('seriel-mainMenuNavigator');
    	if (!dt) dt = mainMenu.data('serielMainMenuNavigator');
    	
    	serMainMenuNav = dt;
    }
}

function MMnav() {
	return serMainMenuNav;
}