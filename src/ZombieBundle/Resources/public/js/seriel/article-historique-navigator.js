$.widget('seriel.articleHistoNav', $.seriel.tabsLightNavigator, {
	_create : function() {
		this.options.tabLightContainer = $(' > .article_histo_container', this.element); // Important de le faire avant d'appeler le constructeur parent !
		this._super();
	},
	verifUrl: function(dest) {
		console.log('test verifUrl');
		
		//get URL
		if (!dest) {
			dest = parseHash(getCleanHash());
		}
		
		//get  key word and paremeter in URL
		if (dest && count(dest) > 2) {
			var key = null;
			var params = null;
			
			key = dest[2].getLabel();
			params = dest[2].getParams();
			
			this.loadContent(getUrlPrefix()+'/article/historique/'+ key +'/'+this.getArticleId());

		} else {
			this.loadContent(getUrlPrefix()+'/article/historique/resume/'+this.getArticleId());
		}
	},
	getArticleId: function() {
		return $(' > .article_histo_container', this.element).attr('article_id');
	},
	options: {
		tabLightContainer: null
	}
});