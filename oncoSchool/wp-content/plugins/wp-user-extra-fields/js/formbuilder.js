(function() {
  rivets.binders.input = {
    publishes: true,
    routine: rivets.binders.value.routine,
    bind: function(el) {
      return jQuery(el).bind('input.rivets', this.publish);
    },
    unbind: function(el) {
      return jQuery(el).unbind('input.rivets');
    }
  };

  rivets.configure({
    prefix: "rv",
    adapter: {
      subscribe: function(obj, keypath, callback) {
        callback.wrapped = function(m, v) {
          return callback(v);
        };
        return obj.on('change:' + keypath, callback.wrapped);
      },
      unsubscribe: function(obj, keypath, callback) {
        return obj.off('change:' + keypath, callback.wrapped);
      },
      read: function(obj, keypath) {
        if (keypath === "cid") {
          return obj.cid;
        }
        return obj.get(keypath);
      },
      publish: function(obj, keypath, value) {
        if (obj.cid) {
          return obj.set(keypath, value);
        } else {
          return obj[keypath] = value;
        }
      }
    }
  });

}).call(this);

(function() {
  var BuilderView, EditFieldView, Formbuilder, FormbuilderCollection, FormbuilderModel, ViewFieldView, _ref, _ref1, _ref2, _ref3, _ref4,
    __hasProp = {}.hasOwnProperty,
    __extends = function(child, parent) { for (var key in parent) { if (__hasProp.call(parent, key)) child[key] = parent[key]; } function ctor() { this.constructor = child; } ctor.prototype = parent.prototype; child.prototype = new ctor(); child.__super__ = parent.prototype; return child; };

  FormbuilderModel = (function(_super) {
    __extends(FormbuilderModel, _super);

    function FormbuilderModel() {
      _ref = FormbuilderModel.__super__.constructor.apply(this, arguments);
      return _ref;
    }

    FormbuilderModel.prototype.sync = function() {};

    FormbuilderModel.prototype.indexInDOM = function() {
      var $wrapper,
        _this = this;
      $wrapper =jQuery(".fb-field-wrapper").filter((function(_, el) {
        return jQuery(el).data('cid') === _this.cid;
      }));
      return jQuery(".fb-field-wrapper").index($wrapper);
    };

    FormbuilderModel.prototype.is_input = function() {
      return Formbuilder.inputFields[this.get(Formbuilder.options.mappings.FIELD_TYPE)] != null;
    };

    return FormbuilderModel;

  })(Backbone.DeepModel);

  FormbuilderCollection = (function(_super) {
    __extends(FormbuilderCollection, _super);

    function FormbuilderCollection() {
      _ref1 = FormbuilderCollection.__super__.constructor.apply(this, arguments);
      return _ref1;
    }

    FormbuilderCollection.prototype.initialize = function() {
      return this.on('add', this.copyCidToModel);
    };

    FormbuilderCollection.prototype.model = FormbuilderModel;

    FormbuilderCollection.prototype.comparator = function(model) {
      return model.indexInDOM();
    };

    FormbuilderCollection.prototype.copyCidToModel = function(model) {
      return model.attributes.cid = model.cid;
    };

    return FormbuilderCollection;

  })(Backbone.Collection);

  ViewFieldView = (function(_super) {
    __extends(ViewFieldView, _super);

    function ViewFieldView() {
      _ref2 = ViewFieldView.__super__.constructor.apply(this, arguments);
      return _ref2;
    }

    ViewFieldView.prototype.className = "fb-field-wrapper";

    ViewFieldView.prototype.events = {
      'click .subtemplate-wrapper': 'focusEditView',
      'click .js-duplicate': 'duplicate',
      'click .js-clear': 'clear'
    };

    ViewFieldView.prototype.initialize = function(options) {
      this.parentView = options.parentView;
      this.listenTo(this.model, "change", this.render);
      return this.listenTo(this.model, "destroy", this.remove);
    };

    ViewFieldView.prototype.render = function() {
      this.$el.addClass('response-field-' + this.model.get(Formbuilder.options.mappings.FIELD_TYPE)).data('cid', this.model.cid).html(Formbuilder.templates["view/base" + (!this.model.is_input() ? '_non_input' : '')]({
        rf: this.model
      }));
      return this;
    };

    ViewFieldView.prototype.focusEditView = function() {
      return this.parentView.createAndShowEditView(this.model);
    };

    ViewFieldView.prototype.clear = function(e) {
      var cb, x,
        _this = this;
      e.preventDefault();
      e.stopPropagation();
      cb = function() {
        _this.parentView.handleFormUpdate();
        return _this.model.destroy();
      };
      x = Formbuilder.options.CLEAR_FIELD_CONFIRM;
      switch (typeof x) {
        case 'string':
          if (confirm(x)) {
            return cb();
          }
          break;
        case 'function':
          return x(cb);
        default:
          return cb();
      }
    };

    ViewFieldView.prototype.duplicate = function() {
      var attrs;
      attrs = _.clone(this.model.attributes);
      delete attrs['id'];
      attrs['label'] += ' Copy';
      return this.parentView.createField(attrs, {
        position: this.model.indexInDOM() + 1
      });
    };

    return ViewFieldView;

  })(Backbone.View);

  EditFieldView = (function(_super) {
    __extends(EditFieldView, _super);

    function EditFieldView() {
      _ref3 = EditFieldView.__super__.constructor.apply(this, arguments);
      return _ref3;
    }

    EditFieldView.prototype.className = "edit-response-field";

    EditFieldView.prototype.events = {
      'click .js-add-option': 'addOption',
      'click .js-remove-option': 'removeOption',
      'click .js-default-updated': 'defaultUpdated',
      'input .option-label-input': 'forceRender'
    };

    EditFieldView.prototype.initialize = function(options) {
      this.parentView = options.parentView;
      return this.listenTo(this.model, "destroy", this.remove);
    };

    EditFieldView.prototype.render = function() {
      this.$el.html(Formbuilder.templates["edit/base" + (!this.model.is_input() ? '_non_input' : '')]({
        rf: this.model
      }));
      rivets.bind(this.$el, {
        model: this.model
      });
      return this;
    };

    EditFieldView.prototype.remove = function() {
      this.parentView.editView = void 0;
      this.parentView.$el.find("[data-target=\"#addField\"]").click();
      return EditFieldView.__super__.remove.apply(this, arguments);
    };

    EditFieldView.prototype.addOption = function(e) {
      var $el, i, newOption, options;
      $el =jQuery(e.currentTarget);
      i = this.$el.find('.option').index($el.closest('.option'));
      options = this.model.get(Formbuilder.options.mappings.OPTIONS) || [];
      newOption = {
        label: "",
        checked: false
      };
      if (i > -1) {
        options.splice(i + 1, 0, newOption);
      } else {
        options.push(newOption);
      }
      this.model.set(Formbuilder.options.mappings.OPTIONS, options);
      this.model.trigger("change:" + Formbuilder.options.mappings.OPTIONS);
      return this.forceRender();
    };

    EditFieldView.prototype.removeOption = function(e) {
      var $el, index, options;
      $el =jQuery(e.currentTarget);
      index = this.$el.find(".js-remove-option").index($el);
      options = this.model.get(Formbuilder.options.mappings.OPTIONS);
      options.splice(index, 1);
      this.model.set(Formbuilder.options.mappings.OPTIONS, options);
      this.model.trigger("change:" + Formbuilder.options.mappings.OPTIONS);
      return this.forceRender();
    };

    EditFieldView.prototype.defaultUpdated = function(e) {
      var $el;
      $el =jQuery(e.currentTarget);
      if (this.model.get(Formbuilder.options.mappings.FIELD_TYPE) !== 'checkboxes') {
        this.$el.find(".js-default-updated").not($el).attr('checked', false).trigger('change');
      }
      return this.forceRender();
    };

    EditFieldView.prototype.forceRender = function() {
      return this.model.trigger('change');
    };

    return EditFieldView;

  })(Backbone.View);

  BuilderView = (function(_super) {
    __extends(BuilderView, _super);

    function BuilderView() {
      _ref4 = BuilderView.__super__.constructor.apply(this, arguments);
      return _ref4;
    }

    BuilderView.prototype.SUBVIEWS = [];

    BuilderView.prototype.events = {
      'click .js-save-form': 'saveForm',
      'click .fb-tabs a': 'showTab',
      'click .fb-add-field-types a': 'addField',
      'mouseover .fb-add-field-types': 'lockLeftWrapper',
      'mouseout .fb-add-field-types': 'unlockLeftWrapper'
    };

    BuilderView.prototype.initialize = function(options) {
      var selector;
      selector = options.selector, this.formBuilder = options.formBuilder, this.bootstrapData = options.bootstrapData;
      if (selector != null) {
        this.setElement(jQuery(selector));
      }
      this.collection = new FormbuilderCollection;
      this.collection.bind('add', this.addOne, this);
      this.collection.bind('reset', this.reset, this);
      this.collection.bind('change', this.handleFormUpdate, this);
      this.collection.bind('destroy add reset', this.hideShowNoResponseFields, this);
      this.collection.bind('destroy', this.ensureEditViewScrolled, this);
      this.render();
      this.collection.reset(this.bootstrapData);
      return this.bindSaveEvent();
    };

    BuilderView.prototype.bindSaveEvent = function() {
      var _this = this;
      this.formSaved = true;
      this.saveFormButton = this.$el.find(".js-save-form");
      this.saveFormButton.attr('disabled', true).text(Formbuilder.options.dict.ALL_CHANGES_SAVED);
      if (!!Formbuilder.options.AUTOSAVE) {
        setInterval(function() {
          return _this.saveForm.call(_this);
        }, 5000);
      }
      return jQuery(window).bind('beforeunload', function() {
        if (_this.formSaved) {
          return void 0;
        } else {
          return Formbuilder.options.dict.UNSAVED_CHANGES;
        }
      });
    };

    BuilderView.prototype.reset = function() {
      this.$responseFields.html('');
      return this.addAll();
    };

    BuilderView.prototype.render = function() {
      var subview, _i, _len, _ref5;
      this.$el.html(Formbuilder.templates['page']());
      this.$fbLeft = this.$el.find('.fb-left');
      this.$responseFields = this.$el.find('.fb-response-fields');
      this.bindWindowScrollEvent();
      this.hideShowNoResponseFields();
      _ref5 = this.SUBVIEWS;
      for (_i = 0, _len = _ref5.length; _i < _len; _i++) {
        subview = _ref5[_i];
        new subview({
          parentView: this
        }).render();
      }
      return this;
    };

    BuilderView.prototype.bindWindowScrollEvent = function() {
      var _this = this;
      return jQuery(window).on('scroll', function() {
        var maxMargin, newMargin;
        if (_this.$fbLeft.data('locked') === true) {
          return;
        }
        newMargin = Math.max(0,jQuery(window).scrollTop() - _this.$el.offset().top);
        maxMargin = _this.$responseFields.height();
        return _this.$fbLeft.css({
          'margin-top': Math.min(maxMargin, newMargin)
        });
      });
    };

    BuilderView.prototype.showTab = function(e) {
      var $el, first_model, target;
      $el =jQuery(e.currentTarget);
      target = $el.data('target');
      $el.closest('li').addClass('active').siblings('li').removeClass('active');
     jQuery(target).addClass('active').siblings('.fb-tab-pane').removeClass('active');
      if (target !== '#editField') {
        this.unlockLeftWrapper();
      }
      if (target === '#editField' && !this.editView && (first_model = this.collection.models[0])) {
        return this.createAndShowEditView(first_model);
      }
    };

    BuilderView.prototype.addOne = function(responseField, _, options) {
      var $replacePosition, view;
      view = new ViewFieldView({
        model: responseField,
        parentView: this
      });
      if (options.$replaceEl != null) {
        return options.$replaceEl.replaceWith(view.render().el);
      } else if ((options.position == null) || options.position === -1) {
        return this.$responseFields.append(view.render().el);
      } else if (options.position === 0) {
        return this.$responseFields.prepend(view.render().el);
      } else if (($replacePosition = this.$responseFields.find(".fb-field-wrapper").eq(options.position))[0]) {
        return $replacePosition.before(view.render().el);
      } else {
        return this.$responseFields.append(view.render().el);
      }
    };

    BuilderView.prototype.setSortable = function() {
      var _this = this;
      if (this.$responseFields.hasClass('ui-sortable')) {
        this.$responseFields.sortable('destroy');
      }
      this.$responseFields.sortable({
        forcePlaceholderSize: true,
        placeholder: 'sortable-placeholder',
        stop: function(e, ui) {
          var rf;
          if (ui.item.data('field-type')) {
            rf = _this.collection.create(Formbuilder.helpers.defaultFieldAttrs(ui.item.data('field-type')), {
              $replaceEl: ui.item
            });
            _this.createAndShowEditView(rf);
          }
          _this.handleFormUpdate();
          return true;
        },
        update: function(e, ui) {
          if (!ui.item.data('field-type')) {
            return _this.ensureEditViewScrolled();
          }
        }
      });
      return this.setDraggable();
    };

    BuilderView.prototype.setDraggable = function() {
      var $addFieldButtons,
        _this = this;
      $addFieldButtons = this.$el.find("[data-field-type]");
      return $addFieldButtons.draggable({
        connectToSortable: this.$responseFields,
        helper: function() {
          var $helper;
          $helper =jQuery("<div class='response-field-draggable-helper' />");
          $helper.css({
            width: _this.$responseFields.width(),
            height: '80px'
          });
          return $helper;
        }
      });
    };

    BuilderView.prototype.addAll = function() {
      this.collection.each(this.addOne, this);
      return this.setSortable();
    };

    BuilderView.prototype.hideShowNoResponseFields = function() {
      return this.$el.find(".fb-no-response-fields")[this.collection.length > 0 ? 'hide' : 'show']();
    };

    BuilderView.prototype.addField = function(e) {
      var field_type;
      field_type =jQuery(e.currentTarget).data('field-type');
      return this.createField(Formbuilder.helpers.defaultFieldAttrs(field_type));
    };

    BuilderView.prototype.createField = function(attrs, options) {
      var rf;
      rf = this.collection.create(attrs, options);
      this.createAndShowEditView(rf);
      return this.handleFormUpdate();
    };

    BuilderView.prototype.createAndShowEditView = function(model) {
      var $newEditEl, $responseFieldEl;
      $responseFieldEl = this.$el.find(".fb-field-wrapper").filter(function() {
        return jQuery(this).data('cid') === model.cid;
      });
      $responseFieldEl.addClass('editing').siblings('.fb-field-wrapper').removeClass('editing');
      if (this.editView) {
        if (this.editView.model.cid === model.cid) {
          this.$el.find(".fb-tabs a[data-target=\"#editField\"]").click();
          this.scrollLeftWrapper($responseFieldEl);
          return;
        }
        this.editView.remove();
      }
      this.editView = new EditFieldView({
        model: model,
        parentView: this
      });
      $newEditEl = this.editView.render().$el;
      this.$el.find(".fb-edit-field-wrapper").html($newEditEl);
      this.$el.find(".fb-tabs a[data-target=\"#editField\"]").click();
      this.scrollLeftWrapper($responseFieldEl);
      return this;
    };

    BuilderView.prototype.ensureEditViewScrolled = function() {
      if (!this.editView) {
        return;
      }
      return this.scrollLeftWrapper(jQuery(".fb-field-wrapper.editing"));
    };

    BuilderView.prototype.scrollLeftWrapper = function($responseFieldEl) {
      var _this = this;
      this.unlockLeftWrapper();
      if (!$responseFieldEl[0]) {
        return;
      }
      return $.scrollWindowTo((this.$el.offset().top + $responseFieldEl.offset().top) - this.$responseFields.offset().top, 200, function() {
        return _this.lockLeftWrapper();
      });
    };

    BuilderView.prototype.lockLeftWrapper = function() {
      return this.$fbLeft.data('locked', true);
    };

    BuilderView.prototype.unlockLeftWrapper = function() {
      return this.$fbLeft.data('locked', false);
    };

    BuilderView.prototype.handleFormUpdate = function() {
      if (this.updatingBatch) {
        return;
      }
      this.formSaved = false;
      return this.saveFormButton.removeAttr('disabled').text(Formbuilder.options.dict.SAVE_FORM);
    };

    BuilderView.prototype.saveForm = function(e) {
      var payload;
      if (this.formSaved) {
        return;
      }
      this.formSaved = true;
      this.saveFormButton.attr('disabled', true).text(Formbuilder.options.dict.ALL_CHANGES_SAVED);
      this.collection.sort();
      payload = JSON.stringify({
        fields: this.collection.toJSON()
      });
      if (Formbuilder.options.HTTP_ENDPOINT) {
        this.doAjaxSave(payload);
      }
      return this.formBuilder.trigger('save', payload);
    };

    BuilderView.prototype.doAjaxSave = function(payload) {
      var _this = this;
      return $.ajax({
        url: Formbuilder.options.HTTP_ENDPOINT,
        type: Formbuilder.options.HTTP_METHOD,
        data: payload,
        contentType: "application/json",
        success: function(data) {
          var datum, _i, _len, _ref5;
          _this.updatingBatch = true;
          for (_i = 0, _len = data.length; _i < _len; _i++) {
            datum = data[_i];
            if ((_ref5 = _this.collection.get(datum.cid)) != null) {
              _ref5.set({
                id: datum.id
              });
            }
            _this.collection.trigger('sync');
          }
          return _this.updatingBatch = void 0;
        }
      });
    };

    return BuilderView;

  })(Backbone.View);

  Formbuilder = (function() {
    Formbuilder.helpers = {
      defaultFieldAttrs: function(field_type) {
        var attrs, _base;
        attrs = {};
        attrs[Formbuilder.options.mappings.LABEL] = 'Untitled';
        attrs[Formbuilder.options.mappings.FIELD_TYPE] = field_type;
        attrs[Formbuilder.options.mappings.REQUIRED] = false; //moficato
        attrs['field_options'] = {};
        return (typeof (_base = Formbuilder.fields[field_type]).defaultAttributes === "function" ? _base.defaultAttributes(attrs) : void 0) || attrs;
      },
      simple_format: function(x) {
        return x != null ? x.replace(/\n/g, '<br />') : void 0;
      }
    };

    Formbuilder.options = {
      BUTTON_CLASS: 'fb-button',
      HTTP_ENDPOINT: '',
      HTTP_METHOD: 'POST',
      AUTOSAVE: false,
      CLEAR_FIELD_CONFIRM: false,
      mappings: {
        SIZE: 'field_options.size',
        UNITS: 'field_options.units',
        LABEL: 'label',
        FIELD_TYPE: 'field_type',
        REQUIRED: 'required',
        ADMIN_ONLY: 'admin_only',
        OPTIONS: 'field_options.options',
        DESCRIPTION: 'field_options.description',
        PLACEHOLDER: 'field_options.placeholder',
        INCLUDE_OTHER: 'field_options.include_other_option',
        INCLUDE_BLANK: 'field_options.include_blank_option',
        INTEGER_ONLY: 'field_options.integer_only',
        MIN: 'field_options.min',
        MAX: 'field_options.max',
        MINLENGTH: 'field_options.minlength',
        MAXLENGTH: 'field_options.maxlength',
        LENGTH_UNITS: 'field_options.min_max_length_units'
      },
      dict: {
        ALL_CHANGES_SAVED: 'All changes saved',
        SAVE_FORM: 'Save form',
        UNSAVED_CHANGES: 'You have unsaved changes. If you leave this page, you will lose those changes!'
      }
    };

    Formbuilder.fields = {};

    Formbuilder.inputFields = {};

    Formbuilder.nonInputFields = {};

    Formbuilder.registerField = function(name, opts) {
      var x, _i, _len, _ref5;
      _ref5 = ['view', 'edit'];
      for (_i = 0, _len = _ref5.length; _i < _len; _i++) {
        x = _ref5[_i];
        opts[x] = _.template(opts[x]);
      }
      opts.field_type = name;
      Formbuilder.fields[name] = opts;
      if (opts.type === 'non_input') {
        return Formbuilder.nonInputFields[name] = opts;
      } else {
        return Formbuilder.inputFields[name] = opts;
      }
    };

    function Formbuilder(opts) {
      var args;
      if (opts == null) {
        opts = {};
      }
      _.extend(this, Backbone.Events);
      args = _.extend(opts, {
        formBuilder: this
      });
      this.mainView = new BuilderView(args);
    }

    return Formbuilder;

  })();

  window.Formbuilder = Formbuilder;

  if (typeof module !== "undefined" && module !== null) {
    module.exports = Formbuilder;
  } else {
    window.Formbuilder = Formbuilder;
  }

}).call(this);

