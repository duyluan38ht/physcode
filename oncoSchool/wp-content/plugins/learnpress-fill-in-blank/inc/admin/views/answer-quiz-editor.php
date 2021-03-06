<?php
/**
 * Admin quiz editor: fib question answer template.
 *
 * @since 3.0.0
 */

?>

<script type="text/x-template" id="tmpl-lp-quiz-fib-question-answer">
    <div class="admin-quiz-fib-question-editor">
		<?php learn_press_fib_admin_view( 'answer-editor' ); ?>
    </div>
</script>

<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var $Vue = window.$Vue || Vue;
        var $store = window.LP_Quiz_Store;
        Vue.component('lp-quiz-fib-question-answer', {
            template: '#tmpl-lp-quiz-fib-question-answer',
            props: ['question'],
            data: function () {
                return {
                    valid: true,
                    canInsertNewBlank: false,
                    blanks: []
                }
            },
            computed: {
                answer: function () {
                    return {
                        answer_order: 1,
                        is_true: '',
                        question_answer_id: this.question.answers[0].question_answer_id,
                        text: this.question.answers[0].text,
                        value: ''
                    };
                },
                answers: function () {
                    return this.question.answers
                }
            },
            mounted: function () {
                var that = this,
                    content = this.getContent();
                this.blanks = this.answers[0].blanks;
                this.$editor = $(this.$el).find('.content-editable');
                this.$editor.html(content);
                this.parseBlanks(content);
                this.$editor[0].addEventListener("DOMCharacterDataModified", function (e) {
                    var $target = $(e.target).parent(), id = $target.data('id');
                    for (var i in that.blanks) {
                        if (that.blanks[i].id == id) {
                            that.blanks[i].fill = $target.html().trim();
                            that.$activeBlank = $target;
                            break;
                        }
                    }
                }, false);
                this.interval = setInterval(function (a) {
                    a.parseBlanks(a.getContent());
                }, 1000, this);
            },
            methods: {
                updateAnswer: function () {
                    var answer = JSON.parse(JSON.stringify(this.answer));
                    answer.text = this.getShortcode();
                    answer.blanks = this.getBlanksForDB();

                    $store.dispatch('lqs/updateQuestionAnswerTitle', {
                        question_id: this.question.id,
                        answer: answer
                    });
                },

                updateAnswerBlank: function (e, blank) {
                    this.updateAnswer();
                },
                getContent: function () {
                    var content = this.answers[0].text,
                        shortcodes = content.match(/\[fib.*?\]/g),
                        uids = {};

                    if (shortcodes) {
                        for (var i = 0; i < shortcodes.length; i++) {
                            var uid,
                                fill,
                                replaceText,
                                props = shortcodes[i].match(/([a-z_]+)="(.*?)"/g),
                                data = [];

                            for (var j in props) {
                                var prop = props[j].match(/([a-z_]+)="(.*?)"/);

                                if (!prop) {
                                    continue;
                                }

                                switch (prop[1]) {
                                    case 'uid':
                                    case 'id':
                                        uid = prop[2];
                                        break;
                                    case 'fill':
                                        fill = prop[2];
                                        break;
                                    default:
                                        data.push('data-' + prop[1] + '="' + prop[2] + '"')
                                }
                            }

                            uid = uid ? uid : LP.uniqueId();

                            if (uids[uid]) {
                                uid = LP.uniqueId();
                            }

                            replaceText = FIB.outerHTML(this.createBlank(fill, uid).attr('data-index', i + 1));// '<span class="fib-blank" id="fib-blank-' + uid + '" data-id="' + uid + '" data-index="' + (i + 1) + '">' + fill + '</span>';
                            uids[uid] = true;

                            content = content.replace(shortcodes[i], replaceText);
                        }
                    }
                    return content;
                },
                activeBlank: function (e) {
                    this.$activeBlank = $(e.target).closest('.fib-blank');
                },


                findBlank: function (id) {
                    for (var i in this.blanks) {
                        if (this.blanks[i].id == id) {
                            return this.blanks[i];
                        }
                    }

                    return false;
                },
                parseBlanks: function (content) {
                    var $container = this.$editor,
                        $inputs = $container.find('.fib-blank'),
                        $input,
                        data,
                        blanks = [], uids = [],
                        i = 0, n = 0;


                    for (i = 0; i < $inputs.length; i++) {
                        $input = $inputs.eq(i).attr('data-index', i + 1);
                        data = $input.data();

                        if (-1 !== $.inArray(data.id, uids)) {
                            data.id = LP.uniqueId();
                        }

                        var oldBlank = this.findBlank(data.id) || {};

                        blanks.push({
                            fill: $input.html().trim(),
                            id: data.id,
                            comparison: data.comparison || oldBlank.comparison || '',
                            match_case: data.match_case || oldBlank.match_case || 0,
                            index: i + 1,
                            open: !!oldBlank.open,
                        });
                        uids.push(data.id);
                    }
                    this.blanks = blanks;
                },
                updateBlanks: function (content) {
                    this.parseBlanks(content !== undefined ? content : this.$editor.html());
                    return this.getShortcode();
                },
                getShortcode: function () {
                    var that = this,
                        $container = this.$editor.clone(),
                        $blanks = $container.find('.fib-blank');

                    $blanks.each(function () {
                        var $blank = $(this),
                            id = $blank.attr('id'),
                            uid = id.replace('fib-blank-', ''),
                            blank = that.getBlankById(uid),
                            code = 'fib';

                        if (blank) {
                            if (!blank.id) {
                                return;
                            }
                            for (var i in blank) {
                                if ($.inArray(i, ['index']) !== -1) {
                                    continue;
                                }

                                if (!blank[i]) {
                                    continue;
                                }

                                code += ' ' + i + '="' + blank[i] + '"';
                            }
                            $blank.replaceWith('[' + code + ']');
                        } else {
                            console.log('Not found: ' + uid)
                            $blank.replaceWith('')
                        }
                    });
                    return $container.html();
                },
                getBlankById: function (id) {
                    var blank = false;
                    $.each(this.blanks, function () {
                        if (id == this.id) {
                            blank = this;
                            return true;
                        }
                    });
                    return blank;
                },
                updateBlank: function (e) {
                    var $el = $(e.target),
                        id = $el.attr('id'),
                        $blank = this.$editor.find('#' + id);
                    $blank.html(e.target.value);
                    this.updateAnswer();
                },
                removeBlank: function (e, id) {
                    e.preventDefault();
                    this.removeBlankById(id);
                    this.updateAll();
                },
                removeBlankById: function (id) {
                    var $blank = this.$editor.find('.fib-blank#fib-blank-' + id);
                    $blank.replaceWith($blank.html());
                },
                updateAll: function () {
                    this.answer.text = this.updateBlanks();
                    this.updateAnswer();
                },
                insertBlank: function () {
                    if (!this.canInsertNewBlank) {
                        return;
                    }

                    var $content = $(this.$el).find('.content-editable'),
                        content = $content.html(),
                        selectedText = FIB.getSelectedText(),
                        selectionRange = FIB.getSelectionRange(),
                        $blank = this.createBlank(selectedText),
                        nodeValue = selectionRange.anchorNode.nodeValue,
                        x = selectionRange.anchorOffset,
                        y = selectionRange.focusOffset;
                        startRange = x < y ? x : y;
                        endRange = startRange == x ? y: x;

                    selectionRange.anchorNode.nodeValue = nodeValue.substr(0, startRange);
                    $($blank).insertAfter(selectionRange.anchorNode);
                    $(FIB.createTextNode(nodeValue.substr(endRange))).insertAfter($blank);

                    var $blanks = $content.find('.fib-blank');
                    $blanks.each(function (i, el) {
                        var $blank = $(this);
                        $blank.attr('data-index', i + 1)
                    });

                    this.parseBlanks($content.html());
                    this.updateAnswer();
                },
                createBlank: function (content, id) {
                    if (!id) {
                        id = LP.uniqueId();
                    }
                    return $('<b class="fib-blank" id="fib-blank-' + id + '" data-id="' + id + '"> ' + content + '</b>');
                },
                clearBlanks: function () {
                    if (!confirm($store.getters['i18n/all'].confirm_remove_blanks)) {
                        return;
                    }

                    for (var i in this.blanks) {
                        this.removeBlankById(this.blanks[i].id);
                    }
                    this.updateAll();
                },
                clearContent: function () {
                    this.$editor.html('');
                    this.updateAnswer();
                },
                canInsertBlank: function () {
                    var $content = $(this.$el).find('.content-editable'),
                        content = $content.html(),
                        selectedText = FIB.getSelectedText(),
                        selectionRange = FIB.getSelectionRange();

                    this.canInsertNewBlank = selectedText.length && !FIB.isContainHtml(selectionRange.anchorNode);
                },
                getBlanksForDB: function () {
                    var blanks = {};
                    for (var i = 0, n = this.blanks.length; i < n; i++) {
                        var id = this.blanks[i].id.replace('fib-blank-', '');
                        blanks[id] = JSON.parse(JSON.stringify(this.blanks[i]));
                        blanks[id].id = id;
                    }
                    return blanks;
                },
                toggleOptions: function (e, id) {
                    e.preventDefault();
                    var that = this;
                    $(e.target).closest('.fib-blank').find('.blank-options ul').slideToggle(function () {
                        that.setBlankProp(id, 'open', !$(this).is(':hidden'))
                    })
                },
                setBlankProp: function (id, prop, value) {
                    for (var i in this.blanks) {
                        if (this.blanks[i].id == id) {
                            if ($.isPlainObject(prop)) {
                                for (var p in prop) {
                                    this.$set(this.blanks[i], p, prop[p]);
                                }
                            } else {
                                this.$set(this.blanks[i], prop, value);
                            }

                            break;
                        }
                    }
                    this.updateAnswer();
                }
                ////////////////////
//                parseBlanks: function (content) {
//                    var answer = this.question.answers[0],
//                        blanks = [];
//
//                    if (content !== undefined) {
//                        this.question.answers[0].text = content;
//                        this.answer.text = content;
//                    } else {
//                        content = answer.text;
//                    }
//
//                    var $container = $('<div />').html(content),
//                        $inputs = $container.find('.fib-blank'),
//                        i = 0, n = 0, data;
//
//                    for (i = 0; i < $inputs.length; i++) {
//                        data = $inputs.eq(i).data();
//                        blanks.push({
//                            fill: data.fill,
//                            id: data.id,
//                            comparison: data.comparison || '',
//                            match_case: data.match_case || 0,
//                            index: i + 1
//                        })
//                    }
//                    this.blanks = blanks;
//                    this.question.blanks = blanks;
//
//                    setTimeout($.proxy(function (blanks, content) {
//                        this.updateAnswer();
//                        this.editor.setContent(content);
//                    }, this), 300, blanks, $container.html())
//
//                },
//                updateBlanks: function (content) {
//                    this.parseBlanks(content !== undefined ? content : this.editor.getContent());
//                    return this.getShortcode();
//                },
//                getShortcode: function () {
//                    var that = this,
//                        $container = $('<div />').html(this.editor.getContent()),
//                        $blanks = $container.find('.fib-blank');
//
//                    $blanks.each(function () {
//                        var $blank = $(this),
//                            id = $blank.attr('id'),
//                            uid = id.replace('fib-blank-', ''),
//                            blank = that.getBlankById(uid),
//                            code = 'fib';
//                        if (blank) {
//                            if (!blank.id) {
//                                return;
//                            }
//                            for (var i in blank) {
//                                if ($.inArray(i, ['index']) !== -1) {
//                                    continue;
//                                }
//
//                                if (!blank[i]) {
//                                    continue;
//                                }
//
//                                code += ' ' + i + '="' + blank[i] + '"';
//                            }
//                            $blank.replaceWith('[' + code + ']');
//                        } else {
//                            console.log('Not found: ' + uid)
//                            $blank.replaceWith('')
//                        }
//                    });
//
//                    return $container.html();
//                },
//                getBlankById: function (id) {
//                    var blank = false;
//                    $.each(this.blanks, function () {
//                        if (id == this.id) {
//                            blank = this;
//                            return true;
//                        }
//                    });
//                    return blank;
//                },
//                parseShortcode: function (content) {
//
//                },
//                updateBlank: function (e) {
//                    var $el = $(e.target),
//                        id = $el.attr('id'),
//                        content = this.editor.getContent(),
//                        $wrap = $('<div />').html(content),
//                        $blank = $wrap.find('#' + id),
//                        pos = $wrap.find('.fib-blank').index($blank) + 1;
//
//                    $blank.attr('data-fill', e.target.value).html('[#' + pos + ']');
//
//                    this.editor.setContent($wrap.html());
//                       if(e.type == 'change'){
//                       		this.editor.save();
//                       }
//                },
//                removeBlank: function (e) {
//                    var that = this,
//                        $li = $(e.target).closest('.fib-blank'),
//                        id = $li.attr('data-id'),
//                        $container = $('<div />').html(this.editor.getContent()),
//                        $blank = $container.find('.fib-blank#' + id);
//
//                    $blank.replaceWith($blank.attr('data-fill'));
//                    this.editor.setContent($container.html());
//                    this.editor.save();
//
//                    $(this.$el).find('.fib-blanks .blank-fill input').each(function () {
//                        that.updateBlank({target: this});
//                    });
//
//                    var blanks = JSON.parse(JSON.stringify(this.blanks));
//                    for (var i = 0; i < blanks.length; i++) {
//                        if (blanks[i].id === id) {
//                            blanks.splice(i, 1);
//                            for (var j = 0; j < blanks.length; j++) {
//                                blanks[j].index = j + 1;
//                            }
//                            break;
//                        }
//                    }
//
//                    this.blanks = blanks;
//                    this.updateAll();
//
//                    e.preventDefault();
//                },
//                onClear: function () {
//                    this.blanks = [];
//                    this.updateAll();
//                },
//                updateAll: function () {
//                    this.answer.text = this.updateBlanks();
//                    this.updateAnswer();
//                },
//                insertBlank: function () {
//                    this.editor.buttons['fib-code'].onclick();
//                },
//                clearBlanks: function () {
//                    this.editor.buttons['fib-clear'].onclick();
//                },

//                getBlanksForDB: function () {
//                    var blanks = {};
//                    for (var i = 0, n = this.blanks.length; i < n; i++) {
//                        var id = this.blanks[i].id.replace('fib-blank-', '');
//                        blanks[id] = JSON.parse(JSON.stringify(this.blanks[i]));
//                        blanks[id].id = id;
//                    }
//                    return blanks;
//                },
//                toggleOptions: function (e) {
//                    e.preventDefault();
//                    $(e.target).closest('.fib-blank').find('.blank-options ul').slideToggle()
//                },
//                maybeUpdateEditor: function () {
//
//                }
            },
            ///
//            created: function () {
//                init.apply(this);
//            }
        })
    });

</script>
