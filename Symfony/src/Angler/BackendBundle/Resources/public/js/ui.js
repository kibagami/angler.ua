function UI(options) {
	var defaultOptions = {
			default_page: null,
			hist: new History()
		},
		opts = $.extend(defaultOptions, options);

	this.default_page = opts.default_page;
	this.hist = opts.hist;
	this.pages = {};
	this.visible = false;
	this.debug = false;
	this.current_page = null;
}

UI.prototype.init = function () {
	this.pages = {
		'auth': new Auth({ id: 'auth' }),
		'landpage': new Landpage({ id: 'landpage' }),
		'catalog': new Catalog({ id: 'catalog' }),
		'wishlist': new Wishlist({ id: 'wishlist' }),
		'articles': new Articles({ id: 'articles' }),
		'profile': new Profile({ id: 'profile' }),
		'information': new Information({ id: 'information' })
	};
};

UI.prototype.show = function () {
	this.visible = true;

	this.showPage(this.hist.parse_hash());
};

UI.prototype.getPage = function (name) {
	return this.pages[name];
};

UI.prototype.showPage = function (href) {
	var pageInfo = this.pageInfo(href),
		page = this.getPage(pageInfo.name);

	if (typeof page == 'undefined') return;

	this.render(pageInfo);
};

UI.prototype.getFader = function (options) {
	return new Fader(options);
}

UI.prototype.pageInfo = function (href) {
	var result = { name: null, params: null };

	if (href == undefined || href == '') return result;
	var list = href.split('?'),
		pageName = list.shift(),
		params = list.shift();

	result.name = pageName || this.default_page;

	if (params != undefined) {
		result.params = params;
	}

	return result;
};

UI.prototype.render = function (options) {
	if (this.current_page != null) var prev_page = this.current_page.id;

	var page = this.getPage(options.name);

	if (typeof page == 'undefined') return;
	page.params = options.params;
	for (var pageName in this.pages) {
		if (this.pages.hasOwnProperty(pageName) && options.name != this.pages[pageName].id)
			this.pages[pageName].hide();
	}

	this.current_page = page;

	if (page.render) page.render();
};

