Interface = function(opts){	
	if(typeof opts == 'undefined') opts = {};
	
	var obj = {
		pages 			: {},
		auth			: null,
		default_page	: opts.default_page,
		visible			: false,
		debug			: false,
		cur_page		: null
	};
	
	obj.hist = new History();
	
	obj.init = function(){
		obj.auth = new Auth({ 'id' : 'auth'});
		obj.pages = {
			'landpage'		: new Landpage({ id : 'landpage' }).init(),
			'catalog'		: new Catalog({ id : 'catalog' }).init(),
			'wishlist'		: new Wishlist({ id : 'wishlist' }).init(),
			/* 'settings'	: new Settings({ id : 'settings' }), */
			'articles'		: new Articles({ id : 'articles' }).init(),
			'profile'		: new Profile({ id : 'profile' }).init(),
			'information'	: new Information({ id : 'information' }).init()
		};
	};
	
	obj.show = function(){
		obj.visible = true;
		
		obj.show_page(obj.hist.parse_hash());
	};
	
	obj.page_instance = function(name){
		return obj.pages[name];
	};
	
	obj.show_page = function(href){

		var pi = obj.page_info(href);
		var page = obj.page_instance(pi.name);
		
		if (typeof page == 'undefined') return;
		
		obj.render(pi);
	};
	
	obj.page_info = function(href)
	{
		var result = { name: obj.default_page, opts: {} };
		
		if (href == undefined || href == '') return result;
		var list = href.split('?'), name = list.shift(), params = list.shift();
		result.name = name;
		
		if(params != undefined){
			result.params = params;
		}
		
		return result;
	};
	
	obj.render = function(opts)
	{
		if(obj.cur_page != null) var prev_page = obj.cur_page.id;
		
		var p = obj.pages[opts.name];
		
		if (typeof p == 'undefined') return;
		p.params = opts.params;	
		for (var n in obj.pages) {
			if (obj.pages[n].id != opts.name) obj.pages[n].hide();
		}
		obj.cur_page = p;
		if(p.render) p.render();
	};
	
	return obj;
}