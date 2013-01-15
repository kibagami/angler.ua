var ViewKnockoutBindingHandler = (function () {
	function ViewKnockoutBindingHandler() { }
	ViewKnockoutBindingHandler.prototype.init = function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
		var value = ko.utils.unwrapObservable(valueAccessor()) || "";
		if(value) {
			ko.utils.domNodeDisposal.addDisposeCallback(element, ViewKnockoutBindingHandler.elementDisposalCallback);
			$(element).data("view-create", true);
			ViewKnockoutBindingHandler.initViewBinding(element, valueAccessor, allBindingsAccessor, viewModel, bindingContext);
		}
		return value.controlsDescendantBindings == false ? undefined : {
			controlsDescendantBindings: true
		};
	};
	ViewKnockoutBindingHandler.prototype.update = function (element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
		var value = ko.utils.unwrapObservable(valueAccessor()) || "";
		if(value) {
			var $e = $(element);
			var handler = $e.data("view-handler");
			if(handler !== null) {
				if(!handler) {
					$e.data("view-update", true);
				} else {
					if(handler.update) {
						handler.update(element, ViewKnockoutBindingHandler.getDataValueAccessor(valueAccessor), allBindingsAccessor, viewModel, bindingContext);
					} else {
						$e.data("view-handler", null);
					}
				}
			}
		}
	};
	ViewKnockoutBindingHandler.initViewBinding = function(element, valueAccessor, allBindingsAccessor, viewModel, bindingContext) {
		var $e = $(element);
		if($e.data("view-create")) {
			var value = ko.utils.unwrapObservable(valueAccessor()) || "";
			var type = value.type || "" + value;
			var handlerName = value.handler;
			var handler;
			if(handlerName) {
				handler = ko.bindingHandlers[handlerName];
			}
			if(handler) {
				var dataValueAccessor = ViewKnockoutBindingHandler.getDataValueAccessor(valueAccessor);
				if(handler.init) {
					handler.init(element, dataValueAccessor, allBindingsAccessor, viewModel, bindingContext);
				}
				$e.data("view-handler", handler);
				if($e.data("view-update")) {
					$e.data("view-update", null);
					if(handler.update) {
						handler.update(element, dataValueAccessor, allBindingsAccessor, viewModel, bindingContext);
					}
				}
			} else {
				$e.data("view-handler", null);
			}
		}
	};
	ViewKnockoutBindingHandler.getDataValueAccessor = function(valueAccessor) {
		return function () {
			return valueAccessor().data;
		}
	};
	ViewKnockoutBindingHandler.elementDisposalCallback = function(element) {
		$(element).removeData();
	};
	return ViewKnockoutBindingHandler;
})();
(ko.bindingHandlers).view = (new ViewKnockoutBindingHandler());
