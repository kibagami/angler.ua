var PageInterface = new Interface('PageInterface', ['init', 'load', 'show']);

function extend(child, parent){
	var object = function(){};
	object.prototype = parent.prototype;
	
	child.prototype = new object();
	child.prototype.constructor = child;
}
