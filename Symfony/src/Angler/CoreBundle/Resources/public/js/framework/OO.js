Function.prototype.extend = function (superClass) {
    var mi = this;
    var F = function() {
        this.constructor = mi;
    };
    F.prototype = superClass.prototype;

    this.prototype = new F();
    this.prototype.constructor = this;
    this.superclass = superClass.prototype;
    if(superClass.prototype.constructor == Object.prototype.constructor) {
        superClass.prototype.constructor = superClass;
    }

    return this;
};

Function.prototype.augment = function (givingClass) {
    if(arguments[2]) { // Only give certain methods.
        for(var i = 2, len = arguments.length; i < len; i++) {
            this.prototype[arguments[i]] = givingClass.prototype[arguments[i]];
        }
    }
    else { // Give all methods.
        for(var methodName in givingClass.prototype) {
            if(!this.prototype[methodName]) {
                this.prototype[methodName] = givingClass.prototype[methodName];
            }
        }
    }

    return this;
};

Angler = window.Angler || {};
Angler.OO = window.Angler.OO || {};

Angler.OO.Inheritable = function () {
    // this is a class that will never be instantiated, it's needed to be able to inherit from TypeScript
};

Angler.OO.createInterface = function (interfaceName) {
    if (interfaceName) {
        Angler.Type.registerNamespace(interfaceName);

        // using eval to avoid setting a property on 'window' (might cause memory leaks in older IE versions, IE8- ?)
        // using globalEval to (a) give a name to the function, (b) prevent name conflicts between the top level namespace and closure variables (argument interfaceName)
        $.globalEval(interfaceName + " = function " + interfaceName.replace(/\./g, "$") +
            "() { return Angler.Angler.OO.enableInterface(this, \"" + interfaceName + "\", arguments, true); }");

        return Angler.Type.resolveNamespace(interfaceName);
    }
    else
    {
        return function()
        {
            return Angler.OO.enableInterface(this, "", arguments, true);
        }
    }
};

Angler.OO.extendInterface = function SDL$Client$Types$OO$extendInterface(baseInterfaceName, newInterfaceName)
{
    if (!this.extendedInterfaces) this.extendedInterfaces = {};
    if (!this.extendedInterfaces[baseInterfaceName])
    {
        this.extendedInterfaces[baseInterfaceName] = [newInterfaceName];
    }
    else
    {
        this.extendedInterfaces[baseInterfaceName].push(newInterfaceName);
    }
};

Angler.OO.implementsInterface = function SDL$Client$Types$OO$implementsInterface(object, interfaceName)
{
    return (object && interfaceName && (interfaceName in (object.interfaces || {})));
};

Angler.OO.importObject = function SDL$Client$Types$OO$importObject(object)
{
    var typeOfObject = (typeof(object)).toLowerCase();

    if (typeOfObject == "number" || typeOfObject == "string" || typeOfObject == "boolean")
    {
        return object;
    }
    else
    {
        var typeName;
        if (Angler.OO.implementsInterface(object, "SDL.Client.Models.MarshallableObject") && (typeName = object.getTypeName()))
        {
            // make sure all 'upgrade' interfaces are added too
            var baseInterfaceName = typeName;
            var ifaces = object.interfaces;
            var upgradedInterface = ifaces[baseInterfaceName].defaultBase;
            while (upgradedInterface && ifaces[upgradedInterface].upgradedToType)
            {
                baseInterfaceName = upgradedInterface;
                upgradedInterface = ifaces[baseInterfaceName].defaultBase;
            }

            var typeConstructor = Angler.Type.resolveNamespace(baseInterfaceName);
            //FIXME: SDL.Client.Diagnostics.Assert.isFunction(typeConstructor, baseInterfaceName + " should be a constructor.");
            var newObject = new typeConstructor();

            if (baseInterfaceName != typeName)	// interface has been upgraded -> upgrade too
            {
                var upgradeType = ifaces[baseInterfaceName].upgradedToType;
                while (upgradeType)
                {
                    if (!newObject.interfaces[upgradeType])
                    {
                        newObject = newObject.upgradeToType(upgradeType);
                    }
                    upgradeType = ifaces[upgradeType].upgradedToType;
                }
            }

            newObject._initializeMarshalledObject(object);
            object.dispose();
            return newObject;
        }
        else
        {
            if (Angler.OO.implementsInterface(object, "DisposableObject"))
            {
                object.dispose();
            }
            return null;
        }
    }
};

