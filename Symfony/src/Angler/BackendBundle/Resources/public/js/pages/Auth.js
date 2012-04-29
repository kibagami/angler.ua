Auth = function(options){
	Page.call(this, options);
	
	this.controller = 'auth';
	this.action = 'form';
	this.forms = {};
	this.popup = new Modal({id: 'popup'});
};

extend(Auth, Page);
	
	
Auth.prototype.init = function(){
	var mi = this;
		
	this.forms = {
		'login'		: new Form({'name' : 'login', 'popup' : mi.popup}),
		'register'	: new Form({'name' : 'register', 'popup' : popup}),
		'forgot'	: new Form({'name' : 'forgot', 'popup' : popup})
	}

	var togglers = $('#login .b-auth-link');

	togglers.live('click', function(){
		var form,
			self = $(this),
			name = self.attr('rel');
			
		form = mi.get_form(name);
		
		if(mi.currentForm && mi.currentForm != form){
			mi.currentForm.clean();
		}
		
		if(form){
			mi.currentForm = form;
			form.render();
		}
		
		return false;
	});

	//var close = $()
		
	return this;
};

Auth.prototype.show = function(name){
	return this.forms[name];
}

Auth.prototype.get_form = function(name){
	return this.forms[name];
}

Form = function(options){
	this.context = null;
	this.action = options.name;
	this.data = null;
	this.loaded = false;

};
	
Form.prototype.load = function(callback){
	if(this.loaded) return;

	var url = '/' + this.module + ((this.action != null) ? '/' + this.action : '') + ((this.params != null) ? '?' + this.params : '');

	// Get form layout
	var options = {
		type: "GET",
		dataType: 'html',
		success: callback,
		context: this
	};
	$.ajax(url, options);

	// Set the flag not to load if it's already loaded
	this.loaded = true;
};

Form.prototype.process = function(data){
	this.popup
		.load(data)
		.show(this.clean);
}

Form.prototype.submit = function(){}

Form.prototype.render = function(){
	this.load(this.process);
}

Form.prototype.clean = function(){
	if( ! this.loaded) return;

	this.loaded = false;
}

Form.prototype.serialize = function(){}