/* Modificato
(function() {
  Formbuilder.registerField('address', {
    order: 50,
    view: "<div class='input-line'>\n  <span class='street'>\n    <input type='text' />\n    <label>Address</label>\n  </span>\n</div>\n\n<div class='input-line'>\n  <span class='city'>\n    <input type='text' />\n    <label>City</label>\n  </span>\n\n  <span class='state'>\n    <input type='text' />\n    <label>State / Province / Region</label>\n  </span>\n</div>\n\n<div class='input-line'>\n  <span class='zip'>\n    <input type='text' />\n    <label>Zipcode</label>\n  </span>\n\n  <span class='country'>\n    <select><option>United States</option></select>\n    <label>Country</label>\n  </span>\n</div>",
    edit: "",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-home\"></span></span> Address"
  });

}).call(this); */

(function() {
  Formbuilder.registerField('checkboxes', {
    order: 10,
    view: "<% for (i in (rf.get(Formbuilder.options.mappings.OPTIONS) || [])) { %>\n  <div>\n    <label class='fb-option'>\n      <input type='checkbox' <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].checked && 'checked' %> onclick=\"javascript: return false;\" />\n      <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].label %>\n    </label>\n  </div>\n<% } %>\n\n<% if (rf.get(Formbuilder.options.mappings.INCLUDE_OTHER)) { %>\n  <div class='other-option'>\n    <label class='fb-option'>\n      <input type='checkbox' />\n      Other\n    </label>\n\n    <input type='text' />\n  </div>\n<% } %>",
    edit: "<%= Formbuilder.templates['edit/options']({ includeOther: true }) %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-square-o\"></span></span> Checkbox (multiple choice)",
    defaultAttributes: function(attrs) {
      attrs.field_options.options = [
        {
          label: "",
          checked: false
        }, {
          label: "",
          checked: false
        }
      ];
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('date', {
    order: 20,
    view: "<div class='input-line'>\n  <span class='month'>\n    <input type=\"text\" />\n    <label>MM</label>\n  </span>\n\n  <span class='above-line'>/</span>\n\n  <span class='day'>\n    <input type=\"text\" />\n    <label>DD</label>\n  </span>\n\n  <span class='above-line'>/</span>\n\n  <span class='year'>\n    <input type=\"text\" />\n    <label>YYYY</label>\n  </span>\n</div>",
    edit: "",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-calendar\"></span></span> Date"
  });

}).call(this);

(function() {
  Formbuilder.registerField('dropdown', {
    order: 24,
    view: "<select>\n  <% if (rf.get(Formbuilder.options.mappings.INCLUDE_BLANK)) { %>\n    <option value=''></option>\n  <% } %>\n\n  <% for (i in (rf.get(Formbuilder.options.mappings.OPTIONS) || [])) { %>\n    <option <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].checked && 'selected' %>>\n      <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].label %>\n    </option>\n  <% } %>\n</select>",
    edit: "<%= Formbuilder.templates['edit/options']({ includeBlank: true }) %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-caret-down\"></span></span> Dropdown",
    defaultAttributes: function(attrs) {
      attrs.field_options.options = [
        {
          label: "",
          checked: false
        }, {
          label: "",
          checked: false
        }
      ];
      attrs.field_options.include_blank_option = false;
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('email', {
    order: 40,
    view: "<input type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' />",
    edit: "<%= Formbuilder.templates['edit/email']() %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-envelope-o\"></span></span> Email"
  });

}).call(this);

(function() {


}).call(this);

(function() {
  Formbuilder.registerField('number', {
    order: 30,
    view: "<input type='text' />\n<% if (units = rf.get(Formbuilder.options.mappings.UNITS)) { %>\n  <%= units %>\n<% } %>",
    edit: "<%= Formbuilder.templates['edit/min_max']() %>\n<%= Formbuilder.templates['edit/units']() %>\n<%= Formbuilder.templates['edit/integer_only']() %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-number\">123</span></span> Number"
  });

}).call(this);

(function() {
  Formbuilder.registerField('paragraph', {
    order: 5,
    view: "<textarea class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>'></textarea>",
    edit: "<%= Formbuilder.templates['edit/size']() %>\n<%= Formbuilder.templates['edit/min_max_length']() %>",
    addButton: "<span class=\"symbol\">&#182;</span> Textarea",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'small';
      return attrs;
    }
  });

}).call(this);

/* (function() {
  Formbuilder.registerField('price', {
    order: 45,
    view: "<div class='input-line'>\n  <span class='above-line'>$</span>\n  <span class='dolars'>\n    <input type='text' />\n    <label>Dollars</label>\n  </span>\n  <span class='above-line'>.</span>\n  <span class='cents'>\n    <input type='text' />\n    <label>Cents</label>\n  </span>\n</div>",
    edit: "",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-usd\"></span></span> Price"
  });

}).call(this); */

(function() {
  Formbuilder.registerField('radio', {
    order: 15,
    view: "<% for (i in (rf.get(Formbuilder.options.mappings.OPTIONS) || [])) { %>\n  <div>\n    <label class='fb-option'>\n      <input type='radio' <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].checked && 'checked' %> onclick=\"javascript: return false;\" />\n      <%= rf.get(Formbuilder.options.mappings.OPTIONS)[i].label %>\n    </label>\n  </div>\n<% } %>\n\n<% if (rf.get(Formbuilder.options.mappings.INCLUDE_OTHER)) { %>\n  <div class='other-option'>\n    <label class='fb-option'>\n      <input type='radio' />\n      Other\n    </label>\n\n    <input type='text' />\n  </div>\n<% } %>",
    edit: "<%= Formbuilder.templates['edit/options']({ includeOther: true }) %>",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-circle-o\"></span></span> Radio (single choice)",
    defaultAttributes: function(attrs) {
      attrs.field_options.options = [
        {
          label: "",
          checked: false
        }, {
          label: "",
          checked: false
        }
      ];
      return attrs;
    }
  });

}).call(this);

/* 
Modificato
(function() {
  Formbuilder.registerField('section_break', {
    order: 0,
    type: 'non_input',
    view: "<label class='section-name'><%= rf.get(Formbuilder.options.mappings.LABEL) %></label>\n<p><%= rf.get(Formbuilder.options.mappings.DESCRIPTION) %></p>",
    edit: "<div class='fb-edit-section-header'>Label</div>\n<input type='text' data-rv-input='model.<%= Formbuilder.options.mappings.LABEL %>' />\n<textarea data-rv-input='model.<%= Formbuilder.options.mappings.DESCRIPTION %>'\n  placeholder='Add a longer description to this field'></textarea>",
    addButton: "<span class='symbol'><span class='fa fa-minus'></span></span> Section Break"
  });

}).call(this); */

(function() {
  Formbuilder.registerField('text', {
    order: 0,
    view: "<input type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' />",
    edit: "<%= Formbuilder.templates['edit/text']() %><%= Formbuilder.templates['edit/size']() %>\n<%= Formbuilder.templates['edit/min_max_length']() %>",
    addButton: "<span class='symbol'><span class='fa fa-font'></span></span> Text",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'small';
      return attrs;
    }
  });

}).call(this);

(function() {
  Formbuilder.registerField('time', {
    order: 25,
    view: "<div class='input-line'>\n  <span class='hours'>\n    <input type=\"text\" />\n    <label>HH</label>\n  </span>\n\n  <span class='above-line'>:</span>\n\n  <span class='minutes'>\n    <input type=\"text\" />\n    <label>MM</label>\n  </span>\n\n  <span class='above-line'>:</span>\n\n  <span class='seconds'>\n    <input type=\"text\" />\n    <label>SS</label>\n  </span>\n\n  <span class='am_pm'>\n    <select>\n      <option>AM</option>\n      <option>PM</option>\n    </select>\n  </span>\n</div>",
    edit: "",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-clock-o\"></span></span> Time"
  });

}).call(this);

(function() {
  Formbuilder.registerField('website', {
    order: 35,
    view: "<input type='text' placeholder='http://' />",
    edit: "",
    addButton: "<span class=\"symbol\"><span class=\"fa fa-link\"></span></span> Website"
  });

}).call(this);


(function() {
  Formbuilder.registerField('file', {
    order: 40,
    view: "<input type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' />",
    edit: "<%= Formbuilder.templates['edit/file']() %>",
    addButton: "<span class='symbol'><span class='fa fa-file'></span></span> File",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'small';
      return attrs;
    }
  });

}).call(this);



	
//Modificato: aggiunto
(function() {
  Formbuilder.registerField('html', {
	order: 41,
	view: "<input type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' />",
	edit: "<%= Formbuilder.templates['edit/html_field']() %>",
	addButton: "<span class='symbol'><span class='fa fa-code'></span></span> HTML",
	defaultAttributes: function(attrs) {
	  attrs.field_options.size = 'small';
	  return attrs;
	}
  });

}).call(this);
	
//Modificato: aggiunto
if(woocommerce_is_active)
	(function() {
	  Formbuilder.registerField('country_and_state', {
		order: 42,
		view: "<input type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' />",
		edit: "<%= Formbuilder.templates['edit/country_and_state']() %>",
		addButton: "<span class='symbol'><span class='fa fa-flag'></span></span> Country & State",
		defaultAttributes: function(attrs) {
		  attrs.field_options.size = 'small';
		  return attrs;
		}
	  });

	}).call(this);
	
//Modificato: aggiunto
if(woocommerce_is_active)
	(function() {
	  Formbuilder.registerField('title_no_input', {
		order: 42,
		view: "<input type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' />",
		edit: "<%= Formbuilder.templates['edit/title_no_input']() %>",
		addButton: "<span class='symbol'><span class='fa fa-text-width'></span></span> Title (no input field)",
		defaultAttributes: function(attrs) {
		  attrs.field_options.size = 'small';
		  return attrs;
		}
	  });

	}).call(this);
	
/* ToDo */
/* (function() {
  Formbuilder.registerField('hidden', {
    order: 42,
    view: "<input type='text' class='rf-size-<%= rf.get(Formbuilder.options.mappings.SIZE) %>' />",
    edit: "<%= Formbuilder.templates['edit/size']() %>\n<%= Formbuilder.templates['edit/hidden_field']() %>",
    addButton: "<span class='symbol'><span class='fa fa-eye-slash'></span></span> Hidden",
    defaultAttributes: function(attrs) {
      attrs.field_options.size = 'small';
      return attrs;
    }
  });

}).call(this); */

this["Formbuilder"] = this["Formbuilder"] || {};
this["Formbuilder"]["templates"] = this["Formbuilder"]["templates"] || {};

this["Formbuilder"]["templates"]["edit/base"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) 
{	
__p +=
((__t = ( Formbuilder.templates['edit/base_header']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.templates['edit/common']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.fields[rf.get(Formbuilder.options.mappings.FIELD_TYPE)].edit({rf: rf}) )) == null ? '' : __t) +
'\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/base_header"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-field-label\'>\n  <span data-rv-text="model.' +
((__t = ( Formbuilder.options.mappings.LABEL )) == null ? '' : __t) +
'"></span>\n  <code class=\'field-type\' data-rv-text=\'model.' +
((__t = ( Formbuilder.options.mappings.FIELD_TYPE )) == null ? '' : __t) +
'\'></code>\n  <span class=\'fa fa-arrow-right pull-right\'></span>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["edit/base_non_input"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p +=
((__t = ( Formbuilder.templates['edit/base_header']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.fields[rf.get(Formbuilder.options.mappings.FIELD_TYPE)].edit({rf: rf}) )) == null ? '' : __t) +
'\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/html_field"]  = function(obj)
{
	obj || (obj = {});
	var __t, __p = '', __e = _.escape;
	
	return __p;	
}

this["Formbuilder"]["templates"]["edit/title_no_input"]  = function(obj)
{
	obj || (obj = {});
	var __t, __p = '', __e = _.escape;
	__p += '<br/><br/><h4>Select which html tag has to be used to render the title</h4>'; 
	__p += '<select data-rv-value=\'model.title_tag\'>';
	__p += '	<option value="">Label</option>';
	__p += '	<option value="h1">H1</option>';
	__p += '	<option value="h2">H2</option>';
	__p += '	<option value="h3">H3</option>';
	__p += '	<option value="h4">H4</option>';
	__p += '	<option value="h5">H5</option>';
	__p += '	<option value="h6">H6</option>';
	__p += '</select>';
	
	__p += '<br/><br/><h4 >Margins</h4>'; 
	__p += '<p>Specifymargins using <strong>CSS notation</strong> (up, right, down, left).<br/>Example: 1px 1px 1px 1px </p>'; 
	__p += '<label class="wpuef_label">Margin</label>'; 
	__p += '<input class="" type=\'text\' placeholder="0px 0px 0px 0px" data-rv-input=\'model.title_margin\' />';
	
	//__p += '<br/><label class="wpuef_label">Padding</label>'; 
	//__p += '<input class="" type=\'text\'  placeholder="0px 0px 0px 0px" data-rv-input=\'model.title_padding\' />';
	
	__p += '<br/><br/><h4>Class</h4>'; 
	__p += '<p>You can assign one or more custom classes to the title field. In case of multple classes, define them in the followin way: "class_1 class_2 class_3"</p>'; 
	
	__p += '<input class="" type=\'text\'  placeholder="class_1 class_2 class_3" data-rv-input=\'model.title_classes\' />';
	
	
	return __p;	
}

this["Formbuilder"]["templates"]["edit/country_and_state"]  = function(obj)
{
	obj || (obj = {});
	var __t, __p = '', __e = _.escape;
	//This field is displayed  only if woocommerce is present
	
	//Option to select which country to show (all country, avaiable coutry, coutry where can be shipped)
	__p += '<br/><br/><h4>Select which countries have to be showed:</h4>'; 
	__p += '<p><strong>NOTE:</strong> if only one country is available, it will be showed only the state/province field.</p>'; 
	__p += '<select data-rv-value=\'model.coutries_to_show\'>';
	__p += '	<option value="">Allowed countries</option>';
	__p += '	<option value="all_countries">All countries</option>';
	__p += '</select>';
	
	
	//Option to not show state field 
	__p += '<h4>Show state/province field?</h4>'; 
	__p += '<select data-rv-value=\'model.show_state\'>';
	__p += '	<option value="">Yes</option>';
	__p += '	<option value="no">No</option>';
	__p += '</select>';
	
	//Option to Overwrite
	__p += '<h4 >WooCommerce overwrite options:</h4>'; 
	__p += '<select data-rv-value=\'model.field_to_override\'>';
	__p += '	<option value="">None</option>';
	__p += '	<option value="shipping_country_and_state">Shipping country & state/province</option>';
	__p += '	<option value="billing_country_and_state">Billing country & state/province</option>';
	__p += '	<option value="billing_country_and_state_both">Both</option>';
	__p += '</select>';
	
	__p += '<h4 >Defaults:</h4>'; 
	__p += '<p>Select default country code (Ex.: IT, US, ...) and state code (Ex.: NY, PI, ...). Leave empty for no default values.</p>'; 
	__p += '<div class="wpuef_label_input_block"><label class="wpuef_state_country_label">Country code</label>'; 
	__p += '<input class="wpuef_country_state_default" type=\'text\' data-rv-input=\'model.default_country\' /></div>';
	
	__p += '<div class="wpuef_label_input_block"><label class="wpuef_state_country_label">State code</label>'; 
	__p += '<input class="wpuef_country_state_default" type=\'text\' data-rv-input=\'model.default_state\' /></div>';
	
	return __p;	
}

this["Formbuilder"]["templates"]["edit/text"]  = function(obj)
{
	obj || (obj = {});
	var __t, __p = '', __e = _.escape;
	/*console.log(BuilderView);
	console.log(BuilderView.editView.model);*/
	__p += '<div class="fb-edit-section-header" >WordPress '; 
	if(woocommerce_is_active)
	{
		__p += 'and WooCommerce '; 
	}
	__p += 'overwrite options </div>'; 
	__p += '<p>Select which field to overwrite'
	if(woocommerce_is_active)
	{
		__p +='.<br/><strong>NOTE:</strong> if the <strong>Password</strong> option is selected, the field will be displayed only in <strong>Register form</strong> (Checkout and Register page) and <strong>My Account page</strong> (according to visibility options) and will not be peformed any strong/weak security check';
	}
	__p +=':</p>';
	//__p += '<input type=\'checkbox\' data-rv-checked=\'model.override_wp_first_name_field\' />\n<strong>First Name:</strong> the field content will be stored in the default WordPress First Name field';
	//__p += '<br/><br/><input type=\'checkbox\' data-rv-checked=\'model.override_wp_last_name_field\' />\n <strong>Last Name:</strong> the field content will be stored in the default WordPress Last Name field';
	__p += '<select data-rv-value=\'model.field_to_override\'>';
	__p += '	<option value="">None</option>';
	__p += '	<option value="first_name">First name</option>';
	__p += '	<option value="last_name">Last name</option>';
	if(woocommerce_is_active)
	{
		__p += '	<option value="password">Password</option>';
		__p += '	<option value="billing_first_name">Billing first name</option>';
		__p += '	<option value="billing_last_name">Billing last name</option>';
		__p += '	<option value="billing_phone">Billing phone</option>';
		__p += '	<option value="billing_company">Billing company</option>';
		__p += '	<option value="billing_address_1">Billing address 1</option>';
		__p += '	<option value="billing_address_2">Billing address 2</option>';
		__p += '	<option value="billing_city">Billing city</option>';
		__p += '	<option value="billing_postcode">Billing postcode</option>';
		__p += '	<option value="shipping_first_name">Shipping first name</option>';
		__p += '	<option value="shipping_last_name">Shipping last name</option>';
		__p += '	<option value="shipping_phone">Shipping phone</option>';
		__p += '	<option value="shipping_company">Shipping company</option>';
		__p += '	<option value="shipping_address_1">Shipping address</option>';
		__p += '	<option value="shipping_address_2">Shipping address 2</option>';
		__p += '	<option value="shipping_postcode">Shipping postcode</option>';
		__p += '	<option value="shipping_city">Shipping city</option>';
		__p += '	<optgroup label="Multiple overwrite options">';
		__p += '	<option value="first_name+billing_first_name">First name, Billing first name</option>';
		__p += '	<option value="first_name+shipping_first_name">First name, Shipping first name</option>';
		__p += '	<option value="billing_first_name+shipping_first_name">Billing first name, Shipping first name</option>';
		__p += '	<option value="first_name+billing_first_name+shipping_first_name">First name, Billing first name, Shipping first name</option>';
		__p += '	<option value="last_name+billing_last_name">Last name, Billing last name</option>';
		__p += '	<option value="last_name+shipping_last_name">Last name, Shipping last name</option>';
		__p += '	<option value="billing_last_name+shipping_last_name">Billing last name, Shipping last name</option>';
		__p += '	<option value="last_name+billing_last_name+shipping_last_name">Last name, Billing last name, Shipping last name</option>';
		__p += '	</optgroup>';
	}
	__p += '</select>';
	return __p;	
}
this["Formbuilder"]["templates"]["edit/email"]  = function(obj)
{
	obj || (obj = {});
	var __t, __p = '', __e = _.escape;
	
	if(woocommerce_is_active)
	{
		__p += '<div class="fb-edit-section-header" >WooCommerce overwrite options</div>'; 
		__p += '<p>Select which field to overwrite:</p>'; 
		__p += '<select data-rv-value=\'model.field_to_override\'>';
		__p += '	<option value="">None</option>';
		__p += '	<option value="billing_email">Billing email</option>';
		__p += '</select>';
	}
	return __p;	
}
this["Formbuilder"]["templates"]["edit/checkboxes"] = function(obj) 
{
	obj || (obj = {});
	var __t, __p = '', __e = _.escape;
	
	with (obj) 
	{
		__p += '<input type=\'checkbox\' data-rv-checked=\'model.' +
		((__t = ( Formbuilder.options.mappings.REQUIRED )) == null ? '' : __t) +
		'\' />\n  Required (for "Checkbox" type, at least one of the option has to been checked. Does not work with date and time pickers)\n</input>\n<!-- <label>\n  <input type=\'checkbox\' data-rv-checked=\'model.' +
		((__t = ( Formbuilder.options.mappings.ADMIN_ONLY )) == null ? '' : __t) +
		'\' />\n  Admin only\n</label> -->';

		__p += '<br/><br/><input type=\'checkbox\' data-rv-checked=\'model.editable_only_by_admin\' />\n Editable <strong>ONLY</strong> by Admin (User can see the field on his profile page but cannot edit it). In case of <strong>HTML</strong> field, the inputted HTML code will be rendered instead of the input text area (according to the visibility options). ';
		/* if(woocommerce_is_active)
			__p += '<strong>If enabled, WooCommerce Checkout visibility options will be ignored</strong>.</input>'; */
		
		__p += '<div class="fb-edit-section-header" >Visibility options</div>'; 
		__p += '<input type=\'checkbox\' data-rv-checked=\'model.visible_only_at_register_page\' />\n Visible <strong>ONLY</strong> in the register page (The field will <strong>NOT</strong> visible in ';
		if(buddypress_is_active)
			__p += '<strong>BuddyPress</strong> ';
		__p += 'user profile page.';
		if(woocommerce_is_active)
			__p += '<br/>In <strong>WooCommerce</strong> Checkout page the field will visible <strong>ONLY</strong> in the checkout user registration form.<br/>Other <strong>WooCommerce</strong> pages visibility options will be ignored';
		__p +=').</input>';
			
		__p += '<br/><br/><input type=\'checkbox\' data-rv-checked=\'model.hide_in_the_register_page\' />\n Hide in the register page. If checked previous option will be ignored. (By default a field is showed in both register and user profile page).</input>';
		
		__p += '<br/><br/><input type=\'checkbox\' data-rv-checked=\'model.invisible\' />\n Invisible: the field will not be visible in any page. You must use shortcodes to display.</input>';
		
		__p += '<br/><br/><input type=\'checkbox\' data-rv-checked=\'model.uneditable\' />\n Uneditable: the field will be visible in user profile pages but will be not editable.</input><br/><br/>';
		
		__p += '<h4>Select for which roles the field will be visible:</h4>';
		__p += '<p><strong>Note:</strong> The field will be visible on register page anyway for not logged users. To hide from that page, use the above special option.</p>';
		//console.log(wpuef_values.roles);
		for (var role_code in wpuef_values.roles)
		{
			if (!wpuef_values.roles.hasOwnProperty(role_code)) continue;
			var role_name = wpuef_values.roles[role_code];
			__p += '<input type=\'checkbox\' data-rv-checked=\'model.visible_per_roles['+role_code+']\' />'+role_name+'</input><br/>';
		}
		__p += '<div class="fb-edit-section-header" >Admin Users table / WooCommerce Customers table visibility options</div>'; /* \n*/
		__p += '<input type=\'checkbox\' data-rv-checked=\'model.add_field_to_user_table_colum\' />\Show field content in users/customers table</input>';
			
		
		
		 if(buddypress_is_active)
		{
			//Modificato: aggiunto obj.rf.attributes.cid
			__p += '<div class="fb-edit-section-header" >BuddyPress options</div>'; 
			__p +='<h4>Profile page</h4>';
			__p += '<input type=\'checkbox\' data-rv-checked=\'model.buddypress_profile_page_show\' />\n Show field in profile page</input><br/><br/>';
			__p += '<input type=\'checkbox\' data-rv-checked=\'model.buddypress_profile_edit_page_show\' />\n Show field in edit profile page</input>';
		} 
		
		//Modificato: Comune per tutti
		if(woocommerce_is_active)
		{
			__p += '<div class="fb-edit-section-header" >WooCommerce options</div>'; /* \n*/
			__p +='<h4>Checkout:</h4>';
			__p += '<input type=\'checkbox\' data-rv-checked=\'model.woocommerce_visible_on_checkout\' />\n Visible on Checkout page. If enabled, customer will be able to edit this field during Checkout. <br/><strong>NOTE:</strong> Field will not be visible if guest checkout is allowed</input><br/><br/>';
			//__p += '<input type=\'checkbox\' data-rv-checked=\'model.woocommerce_checkout_only_editable\' />\n Editable <strong>ONLY</strong> on Checkout page (previous option must be enable and "Edit pages" options will be ignored).</input><br/><br/>';
			__p += '<input type=\'checkbox\' data-rv-checked=\'model.woocommerce_hide_on_checkout_register_form\' />\nHide from Checkout registration form. By default, in case of guest users, fields are automatically showed on Checkout register form. Check this option to avoid the field to be showed in the Checkout register form.</input><br/><br/>';
			
			__p +='<h4>Order:</h4>';
			__p += '<input type=\'checkbox\' data-rv-checked=\'model.woocommerce_save_on_checkout_as_order_field\' />\n Save as order extra field.<br/><strong>NOTE:</strong> this setting will take effect only if the field is <strong>visible on checkout page</strong> (previous option). </label>';
			
			__p += '<h4>Row size:</h4>'; 
			__p += '<p><strong>NOTE:</strong> this setting is ignored in case of <i>State & Country</i> and <i>Title</i> field types.</p>'; 
			__p += '<select data-rv-value=\'model.checkout_row_size\'>';
			__p += '	<option value="">Wide (full)</option>';
			__p += '	<option value="first">Left (half)</option>';
			__p += '	<option value="last">Right (half)</option>';
			__p += '</select><br/><br/>';
			
			__p += '<h4>Visble and Editable in following pages (according to Visibility restricion options): </h4>';
			__p += '<input type=\'checkbox\' data-rv-checked=\'model.woocommerce_edit_on_my_account_page\' />\n <strong>Account Details</strong> page</input><br/><br/>';
			__p += '<input type=\'checkbox\' data-rv-checked=\'model.woocommerce_edit_on_billing_address_page\' />\n <strong>Billing Address</strong> page</input><br/><br/>';
			__p += '<input type=\'checkbox\' data-rv-checked=\'model.woocommerce_edit_on_shipping_address_page\' />\n <strong>Shipping Address</strong> page</input><br/><br/>';
			
			__p +='<h4>Email:</h4>';
			__p += '<input type=\'checkbox\' data-rv-checked=\'model.woocommerce_include_on_woocommerce_emails\' />\n Include in WooCommerce emails.</input><br/>';
			
		}
		
	}
	return __p;
};

this["Formbuilder"]["templates"]["edit/hidden_field"] = function(obj) 
{
	obj || (obj = {});
	var __t, __p = '', __e = _.escape;
	with (obj) 
	{
		__p += '<input type=\'hidden\' data-rv-checked=\'model.hidden_field\' value=\'yes\' />';

	}
	return __p 
};

this["Formbuilder"]["templates"]["edit/common"] = function(obj) 
{
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) 
{
	__p += '<div class=\'fb-edit-section-header\'>Label</div>\n\n<div class=\'fb-common-wrapper\'>\n  <div class=\'fb-label-description\'>\n    ' +
	((__t = ( Formbuilder.templates['edit/label_description']() )) == null ? '' : __t) +
	'\n  </div>\n  <div class=\'fb-common-checkboxes\'>\n    ' +
	((__t = ( Formbuilder.templates['edit/checkboxes']() )) == null ? '' : __t) + //Richiamo della funzione comune
	'\n  </div>\n  <div class=\'fb-clear\'></div>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/integer_only"] = function(obj) {
/* Modificato
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Integer only</div>\n<label>\n  <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.INTEGER_ONLY )) == null ? '' : __t) +
'\' />\n  Only accept integers\n</label>\n';

}
return __p */
};

this["Formbuilder"]["templates"]["edit/label_description"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<input type=\'text\' data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.LABEL )) == null ? '' : __t) +
'\' />\n<textarea data-rv-input=\'model.' +
((__t = ( Formbuilder.options.mappings.DESCRIPTION )) == null ? '' : __t) +
'\'\n  placeholder=\'Add a longer description to this field\'></textarea>'+
' \n<input type=\'text\' data-rv-input=\'model.field_options.placeholder\'  placeholder=\'Place holder text (visible only if the html field supports it)\'></input>'


;

}
return __p
};

this["Formbuilder"]["templates"]["edit/min_max"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Minimum / Maximum</div>\n\Min\n<input type="text" class="wpuef_num_input" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.MIN )) == null ? '' : __t) +
'" />Max<input type="text" class="wpuef_num_input" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.MAX )) == null ? '' : __t) +
'"  />\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/min_max_length"] = function(obj) {
/* 
modificato
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Length Limit</div>\n\nMin\n<input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.MINLENGTH )) == null ? '' : __t) +
'" style="width: 30px" />\n\n&nbsp;&nbsp;\n\nMax\n<input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.MAXLENGTH )) == null ? '' : __t) +
'" style="width: 30px" />\n\n&nbsp;&nbsp;\n\n<select data-rv-value="model.' +
((__t = ( Formbuilder.options.mappings.LENGTH_UNITS )) == null ? '' : __t) +
'" style="width: auto;">\n  <option value="characters">characters</option>\n  <option value="words">words</option>\n</select>\n';

}
return __p */
};

