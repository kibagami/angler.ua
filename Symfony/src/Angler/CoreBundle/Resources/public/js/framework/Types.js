Angler = window.Angler || {};

Array.prototype.isArray = true;
Date.prototype.isDate = true;
Function.prototype.isFunction = true;

Angler.Type = {
    registerNamespace: function (text)
    {
        var arr = text.split(".");
        var length = arr.length;

        if (length > 0)
        {
            var base = window;
            var part;
            for (var i = 0, l = arr.length; i < l; i++)
            {
                if (!base[part = arr[i]])
                {
                    if (i == 0)
                    {
                        eval(part + "={};");	// this is to prevent creating properties on the 'window' object, which will cause circular references and memory leaks
                    }
                    else
                    {
                        base[part] = {};
                    }
                }
                base = base[part];
            }
        }
    },
    resolveNamespace: function (typeName)
    {
        var typeNames = typeName.split(".");
        var base = window;
        for (var i = 0, l = typeNames.length; i < l && base; i++)
        {
            base = base[typeNames[i]];
        }
        return base;
    },

    /**
     * Returns a value indicating whether the supplied value is an array.
     * @return {Boolean} <c>true</c> if the supplied value is an array, otherwise <c>false</c>.
     */
    isArray: function (value)
    {
        return (value &&
            (value.isArray ||
                (
                    this.isNumber(value.length) &&
                        !this.isWindow(value) && !this.isElement(value) &&
                        this.isObject(value) && !this.isString(value)
                    )
                )
            ) ? true : false;
    },

    /**
     * Returns a value indicating whether the supplied value is a date.
     * @return {Boolean} <c>true</c> if the supplied value is an date, otherwise <c>false</c>.
     */
    isDate: function (value)
    {
        return this.isObject(value) && value.isDate;
    },

    /**
     * Returns a value indicating whether the supplied value is a boolean.
     * @return {Boolean} <c>true</c> if the supplied value is a boolean, otherwise <c>false</c>.
     */
    isBoolean: function (value)
    {
        return typeof value == 'boolean';
    },

    /**
     * Returns a value indicating whether the supplied value is a function.
     * @return {Boolean} <c>true</c> if the supplied value is a function, otherwise <c>false</c>.
     */
    isFunction: function (value)
    {
        return (value && value.isFunction == true) || false;
    },

    /**
     * Returns a value indicating whether the supplied value is a number.
     * @return {Boolean} <c>true</c> if the supplied value is a number, otherwise <c>false</c>.
     */
    isNumber: function (value)
    {
        return typeof value == 'number' && isFinite(value);
    },

    /**
     * Returns a value indicating whether the supplied value is a number or can be converted to a number.
     * @return {Boolean} <c>true</c> if the supplied value is an array, otherwise <c>false</c>.
     */
    isNumeric: function (value)
    {
        return value != null && !isNaN(value);
    },

    /**
     * Returns a value indicating whether the supplied value is an object.
     * @return {Boolean} <c>true</c> if the supplied value is an object, otherwise <c>false</c>.
     */
    isObject: function (value)
    {
        return value && ((value.window == value) ||
            (typeof value == 'object' &&
                (!value.constructor ||
                    value.constructor != String &&
                        value.constructor != Number)) || this.isFunction(value));
    },

    /**
     * Returns a value indicating whether the supplied value is a string.
     * @return {Boolean} <c>true</c> if the supplied value is a string, otherwise <c>false</c>.
     */
    isString: function (value)
    {
        return (typeof value == 'string') || (value != null && value.constructor == String);
    },

    /**
     * Returns a value indicating whether the supplied value is an xml or html node.
     * @return {Boolean} <c>true</c> if the supplied value is an xml or html node, otherwise <c>false</c>.
     */
    isNode: function (value)
    {
        return this.isObject(value) && (value.nodeType != null);
    },

    /**
     * Returns a value indicating whether the supplied value is an HTML element.
     * @return {Boolean} <c>true</c> if the supplied value is an HTML element, otherwise <c>false</c>.
     */
    isHtmlElement: function (value)
    {
        return this.isElement(value) && (value.style != null);
    },

    /**
     * Returns a value indicating whether the supplied value is a window.
     * @return {Boolean} <c>true</c> if the supplied value is a window, otherwise <c>false</c>.
     */
    isWindow: function (value)
    {
        return this.isObject(value) && value.window == value;
    },

    /**
     * Returns a value indicating whether the supplied value is an element.
     * @return {Boolean} <c>true</c> if the supplied value is an element, otherwise <c>false</c>.
     */
    isElement: function (value)
    {
        return this.isNode(value) && (value.nodeType == 1);
    },

    /**
     * Returns a value indicating whether the supplied value is an xml or html document.
     * @return {Boolean} <c>true</c> if the supplied value is an xml or html document, otherwise <c>false</c>.
     */
    isDocument: function (value)
    {
        return this.isNode(value) && (value.nodeType == 9);
    },

    toNumber: function (value)
    {
        return Number(this.toNumberString(value));
    },

    toNumberString: function (value)
    {
        if (isNaN(Number(value)) && this.isString(value))
        {
            // convert japanese numbers
            return value.replace(/[\u3002\uff0e\uff0d\uff61\uff10-\uff19]/g, function(c)
            {
                var charCode = c.charCodeAt(0);
                switch (charCode)
                {
                    case 0x3002:
                    case 0xff0e:
                    case 0xff61:
                        return ".";
                    case 0xff0d:
                        return "-";
                    default:
                        return charCode - 0xff10;	//0xff10 -> '0'
                }
            });
        }
        return value;
    }
};
