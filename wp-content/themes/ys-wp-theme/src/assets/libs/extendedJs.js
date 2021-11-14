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
