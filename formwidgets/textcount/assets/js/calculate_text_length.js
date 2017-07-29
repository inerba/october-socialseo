function calculateTextLength(field,maxlen){
    var text = field.val();
    var max = maxlen;
    var len = text.length;
    var char = max - len;

    if (len > max) {
        $(field.selector+'charNum').html('<p class="text-danger"><i class="icon-text-width"></i> <strong>' + (char*-1) + '</strong> caratteri in eccesso.</p>');
    } else if (len == max) {
        $(field.selector+'charNum').html('<p class="text-success"><i class="icon-text-width"></i> Lunghezza ideale! </p>');
    } else {
        $(field.selector+'charNum').html('<p class="text-success"><i class="icon-text-width"></i> <strong>'+ char + '</strong> caratteri rimanenti.</p>');
    }
}