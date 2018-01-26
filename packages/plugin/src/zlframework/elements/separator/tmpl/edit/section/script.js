/* ===================================================
 * ZOOtools Separator Section
 * https://zoolanders.com/extensions/zootools
 * ===================================================
 * Copyright (C) JOOlanders SL
 * http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 * ========================================================== */
(function ($) {
  var Plugin = function(){};
  Plugin.prototype = $.extend(Plugin.prototype, {
    name: 'ZOOtoolsSeparatorSection',
    options: {
      title: '',
      folding: 0 // 0 = disabled, 1 = start unfolded, 2 = start folded
    },
    initialize: function(element, options) {
      this.options = $.extend({}, this.options, options);
      var $this = this;

      // save separator content in cache
      var separator = $('<div class="zl-separator-section zl-bootstrap" />')
        title = $('<span class="zlux-x-title">'+$this.options.title+'</span>'),


      // set title
      title.appendTo(separator);

      // replace element by separator
      element.parent().before(separator).remove();

      // perform when other separators ready
      $(document).ready(function()
      {
        var  nextSeparator = separator.nextAll('.zl-separator-section').first(),
          childs = separator.nextUntil(nextSeparator, '.element'); // match only elements

        // if start folded on
        ($this.options.folding == 2) && childs.hide();

        // set fold button
        if($this.options.folding){

          var iconClass = $this.options.folding == 2 ? 'open' : 'close',
            btnFold = $('<a class="btn btn-small fold" href="#"><i class="icon-eye-'+iconClass+'"></i></a>')

          // append
          .appendTo(separator)

          // set actions
          .on('click', function(e){
            e.preventDefault();
            childs.toggle();
            btnFold.find('i').toggleClass('icon-eye-open icon-eye-close');
          });
        }
      });
    },
    exFunc: function() {
      var $this = this;
    }
  });
  // Don't touch
  $.fn[Plugin.prototype.name] = function() {
    var args   = arguments;
    var method = args[0] ? args[0] : null;
    return this.each(function() {
      var element = $(this);
      if (Plugin.prototype[method] && element.data(Plugin.prototype.name) && method != 'initialize') {
        element.data(Plugin.prototype.name)[method].apply(element.data(Plugin.prototype.name), Array.prototype.slice.call(args, 1));
      } else if (!method || $.isPlainObject(method)) {
        var plugin = new Plugin();
        if (Plugin.prototype['initialize']) {
          plugin.initialize.apply(plugin, $.merge([element], args));
        }
        element.data(Plugin.prototype.name, plugin);
      } else {
        $.error('Method ' +  method + ' does not exist on jQuery.' + Plugin.name);
      }
    });
  };
})(jQuery);
