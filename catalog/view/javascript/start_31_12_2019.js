"use strict";

function _readOnlyError(name) { throw new Error("\"" + name + "\" is read-only"); }

function _instanceof(left, right) { if (right != null && typeof Symbol !== "undefined" && right[Symbol.hasInstance]) { return !!right[Symbol.hasInstance](left); } else { return left instanceof right; } }

function _classCallCheck(instance, Constructor) { if (!_instanceof(instance, Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }

var values = {};
var plates = [];
var widthOf1Crop;
var canvas, image, drawImage;
var firstTime = true;
var material, nameMaterial;
var timthumbSrc;
var mirror = true;
var changeUrlImage = false;
var checkFill = true;

var Crop =
/*#__PURE__*/
function () {
  function Crop(image) {
    _classCallCheck(this, Crop);

    this.image = image;
  }

  _createClass(Crop, [{
    key: "setValueX",
    value: function setValueX() {
      var numberPlates = parseInt($('#anzahlPlatten').val());
      return drawImage.getDirection() == 'horizontal' ? values.width / numberPlates * 3 : values.width;
    }
  }, {
    key: "setValueY",
    value: function setValueY() {
      var numberPlates = parseInt($('#anzahlPlatten').val());
      return drawImage.getDirection() == 'horizontal' ? values.height : values.height / numberPlates * 3;
    }
  }, {
    key: "getMaxWidth",
    value: function getMaxWidth() {
      var numberPlates = parseInt($('#anzahlPlatten').val());
      return drawImage.getDirection() == 'horizontal' ? widthOf1Crop * numberPlates : values.width;
    }
  }, {
    key: "getMaxHeight",
    value: function getMaxHeight() {
      var numberPlates = parseInt($('#anzahlPlatten').val());
      return drawImage.getDirection() == 'horizontal' ? values.height : values.height / 3 * numberPlates;
    }
  }, {
    key: "isInitCrop",
    value: function isInitCrop() {
      return firstTime;
    }
  }, {
    key: "initCrop",
    value: function initCrop() {
      var parent = this;
      var width = values.width;
      var height = values.height;

      var inputs = {
        x: $('#x'),
        y: $('#y'),
        width: $('#width'),
        height: $('#height')
      },
          fill = function fill() {
        if (parent.isInitCrop()) {
          //values.x = 0;
          //parent.drawLines();
          parent.image.rcrop('resize', width, height, values.x, 0);
          values.y = 0;
          firstTime = false;
        } else {
          firstTime = false;
          checkFill = false;
          values = parent.image.rcrop('getValues'); //drawImage.setAreaDrawCrop();

          drawImage.setAreaDrawCropFill();
          parent.image.rcrop('resize', values.width, values.height, values.x, values.y);
          parent.drawLines();
        }
      };

      this.image.rcrop();
      this.image.on('rcrop-changed rcrop-ready', fill);
      setTimeout(function () {
        parent.drawLines();
        //parent.image.rcrop('resize', width, height, values.x, 0);
      }, 500);
    }
  }, {
    key: "clearAllLines",
    value: function clearAllLines() {
      $('#draw-line').html('');
    }
  }, {
    key: "drawLines",
    value: function drawLines() {
      var numberPlates = parseInt($('#anzahlPlatten').val());
      var widthCrop, width;
      widthCrop = values.width;
      var i, str, xStart, xEnd, yStart, yEnd;
      this.clearAllLines();

      for (i = 1; i < numberPlates; i++) {
        if (new DrawImage().getDirection() == 'horizontal') {
          width = widthCrop / new DrawImage().getTotalWidthPlate() * new DrawImage().getWidthPlate(i - 1);
          xStart = i == 1 ? values.x + width : width;
          xEnd = xStart;
          yStart = values.y;
          yEnd = values.y + values.height;
          str = '<svg height=' + yEnd + ' width=' + xStart + '><line x1=' + xStart + ' y1=' + yStart + ' x2=' + xEnd + ' y2=' + yEnd + ' style="stroke:#999;stroke-width:1;" /></svg>';
          $('#draw-line').append(str);
        } else {
          yStart = i == 1 ? values.y + values.height / 3 : values.height / 3;
          yEnd = yStart;
          xStart = values.x;
          xEnd = values.x + values.width;
          str = '<svg height=' + yEnd + ' width=' + xEnd + '> <line x1=' + xStart + ' y1=' + yStart + ' x2=' + xEnd + ' y2=' + yEnd + ' style="stroke:#fff;stroke-width:1" /></svg>';
          $('#draw-line').append(str);
        }
      } //var str = '<svg> <line x1="0" y1="0" x2="200" y2="200" style="stroke:rgb(255,0,0);stroke-width:2" /></svg>';

    }
  }, {
    key: "rCrop",
    value: function rCrop() {
      var width = values.width;
      var height = values.height;
      var numberPlates = parseInt($('#anzahlPlatten').val());
      this.drawLines();
      this.image.rcrop('resize', width, height, values.x, values.y);
    }
  }, {
    key: "processCutout",
    value: function processCutout() {
      var parent = this;
      $('nav#top').hide();
      $('#myModal').addClass('show');
      setTimeout(function () {
        parent.cutOut();
      }, 1000);
    }
  }, {
    key: "cutOut",
    value: function cutOut() {
      if (this.isInitCrop()) {
        this.initCrop();
      } else {
        this.rCrop();
      }
    }
  }]);

  return Crop;
}();

var DrawImage =
/*#__PURE__*/
function () {
  function DrawImage(canvas, image) {
    _classCallCheck(this, DrawImage);

    this.canvas = canvas;
    this.image = image;
    this.spaceX = 2;
    this.spaceY = 2;
    this.defaultHeightPlate = 2000;
    this.defaultWidthPlate = 900;
    this.minWidth = 100;
    this.maxWidth1500 = 1500;
    this.maxWidth3000 = 3000;
  }

  _createClass(DrawImage, [{
    key: "getWidthAreaDraw",
    value: function getWidthAreaDraw() {
      return $('.drawimage').width();
    }
  }, {
    key: "getHeightcanvas",
    value: function getHeightcanvas() {
      var i,
          width = 0,
          height,
          percent = 0.7;
      height = plates[0].y;

      for (i = 0; i < plates.length; i++) {
        width = width + plates[i].x; //height = height + plates[i].y;
      }

      if (width < height) return this.getWidthAreaDraw() * percent;else return this.getWidthAreaDraw() / width * height * percent;
    }
  }, {
    key: "getWidthcanvas",
    value: function getWidthcanvas() {
      var i,
          width = 0,
          height;
      height = plates[0].y;

      for (i = 0; i < plates.length; i++) {
        width = width + plates[i].x; //height = height + plates[i].y;
      }

      if (width < height) return this.getWidthAreaDraw() / height * width;else return this.getWidthAreaDraw();
    }
  }, {
    key: "getWidthPlate",
    value: function getWidthPlate(number) {
      var i,
          width = 0;

      for (i = 0; i < plates.length; i++) {
        width = width + plates[i].x;
      }

      return Math.round(this.getWidthcanvas() / width * plates[number].x);
    }
  }, {
    key: "getTotalWidthPlate",
    value: function getTotalWidthPlate() {
      var i,
          width = 0;

      for (i = 0; i < plates.length; i++) {
        width = width + this.getWidthPlate(i);
      }

      return width;
    }
  }, {
    key: "setValuewidthOf1Crop",
    value: function setValuewidthOf1Crop() {
      var numberPlates = parseInt($('#anzahlPlatten').val());
      var width = this.image.width;

      if (widthOf1Crop * numberPlates >= width) {
        values.x = 0;
        widthOf1Crop = Math.round(width / numberPlates);
      } else if (widthOf1Crop * numberPlates + values.x >= width) {
        values.x = width - widthOf1Crop * numberPlates;
      }
    }
  }, {
    key: "getWidthCrop",
    value: function getWidthCrop(number) {
      var i,
          width = 0;
      var numberPlates = parseInt($('#anzahlPlatten').val());

      for (i = 0; i < plates.length; i++) {
        width = width + this.getWidthPlate(i);
      } //return Math.round(((widthOf1Crop * numberPlates)/width) * this.getWidthPlate(number));


      return Math.round(values.width / width * this.getWidthPlate(number));
    }
  }, {
    key: "getDirection",
    value: function getDirection() {
      return $('input[type=radio][name=Ausrichgung]:checked').attr('value');
    }
  }, {
    key: "clear",
    value: function clear() {
      var ctx = this.canvas.getContext('2d');
      ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
      var w = canvas.width;
      canvas.width = 1;
      canvas.width = w;
      canvas.height = 600;
    }
  }, {
    key: "drawImage",
    value: function drawImage(sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight) {
      var _this = this;

      var ctx = this.canvas.getContext('2d'); //ctx.drawImage(this.image, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight);

      this.image.addEventListener('load', function (e) {
        ctx.drawImage(_this.image, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight); //ctx.rotate(90 * Math.PI/180);
      });
    }
  }, {
    key: "drawImage1",
    value: function drawImage1(sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight) {
      var ctx = this.canvas.getContext('2d');
      ctx.setTransform(1, 0, 0, 1, 0, 0);
      ctx.drawImage(this.image, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight);
    }
  }, {
    key: "mirror",
    value: function mirror(sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight) {
      var ctx = this.canvas.getContext('2d');
      ctx.setTransform(-1, 0, 0, 1, this.canvas.width, 0);
      ctx.drawImage(this.image, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight);
    }
  }, {
    key: "mirror1",
    value: function mirror1(canvas, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight) {
      var ctx = canvas.getContext('2d');
      ctx.setTransform(-1, 0, 0, 1, canvas.width, 0);
      ctx.drawImage(this.image, sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight);
    }
  }, {
    key: "setValueStartOfWidthHeight",
    value: function setValueStartOfWidthHeight() {
      var numberPlates = parseInt($('#anzahlPlatten').val());

      switch (numberPlates) {
        case 2:
          if (plates[1].x == 0) {
            plates[1].x = this.defaultWidthPlate;
            plates[1].y = plates[0].y;
            $('#width2').val(plates[1].x);
          }

          plates[2].x = 0;
          plates[2].y = 0;
          break;

        case 3:
          if (plates[2].x == 0) {
            plates[2].x = this.defaultWidthPlate;
            plates[2].y = plates[0].y;
            $('#width3').val(plates[2].x);
          }

          if (plates[1].x == 0) {
            plates[1].x = plates[0].y > plates[0].x ? plates[0].y - plates[0].x : plates[0].x - plates[0].y;
            plates[1].x = plates[1].x == 0 ? 1000 : plates[1].x;
            plates[1].y = plates[0].y;
            $('#width2').val(plates[1].x);
          }

          break;

        default:
          if (plates[0] == undefined) {
            plates[0] = {};
            plates[0].x = this.defaultWidthPlate;
            plates[0].y = this.defaultHeightPlate;
            $('#height1').val(plates[0].y);
            $('#width1').val(plates[0].x);
          } //plates[0] = {};


          plates[1] = {};
          plates[2] = {}; //plates[0].x = this.defaultWidthPlate;
          //plates[0].y = this.defaultHeightPlate;

          plates[1].x = 0;
          plates[1].y = 0;
          plates[2].x = 0;
          plates[2].y = 0;
      }
    }
  }, {
    key: "getQmPlates",
    value: function getQmPlates() {
      var i,
          qm = 0,
          qm_,
          test;

      for (i = 0; i <= 2; i++) {
        qm = qm + plates[i].x * plates[i].y;
      }

      qm = (qm / 1000000).toFixed(6).toString().split('.'); // qm = (qm/1000000).toFixed(2).toString().split('.');
      // qm_ = Math.floor((qm[1])/10).toString();

      var qm2 = qm.toString().replace(',', '.');
      return qm2; // return qm[0] + '.' + qm_;
    }
  }, {
    key: "setAreaDrawCrop",
    value: function setAreaDrawCrop(first) {
      var widthCanvas = this.getWidthcanvas();
      var heightCanvas = this.getHeightcanvas();
      var maxWidthCrop = this.image.width;
      var maxHeightCrop = this.image.height;
      var numberPlates = parseInt($('#anzahlPlatten').val()); //if(numberPlates == 1)
      //values.height = maxHeightCrop;

      values.width = Math.round(widthCanvas * values.height / heightCanvas);

      if (values.width >= maxWidthCrop) {
        values.x = 0;
        values.width = maxWidthCrop;
        values.height = Math.round(heightCanvas * values.width / widthCanvas);
        if (values.height + values.y >= maxHeightCrop) values.y = values.height + values.y - maxHeightCrop;
      } else if (values.width + values.x >= maxWidthCrop) values.x = values.width + values.x - maxWidthCrop;else if (first) {
        values.x = Math.round((maxWidthCrop - values.width) / 2);
      } else if (checkFill) {
        values.height = maxHeightCrop;
        values.width = Math.round(widthCanvas * values.height / heightCanvas);
        values.x = Math.round((maxWidthCrop - values.width) / 2);
      }
    }
  }, {
    key: "setAreaDrawCropFill",
    value: function setAreaDrawCropFill() {
      var widthCanvas = this.getWidthcanvas();
      var heightCanvas = this.getHeightcanvas();
      var maxWidthCrop = this.image.width;
      var maxHeightCrop = this.image.height;
      values.width = Math.round(widthCanvas * values.height / heightCanvas);

      if (values.width >= maxWidthCrop) {
        values.x = 0;
        values.width = maxWidthCrop;
        values.height = Math.round(heightCanvas * values.width / widthCanvas);
        if (values.height + values.y >= maxHeightCrop) values.y = values.height + values.y - maxHeightCrop;
      } else if (values.width + values.x >= maxWidthCrop) values.x = values.width + values.x - maxWidthCrop;
    }
  }, {
    key: "setImgUseTimthumbStart",
    value: function setImgUseTimthumbStart() {
      var dom = $('#source');
      var dom1 = $('#sourcez');
      var width = dom.width();
      var src = dom1.attr('src');
      timthumbSrc = 'timthumb.php?src=' + src + '&w=' + width + '&zc=1';
      this.setImgTimthumb(timthumbSrc);
    }
  }, {
    key: "setImgTimthumb",
    value: function setImgTimthumb(src) {
      var dom = $('#source');
      var dom1 = $('#sourcez');
      dom.attr('src', src);
      dom1.attr('src', src);
    }
  }, {
    key: "setDefaultMaterial",
    value: function setDefaultMaterial() {
      var count = 1;
      var numberPlate = parseInt($('#number_plate').val());
      $('.number_plates .padding_choose_number').each(function (e) {
        if (count == numberPlate) {
          this.click();
          count++;
        }

        count++;
      });
      count = 0;
      $('.checkbox input:radio').each(function (e) {
        if (count == 0) this.click();
        count++;
      });
    }
  }, {
    key: "drawCanvasMirror",
    value: function drawCanvasMirror() {
      var width = this.image.width;
      var height = this.image.height;
      var canvas = document.getElementById('canvas_mirror');
      $('#canvas_mirror').attr('width', '' + width + '');
      $('#canvas_mirror').attr('height', '' + height + '');
      this.mirror1(canvas, 0, 0, width, height, 0, 0, width, height);
      var imagedata = canvas.toDataURL('image/png');
      var imgdata = imagedata.replace(/^data:image\/(png|jpg);base64,/, "");
      var filename = $('#img_mirror').val() + '.png';
      $.ajax({
        url: 'index.php?route=product/product/drawCanvasMirror',
        type: 'post',
        data: {
          imgdata: imgdata,
          filename: filename
        },
        dataType: 'json',
        beforeSend: function beforeSend() {},
        complete: function complete() {
          $('#canvas_mirror').hide();
        },
        success: function success(json) {
          $('#canvas_mirror').hide();
        },
        error: function error(xhr, ajaxOptions, thrownError) {}
      });
    }
  }, {
    key: "processMirror",
    value: function processMirror() {
      var src = $('#source').attr('src'); //src = 'timthumb.php?src='+src+'&w='+width+'&zc=1';

      var image = document.getElementById('sourcez');
      var src_ = 'image/uploads/' + $('#img_mirror').val() + '.png';
      var width = this.image.width;
      var timthumbSrc_ = 'timthumb.php?src=' + src_ + '&w=' + width + '&zc=1';
      if (src == timthumbSrc) this.setImgTimthumb(timthumbSrc_);else this.setImgTimthumb(timthumbSrc);
      image.addEventListener('load', function (e) {
        changeUrlImage = false;
        mirror = true;
        var src = $('#source').attr('src');

        if (src != timthumbSrc) {
          new Crop($('#sourcez')).processCutout();
        }
      });
    }
  }, {
    key: "start",
    value: function start() {
      var _this2 = this;

      this.setImgUseTimthumbStart();
      this.image.addEventListener('load', function (e) {
        if (!changeUrlImage) {
          values.height = _this2.image.height;
          _this2.image.style.display = 'none';
          var left = values.width / 3;

          _this2.drawCanvasMirror();

          if (_this2.getDirection() == 'horizontal') {
            _this2.showImage(true);

            _this2.setDefaultMaterial();

            $('#height').css('top', 10);
            $('#height').css('left', values.width / 3 + 10);
          } else {
            left = (_readOnlyError("left"), x);
            top = y / 3;
            $(canvas).attr('width', left);
            $(canvas).attr('height', top);

            _this2.drawImage(0, 0, left, top, 0, 0, left, top);

            $('#height').css('top', top / 2);
            $('#height').css('left', left + 10);

            _this2.setValueWidthHeight(left, top);
          }
        }
      });
    }
  }, {
    key: "hideWidth",
    value: function hideWidth(position) {
      // hide height input of 2, 3
      $('#height2').hide();
      $('#height3').hide();

      switch (position) {
        case 2:
          $('#width3').hide();
          break;

        case 1:
          $('#width2').hide();
          $('#width3').hide();
          break;
      }
    }
  }, {
    key: "hideHeight",
    value: function hideHeight(position) {
      // hide width input of 2, 3
      $('#width2').hide();
      $('#width3').hide();

      switch (position) {
        case 2:
          $('#height3').hide();
          break;

        case 1:
          $('#height2').hide();
          $('#height3').hide();
          break;
      }
    }
  }, {
    key: "getLeftPlate",
    value: function getLeftPlate(position) {
      var i,
          left = 0,
          width;

      for (i = 0; i < position; i++) {
        width = this.getWidthPlate(i) > 90 ? this.getWidthPlate(i) : 90;
        left = left + width;
      }

      return left;
    }
  }, {
    key: "setPaddingLeftCanvas",
    value: function setPaddingLeftCanvas() {
      var width = $(window).width();
      var paddingLeft = (this.getWidthAreaDraw() - this.getWidthcanvas()) / 2;

      if (width > 786) {
        $(canvas).parent().css('padding-left', '' + paddingLeft + 'px');
        $('#width').css('left', '' + paddingLeft + 'px');
      }
    }
  }, {
    key: "horizontalDirection",
    value: function horizontalDirection() {
      var sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight, marginLeftPlate;
      var numberPlates = parseInt($('#anzahlPlatten').val());
      sHeight = values.height;
      dx = 0;
      dy = 0;
      dHeight = this.getHeightcanvas();
      var i;
      $(canvas).attr('width', this.getWidthcanvas());
      $(canvas).attr('height', this.getHeightcanvas());
      this.setPaddingLeftCanvas();
      this.hideWidth(numberPlates); //set value.x and widthOf1Crop

      this.setValuewidthOf1Crop();
      sx = values.x == undefined ? 0 : values.x;
      sy = values.y == undefined ? 0 : values.y;

      for (i = 0; i < numberPlates; i++) {
        dWidth = this.getWidthPlate(i);
        sWidth = this.getWidthCrop(i);
        if (!changeUrlImage) this.drawImage1(sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight);else this.mirror(sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight); //show width of plate i if i is not first

        if (i != 0) {
          if (parseInt($('#width1').val()) < 500) $('.breiteText').addClass("movedown");else $('.breiteText').removeClass("movedown");
          $('#width' + (i + 1) + '').css('left', this.getLeftPlate(i));
          $('#width' + (i + 1) + '').css('opacity', '1');
          $('#width' + (i + 1) + '').css('display', 'inline-block');
        }

        sx = sx + sWidth;
        dx = dx + dWidth + this.spaceX; //set left position of height input

        $('#height').css('left', dx);
      }
    }
  }, {
    key: "verticalDirection",
    value: function verticalDirection(width, height) {
      var x = width;
      var y = height; //this.setValueStartOfWidthHeight(x,y/3);

      var sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight;
      var numberPlates = parseInt($('#anzahlPlatten').val());
      sWidth = x;
      sHeight = y / 3;
      sx = values.x != undefined ? values.x : 0;
      sy = values.y != undefined ? values.y : 0;
      dx = 0;
      dy = 0;
      dHeight = 0;
      var i; //set height canvas

      for (i = 1; i <= numberPlates; i++) {
        dHeight = parseInt($('#height' + i + '').val());
        dy = dy + dHeight + this.spaceY;
      }

      $(canvas).attr('height', dy);
      $(canvas).attr('width', sWidth); //set left position of height input

      $('#height').css('left', sWidth + 10); //restart dy

      dy = 0; //hide height base on numberPlates

      this.hideHeight(numberPlates);

      for (i = 1; i <= numberPlates; i++) {
        dWidth = parseInt($('#width1').val());
        dHeight = parseInt($('#height' + i + '').val());
        this.drawImage1(sx, sy, sWidth, sHeight, dx, dy, dWidth, dHeight); //show width of plate i if i is not first

        if (i != 1) {
          $('#height' + i + '').css('margin-top', dHeight);
          $('#height' + i + '').css('opacity', '1');
          $('#height' + i + '').show();
        }

        sy = sy + sHeight;
        dy = dy + dHeight + this.spaceY;
      }
    }
  }, {
    key: "setValuePlate",
    value: function setValuePlate() {
      plates[0].y = parseInt($('#height1').val());
      plates[0].x = parseInt($('#width1').val());

      if (plates[1].x != 0) {
        plates[1].x = parseInt($('#width2').val());
        plates[1].y = parseInt($('#height1').val());
      }

      if (plates[2].x != 0) {
        plates[2].x = parseInt($('#width3').val());
        plates[2].y = parseInt($('#height1').val());
      }
    }
  }, {
    key: "checkShowImageWidth",
    value: function checkShowImageWidth() {
      if (parseInt($('#width1').val()) < this.minWidth || parseInt($('#width2').val()) < this.minWidth || parseInt($('#width3').val()) < this.minWidth) return 'Das Mindestmaß beträgt 100 mm';else if (parseInt($('#width1').val()) > this.maxWidth3000 || parseInt($('#width2').val()) > this.maxWidth3000 || parseInt($('#width3').val()) > this.maxWidth3000) return 'Das Höchstmaß beträgt 3000 mm';else if (parseInt($('#width1').val()) > this.maxWidth1500 || parseInt($('#width2').val()) > this.maxWidth1500 || parseInt($('#width3').val()) > this.maxWidth1500) {
        if (parseInt($('#height1').val()) > this.maxWidth1500) return 'Das Höchstmaß beträgt 1500 mm x 3000 mm';else return 1;
      }
      return 1;
    }
  }, {
    key: "checkShowImageHeight",
    value: function checkShowImageHeight() {
      if (parseInt($('#height1').val()) < this.minWidth) return 'Das Mindestmaß beträgt 100 mm';else if (parseInt($('#height1').val()) > this.maxWidth3000) return 'Das Höchstmaß beträgt 3000 mm';else if (parseInt($('#height1').val()) > this.maxWidth1500) {
        if (parseInt($('#width1').val()) > this.maxWidth1500 || parseInt($('#width2').val()) > this.maxWidth1500 || parseInt($('#width3').val()) > this.maxWidth1500) return 'Das Höchstmaß beträgt 1500 mm x 3000 mm';else return 1;
      }
      return 1;
    }
  }, {
    key: "showImage",
    value: function showImage(firstTime) {
      this.setValueStartOfWidthHeight();
      this.setAreaDrawCrop(firstTime);
      if (this.getDirection() == 'horizontal') this.horizontalDirection();else this.verticalDirection(0, 0);
    }
  }, {
    key: "showImageChangeWidth",
    value: function showImageChangeWidth(first) {
      this.setAreaDrawCrop(first);
      if (this.getDirection() == 'horizontal') this.horizontalDirection();else this.verticalDirection(0, 0);
    }
  }, {
    key: "showImageCutout",
    value: function showImageCutout(setValueWidthHeight) {
      var numberPlates = parseInt($('#anzahlPlatten').val());
      var x = values.width;
      var y = values.height;

      if (this.getDirection() == 'horizontal') {
        if (setValueWidthHeight) this.setValueStartOfWidthHeight(x / numberPlates, y);
        this.horizontalDirection(x * 3 / numberPlates, y);
      } else {
        if (setValueWidthHeight) this.setValueStartOfWidthHeight(x, y / numberPlates);
        this.verticalDirection(x, y * 3 / numberPlates);
      }
    }
  }, {
    key: "setPrice",
    value: function setPrice(material_id) {
      var qm = this.getQmPlates();
      $.ajax({
        url: 'index.php?route=product/product/getPriceMaterial',
        type: 'post',
        data: {
          'material_id': material_id,
          'qm': qm
        },
        dataType: 'json',
        beforeSend: function beforeSend() {},
        complete: function complete(obj) {},
        success: function success(result) {
          console.log(' qm: ' + qm + ' - ' + result.price + ' - ' + result.qqm);
          $('.col-3-price .price').show();
          $('.col-3-price .price').html(result.price.replace(".", ",") + '€');
          $('#product_price').val(result.price);
        }
      });
    }
  }, {
    key: "mirrorX",
    value: function mirrorX() {
      var dy = values.y != undefined ? values.y : 0;
      this.mirror(values.x, dy, values.width, values.height, 0, 0, this.canvas.width, this.canvas.height);
    }
  }, {
    key: "notMirrorX",
    value: function notMirrorX() {
      var dy = values.y != undefined ? values.y : 0;
      this.drawImage1(values.x, dy, values.width, values.height, 0, 0, this.canvas.width, this.canvas.height);
    }
  }]);

  return DrawImage;
}();

$(document).ready(function () {
  canvas = document.getElementById('canvas');
  image = document.getElementById('source');
  drawImage = new DrawImage(canvas, image);
  $('#anzahlPlatten').change(function () {
    drawImage.initData();
    drawImage.showImage();
  });
  $('.custom-control-input').click(function () {
    drawImage.showImage();
  });
  $('.drawimage :input').on('input', function (e) {
    var warn = drawImage.checkShowImageWidth();
    checkFill = true;

    if (warn == 1) {
      drawImage.setValuePlate();
      drawImage.showImageChangeWidth(false);
      drawImage.setPrice(material);
      $('#warnW').hide();
    } else {
      $('#warnW').html(warn);
      $('#warnW').show();
    }
  });
  $('#height :input').on('input', function (e) {
    var warn = drawImage.checkShowImageHeight();
    checkFill = true; // console.log(warn);

    if (warn == 1) {
      drawImage.setValuePlate();
      drawImage.showImageChangeWidth(false);
      drawImage.setPrice(material);
      $('#warnW').hide();
    } else {
      $('#warnW').html(warn);
      $('#warnW').show();
    }
  });
  /*
    $('#height :input').on('focusout',function(e){
  	checkMinMax();
      drawImage.setValuePlate();
      drawImage.showImage();
      drawImage.setPrice(material);
    });
  */

  $('.button-cut').click(function () {
    if (changeUrlImage) {
      drawImage.processMirror();
    } else {
      new Crop($('#sourcez')).processCutout();
    }
  });
  $('.change-direction').click(function () {
    if (mirror) {
      mirror = false;
      changeUrlImage = true;
      drawImage.showImage();
    } else {
      mirror = true;
      changeUrlImage = false;
      drawImage.showImage();
    }
  });
  $('.btn-save').click(function () {
    $('nav#top').show();
    drawImage.showImage();
  });
  $('.radio input:radio').change(function () {
    var addition = parseInt($(this).attr('price').replace('$', '').replace('ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬', ''));
    value = value == null ? parseInt($('.price-product li span').html().replace('$', '').replace('ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬', '')) : value;
    var newValue = addition + value;
    $('.price-product li span').html('ÃƒÂ¢Ã¢â‚¬Å¡Ã‚Â¬' + newValue + '');
  });
  $('.checkbox input:radio').change(function () {
    var id = $(this).attr('id');
    material = id;
    nameMaterial = $(this).parent().find('.name').html();
    drawImage.setPrice(id);
  });
  $('.padding_choose_number').click(function () {
    $('.plate_1').css('background', 'url("image/number_plates/1.png")');
    $('.plate_2').css('background', 'url("image/number_plates/2.png")');
    $('.plate_3').css('background', 'url("image/number_plates/3.png")');
    var number = parseInt($(this).html());
    $(this).css('background', 'url("image/number_plates/' + number + '_.png")');
    $('#anzahlPlatten').val(number);
    drawImage.showImage();
    drawImage.setPrice(material);
  });
  $('label.check').click(function () {
    $(this).parent().find('input[type=radio]').click();
  });
  $('#myModal .close').click(function () {
    $('nav#top').show();
  });
});
$(window).load(function () {
  drawImage.start();
});