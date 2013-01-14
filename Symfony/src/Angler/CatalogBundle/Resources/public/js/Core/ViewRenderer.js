function ViewRenderer() {}

ViewRenderer.templateRenderers = {
};

ViewRenderer.types = {};

ViewRenderer.createdViews = {};

ViewRenderer.registerTemplateRenderer = function (type, renderer) {
	ViewRenderer.templateRenderers[type] = renderer;
};

ViewRenderer.getTemplateRenderer = function (type) {
	return ViewRenderer.templateRenderers[type];
};

ViewRenderer.loadViewResources = function (type, callback) {
	if(!ViewRenderer.types[type]) {
		SDL.Client.Resources.ResourceManager.load(type, function () {
			if(!(type in ViewRenderer.types)) {
				ViewRenderer.types[type] = null;
			}
			if(callback) {
				callback();
			}
		});
	} else {
		if(callback) {
			callback();
		}
	}
};

ViewRenderer.renderView = function (type, element, settings, callback, errorcallback) {
	if(element) {
		ko.utils.domNodeDisposal.addDisposeCallback(element, ViewRenderer.elementDisposalCallback);
		SDL.jQuery(element).data("view-create", true);
	}
	SDL.Client.Resources.ResourceManager.load(type, function () {
		if(!element || SDL.jQuery(element).data("view-create")) {
			var ctor = ViewRenderer.types[type];
			if(!ctor) {
				ctor = ViewRenderer.types[type] = ViewRenderer.getTypeConstructor(type);
			}
			if(!element) {
				if(ctor.createElement) {
					element = ctor.createElement(settings, document);
				} else {
					element = SDL.jQuery("<div />")[0];
				}
			}
			var view = new ctor(element, settings);
			if(!SDL.Client.Types.OO.implementsInterface(view, "SDL.UI.Core.View.ViewBase")) {
				SDL.Client.Diagnostics.Assert.raiseError("'" + type + "' must implement SDL.UI.Core.View.ViewBase interface.");
			}
			ViewRenderer.addViewDisposalCallback(view);
			view.initialize();
			view.render(!callback ? null : function () {
				callback(view);
			});
		}
	}, errorcallback);
};

ViewRenderer.onViewCreated = function (view) {
	var type = view.getTypeName();
	ViewRenderer.createdViews[type] = (ViewRenderer.createdViews[type] || 0) + 1;
};

ViewRenderer.onViewDisposed = function (view) {
	var type = view.getTypeName();
	ViewRenderer.createdViews[type] = (ViewRenderer.createdViews[type] || 0) - 1;
};

ViewRenderer.getCreatedViewCounts = function () {
	return ViewRenderer.createdViews;
};

ViewRenderer.getTypeConstructor = function (type) {
	$.isString(type, "View type name is expected.");
	var ctor;
	try  {
		ctor = SDL.Client.Type.resolveNamespace(type);
	} catch (err) {
		SDL.Client.Diagnostics.Assert.raiseError("Unable to evaluate \"" + type + "\": " + err.description);
	}
	SDL.Client.Diagnostics.Assert.isFunction(ctor);
	return ctor;
};

ViewRenderer.addViewDisposalCallback = function (view) {
	ko.utils.domNodeDisposal.addDisposeCallback(view.getElement(), function (element) {
		view.dispose();
	});
};

ViewRenderer.elementDisposalCallback = function (element) {
	$(element).removeData();
};

