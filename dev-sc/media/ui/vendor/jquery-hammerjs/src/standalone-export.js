// AMD
if(typeof define == 'function' && define.amd) {
    define(['../.', 'jquery'], setupPlugin);
} else {
    setupPlugin(window.Hammer, window.jQuery || window.Zepto);
}