/**
 *  Attaches one or more space-separated event handlers for element
 *
 *  @param {String}   [selector]      The CSS selector of elements on which will attach event handler
 *  @param {String}   events          Single event or space-separated list of events on which will attach handler
 *  @param {Function} handler         Event handler
 *  @param {Boolean}  [capture=false]
 */
EventTarget.prototype.addEvent = Window.prototype.addEvent = function()
{
    if (arguments.length < 2) {
        return console.error('ExtendedJS: Invalid amount of arguments. Method addEvent needs minimum 2 arguments.');
    }

    var args = Array.prototype.slice.call(arguments, 0, 4);
    typeof args[1] === 'function' && args.unshift(null);
    typeof args[3] === 'undefined' && (args[3] = false);
    args[1] = args[1].split(/\s+/);

    if (typeof args[2] !== 'function') {
        return console.error('ExtendedJS: Invalid callback function.');
    }

    for (var i = args[1].length; i--; ) {
        this.addEventListener(args[1][i], function(e)
        {
            if (args[0]) {
                var element = e.target;
                while (element && element !== this && !element.matches(args[0])) {
                    element = element.parentElement;
                }
                element instanceof Element && element !== this && args[2].call(element, e);
            } else {
                args[2].call(this, e);
            }
        }, args[3]);
    }
};