//Modificato
this["Formbuilder"]["templates"]["edit/file"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
	
if(woocommerce_is_active)
{
	//Modificato: aggiunto obj.rf.attributes.cid
	__p += '<div class="fb-edit-section-header" >My Account page options</div>';
}
else
	__p += '<div class="fb-edit-section-header" >User profile page options</div>';

__p += '<label>\n  <input type=\'checkbox\' data-rv-checked=\'model.one_time_upload\' />\n One time upload (the user can upload the file just one time)</label>';
//__p += '<label>\n  <input type=\'checkbox\' data-rv-checked=\'model.re_upload\' />\n Let user re-upload a file (this will overwrite the previous uploaded file)</label>';
//__p += '<br/><br/><label>\n  <input type=\'checkbox\' data-rv-checked=\'model.can_delete_file\' />\n User can delete upload</label>';

__p += '<div class="fb-edit-section-header" >File options</div>';
__p += '<label>\n  <input type=\'checkbox\' data-rv-checked=\'model.image_preview\' />\n Display preview (Only valid for images files)</label>';
__p += '<br/><br/><label>Preview width</label><br/><input type=\'number\' min="1" step="1" data-rv-input=\'model.preview_width\' /><br/>If left empty will be used the default value of 120px.';
__p += '<br/><br/><label>Size limit (MB)</label><br/><input type=\'number\' data-rv-input=\'model.file_size\' /><br/>Leave empty for no limits.';
__p += '<br/><br/><label>Allowed file type(s)</label><br/><input type=\'text\' data-rv-input=\'model.file_types\' style="width:150px;" placeholder=""/><br/>For multiple types use comma separeted format, for example: <i>.jpg,.bmp,.png</i><br/>leave field empty to accept all file types.';
}
return __p
};