Angler.OO.nonInheritable = function (member)
{
    member.noninheritable = true;
    return member;
};

(function()
{
    Angler.OO.enableInterface = function (object, interfaceName, args, createdInterface)
    {
        var isMainInterface = !object.interfaces;
        if (isMainInterface)
        {
            var m = object.prototypeMembers = {};
            for (var p in object) //make sure we preserve members defined with prototype when adding interfaces
            {
                m[p] = true;
            }
            object.interfaces = { type:interfaceName };
            object.properties = { delegates:[] };
        }
        else if (interfaceName in object.interfaces)
        {
            return false;
        }

        object.addInterface = addInterface;
        object.upgradeToType = upgradeToType;
        object.getTypeName = getTypeName;
        object.getInterface = getInterface;
        object.getInterfaceNames = getInterfaceNames;
        object.getMainInterface = getMainInterface;
        object.getDelegate = getDelegate;
        object.removeDelegate = removeDelegate;
        object.callInterfaces = callInterfaces;
        object.callBase = callBase;

        object.interfaces[interfaceName] = object;

        var extensions = Angler.OO.extendedInterfaces;
        if (extensions)
        {
            extensions = extensions[interfaceName];
            if (extensions && extensions.length && !createdInterface)
            {
                throw new Error("Unable to extend interface \"" + interfaceName + "\": the interface must be created using Angler.Angler.OO.createInterface().");
            }
        }

        if (object.$constructor)
        {
            object.$constructor.apply(object, args || []);

            if (extensions)
            {
                for (var i = 0; i < extensions.length; i++)
                {
                    object = object.upgradeToType(extensions[i], args, interfaceName);
                }
            }
        }

        if (isMainInterface && createdInterface && object.$initialize)
        {
            object.$initialize();
        }

        return object;
    };

    var addInterface = Angler.OO.nonInheritable(function (interfaceName, args)
    {
        var object = this;
        var iface = object.interfaces[interfaceName];
        if (!iface)
        {
            var constructor = Angler.Type.resolveNamespace(interfaceName);

            if (!constructor)
            {
                throw new Error("Unable to inherit from \"" + interfaceName + "\": constructor is not defined.");
            }
            else
            {
                // not using an ordinary object creation with a constructor as we need to have
                // "interfaces" property set before running the constructor
                iface = {};
                iface.interfaces = object.interfaces;
                iface.properties = object.properties;
                iface.prototypeMembers = {};

                var m = iface.prototypeMembers;
                var p = constructor.prototype;

                if (p)
                {
                    for (var prop in p)
                    {
                        iface[prop] = p[prop];
                        m[prop] = true;
                    }
                }
                constructor.apply(iface, args || []);
            }
        }

        while (iface.upgradedToType)
        {
            interfaceName = iface.upgradedToType;
            iface = object.interfaces[interfaceName];
        }

        delete iface["prototypeMembers"];
        for (var member in iface)
        {
            switch (member)
            {
                case "interfaces":
                case "properties":
                    // these properties are already added
                    break;
                case "upgradedToType":
                case "$constructor":
                    // these properties should not be inherited
                    break;
                default:
                    var value = iface[member];
                    if (value && !value.noninheritable)
                    {
                        if (!(member in object.prototypeMembers))
                        {
                            object[member] = value;
                        }

                        if (!value.implementingInterface)
                        {
                            value.implementingInterface = interfaceName;
                        }
                    }
            }
        }

        object.defaultBase = interfaceName;
    });
    var upgradeToType = Angler.OO.nonInheritable(function (interfaceName, args, typeToExtend)
    {
        var object = this;
        var interfaces = object.interfaces;
        var iface = interfaces[interfaceName];
        if (!iface)
        {
            var constructor = Angler.Type.resolveNamespace(interfaceName);
            if (!constructor)
            {
                throw Error("Unable to upgrade to type \"" + interfaceName + "\". Constructor is not defined!");
            }
            else
            {
                var baseType;

                while (typeToExtend && interfaces[typeToExtend].upgradedToType)
                {
                    typeToExtend = interfaces[typeToExtend].upgradedToType;
                }

                if (!typeToExtend || interfaces.type == typeToExtend)
                {
                    baseType = interfaces.type;
                    interfaces.type = interfaceName;
                }
                else
                {
                    baseType = typeToExtend;
                }

                // not using an ordinary object creation with a constructor as we need to have "interfaces" property set before running the constructor
                iface = { "interfaces": interfaces, "properties": object.properties };
                var m = iface.prototypeMembers = {};
                var p = constructor.prototype;
                if (p)
                {
                    for (var prop in p)
                    {
                        iface[prop] = p[prop];
                        m[prop] = true;
                    }
                }
                constructor.apply(iface, args || []);

                while (iface.upgradedToType)
                {
                    interfaceName = iface.upgradedToType;
                    iface = object.interfaces[interfaceName];
                }

                if (iface.defaultBase != baseType)
                {
                    var baseUpgradedToType = interfaces[baseType].upgradedToType;
                    while (baseUpgradedToType && baseUpgradedToType != iface.defaultBase)
                    {
                        baseUpgradedToType = interfaces[baseUpgradedToType].upgradedToType;
                    }

                    if (!baseUpgradedToType)
                    {
                        throw new Error("Unable to upgrade \"" + baseType + "\" to \"" + interfaceName + "\". Interface \"" + baseType + "\" or its upgraded interface must be the default interface for \"" + interfaceName + "\".");
                    }

                    delete object["prototypeMembers"];
                    for (var member in object)
                    {
                        switch (member)
                        {
                            case "interfaces":
                            case "properties":
                                // these properties are already added
                                break;
                            case "upgradedToType":
                            case "$constructor":
                                // these properties should not be inherited
                                break;
                            default:
                                var value = object[member];
                                if (value && !value.noninheritable)
                                {
                                    if (!(member in iface))
                                    {
                                        iface[member] = value;
                                    }

                                    if (!value.implementingInterface)
                                    {
                                        value.implementingInterface = baseUpgradedToType;
                                    }
                                }
                        }
                    }
                }

                object.upgradedToType = interfaceName;
                delete iface["prototypeMembers"];
            }
        }
        else
        {
            throw new Error("Unable to upgrade \"" + baseType + "\" to \"" + interfaceName + "\". Interface \"" + baseType + "\" already implements \"" + interfaceName + "\".");
        }
        return iface;
    });
    var getTypeName = Angler.OO.nonInheritable(function ()
    {
        var interfaces = this.interfaces;
        return interfaces ? interfaces.type : undefined;
    });
    var getInterface = Angler.OO.nonInheritable(function (interfaceName)
    {
        var object = this;
        if (interfaceName in object.interfaces)
        {
            return object.interfaces[interfaceName];
        }
        else
        {
            throw Error("Object does not implement interface " + interfaceName + ".");
        }
    });
    var getInterfaceNames = Angler.OO.nonInheritable(function ()
    {
        var object = this;
        var interfaces = [];
        for (var i in object.interfaces)
        {
            if (i != "type")
            {
                interfaces.push(i);
            }
        }
        return interfaces;
    });
    var getMainInterface = Angler.OO.nonInheritable(function ()
    {
        var interfaces = this.interfaces;
        return interfaces[interfaces.type];
    });
    var getDelegate = Angler.OO.nonInheritable(function (method, args)
    {
        var delegates = this.properties.delegates;
        if (delegates)
        {
            var delegate;
            for (var i = 0, len = delegates.length; i < len; i++)
            {
                delegate = delegates[i];
                if (delegate.method == method)
                {
                    if (delegate.args == args)
                    {
                        return delegate.delegate;
                    }
                    else
                    {
                        var delegate_args = delegate.args;
                        if (delegate_args && args && delegate_args.length == args.length)
                        {
                            var equal = true;
                            for (var j = 0, lenj = args.length; j < lenj; j++)
                            {
                                if (delegate_args[j] !== args[j])
                                {
                                    equal = false;
                                    break;
                                }
                            }
                            if (equal)
                            {
                                return delegate.delegate;
                            }
                        }
                    }
                }
            }
            var interfaces = this.interfaces;
            delegate = Function.getDelegate(interfaces[interfaces.type], method, args);	// main interface
            delegates.push({method: method, args: args, delegate: delegate});
            return delegate;
        }
    });
    var removeDelegate = Angler.OO.nonInheritable(function (method, args)
    {
        var delegates = this.properties.delegates;
        if (delegates)
        {
            var delegate;
            for (var i = 0; i < delegates.length; i++)
            {
                delegate = delegates[i];
                if (delegate.method == method)
                {
                    if (delegate.args == args)
                    {
                        delegate = delegate.delegate;
                        Array.removeAt(delegates, i);
                        return delegate;
                    }
                    else
                    {
                        var delegate_args = delegate.args;
                        if (delegate_args && args && delegate_args.length == args.length)
                        {
                            var equal = true;
                            for (var j = 0, lenj = args.length; j < lenj; j++)
                            {
                                if (delegate_args[j] !== args[j])
                                {
                                    equal = false;
                                    break;
                                }
                            }
                            if (equal)
                            {
                                delegate = delegate.delegate;
                                Array.removeAt(delegates, i);
                                return delegate;
                            }
                        }
                    }
                }
            }
            return;
        }
    });
    var callInterfaces = Angler.OO.nonInheritable(function (method, args)
    {
        var interfaces = this.interfaces;
        if (interfaces)
        {
            for (var i in interfaces)
            {
                var iface = interfaces[i];
                var fnc = iface[method];
                if (fnc && fnc.noninheritable)
                {
                    fnc.apply(this, args || []);
                }
            }
        }
    });
    var callBase = Angler.OO.nonInheritable(function (interfaceName, methodName, args)
    {
        var interfaces = this.interfaces;

        if (!interfaces[interfaceName])
        {
            throw new Error("Current object doesn't implement interface '" + interfaceName + "'");
        }

        var callingInterface = arguments.callee.caller && arguments.callee.caller.implementingInterface || interfaces.type;

        var interfaceUpgradedToType = interfaces[interfaceName].upgradedToType;
        if (interfaceUpgradedToType && callingInterface != interfaceUpgradedToType)
        {
            do
            {
                interfaceName = interfaceUpgradedToType;
                if (!interfaces[interfaceName])
                {
                    throw new Error("Current object doesn't implement interface '" + interfaceName + "'");
                }
                interfaceUpgradedToType = interfaces[interfaceName].upgradedToType;
            } while (interfaceUpgradedToType && callingInterface != interfaceUpgradedToType);
        }

        var method = interfaces[interfaceName][methodName];
        if (!method)
        {
            throw new Error("Interface '" + interfaceName + "' doesn't implement method '" + methodName + "'");
        }
        if (method.noninheritable)
        {
            throw new Error("Unable to execute a non-inheritable method with callBase('" + interfaceName + "', '" + methodName + "').");
        }
        if (!method.implementingInterface)
        {
            throw new Error("Unable to execute a method that was not inherited: callBase('" + interfaceName + "', '" + methodName + "').");
        }
        return interfaces[interfaceName][methodName].apply(this, args || []);
    });
})();
