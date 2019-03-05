$.widget('seriel.articleNav', $.seriel.navigator, {
    _create: function () {
        this._super();
        
        var westBlock = $(' > .menu_left', this.element);
        var westWidth = westBlock.attr('layout-size');
        if (!westWidth)
            westWidth = westBlock.width();


        this.element.serielLayout({
            defaults: {
                spacing_open: 0,
                spacing_closed: 0
            },
            west: {
                size: westWidth ? westWidth : 'auto'
            }
        });
        
        this.options.article_id = $('.article_id', this.element).html();

        this.options.destDiv = $(' > .cont', this.element);
        this.initHasDestDivContainer(this.options.destDiv);
        
        this.options.menuUl = $('.menu_left > .left_block > ul.datas_container', this.element);
        this.options.contratsUl = $('.menu_left > .left_block > ul.contrats_container', this.element);
        
        this.verifUrl();
        westBlock.perfectScrollbar();
    },
    load: function (dest) {
        this.loadContent('./' + dest);
        return true;
    },
    getDestDiv: function () {
    	if (!this.options.destDiv) {
			return $(' > .cont', this.element);
		}
        return this.options.destDiv;
    },
    onAction: function (action, title) {
        // Let's deal with this.
        console.log("CONSOMMABLE_NAV_ACTION[" + action + "]");

        if (action == '') {
        }
    },
    checkContent: function(url, key, serhash) {
		if (!serhash) serhash = parseHash(getCleanHash());
		this.verifUrl(serhash);
	},
    verifUrl: function(dest) {
		//Get URL
		if (!dest) {
			dest = parseHash(getCleanHash());
		}
		
		// get  key word and paremeter in URL
		if (dest && count(dest) > 1) {
			var key = null;
			var params = null;
			
			key = dest[1].getLabel();
			params = dest[1].getParams();
			
			//key word 
			if (key == 'historique') {
				$('li.selected', this.element).removeClass('selected');//remove class selected in menu
				$('li.article_histo', this.element).addClass('selected');
				this.loadContent(getUrlPrefix()+'/article/historique/'+this.getArticleId());
			} else if (key == 'semantique') {
				$('li.selected', this.element).removeClass('selected');//remove class selected in menu
				$('li.article_semantique', this.element).addClass('selected');
				this.loadContent(getUrlPrefix()+'/article/semantique/'+this.getArticleId());
			}
		} else {
			// view information page  by default.
			$('li.selected', this.element).removeClass('selected');//remove class selected in menu
			$('li.article_infos', this.element).addClass('selected');
			this.loadContent(getUrlPrefix()+'/article/infos/'+this.getArticleId());
		}
	},
	dealWithKeyPressed: function (event) {
        return true;
    },
	getArticleId: function() {
		return this.options.article_id;
	},
	refreshMenu: function(menu) {
		if (!menu) return;
		
		// get selected element.
		var selected_id = null;
		var selected = $('.menu_left > .left_block > ul > li.selected', this.element);
		if (selected.size() == 1) {
			selected_id = selected.attr('id');
		}
		
		this.options.menuUl.html($('> .left_block > ul.datas_container', menu).html());
		this.options.contratsUl.html($('> .left_block > ul.contrats_container', menu).html());
		
		$('.menu_left > .left_block > ul > li.selected', this.element).removeClass('selected');
		
		if (selected_id) $('.menu_left > .left_block > ul > li#'+selected_id, this.element).addClass('selected');
	},
	updateInfos : function(content) {
		console.log('update info : article-navigator');
		this.refreshElementWithContent('/article/infos/'+this.getArticleId(), content.html());
	},
	refresh: function(content) {
		var menu_content = $('.container .menu_left', content);
		this.refreshMenu(menu_content);
		var consommable_content = $('.consommable', content);
		this.updateInfos(consommable_content);
	},
    options: {
    	article_id: null,
    	menuUl: null
    }
});;