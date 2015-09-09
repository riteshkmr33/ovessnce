function ucfirst(str) {

    var text = str.toLowerCase();


    var parts = text.split(' '),
            len = parts.length,
            i, words = [];
    for (i = 0; i < len; i++) {
        var part = parts[i];
        var first = part[0].toUpperCase();
        var rest = part.substring(1, part.length);
        var word = first + rest;
        words.push(word);

    }

    return words.join(' ');
}

function applyAutoComplete(element, fields)
{
    $(element).autoComplete({
        minChars: 1,
        source: function(term, suggest) {
            var suggestions = [];
            $.ajax({
                url: OVEconfig.BASEURL + '/practitioner/getaddress/',
                type: 'POST',
                async: false,
                data: {word: term, fields:fields},
                success: function(data) {
                    if (data) {
                        data = JSON.parse(data);
                        if (data.status == '1') {
                            $.each(data.results, function(key, value) {
                                suggestions.push(value);
                            });
                        }
                    }
                },
                error: function(xhr, errorType, errorMsg) {
                    console.log(errorMsg);
                }
            });
            /*term = term.toLowerCase();
             var choices = ['ActionScript', 'AppleScript', 'Asp', 'Assembly', 'BASIC', 'Batch', 'C', 'C++', 'CSS', 'Clojure', 'COBOL', 'ColdFusion', 'Erlang', 'Fortran', 'Groovy', 'Haskell', 'HTML', 'Java', 'JavaScript', 'Lisp', 'Perl', 'PHP', 'PowerShell', 'Python', 'Ruby', 'Scala', 'Scheme', 'SQL', 'TeX', 'XML'];
             var suggestions = [];
             for (i=0;i<choices.length;i++)
             if (~choices[i].toLowerCase().indexOf(term)) suggestions.push(choices[i]);
             */
            suggest(suggestions);
        }
    });
}

function applyAutoCompleteName(element, callback)
{
    $(element).autoComplete({
        minChars: 1,
        source: function(term, suggest) {
            var suggestions = [];
            $.ajax({
                url: OVEconfig.BASEURL + '/practitioner/getserviceproviders/',
                type: 'POST',
                async: false,
                data: {word: term},
                success: function(data) {
                    if (data) {
                        data = JSON.parse(data);
                        if (data.status == '1') {
                            $.each(data.results, function(key, value) {
                                suggestions.push([key, value]);
                            });
                        }
                    }
                },
                error: function(xhr, errorType, errorMsg) {
                    console.log(errorMsg);
                }
            });
            /*term = term.toLowerCase();
             var choices = ['ActionScript', 'AppleScript', 'Asp', 'Assembly', 'BASIC', 'Batch', 'C', 'C++', 'CSS', 'Clojure', 'COBOL', 'ColdFusion', 'Erlang', 'Fortran', 'Groovy', 'Haskell', 'HTML', 'Java', 'JavaScript', 'Lisp', 'Perl', 'PHP', 'PowerShell', 'Python', 'Ruby', 'Scala', 'Scheme', 'SQL', 'TeX', 'XML'];
             var suggestions = [];
             for (i=0;i<choices.length;i++)
             if (~choices[i].toLowerCase().indexOf(term)) suggestions.push(choices[i]);
             */
            suggest(suggestions);
        },
        onGenerate: function(data, i, re) {
            return '<div class="autocomplete-suggestion" data-key="' + data[i][0] + '" data-val="' + data[i][1] + '">' + data[i][1].replace(re, "<b>$1</b>") + '</div>';
        },
        onSelect: function(data) {
            if (callback != '') {
                callback(data);
            }
        },
    });
}

function setAddress(element, city, state, zip, country)
{
    var address = element.value.split(',');
    
    if (address.length == 4) {
        $(city).val(address[0].trim());
        $(zip).val(address[2].trim());
        //console.log($(country).find('option:contains("'+ucfirst(address[3].trim())+'")').attr('value'))
        $(country).val($(country).find('option:contains("' + ucfirst(address[3].trim()) + '")').last().attr('value'));
        $(state).val($(state).find('option:contains("' + ucfirst(address[1].trim()) + '")').last().attr('value'));
    } else {
        $(city).val(address[0].trim());
        $(zip).val(address[3].trim());
        //console.log($(country).find('option:contains("'+ucfirst(address[3].trim())+'")').attr('value'))
        (address[4].trim().length > 3)?$(country).val($(country).find('option:contains("' + ucfirst(address[4].trim()) + '")').last().attr('value')):$(country).val($(country).find('option:contains("' + address[4].trim() + '")').last().attr('value'));
        $(state).val($(state).find('option:contains("' + ucfirst(address[2].trim()) + '")').last().attr('value'));
    }
}