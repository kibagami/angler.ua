function Interface(name, methods) {
	if (arguments.length != 2) {
		throw new Error("Interface constructor called with " + arguments.length + "arguments, but expected exactly 2.");
	}

	this.name = name;
	this.methods = [];

	for (var i = 0, len = methods.length; i < len; i++) {
		if (typeof methods[i] !== 'string') {
			throw new Error("Interface constructor expects method names to be passed in as a string.");
		}

		this.methods.push(methods[i]);
	}
}

Interface.prototype.ensureImplements = function (object) {
	var len = arguments.length;
	if (len < 2) {
		throw new Error("Function Interface.ensureImplements called with " + arguments.length + "arguments, but expected at least 2.");
	}

	for (var i = 1; i < len; i++) {
		var paradigm = arguments[i];

		if (Interface !== paradigm.constructor) {
			throw new Error("Function Interface.ensureImplements expects arguments two and above to be instances of Interface.");
		}
		var methodsLen = paradigm.methods.length;
		for (var j = 0; j < methodsLen; j++) {
			var method = paradigm.methods[j];

			if (!object[method] || typeof object[method] !== 'function') {
				throw new Error("Function Interface.ensureImplements: object does not implement the " + paradigm.name + " interface. Method " + method + " was not found.");
			}
		}
	}
};

var PageInterface = new Interface('PageInterface', ['init', 'load', 'show']);
