/**
 *  Adds classList object with add, remove and toggle methods into NodeList
 */
function ClassListForNodeList(nodeList) {
    return {
        add   : function (className) {
            for (let elIndex = nodeList.length; elIndex--;) {
                for (var i = arguments.length; i--; nodeList[elIndex].classList.add(arguments[i])) ;
            }
        },
        remove: function (className) {
            for (var elIndex = nodeList.length; elIndex--;) {
                for (var i = arguments.length; i--; nodeList[elIndex].classList.remove(arguments[i])) ;
            }
        },
        toggle: function (className, condition) {
            for (var elIndex = nodeList.length; elIndex--; nodeList[elIndex].classList.toggle(className, condition)) ;
        }
    }
}

let classListObj = {
    get: function () {
        return this.classList = new ClassListForNodeList(this);
    },
    set: function () {}
};

!NodeList.prototype.classList && Object.defineProperty(NodeList.prototype, 'classList', classListObj);
!HTMLCollection.prototype.classList && Object.defineProperty(HTMLCollection.prototype, 'classList', classListObj);

NodeList.prototype.indexOf = HTMLCollection.prototype.indexOf = function (element) {
    return element instanceof Element ? Array.prototype.indexOf.call(this, element) : -1;
};

HTMLElement.prototype.fadeIn = function (duration = 1000, animationEnd = () => {}) {
    this.classList.remove('is-hidden');
    this.style.opacity = '0';
    let opacity = 0,
        step    = 1 / (duration / 10),
        timer   = setInterval(() => {
            if (opacity < 1) {
                this.style.opacity = opacity;
                opacity += step;
                return;
            }

            clearInterval(timer);
            this.style.opacity = '1';
            typeof animationEnd === 'function' && animationEnd();
        }, 10);
}

HTMLElement.prototype.fadeOut = function (duration = 1000, animationEnd = () => {}) {
    let opacity = 1,
        step    = 1 / (duration / 10),
        timer   = setInterval(() => {
            if (opacity > 0) {
                this.style.opacity = opacity;
                opacity -= step;
                return;
            }

            clearInterval(timer);
            this.classList.add('is-hidden');
            this.style.opacity = '0';
            typeof animationEnd === 'function' && animationEnd();
        }, 10);
}