this["Formbuilder"]["templates"]["edit/options"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Options</div>\n\n';
 if (typeof includeBlank !== 'undefined'){ ;
__p += '\n  <label>\n    <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.INCLUDE_BLANK )) == null ? '' : __t) +
'\' />\n    Include blank (by the default this will be the one selected). The <i>Placeholder</i> will be used as text for the blank field.\n  </label>\n';

 } ;
__p += '\n\n<div class=\'option\' data-rv-each-option=\'model.' +
((__t = ( Formbuilder.options.mappings.OPTIONS )) == null ? '' : __t) +
'\'>\n  <input type="checkbox" class=\'js-default-updated\' data-rv-checked="option:checked" />\n  <input type="text" data-rv-input="option:label" class=\'option-label-input\' />\n  <a class="js-add-option ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="Add Option"><i class=\'fa fa-plus-circle\'></i></a>\n  <a class="js-remove-option ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="Remove Option"><i class=\'fa fa-minus-circle\'></i></a>\n</div>\n\n';
/*  if (typeof includeOther !== 'undefined'){ ;
__p += '\n  <label>\n    <input type=\'checkbox\' data-rv-checked=\'model.' +
((__t = ( Formbuilder.options.mappings.INCLUDE_OTHER )) == null ? '' : __t) +
'\' />\n    Include "other"\n  </label>\n';
 } ; */
__p += '\n\n<div class=\'fb-bottom-add\'>\n  <a class="js-add-option ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'">Add option</a>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["edit/size"] = function(obj) {
/* Modificato 
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Size</div>\n<select data-rv-value="model.' +
((__t = ( Formbuilder.options.mappings.SIZE )) == null ? '' : __t) +
'">\n  <option value="small">Small</option>\n  <option value="medium">Medium</option>\n  <option value="large">Large</option>\n</select>\n';

}
return __p */
};

