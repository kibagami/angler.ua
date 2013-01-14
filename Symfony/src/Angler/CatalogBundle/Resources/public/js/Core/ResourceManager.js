$.ajaxSetup({
	cache: true
});

var ResourceManagerMode = {
	_map: [],
	NORMAL: 0,
	REVERSE: 1,
	SYNCHRONOUS:2
};


function ResourceManager() {
	this.mode = ResourceManagerMode.NORMAL;
	this.unloadedResourceDefinitions = {
	};
	this.registeredResources = {
	};
	this.callbacks = {
	};
}
ResourceManager.prototype.setMode = function (mode) {
	this.mode = mode;
};
ResourceManager.prototype.addResourceDefinitions = function (url) {
	var name = "#dependency:" + url;
	if(!(name in this.registeredResources) && !(name in this.unloadedResourceDefinitions)) {
		this.newResourceGroup({
			name: name,
			files: [
				url
			]
		});
		this.unloadedResourceDefinitions[name] = 1;
	}
};
ResourceManager.prototype.newResourceGroup = function (options) {
	if(!this.registeredResources[options.name]) {
		this.registeredResources[options.name] = options;
	} else {
		throw Error("Resource group with name '" + options.name + "' is already registered.");
	}
};
ResourceManager.prototype.requiresLoading = function (resourceGroupName) {
	var dep = this.registeredResources[resourceGroupName];
	return dep ? !dep.loaded : this.hasUnloadedResourceDefinitions();
};
ResourceManager.prototype.getTemplateResource = function (templateId) {
	return Resources.FileResourceHandler.getTemplateResource(templateId);
};
ResourceManager.prototype.load = function (resourceGroupName, callback, errorcallback) {
	this._load(resourceGroupName, callback, errorcallback);
};
ResourceManager.prototype.readConfiguration = function () {
	var _this = this;
	var xml = SDL.Client.Xml;
	var config = SDL.Client.Configuration.ConfigurationManager.configuration;
	Resources.FileResourceHandler.rootPath = SDL.Client.Configuration.ConfigurationManager.getAppSetting("corePath");
	SDL.jQuery.each(xml.selectElements(config, ".//configuration/resourceGroups/resourceGroup"), function (index, element) {
		var name = element.getAttribute("name");
		var resourceGroup = {
			name: name,
			files: [],
			dependencies: [],
			extensions: []
		};
		SDL.jQuery.each(xml.selectNodes(element, "files/file/@name"), function (index, file) {
			resourceGroup.files.push((file).value);
		});
		SDL.jQuery.each(xml.selectNodes(element, "dependencies/dependency/@name"), function (index, dependency) {
			resourceGroup.dependencies.push((dependency).value);
		});
		SDL.jQuery.each(xml.selectNodes(config, ".//configuration/extensions/resourceExtension[@for = \"" + name + "\"]/insert[@position = 'before']/@name"), function (index, dependency) {
			resourceGroup.dependencies.push((dependency).value);
		});
		SDL.jQuery.each(xml.selectNodes(config, ".//configuration/extensions/resourceExtension[@for = \"" + name + "\"]/insert[not(@position) or @position = 'after']/@name"), function (index, extension) {
			resourceGroup.extensions.push((extension).value);
		});
		_this.newResourceGroup(resourceGroup);
	});
};
ResourceManager.prototype._load = function (resourceGroupName, callback, errorcallback, callstack) {
	var _this = this;
	var resourceSettings = this.registeredResources[resourceGroupName];
	if(!resourceSettings) {
		if(this.hasUnloadedResourceDefinitions()) {
			this.loadResourceDefinitions(function () {
				_this._load(resourceGroupName, callback, errorcallback, callstack);
			});
			return;
		} else {
			var error = "Resource group with name '" + resourceGroupName + "' does not exist";
			if(errorcallback) {
				errorcallback(error);
				return;
			} else {
				throw Error(error);
			}
		}
	}
	var extensions = resourceSettings.extensions;
	if(extensions) {
		var extensionsCount = extensions.length;
		if(extensionsCount && (!callstack || SDL.jQuery.inArray(resourceGroupName, callstack) == -1)) {
			var _callback = callback;
			callback = function () {
				var extensionInCallstack = -1;
				var onExtensionLoaded;
				if(_callback) {
					if(callstack) {
						for(var i = 0; i < extensionsCount; i++) {
							if(SDL.jQuery.inArray(extensions[i], callstack) != -1) {
								extensionInCallstack = i;
								break;
							}
						}
					}
					if(extensionInCallstack != -1) {
						_callback();
						errorcallback = null;
					} else {
						var renderedExtensions = 0;
						onExtensionLoaded = function () {
							if(++renderedExtensions == extensionsCount) {
								_callback();
							}
						};
					}
				}
				var ownCallstack = callstack ? callstack.concat([
					resourceGroupName
				]) : [
					resourceGroupName
				];
				for(var i = 0; i < extensionsCount; i++) {
					if(extensionInCallstack != i) {
						_this._load(extensions[i], onExtensionLoaded, errorcallback, ownCallstack);
					}
				}
			};
		}
	}
	if(resourceSettings.loaded) {
		if(callback) {
			callback();
		}
	} else {
		if(resourceSettings.loading) {
			if(callstack) {
				var index = SDL.jQuery.inArray(resourceGroupName, callstack);
				if(index != -1) {
					index++;
					for(var len = callstack.length; index < len; index++) {
						if(this.registeredResources[callstack[index]].loaded) {
							if(callback) {
								callback();
							}
							return;
						}
					}
					var error = "Circular dependency detected: " + callstack.join(" -> ") + " -> " + resourceGroupName;
					if(errorcallback) {
						errorcallback(error);
					} else {
						throw Error(error);
					}
					return;
				}
			}
			if(callback) {
				this.callbacks[resourceGroupName].add(callback);
			}
		} else {
			resourceSettings.loading = true;
			this.callbacks[resourceGroupName] = SDL.jQuery.Callbacks("once");
			var renderCallbackHandler = function () {
				resourceSettings.loaded = true;
				resourceSettings.loading = false;
				if(resourceGroupName in _this.unloadedResourceDefinitions) {
					delete _this.unloadedResourceDefinitions[resourceGroupName];
				}
				if(callback) {
					callback();
				}
				_this.callbacks[resourceGroupName].fire();
				_this.callbacks[resourceGroupName].empty();
				delete _this.callbacks[resourceGroupName];
			};
			var dependenciesCount = resourceSettings.dependencies ? resourceSettings.dependencies.length : 0;
			var filesCount = resourceSettings.files ? resourceSettings.files.length : 0;
			if(dependenciesCount || filesCount) {
				var renderedDependenciesCount = 0;
				var loadedFilesCount = 0;
				var renderedFilesCount = 0;
				var renderLoadedFiles = function () {
					while(renderedFilesCount < filesCount) {
						var rendered = false;
						if(errorcallback) {
							try  {
								rendered = Resources.FileResourceHandler.renderFileIfLoaded(resourceSettings.files[renderedFilesCount]);
							} catch (err) {
								errorcallback("Error executing '" + resourceSettings.files[renderedFilesCount] + "': " + err.message);
								rendered = false;
							}
						} else {
							rendered = Resources.FileResourceHandler.renderFileIfLoaded(resourceSettings.files[renderedFilesCount]);
						}
						if(!rendered) {
							break;
						}
						renderedFilesCount++;
					}
				};
				var loadCallbackHandler = function () {
					if(dependenciesCount == renderedDependenciesCount) {
						renderLoadedFiles();
						if(renderedFilesCount == filesCount && loadedFilesCount == filesCount) {
							renderCallbackHandler();
						}
					}
				};
				var dependencyCallbackHandler = function () {
					renderedDependenciesCount++;
					loadCallbackHandler();
				};
				var fileCallbackHandler = function () {
					loadedFilesCount++;
					loadCallbackHandler();
				};
				if(dependenciesCount) {
					var ownCallstack = callstack ? callstack.concat([
						resourceGroupName
					]) : [
						resourceGroupName
					];
					SDL.jQuery.each((this.mode & ResourceManagerMode.REVERSE) ? resourceSettings.dependencies.reverse() : resourceSettings.dependencies, function (index, value) {
						return _this._load(value, dependencyCallbackHandler, errorcallback, ownCallstack);
					});
				}
				if(filesCount) {
					SDL.jQuery.each(resourceSettings.files, function (index, value) {
						return Resources.FileResourceHandler.loadFile(value, fileCallbackHandler, errorcallback, (_this.mode & ResourceManagerMode.SYNCHRONOUS) != 0);
					});
				}
			} else {
				renderCallbackHandler();
			}
		}
	}
};
ResourceManager.prototype.hasUnloadedResourceDefinitions = function () {
	for(var name in this.unloadedResourceDefinitions) {
		return true;
	}
	return false;
};
ResourceManager.prototype.loadResourceDefinitions = function (callback, errorcallback) {
	var deps = this.unloadedResourceDefinitions;
	var count = 0;
	var renderedCount = -1;
	function definitionsLoaded() {
		if(count == ++renderedCount) {
			if(callback) {
				callback();
			}
		}
	}
	for(var name in deps) {
		count++;
		this._load(name, definitionsLoaded, errorcallback);
	}
	definitionsLoaded();
};

