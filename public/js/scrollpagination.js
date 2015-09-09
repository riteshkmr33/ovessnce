/*
 **	Anderson Ferminiano
 **	contato@andersonferminiano.com -- feel free to contact me for bugs or new implementations.
 **	jQuery ScrollPagination
 **	28th/March/2011
 **	http://andersonferminiano.com/jqueryscrollpagination/
 **	You may use this script for free, but keep my credits.
 **	Thank you.
 **  Modified by piyush arya <piyush@clavax.us> 
 */

(function($) {


    $.fn.scrollPagination = function(options) {

        var opts = $.extend($.fn.scrollPagination.defaults, options);
        var target = opts.scrollTarget;
        if (target == null) {
            target = obj;
        }
        opts.scrollTarget = target;

        return this.each(function() {
            $.fn.scrollPagination.init($(this), opts);
        });

    };

    $.fn.stopScrollPagination = function() {
        return this.each(function() {
            $(this).attr('scrollPagination', 'disabled');
        });

    };

    $.fn.scrollPagination.loadContent = function(obj, opts) {

        // which style is active i.e 'list' or 'grid'
        if ($('#list-view-data').is(':visible')) {
            var style = 'list';
        } else {
            var style = 'grid';
        }

        var page = $("input[name='next']").val(); // getting the next page 

        var target = opts.scrollTarget;
        var mayLoadContent = $(target).scrollTop() + opts.heightOffset >= $(document).height() - $(target).height();
        if (mayLoadContent) {
            if (opts.beforeLoad != null) {
                opts.beforeLoad();
            }
            $(obj).children().attr('rel', 'loaded');
            var dataToSend = getDataForList();  // defined on splist.js
            dataToSend.next = page
            $.ajax({
                type: 'POST',
                url: opts.contentPage,
                data: dataToSend,
                beforeSend: function() {
                    paginateflag = false;
                },
                success: function(data) {
                    var list = new Array();
                    var grid = new Array();

                    var allData = $.parseJSON(data);
                    dataCount(allData);
                    var next = allData.next;
                    if (next != '') {
                        //var page = next.split('?')[1].split('=')[1];
                        var page = next;
                    } else {
                        var page = '';
                    }
                    $('input[name=next]').val(page); // set next page value

                    if (allData['result'].length > 0) {
                        var result = prepareData(list, grid, allData['result'], true);
                        list_html = result[0];
                        grid_html = result[1];
                    } else {
                        list_html = '';
                        grid_html = '';
                    }
                    var script = '//s7.addthis.com/js/300/addthis_widget.js#domready=1';
                    if (window.addthis) {
                        window.addthis = null;
                        window._ate = null;
                    }
                    $.getScript(script);

                    $('#list-data').append(list_html);
                    $('#grid-data').append(grid_html);
                    //$(obj).append(data); 

                    var objectsRendered = $(obj).children('[rel!=loaded]');

                    if (opts.afterLoad != null) {
                        opts.afterLoad(objectsRendered);
                    }

                    if (page == '') {
                        // if next is null means this is a last page hence no ajax calls required
                        paginateflag = false;
                        $('#nomoreresults').fadeIn();
                        $('#list-data').stopScrollPagination();
                        $('#grid-data').stopScrollPagination();
                    } else {
                        // continue ajax request
                        paginateflag = true;
                    }
                },
                dataType: 'html'
            });
        }

    };

    $.fn.scrollPagination.init = function(obj, opts) {
        var target = opts.scrollTarget;
        $(obj).attr('scrollPagination', 'enabled');

        $(target).scroll(function(event) {
            if (paginateflag) {
                if ($(obj).attr('scrollPagination') == 'enabled') {
                    $.fn.scrollPagination.loadContent(obj, opts);
                }
                else {
                    event.stopPropagation();
                }
            }
        });

        $.fn.scrollPagination.loadContent(obj, opts);

    };

    $.fn.scrollPagination.defaults = {
        'contentPage': null,
        'contentData': {},
        'beforeLoad': null,
        'afterLoad': null,
        'scrollTarget': null,
        'heightOffset': 0
    };
})(jQuery);