this["Formbuilder"]["templates"]["edit/units"] = function(obj) {
/* Modificato
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-edit-section-header\'>Units</div>\n<input type="text" data-rv-input="model.' +
((__t = ( Formbuilder.options.mappings.UNITS )) == null ? '' : __t) +
'" />\n';

}
return __p */
};

this["Formbuilder"]["templates"]["page"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p +=

((__t = ( Formbuilder.templates['partials/left_side']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.templates['partials/right_side']() )) == null ? '' : __t) +
'\n' +
((__t = ( Formbuilder.templates['partials/save_button']() )) == null ? '' : __t) +
'\n<div class=\'fb-clear\'></div>';

}
return __p
};

this["Formbuilder"]["templates"]["partials/add_field"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<div class=\'fb-tab-pane active\' id=\'addField\'>\n  <div class=\'fb-add-field-types\'>\n    <div class=\'section\'>\n      ';
 _.each(_.sortBy(Formbuilder.inputFields, 'order'), function(f){ ;
__p += '\n        <a data-field-type="' +
((__t = ( f.field_type )) == null ? '' : __t) +
'" class="' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'">\n          ' +
((__t = ( f.addButton )) == null ? '' : __t) +
'\n        </a>\n      ';
 }); ;
__p += '\n    </div>\n\n    <div class=\'section\'>\n      ';
 _.each(_.sortBy(Formbuilder.nonInputFields, 'order'), function(f){ ;
__p += '\n        <a data-field-type="' +
((__t = ( f.field_type )) == null ? '' : __t) +
'" class="' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'">\n          ' +
((__t = ( f.addButton )) == null ? '' : __t) +
'\n        </a>\n      ';
 }); ;
__p += '\n    </div>\n  </div>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["partials/edit_field"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-tab-pane\' id=\'editField\'>\n  <div class=\'fb-edit-field-wrapper\'></div>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["partials/left_side"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-left\'>\n  <ul class=\'fb-tabs\'>\n    <li class=\'active\'><a data-target=\'#addField\'>Add new field</a></li>\n    <li><a data-target=\'#editField\'>Edit field</a></li>\n  </ul>\n\n  <div class=\'fb-tab-content\'>\n    ' +
((__t = ( Formbuilder.templates['partials/add_field']() )) == null ? '' : __t) +
'\n    ' +
((__t = ( Formbuilder.templates['partials/edit_field']() )) == null ? '' : __t) +
'\n  </div>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["partials/right_side"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-right\'>\n  <div class=\'fb-no-response-fields\'>No response fields</div>\n  <div class=\'fb-response-fields\'></div>\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["partials/save_button"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'fb-save-wrapper\'>\n  <button class=\'js-save-form ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'\'></button>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["view/base"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
	//console.log(obj.rf);
__p += '<div class=\'subtemplate-wrapper\'>\n  <div class=\'cover\'></div>\n  ' +
((__t = ( Formbuilder.templates['view/label']({rf: rf}) )) == null ? '' : __t+" <span class='cid_box'>id: "+obj.rf.attributes.cid+"</span>") +
'\n\n  ' +
((__t = ( Formbuilder.fields[rf.get(Formbuilder.options.mappings.FIELD_TYPE)].view({rf: rf}) )) == null ? '' : __t) +
'\n\n  ' +
((__t = ( Formbuilder.templates['view/description']({rf: rf}) )) == null ? '' : __t) +
'\n  ' +
((__t = ( Formbuilder.templates['view/duplicate_remove']({rf: rf}) )) == null ? '' : __t) +
'\n</div>\n';

}
return __p
};

