!function(window, undefined) {
  var $, win, ready = {
    getPath:function() {
      var js = document.scripts, script = js[js.length - 1], jsPath = script.src;
      if (script.getAttribute("merge")) {
        return;
      }
      return jsPath.substring(0, jsPath.lastIndexOf("/") + 1);
    }(),
    config:{},
    end:{},
    minIndex:0,
    minLeft:[],
    btn:[ "&#x786E;&#x5B9A;", "&#x53D6;&#x6D88;" ],
    type:[ "dialog", "page", "iframe", "loading" ]
  };
  var layer = {
    v:"3.0.1",
    ie:function() {
      var agent = navigator.userAgent.toLowerCase();
      return !!window.ActiveXObject || "ActiveXObject" in window ? (agent.match(/msie\s(\d+)/) || [])[1] || "11" :false;
    }(),
    index:window.layer && window.layer.v ? 1e5 :0,
    path:ready.getPath,
    config:function(options, fn) {
      options = options || {};
      layer.cache = ready.config = $.extend({}, ready.config, options);
      layer.path = ready.config.path || layer.path;
      typeof options.extend === "string" && (options.extend = [ options.extend ]);
      if (ready.config.path) {
        layer.ready();
      }
      if (!options.extend) {
        return this;
      }
      layer.link("skin/" + options.extend);
      return this;
    },
    link:function(href, fn, cssname) {
      if (!layer.path) {
        return;
      }
      var head = $("head")[0], link = document.createElement("link");
      if (typeof fn === "string") {
        cssname = fn;
      }
      var app = (cssname || href).replace(/\.|\//g, "");
      var id = "layercss-" + app, timeout = 0;
      link.rel = "stylesheet";
      link.href = layer.path + href;
      link.id = id;
      if (!$("#" + id)[0]) {
        head.appendChild(link);
      }
      if (typeof fn !== "function") {
        return;
      }
      (function poll() {
        if (++timeout > 8 * 1e3 / 100) {
          return window.console && console.error("layer.css: Invalid");
        }
        parseInt($("#" + id).css("width")) === 1989 ? fn() :setTimeout(poll, 100);
      })();
    },
    ready:function(callback) {
      var cssname = "skinlayercss", ver = "1110";
      layer.link("skin/default/layer.min.css?v=" + layer.v + ver, callback, cssname);
      return this;
    },
    alert:function(content, options, yes) {
      var type = typeof options === "function";
      if (type) {
        yes = options;
      }
      return layer.open($.extend({
        content:content,
        yes:yes
      }, type ? {} :options));
    },
    confirm:function(content, options, yes, cancel) {
      var type = typeof options === "function";
      if (type) {
        cancel = yes;
        yes = options;
      }
      return layer.open($.extend({
        content:content,
        btn:ready.btn,
        yes:yes,
        btn2:cancel
      }, type ? {} :options));
    },
    msg:function(content, options, end) {
      var type = typeof options === "function", rskin = ready.config.skin;
      var skin = (rskin ? rskin + " " + rskin + "-msg" :"") || "layer-msg";
      var anim = doms.anim.length - 1;
      if (type) {
        end = options;
      }
      return layer.open($.extend({
        content:content,
        time:3e3,
        shade:false,
        skin:skin,
        title:false,
        closeBtn:false,
        btn:false,
        resize:false,
        end:end
      }, type && !ready.config.skin ? {
        skin:skin + " layer-hui",
        anim:anim
      } :function() {
        options = options || {};
        if (options.icon === -1 || options.icon === undefined && !ready.config.skin) {
          options.skin = skin + " " + (options.skin || "layer-hui");
        }
        return options;
      }()));
    },
    load:function(icon, options) {
      return layer.open($.extend({
        type:3,
        icon:icon || 0,
        resize:false,
        shade:.01
      }, options));
    }
  };
  var Class = function(setings) {
    var that = this;
    that.index = ++layer.index;
    that.config = $.extend({}, that.config, ready.config, setings);
    document.body ? that.creat() :setTimeout(function() {
      that.creat();
    }, 50);
  };
  Class.pt = Class.prototype;
  var doms = [ "layer", ".layer-title", ".layer-main", ".layer-dialog", "layer-iframe", "layer-content", "layer-btn", "layer-close" ];
  doms.anim = [ "layer-anim", "layer-anim-01", "layer-anim-02", "layer-anim-03", "layer-anim-04", "layer-anim-05", "layer-anim-06" ];
  Class.pt.config = {
    type:0,
    shade:.3,
    fixed:true,
    move:doms[1],
    title:"&#x4FE1;&#x606F;",
    offset:"auto",
    area:"auto",
    closeBtn:1,
    time:0,
    zIndex:19891014,
    maxWidth:360,
    anim:0,
    icon:-1,
    moveType:1,
    resize:true,
    scrollbar:true
  };
  Class.pt.vessel = function(conType, callback) {
    var that = this, times = that.index, config = that.config;
    var zIndex = config.zIndex + times, titype = typeof config.title === "object";
    var ismax = config.maxmin && (config.type === 1 || config.type === 2);
    var titleHTML = config.title ? '<div class="layer-title" style="' + (titype ? config.title[1] :"") + '">' + (titype ? config.title[0] :config.title) + "</div>" :"";
    config.zIndex = zIndex;
    callback([ config.shade ? '<div class="layer-shade" id="layer-shade' + times + '" times="' + times + '" style="' + ("z-index:" + (zIndex - 1) + "; background-color:" + (config.shade[1] || "#000") + "; opacity:" + (config.shade[0] || config.shade) + "; filter:alpha(opacity=" + (config.shade[0] * 100 || config.shade * 100) + ");") + '"></div>' :"", '<div class="' + doms[0] + (" layer-" + ready.type[config.type]) + ((config.type == 0 || config.type == 2) && !config.shade ? " layer-border" :"") + " " + (config.skin || "") + '" id="' + doms[0] + times + '" type="' + ready.type[config.type] + '" times="' + times + '" showtime="' + config.time + '" conType="' + (conType ? "object" :"string") + '" style="z-index: ' + zIndex + "; width:" + config.area[0] + ";height:" + config.area[1] + (config.fixed ? "" :";position:absolute;") + '">' + (conType && config.type != 2 ? "" :titleHTML) + '<div id="' + (config.id || "") + '" class="layer-content' + (config.type == 0 && config.icon !== -1 ? " layer-padding" :"") + (config.type == 3 ? " layer-loading" + config.icon :"") + '">' + (config.type == 0 && config.icon !== -1 ? '<i class="layer-ico layer-ico' + config.icon + '"></i>' :"") + (config.type == 1 && conType ? "" :config.content || "") + "</div>" + '<span class="layer-setwin">' + function() {
      var closebtn = ismax ? '<a class="layer-min" href="javascript:;"><cite></cite></a><a class="layer-ico layer-max" href="javascript:;"></a>' :"";
      config.closeBtn && (closebtn += '<a class="layer-ico ' + doms[7] + " " + doms[7] + (config.title ? config.closeBtn :"2") + '" href="javascript:;"></a>');
      return closebtn;
    }() + "</span>" + (config.btn ? function() {
      var button = "";
      typeof config.btn === "string" && (config.btn = [ config.btn ]);
      for (var i = 0, len = config.btn.length; i < len; i++) {
        button += '<a class="' + doms[6] + "" + i + '" href="javascript:void(0);">' + config.btn[i] + "</a>";
      }
      return '<div class="' + doms[6] + ' layer-btn-c" href="javascript:void(0);">' + button + "</div>";
    }() :"") + (config.resize ? '<span class="layer-resize"></span>' :"") + "</div>" ], titleHTML, $('<div class="layer-move"></div>'));
    return that;
  };
  Class.pt.creat = function() {
    var that = this, config = that.config, times = that.index, nodeIndex, content = config.content, conType = typeof content === "object", body = $("body");
    if ($("#" + config.id)[0]) {
      return;
    }
    if (typeof config.area === "string") {
      config.area = config.area === "auto" ? [ "", "" ] :[ config.area, "" ];
    }
    if (config.shift) {
      config.anim = config.shift;
    }
    if (layer.ie == 6) {
      config.fixed = false;
    }
    switch (config.type) {
     case 0:
      config.btn = "btn" in config ? config.btn :ready.btn[0];
      layer.closeAll("dialog");
      break;

     case 2:
      var content = config.content = conType ? config.content :[ config.content, "auto" ];
      config.content = '<iframe scrolling="' + (config.content[1] || "auto") + '" allowtransparency="true" id="' + doms[4] + "" + times + '" name="' + doms[4] + "" + times + '" onload="this.className=\'\';" class="layer-load" frameborder="0" src="' + config.content[0] + '"></iframe>';
      break;

     case 3:
      delete config.title;
      delete config.closeBtn;
      config.icon === -1 && config.icon === 0;
      layer.closeAll("loading");
      break;
    }
    that.vessel(conType, function(html, titleHTML, moveElem) {
      body.append(html[0]);
      conType ? function() {
        config.type == 2 ? function() {
          $("body").append(html[1]);
        }() :function() {
          if (!content.parents("." + doms[0])[0]) {
            content.data("display", content.css("display")).show().addClass("layer-wrap").wrap(html[1]);
            $("#" + doms[0] + times).find("." + doms[5]).before(titleHTML);
          }
        }();
      }() :body.append(html[1]);
      $(".layer-move")[0] || body.append(ready.moveElem = moveElem);
      that.layero = $("#" + doms[0] + times);
      config.scrollbar || doms.html.css("overflow", "hidden").attr("layer-full", times);
    }).auto(times);
    config.type == 2 && layer.ie == 6 && that.layero.find("iframe").attr("src", content[0]);
    that.offset();
    if (config.fixed) {
      win.on("resize", function() {
        that.offset();
        (/^\d+%$/.test(config.area[0]) || /^\d+%$/.test(config.area[1])) && that.auto(times);
      });
    }
    config.time <= 0 || setTimeout(function() {
      layer.close(that.index);
    }, config.time);
    that.move().callback();
    if (doms.anim[config.anim]) {
      that.layero.addClass(doms.anim[config.anim]).data("anim", true);
    }
  };
  Class.pt.auto = function(index) {
    var that = this, config = that.config, layero = $("#" + doms[0] + index);
    if (config.area[0] === "" && config.maxWidth > 0) {
      if (layer.ie && layer.ie < 8 && config.btn) {
        layero.width(layero.innerWidth());
      }
      layero.outerWidth() > config.maxWidth && layero.width(config.maxWidth);
    }
    var area = [ layero.innerWidth(), layero.innerHeight() ];
    var titHeight = layero.find(doms[1]).outerHeight() || 0;
    var btnHeight = layero.find("." + doms[6]).outerHeight() || 0;
    function setHeight(elem) {
      elem = layero.find(elem);
      elem.height(area[1] - titHeight - btnHeight - 2 * (parseFloat(elem.css("padding")) | 0));
    }
    switch (config.type) {
     case 2:
      setHeight("iframe");
      break;

     default:
      if (config.area[1] === "") {
        if (config.fixed && area[1] >= win.height()) {
          area[1] = win.height();
          setHeight("." + doms[5]);
        }
      } else {
        setHeight("." + doms[5]);
      }
      break;
    }
    return that;
  };
  Class.pt.offset = function() {
    var that = this, config = that.config, layero = that.layero;
    var area = [ layero.outerWidth(), layero.outerHeight() ];
    var type = typeof config.offset === "object";
    that.offsetTop = (win.height() - area[1]) / 2;
    that.offsetLeft = (win.width() - area[0]) / 2;
    if (type) {
      that.offsetTop = config.offset[0];
      that.offsetLeft = config.offset[1] || that.offsetLeft;
    }
    if (!config.fixed) {
      that.offsetTop = /%$/.test(that.offsetTop) ? win.height() * parseFloat(that.offsetTop) / 100 :parseFloat(that.offsetTop);
      that.offsetLeft = /%$/.test(that.offsetLeft) ? win.width() * parseFloat(that.offsetLeft) / 100 :parseFloat(that.offsetLeft);
      that.offsetTop += win.scrollTop();
      that.offsetLeft += win.scrollLeft();
    }
    if (layero.attr("minLeft")) {
      that.offsetTop = win.height() - (layero.find(doms[1]).outerHeight() || 0);
      that.offsetLeft = layero.css("left");
    }
    layero.css({
      top:that.offsetTop,
      left:that.offsetLeft
    });
  };
  Class.pt.move = function() {
    var that = this, config = that.config, _DOC = $(document), layero = that.layero, moveElem = layero.find(config.move), resizeElem = layero.find(".layer-resize"), dict = {};
    if (config.move) {
      moveElem.css("cursor", "move");
    }
    moveElem.on("mousedown", function(e) {
      e.preventDefault();
      if (config.move) {
        dict.moveStart = true;
        dict.offset = [ e.clientX - parseFloat(layero.css("left")), e.clientY - parseFloat(layero.css("top")) ];
        ready.moveElem.css("cursor", "move").show();
      }
    });
    resizeElem.on("mousedown", function(e) {
      e.preventDefault();
      dict.resizeStart = true;
      dict.offset = [ e.clientX, e.clientY ];
      dict.area = [ layero.outerWidth(), layero.outerHeight() ];
      ready.moveElem.css("cursor", "se-resize").show();
    });
    _DOC.on("mousemove", function(e) {
      if (dict.moveStart) {
        var X = e.clientX - dict.offset[0], Y = e.clientY - dict.offset[1], fixed = layero.css("position") === "fixed";
        e.preventDefault();
        dict.stX = fixed ? 0 :win.scrollLeft();
        dict.stY = fixed ? 0 :win.scrollTop();
        if (!config.moveOut) {
          var setRig = win.width() - layero.outerWidth() + dict.stX, setBot = win.height() - layero.outerHeight() + dict.stY;
          X < dict.stX && (X = dict.stX);
          X > setRig && (X = setRig);
          Y < dict.stY && (Y = dict.stY);
          Y > setBot && (Y = setBot);
        }
        layero.css({
          left:X,
          top:Y
        });
      }
      if (config.resize && dict.resizeStart) {
        var X = e.clientX - dict.offset[0], Y = e.clientY - dict.offset[1];
        e.preventDefault();
        layer.style(that.index, {
          width:dict.area[0] + X,
          height:dict.area[1] + Y
        });
        dict.isResize = true;
      }
    }).on("mouseup", function(e) {
      if (dict.moveStart) {
        delete dict.moveStart;
        ready.moveElem.hide();
        config.moveEnd && config.moveEnd();
      }
      if (dict.resizeStart) {
        delete dict.resizeStart;
        ready.moveElem.hide();
      }
    });
    return that;
  };
  Class.pt.callback = function() {
    var that = this, layero = that.layero, config = that.config;
    that.openLayer();
    if (config.success) {
      if (config.type == 2) {
        layero.find("iframe").on("load", function() {
          config.success(layero, that.index);
        });
      } else {
        config.success(layero, that.index);
      }
    }
    layer.ie == 6 && that.IE6(layero);
    layero.find("." + doms[6]).children("a").on("click", function() {
      var index = $(this).index();
      if (index === 0) {
        if (config.yes) {
          config.yes(that.index, layero);
        } else {
          if (config["btn1"]) {
            config["btn1"](that.index, layero);
          } else {
            layer.close(that.index);
          }
        }
      } else {
        var close = config["btn" + (index + 1)] && config["btn" + (index + 1)](that.index, layero);
        close === false || layer.close(that.index);
      }
    });
    function cancel() {
      var close = config.cancel && config.cancel(that.index, layero);
      close === false || layer.close(that.index);
    }
    layero.find("." + doms[7]).on("click", cancel);
    if (config.shadeClose) {
      $("#layer-shade" + that.index).on("click", function() {
        layer.close(that.index);
      });
    }
    layero.find(".layer-min").on("click", function() {
      var min = config.min && config.min(layero);
      min === false || layer.min(that.index, config);
    });
    layero.find(".layer-max").on("click", function() {
      if ($(this).hasClass("layer-maxmin")) {
        layer.restore(that.index);
        config.restore && config.restore(layero);
      } else {
        layer.full(that.index, config);
        setTimeout(function() {
          config.full && config.full(layero);
        }, 100);
      }
    });
    config.end && (ready.end[that.index] = config.end);
  };
  ready.reselect = function() {
    $.each($("select"), function(index, value) {
      var sthis = $(this);
      if (!sthis.parents("." + doms[0])[0]) {
        sthis.attr("layer") == 1 && $("." + doms[0]).length < 1 && sthis.removeAttr("layer").show();
      }
      sthis = null;
    });
  };
  Class.pt.IE6 = function(layero) {
    $("select").each(function(index, value) {
      var sthis = $(this);
      if (!sthis.parents("." + doms[0])[0]) {
        sthis.css("display") === "none" || sthis.attr({
          layer:"1"
        }).hide();
      }
      sthis = null;
    });
  };
  Class.pt.openLayer = function() {
    var that = this;
    layer.zIndex = that.config.zIndex;
    layer.setTop = function(layero) {
      var setZindex = function() {
        layer.zIndex++;
        layero.css("z-index", layer.zIndex + 1);
      };
      layer.zIndex = parseInt(layero[0].style.zIndex);
      layero.on("mousedown", setZindex);
      return layer.zIndex;
    };
  };
  ready.record = function(layero) {
    var area = [ layero.width(), layero.height(), layero.position().top, layero.position().left + parseFloat(layero.css("margin-left")) ];
    layero.find(".layer-max").addClass("layer-maxmin");
    layero.attr({
      area:area
    });
  };
  ready.rescollbar = function(index) {
    if (doms.html.attr("layer-full") == index) {
      if (doms.html[0].style.removeProperty) {
        doms.html[0].style.removeProperty("overflow");
      } else {
        doms.html[0].style.removeAttribute("overflow");
      }
      doms.html.removeAttr("layer-full");
    }
  };
  window.layer = layer;
  layer.getChildFrame = function(selector, index) {
    index = index || $("." + doms[4]).attr("times");
    return $("#" + doms[0] + index).find("iframe").contents().find(selector);
  };
  layer.getFrameIndex = function(name) {
    return $("#" + name).parents("." + doms[4]).attr("times");
  };
  layer.iframeAuto = function(index) {
    if (!index) {
      return;
    }
    var heg = layer.getChildFrame("html", index).outerHeight();
    var layero = $("#" + doms[0] + index);
    var titHeight = layero.find(doms[1]).outerHeight() || 0;
    var btnHeight = layero.find("." + doms[6]).outerHeight() || 0;
    layero.css({
      height:heg + titHeight + btnHeight
    });
    layero.find("iframe").css({
      height:heg
    });
  };
  layer.iframeSrc = function(index, url) {
    $("#" + doms[0] + index).find("iframe").attr("src", url);
  };
  layer.style = function(index, options, limit) {
    var layero = $("#" + doms[0] + index), contElem = layero.find(".layer-content"), type = layero.attr("type"), titHeight = layero.find(doms[1]).outerHeight() || 0, btnHeight = layero.find("." + doms[6]).outerHeight() || 0, minLeft = layero.attr("minLeft");
    if (type === ready.type[3] || type === ready.type[4]) {
      return;
    }
    if (!limit) {
      if (parseFloat(options.width) <= 260) {
        options.width = 260;
      }
      if (parseFloat(options.height) - titHeight - btnHeight <= 64) {
        options.height = 64 + titHeight + btnHeight;
      }
    }
    layero.css(options);
    btnHeight = layero.find("." + doms[6]).outerHeight();
    if (type === ready.type[2]) {
      layero.find("iframe").css({
        height:parseFloat(options.height) - titHeight - btnHeight
      });
    } else {
      contElem.css({
        height:parseFloat(options.height) - titHeight - btnHeight - parseFloat(contElem.css("padding-top")) - parseFloat(contElem.css("padding-bottom"))
      });
    }
  };
  layer.min = function(index, options) {
    var layero = $("#" + doms[0] + index), titHeight = layero.find(doms[1]).outerHeight() || 0, left = layero.attr("minLeft") || 181 * ready.minIndex + "px", position = layero.css("position");
    ready.record(layero);
    if (ready.minLeft[0]) {
      left = ready.minLeft[0];
      ready.minLeft.shift();
    }
    layero.attr("position", position);
    layer.style(index, {
      width:180,
      height:titHeight,
      left:left,
      top:win.height() - titHeight,
      position:"fixed",
      overflow:"hidden"
    }, true);
    layero.find(".layer-min").hide();
    layero.attr("type") === "page" && layero.find(doms[4]).hide();
    ready.rescollbar(index);
    if (!layero.attr("minLeft")) {
      ready.minIndex++;
    }
    layero.attr("minLeft", left);
  };
  layer.restore = function(index) {
    var layero = $("#" + doms[0] + index), area = layero.attr("area").split(",");
    var type = layero.attr("type");
    layer.style(index, {
      width:parseFloat(area[0]),
      height:parseFloat(area[1]),
      top:parseFloat(area[2]),
      left:parseFloat(area[3]),
      position:layero.attr("position"),
      overflow:"visible"
    }, true);
    layero.find(".layer-max").removeClass("layer-maxmin");
    layero.find(".layer-min").show();
    layero.attr("type") === "page" && layero.find(doms[4]).show();
    ready.rescollbar(index);
  };
  layer.full = function(index) {
    var layero = $("#" + doms[0] + index), timer;
    ready.record(layero);
    if (!doms.html.attr("layer-full")) {
      doms.html.css("overflow", "hidden").attr("layer-full", index);
    }
    clearTimeout(timer);
    timer = setTimeout(function() {
      var isfix = layero.css("position") === "fixed";
      layer.style(index, {
        top:isfix ? 0 :win.scrollTop(),
        left:isfix ? 0 :win.scrollLeft(),
        width:win.width(),
        height:win.height()
      }, true);
      layero.find(".layer-min").hide();
    }, 100);
  };
  layer.title = function(name, index) {
    var title = $("#" + doms[0] + (index || layer.index)).find(doms[1]);
    title.html(name);
  };
  layer.close = function(index) {
    var layero = $("#" + doms[0] + index), type = layero.attr("type"), closeAnim = "layer-anim-close";
    if (!layero[0]) {
      return;
    }
    var WRAP = "layer-wrap", remove = function() {
      if (type === ready.type[1] && layero.attr("conType") === "object") {
        layero.children(":not(." + doms[5] + ")").remove();
        var wrap = layero.find("." + WRAP);
        for (var i = 0; i < 2; i++) {
          wrap.unwrap();
        }
        wrap.css("display", wrap.data("display")).removeClass(WRAP);
      } else {
        if (type === ready.type[2]) {
          try {
            var iframe = $("#" + doms[4] + index)[0];
            iframe.contentWindow.document.write("");
            iframe.contentWindow.close();
            layero.find("." + doms[5])[0].removeChild(iframe);
          } catch (e) {}
        }
        layero[0].innerHTML = "";
        layero.remove();
      }
    };
    if (layero.data("anim")) {
      layero.addClass(closeAnim);
    }
    $("#layer-moves, #layer-shade" + index).remove();
    layer.ie == 6 && ready.reselect();
    ready.rescollbar(index);
    typeof ready.end[index] === "function" && ready.end[index]();
    delete ready.end[index];
    if (layero.attr("minLeft")) {
      ready.minIndex--;
      ready.minLeft.push(layero.attr("minLeft"));
    }
    setTimeout(function() {
      remove();
    }, layer.ie && layer.ie < 10 || !layero.data("anim") ? 0 :200);
  };
  layer.closeAll = function(type) {
    $.each($("." + doms[0]), function() {
      var othis = $(this);
      var is = type ? othis.attr("type") === type :1;
      is && layer.close(othis.attr("times"));
      is = null;
    });
  };
  var cache = layer.cache || {}, skin = function(type) {
    return cache.skin ? " " + cache.skin + " " + cache.skin + "-" + type :"";
  };
  layer.photos = function(options, loop, key) {
    var dict = {};
    options = options || {};
    if (!options.photos) {
      return;
    }
    var type = options.photos.constructor === Object;
    var photos = type ? options.photos :{}, data = photos.data || [];
    var start = photos.start || 0;
    dict.imgIndex = (start | 0) + 1;
    options.img = options.img || "img";
    if (!type) {
      var parent = $(options.photos), pushData = function() {
        data = [];
        parent.find(options.img).each(function(index) {
          var othis = $(this);
          othis.attr("layer-index", index);
          data.push({
            alt:othis.attr("alt"),
            pid:othis.attr("layer-pid"),
            src:othis.attr("layer-src") || othis.attr("src"),
            thumb:othis.attr("src")
          });
        });
      };
      pushData();
      if (data.length === 0) {
        return;
      }
      loop || parent.on("click", options.img, function() {
        var othis = $(this), index = othis.attr("layer-index");
        layer.photos($.extend(options, {
          photos:{
            start:index,
            data:data,
            tab:options.tab
          },
          full:options.full
        }), true);
        pushData();
      });
      if (!loop) {
        return;
      }
    } else {
      if (data.length === 0) {
        return layer.msg("&#x6CA1;&#x6709;&#x56FE;&#x7247;");
      }
    }
    dict.imgprev = function(key) {
      dict.imgIndex--;
      if (dict.imgIndex < 1) {
        dict.imgIndex = data.length;
      }
      dict.tabimg(key);
    };
    dict.imgnext = function(key, errorMsg) {
      dict.imgIndex++;
      if (dict.imgIndex > data.length) {
        dict.imgIndex = 1;
        if (errorMsg) {
          return;
        }
      }
      dict.tabimg(key);
    };
    dict.keyup = function(event) {
      if (!dict.end) {
        var code = event.keyCode;
        event.preventDefault();
        if (code === 37) {
          dict.imgprev(true);
        } else {
          if (code === 39) {
            dict.imgnext(true);
          } else {
            if (code === 27) {
              layer.close(dict.index);
            }
          }
        }
      }
    };
    dict.tabimg = function(key) {
      if (data.length <= 1) {
        return;
      }
      photos.start = dict.imgIndex - 1;
      layer.close(dict.index);
      layer.photos(options, true, key);
    };
    dict.event = function() {
      dict.bigimg.hover(function() {
        dict.imgsee.show();
      }, function() {
        dict.imgsee.hide();
      });
      dict.bigimg.find(".layer-imgprev").on("click", function(event) {
        event.preventDefault();
        dict.imgprev();
      });
      dict.bigimg.find(".layer-imgnext").on("click", function(event) {
        event.preventDefault();
        dict.imgnext();
      });
      $(document).on("keyup", dict.keyup);
    };
    function loadImage(url, callback, error) {
      var img = new Image();
      img.src = url;
      if (img.complete) {
        return callback(img);
      }
      img.onload = function() {
        img.onload = null;
        callback(img);
      };
      img.onerror = function(e) {
        img.onerror = null;
        error(e);
      };
    }
    dict.loadi = layer.load(1, {
      shade:"shade" in options ? false :.9,
      scrollbar:false
    });
    loadImage(data[start].src, function(img) {
      layer.close(dict.loadi);
      dict.index = layer.open($.extend({
        type:1,
        area:function() {
          var imgarea = [ img.width, img.height ];
          var winarea = [ $(window).width() - 100, $(window).height() - 100 ];
          if (!options.full && (imgarea[0] > winarea[0] || imgarea[1] > winarea[1])) {
            var wh = [ imgarea[0] / winarea[0], imgarea[1] / winarea[1] ];
            if (wh[0] > wh[1]) {
              imgarea[0] = imgarea[0] / wh[0];
              imgarea[1] = imgarea[1] / wh[0];
            } else {
              if (wh[0] < wh[1]) {
                imgarea[0] = imgarea[0] / wh[1];
                imgarea[1] = imgarea[1] / wh[1];
              }
            }
          }
          return [ imgarea[0] + "px", imgarea[1] + "px" ];
        }(),
        title:false,
        shade:.9,
        shadeClose:true,
        closeBtn:false,
        move:".layer-phimg img",
        moveType:1,
        scrollbar:false,
        moveOut:true,
        anim:Math.random() * 5 | 0,
        skin:"layer-photos" + skin("photos"),
        content:'<div class="layer-phimg">' + '<img src="' + data[start].src + '" alt="' + (data[start].alt || "") + '" layer-pid="' + data[start].pid + '">' + '<div class="layer-imgsee">' + (data.length > 1 ? '<span class="layer-imguide"><a href="javascript:;" class="layer-iconext layer-imgprev"></a><a href="javascript:;" class="layer-iconext layer-imgnext"></a></span>' :"") + '<div class="layer-imgbar" style="display:' + (key ? "block" :"") + '"><span class="layer-imgtit"><a href="javascript:;">' + (data[start].alt || "") + "</a><em>" + dict.imgIndex + "/" + data.length + "</em></span></div>" + "</div>" + "</div>",
        success:function(layero, index) {
          dict.bigimg = layero.find(".layer-phimg");
          dict.imgsee = layero.find(".layer-imguide,.layer-imgbar");
          dict.event(layero);
          options.tab && options.tab(data[start], layero);
        },
        end:function() {
          dict.end = true;
          $(document).off("keyup", dict.keyup);
        }
      }, options));
    }, function() {
      layer.close(dict.loadi);
      layer.msg("&#x5F53;&#x524D;&#x56FE;&#x7247;&#x5730;&#x5740;&#x5F02;&#x5E38;<br>&#x662F;&#x5426;&#x7EE7;&#x7EED;&#x67E5;&#x770B;&#x4E0B;&#x4E00;&#x5F20;&#xFF1F;", {
        time:3e4,
        btn:[ "&#x4E0B;&#x4E00;&#x5F20;", "&#x4E0D;&#x770B;&#x4E86;" ],
        yes:function() {
          data.length > 1 && dict.imgnext(true, true);
        }
      });
    });
  };
  ready.run = function(_$) {
    $ = _$;
    win = $(window);
    doms.html = $("html");
    layer.open = function(deliver) {
      var o = new Class(deliver);
      return o.index;
    };
  };
  ready.run(window.jQuery);
  layer.ready();
}(window);