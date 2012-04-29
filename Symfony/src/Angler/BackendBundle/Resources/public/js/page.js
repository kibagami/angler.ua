function Page(options){
	if(options == undefined) return null;
	var context = $('#pages');
	
	this.container		= $('#page-' + options.id, context);
	this.visible		= false;
	this.controller		= null;
	this.action			= null;
	this.params			= null;
	this.request	
}

Page.prototype._init = function(){	
	// Abstract method
}

Page.prototype.init = function(){	
	return this;
}

Page.prototype.parse_params = function(){}

Page.prototype.load = function(){
	var url = '/' + this.controller + ((this.action != null) ? '/' + this.action : '') + ((this.params != null) ? '?' + this.params : '');

	this.request = $.ajax({
		type: 'GET',
		url: url,
		success: this.show,
		dataType: 'html',
		context: this
	});
}

Page.prototype.show = function(data){
	
	this.container
		.html(data)
		.removeClass('hidden');
	this.visible = true;
}	

Page.prototype.render = function(){
	this.load();
}

Page.prototype.clean = function(){
	this.container.empty();
}

Page.prototype.hide = function(){
	this.container.addClass('hidden');
	this.visible = false;
}