this["Formbuilder"]["templates"]["view/base_non_input"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '';

}
return __p
};

this["Formbuilder"]["templates"]["view/description"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<span class=\'help-block\'>\n  ' +
((__t = ( Formbuilder.helpers.simple_format(rf.get(Formbuilder.options.mappings.DESCRIPTION)) )) == null ? '' : __t) +
'\n</span>\n';

}
return __p
};

this["Formbuilder"]["templates"]["view/duplicate_remove"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape;
with (obj) {
__p += '<div class=\'actions-wrapper\'>\n  <a class="js-duplicate ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="Duplicate Field"><i class=\'fa fa-plus-circle\'></i></a>\n  <a class="js-clear ' +
((__t = ( Formbuilder.options.BUTTON_CLASS )) == null ? '' : __t) +
'" title="Remove Field"><i class=\'fa fa-minus-circle\'></i></a>\n</div>';

}
return __p
};

this["Formbuilder"]["templates"]["view/label"] = function(obj) {
obj || (obj = {});
var __t, __p = '', __e = _.escape, __j = Array.prototype.join;
function print() { __p += __j.call(arguments, '') }
with (obj) {
__p += '<label>\n  <span>' +
((__t = ( Formbuilder.helpers.simple_format(rf.get(Formbuilder.options.mappings.LABEL)) )) == null ? '' : __t) +
'\n  ';
 if (rf.get(Formbuilder.options.mappings.REQUIRED)) { ;
__p += '\n    <abbr title=\'required\'>*</abbr>\n  ';
 } ;
__p += '\n</label>\n';

}
return __p
};